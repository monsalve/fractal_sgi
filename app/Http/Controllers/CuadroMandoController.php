<?php

namespace App\Http\Controllers;

use App\Actividad;
use App\Asignacion;
use App\ConfProyecto;
use App\RaicContingencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CuadroMandoController extends Controller
{
    /**
     * Muestra una lista de las actividades por opción.
     *
     * @return \Illuminate\Http\Response
     */
    public function informeActividades(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $fkIdEmpresa = Session::get('id_empresa');
        $anioActual = date('Y');
        // Calcula el encabezado Actividades
        $encabezado = Actividad::join('planes_accion', 'actividades.fk_id_planes_accion', '=', 'planes_accion.id')
        ->select('actividades.id', 'ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic')
        ->where([
            ['planes_accion.anio', '=', intval($anioActual)]
            , ['actividades.estado', '=', 1]
            , ['actividades.fk_id_empresas', '=', $fkIdEmpresa]
        ])->with('seguimientos:mes,fk_id_actividades')->get();

        // Actividades cumplidas en todo el año
        $actAnualCump = $encabezado->pluck('seguimientos')->collapse()->count();
        // Actividades programadas en todo el año
        $actAnual = $encabezado->sum(function ($actividad) {
            return $actividad->ene + $actividad->feb + $actividad->mar + $actividad->abr
                + $actividad->may + $actividad->jun + $actividad->jul + $actividad->ago
                + $actividad->sep + $actividad->oct + $actividad->nov + $actividad->dic;
        });

        $mesActual = date('n');
        $diaActual = date('j');
        $meses = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
        $mesesHastaHoy = [];
        $actHastaHoyCump = 0;
        // Actividades programadas hasta hoy
        $actHastaHoy = $encabezado->sum(function ($actividad) use ($meses, $mesActual, $diaActual, &$actHastaHoyCump) {
            $cont = 0;
            $mesLimite = $actividad->diaMesLimite < $diaActual ? $mesActual - 1 : $mesActual;
            foreach ($meses as $key => $mes) {
                if ($key == $mesLimite) break;
                $cont += $actividad->$mes;
                // Actividades cumplidas hasta hoy
                if ($actividad->seguimientos->where('mes', $mes)->isNotEmpty()) $actHastaHoyCump++;
            }
            return $cont;
        });

        // Calcula el contenido de la card de Actividades
        $abrev = $mesActual == '1'? 'ene'
        : ( $mesActual == '2'? 'feb'
        : ( $mesActual == '3'? 'mar'
        : ( $mesActual == '4'? 'abr'
        : ( $mesActual == '5'? 'may'
        : ( $mesActual == '6'? 'jun'
        : ( $mesActual == '7'? 'jul'
        : ( $mesActual == '8'? 'ago'
        : ( $mesActual == '9'? 'sep'
        : ( $mesActual =='10'? 'oct'
        : ( $mesActual =='11'? 'nov'
        : 'dic'))))))))));
        
        $actividades = Actividad::join('planes_accion', 'actividades.fk_id_planes_accion', '=', 'planes_accion.id')
        ->join('segmentos', 'actividades.fk_id_segmentos', '=', 'segmentos.id')
        ->join('proyectos', 'actividades.fk_id_proyectos', '=', 'proyectos.id')
        ->leftJoin('actividades_seguimientos', function ($join) use ($abrev) {
            $join->on('actividades_seguimientos.fk_id_actividades', '=', 'actividades.id')->where('mes', '=', $abrev);
        })
        ->select(
            'actividades.id'
            , 'actividad'
            , 'diaMesLimite'
            , 'fechaReporte'
            , 'fk_id_planes_accion'
            , 'planes_accion.planAccion'
            , 'actividades.fk_id_segmentos'
            , 'segmentos.segmento'
            , 'fk_id_proyectos'
            , 'proyectos.proyecto'
        )->where([
            [$abrev, '=', 1]
            , ['actividades.estado', '=', 1]
            , ['actividades.fk_id_empresas', '=', $fkIdEmpresa]
        ])->orderBy('actividad')->with('responsables:users.id,nombre_completo')->get();

        $segmentos = $actividades->unique('fk_id_segmentos')->map->only(['fk_id_segmentos', 'segmento'])->sortBy('segmento')->values();
        $planesAccion = $actividades->unique('fk_id_planes_accion')->map->only(['fk_id_planes_accion', 'planAccion'])->sortBy('planAccion')->values();
        $proyectos = $actividades->unique('fk_id_proyectos')->map->only(['fk_id_proyectos', 'proyecto'])->sortBy('proyecto')->values();

        $asignacionesPorAnio = Asignacion::select('condicion')->where([
            ['fk_id_empresas', '=', 2]
            , ['estado', '=', 1]
        ])->whereBetween('fechaLimite', ["$anioActual-01-01", "$anioActual-12-31"])->get();

        // Proyectos, pendientes y asignaciones programados en el mes actual
        $mdaProyectos = ConfProyecto::select('id', 'proyecto')->where([
            ['estado', '=', 1]
            , ['fk_id_empresas', '=', $fkIdEmpresa]
        ])->with([
            'pendientes' => function ($query) {
                $query->select('id', 'pendiente', 'condicion', 'fk_id_proyectos')->where('estado', '=', 1)->orderBy('pendiente');
            }
            , 'pendientes.asignaciones' => function ($query) use ($anioActual, $mesActual) {
                $query->select('id', 'fechaLimite', 'asignacion', 'condicion', 'fk_id_pendientes', 'fk_respons_users')->where('estado', '=', 1)->whereBetween('fechaLimite', ["$anioActual-$mesActual-01", "$anioActual-$mesActual-31"])->orderBy('asignacion');
            }
            , 'pendientes.asignaciones.responsable:id,nombre_completo'
        ])->get();

        $contingenciasPorAnio = RaicContingencia::select('condicion')->where([
            ['fk_id_empresas', '=', 2]
            , ['estado', '=', 1]
        ])->whereBetween('fechaLimite', ["$anioActual-01-01", "$anioActual-12-31"])->get();

        // Proyectos, raics y contingencias programados en el mes actual
        $raicProyectos = ConfProyecto::select('id', 'proyecto')->where([
            ['estado', '=', 1]
            , ['fk_id_empresas', '=', $fkIdEmpresa]
        ])->with([
            'raics' => function ($query) {
                $query->select('id', 'fechaReporte', 'descripcionCorta', 'condicion', 'fk_id_proyectos')->where('estado', '=', 1)->orderBy('descripcionCorta');
            }
            , 'raics.contingencias' => function ($query) use ($anioActual, $mesActual) {
                $query->select('id', 'consecutivo', 'fechaLimite', 'condicion', 'fk_id_raics', 'fk_respons_users')->where('estado', '=', 1)->whereBetween('fechaLimite', ["$anioActual-$mesActual-01", "$anioActual-$mesActual-31"])->orderBy('consecutivo');
            }
            , 'raics.contingencias.responsable:id,nombre_completo'
        ])->get();
        
        return [
            'actsAnual' => $actAnual
            , 'actsCump' => $actAnualCump
            , 'actsHastaHoy' => $actHastaHoy
            , 'actsHastaHoyCump' => $actHastaHoyCump

            , 'actividades' => $actividades//->map->only(['id', 'actividad', 'fk_id_planes_accion', 'fk_id_segmentos', 'fk_id_proyectos', 'fechaReporte', 'responsables'])
            , 'segmentos' => $segmentos
            , 'planesAccion' => $planesAccion
            , 'proyectos' => $proyectos

            , 'asigsAnual' => $asignacionesPorAnio->count()
            , 'asigsAnualCump' => $asignacionesPorAnio->where('condicion', 'Cerrado')->count()
            , 'mdaProyectos' => $mdaProyectos

            , 'contsAnual' => $contingenciasPorAnio->count()
            , 'contsAnualCump' => $contingenciasPorAnio->where('condicion', 'Cerrado')->count()
            , 'raicProyectos' => $raicProyectos
        ];
    }

    /**
     * Muestra una lista de las actividades por segmento.
     *
     * @return \Illuminate\Http\Response
     */
    public function actividadesPorSegmento()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
