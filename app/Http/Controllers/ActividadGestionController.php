<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ActividadSeguimiento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ActividadGestionController extends Controller
{
    // SOLO LOS USUARIOS REGISTRADOS COMO RESPONSABLES DE UNA ACTIVIDAD PUEDEN GESTIONARLAS
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $respuesta = DB::table('indicadores_actividades')
        ->join('indicadores', 'fk_id_indicadores', '=', 'indicadores.id')
        ->rightJoin('actividades', 'indicadores_actividades.fk_id_actividades', '=', 'actividades.id')
        ->join('usuarios_actividades', 'usuarios_actividades.fk_id_actividades', '=', 'actividades.id')
        ->leftJoin('planes_accion AS pa_asociados', 'fk_planAsoc_planes_accion', '=', 'pa_asociados.id')
        ->join('planes_accion', 'fk_id_planes_accion', '=', 'planes_accion.id')
        ->join('segmentos', 'actividades.fk_id_segmentos', '=', 'segmentos.id')
        ->join('proyectos', 'fk_id_proyectos', '=', 'proyectos.id');
        
        $where = [
            ['planes_accion.anio', '=', $request->anio]
            , ['actividades.fk_id_empresas', '=', Session::get('id_empresa')]
            , ['fk_id_users', '=', Auth::user()->id]
            , ['actividades.estado', '=', 1]
        ];
        if ($request->fkIdPlanesAccion) {
            $where[] = ['fk_id_planes_accion', '=', $request->fkIdPlanesAccion];
        }
        if ($request->fkIdSegmentos) {
            $where[] = ['actividades.fk_id_segmentos', '=', $request->fkIdSegmentos];
        }
        if ($request->fkIdProyectos) {
            $where[] = ['fk_id_proyectos', '=', $request->fkIdProyectos];
        }
        $respuesta->where($where)->select(
            'actividades.id'
            , 'actividad'
            , 'segmentos.segmento'
            , 'planes_accion.planAccion'
            , 'proyecto'
            , 'ponderacion'
            , 'indicador'
            , 'pa_asociados.planAccion as planAsociado'
            , 'diaMesLimite'
            , 'ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'
        );
        if ($request->ordenColumna) {
            $respuesta->orderBy($request->ordenColumna, $request->ordenSentido);
        }
        $actividades = $respuesta->paginate($request->numRegs);
        
        $seguimientos = ActividadSeguimiento::select('id', 'mes', 'fk_id_actividades')->whereIn('fk_id_actividades', $actividades->pluck('id'))->get();
        $arrMeses = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
        $collSegs = [];
        $fechaActual = date('Ymd');
        foreach($actividades as $actividad) {
            $arrRegSegsAnual = [];
            $diaLimite = $actividad->diaMesLimite < 10 ? "0$actividad->diaMesLimite" : $actividad->diaMesLimite; // 1-9 -> 01-09 | 10-31 
            foreach ($arrMeses as $indice => $mes) {
                if ($actividad->{$mes}) {
                    $arrRegSegsAnual[$mes]['estilo'] = 'btn btn-success btn-sm';
                    $filtroSegs = $seguimientos->where('fk_id_actividades', $actividad->id)->firstWhere('mes', $mes);
                    if ($filtroSegs) {
                        $arrRegSegsAnual[$mes]['id'] = $filtroSegs->id;
                    } else {
                        $arrRegSegsAnual[$mes]['estilo'] = 'btn btn-warning btn-sm';
                        $mesIndice = $indice + 1;
                        $mesActividad = ($mesIndice < 10 ? '0' : ''). $mesIndice;
                        $fechaActividad = $request->anio .''. $mesActividad .''. $diaLimite;
                        if ($fechaActual > $fechaActividad) {
                            $arrRegSegsAnual[$mes]['estilo'] = 'btn btn-danger btn-sm';
                        }
                    }
                }
            }
            $collSegs[$actividad->id] = $arrRegSegsAnual;
        }
        return [
            'pagination' => [
                'total'        => $actividades->total(),
                'current_page' => $actividades->currentPage(),
                'per_page'     => $actividades->perPage(),
                'last_page'    => $actividades->lastPage(),
                'from'         => $actividades->firstItem(),
                'to'           => $actividades->lastItem(),
            ]
            , 'actividades' => $actividades
            , 'collSegs' => $collSegs
        ];
    }
}