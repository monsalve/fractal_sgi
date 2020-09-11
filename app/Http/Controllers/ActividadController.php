<?php

namespace App\Http\Controllers;

use App\User;
use App\Empresa;
use App\Actividad;
use App\PlanAccion;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Actividad as ActividadNotificador;

class ActividadController extends Controller
{
    public function basicoListarPlanesAccionAsociables(Request $request)
    {
        $planesAccionAsoc = PlanAccion::select('id', 'planAccion')->where([
            ['fk_id_empresas', '=', Session::get('id_empresa')]
            //, ['fk_id_segmentos', '=', $request->segmentoId]
            , ['id', '!=', $request->planAccionId]
        ])->orderBy('planAccion')->get();

        return ['planesAccionAsoc' => $planesAccionAsoc];
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $respuestaActividades = Actividad::leftJoin('planes_accion', 'actividades.fk_planAsoc_planes_accion', '=', 'planes_accion.id')
        ->whereIn('actividades.estado', [0, 1]);
        $where = [
            ['actividades.fk_id_empresas', '=', Session::get('id_empresa')]
            , ['actividades.fk_id_segmentos', '=', intval($request->segmentoId)]
            , ['actividades.fk_id_planes_accion', '=', intval($request->planAccionId)]
            , ['actividades.fk_id_proyectos', '=', intval($request->proyectoId)]
        ];
        if ($request->leer != 1) {
            $where[] = ['actividades.fk_usuCrea_users', '=', Auth::user()->id];
        }
        $actividades = $respuestaActividades->where($where)->select(
            'actividades.id'
            , 'actividad'
            , 'tipoActividad'
            , 'ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'
            , 'diaMesLimite'
            , 'ponderacion'
            , 'actividades.estado'
            , 'fk_planAsoc_planes_accion'
            , 'planes_accion.planAccion'
        )->paginate($request->numRegs);
        
        $respuestaRespons = DB::table('usuarios_actividades')
        ->join('users', 'usuarios_actividades.fk_id_users', '=', 'users.id')
        ->whereIn('usuarios_actividades.fk_id_actividades', $actividades->pluck('id'))
        ->select('usuarios_actividades.fk_id_actividades', 'usuarios_actividades.fk_id_users', 'users.nombre_completo')
        ->orderBy('usuarios_actividades.fk_id_actividades')->orderBy('users.nombre_completo')->get();

        foreach ($actividades as $actividad) {
            $actividadRespons = [];
            foreach ($respuestaRespons as $respon) {
                if ($respon->fk_id_actividades == $actividad->id) {
                    $actividadRespons[] = ['id' => $respon->fk_id_users, 'nombre_completo' => $respon->nombre_completo];
                }
            }
            $actividad->setAttribute('responsables', $actividadRespons);
        }

        return [
            'pagination' => [
                'total'        => $actividades->total(),
                'current_page' => $actividades->currentPage(),
                'per_page'     => $actividades->perPage(),
                'last_page'    => $actividades->lastPage(),
                'from'         => $actividades->firstItem(),
                'to'           => $actividades->lastItem(),
            ],
            'actividades' => $actividades
        ];
    }

    // Envía notificaciones vía correo electrónico a los nuevos responsables asignados a la actividad.
    public function notificar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $actividad = Actividad::find($request->actividadId);
        $destinatarios = User::with('persona:id,email')->find(Arr::pluck($request->destinatarios, 'id'))->pluck('persona');

        Notification::send($destinatarios, new ActividadNotificador(
            $actividad->actividad
            , $actividad->planAccion->planAccion
            , $actividad->planAccion->segmento->segmento
            , $actividad->proyecto->proyecto
            , $actividad->empresa->nombre
        ));
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $idEmpresa = Session::get('id_empresa');
        $usu = Auth::user()->id;

        $meses = [];
        foreach ($request->mesesArr as $mesRegistrado) {
            $meses[$mesRegistrado['id']] = 1;
        }
        $nuevaActividad = $request->only([
            'actividad'
            , 'diaMesLimite'
            , 'fk_id_planes_accion'
            , 'fk_id_segmentos'
            , 'fk_id_proyectos'
            #, 'fk_planAsoc_planes_accion'
            , 'ponderacion'
            , 'tipoActividad'
        ]) + $meses + [
            'fk_id_empresas' => $idEmpresa,
            'fk_usuCrea_users' => $usu,
            'fk_usuActualiza_users' => $usu,
        ];
        
        $actividad = Actividad::create($nuevaActividad);

        $responsIds = Arr::pluck($request->responsArr, 'id');

        $nuevosResponsables =[];
        foreach ($responsIds as $responsId) {
            $nuevosResponsables[$responsId] = ['fk_id_empresas' => $idEmpresa, 'fk_usuCrea_users' => $usu];
        }
        $actividad->responsables()->attach($nuevosResponsables);

        return ['actividadId' => $actividad->id];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Actividad  $actividad
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Actividad $actividad)
    {
        if (!$request->ajax()) return redirect('/');
        $empresaId = Session::get('id_empresa');
        $usu = Auth::user()->id;

        if ($request->actividad != $actividad->actividad)
            $actividad->actividad = $request->actividad;

        if ($request->diaMesLimite != $actividad->diaMesLimite)
            $actividad->diaMesLimite = $request->diaMesLimite;

        if ($request->fk_planAsoc_planes_accion != $actividad->fk_planAsoc_planes_accion)
            $actividad->fk_planAsoc_planes_accion = $request->fk_planAsoc_planes_accion;

        if ($request->ponderacion != $actividad->ponderacion)
            $actividad->ponderacion = $request->ponderacion;

        if ($request->tipoActividad != $actividad->tipoActividad)
            $actividad->tipoActividad = $request->tipoActividad;
        
        $arrMeses = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
        $mesesElegidos = $request->mesesArr;
        foreach ($arrMeses as $mes) {
            $actividad->{$mes} = 0;
        }
        foreach ($mesesElegidos as $mesElegido) {
            $actividad->{$mesElegido['id']} = 1;
        }

        if ($actividad->isDirty()) {
            $actividad->fk_usuActualiza_users = $usu;
            $actividad->save();
        }

        $nuevosResponsables = User::with('persona:id,email')->find(Arr::pluck($request->responsArr, 'id'))->pluck('persona');
        $porNotificar = $nuevosResponsables->diff($actividad->responsables()->with('persona:id,email')->get()->pluck('persona'));

        $actividad->responsables()->detach();
        $usuariosActividad = [];
        foreach ($nuevosResponsables as $responsable) {
            $usuariosActividad[$responsable->id] = ['fk_id_empresas' => $empresaId, 'fk_usuCrea_users' => $usu];
        }
        $actividad->responsables()->attach($usuariosActividad);
    }

    public function desactivar(Request $request, Actividad $actividad)
    {
        if (!$request->ajax()) return redirect('/');
        $actividad->estado = 0;
        $actividad->save();
    }
    public function activar(Request $request, Actividad $actividad)
    {
        if (!$request->ajax()) return redirect('/');
        $actividad->estado = 1;
        $actividad->save();
    }
}
