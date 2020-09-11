<?php

namespace App\Http\Controllers;

use App\User;
use App\PlanAccion;
use Illuminate\Support\Arr;
//use App\Jobs\ProcessPlanAccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Notifications\PlanAccion as PlanAccionNotificador;

class PlanAccionController extends Controller
{
    public function basicoProyectosRegistrados(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $respuesta = DB::table('planes_accion_proyectos')
        ->join('proyectos', 'planes_accion_proyectos.fk_id_proyectos', '=', 'proyectos.id')
        ->where('fk_id_planes_accion', '=', $request->planAccionId)
        ->select('proyectos.id', 'proyectos.proyecto')->get();
        return ['proyectosRegistrados' => $respuesta];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $respuesta = PlanAccion::join('users', 'planes_accion.fk_respons_users', '=', 'users.id')
        ->join('segmentos', 'planes_accion.fk_id_segmentos', '=', 'segmentos.id')
        ->select(
            'planes_accion.id'
            , 'planes_accion.planAccion'
            , 'planes_accion.anio'
            , 'planes_accion.puntaje'
            , 'planes_accion.fk_respons_users'
            , 'users.nombre_completo'
            , 'planes_accion.estado'
            , 'planes_accion.fk_id_segmentos'
            , 'segmentos.segmento'
        );
        
        $where = [];
        $where[] = ['planes_accion.fk_id_empresas', '=', Session::get('id_empresa')];
        if ($request->anio) {
            $where[] = ['planes_accion.anio', '=', $request->anio];
        }
        if (isset($request->estado)) {
            $where[] = ['planes_accion.estado', '=', $request->estado];
        }
        if ($request->leer != 1) {
            $where[] = ['planes_accion.fk_usuCrea_users', '=', Auth::user()->id];
        }
        if ($request->planAccion) {
            $where[] = ['planes_accion.planAccion', 'LIKE', "%$request->planAccion%"];
        }
        $where[] = ['planes_accion.fk_id_segmentos', '=', $request->segmentoId];
        if ($request->proyectoId) {
            $planAccionIds = DB::table('planes_accion_proyectos')->where('fk_id_proyectos', '=', $request->proyectoId)->select('fk_id_planes_accion');
            $respuesta->whereIn('planes_accion.id', $planAccionIds->pluck('fk_id_planes_accion'));
        }
        if ($request->userRespons) {
            $where[] = ['planes_accion.fk_respons_users', '=', $request->userRespons];
        }
        $planesAccion = $respuesta->where($where)->orderBy('planes_accion.id', 'desc')->paginate($request->numRegs);
        return [
            'pagination' => [
                'total'        => $planesAccion->total(),
                'current_page' => $planesAccion->currentPage(),
                'per_page'     => $planesAccion->perPage(),
                'last_page'    => $planesAccion->lastPage(),
                'from'         => $planesAccion->firstItem(),
                'to'           => $planesAccion->lastItem(),
            ],
            'planesAccion' => $planesAccion
        ];
    }

    public function notificar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $planAccion = PlanAccion::find($request->planAccionId);

        User::find($request->destinatarioId)->persona->notify(new PlanAccionNotificador(
            $planAccion->planAccion
            , $planAccion->segmento->segmento
            , $planAccion->empresa->nombre
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
        $nuevoPlanAccion = $request->except('proyectoArr') + [
            'fk_id_empresas' => $idEmpresa,
            'fk_usuCrea_users' => $usu,
            'fk_usuActualiza_users' => $usu,
        ];
        $planAccion = PlanAccion::create($nuevoPlanAccion);
        $proyectoIds = Arr::pluck($request->proyectoArr, 'id');

        $nuevosProyectos = [];
        foreach ($proyectoIds as $proyectoId) {
            $nuevosProyectos[$proyectoId] = ['fk_id_empresas' => $idEmpresa, 'fk_usuCrea_users' => $usu];
        }
        $planAccion->proyectos()->attach($nuevosProyectos);
        
        return ['planAccionId' => $planAccion->id];
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PlanAccion  $planAccion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PlanAccion $planAccion)
    {
        if (!$request->ajax()) return redirect('/');
        $idEmpresa = Session::get('id_empresa');
        $usu = Auth::user()->id;

        if ($request->anio != $planAccion->anio)
            $planAccion->anio = $request->anio;

        if ($request->fk_id_segmentos != $planAccion->fk_id_segmentos)
            $planAccion->fk_id_segmentos = $request->fk_id_segmentos;

        if ($request->fk_respons_users != $planAccion->fk_respons_users)
            $planAccion->fk_respons_users = $request->fk_respons_users;

        if ($request->planAccion != $planAccion->planAccion)
            $planAccion->planAccion = $request->planAccion;

        if ($request->puntaje != $planAccion->puntaje)
            $planAccion->puntaje = $request->puntaje;

        if ($planAccion->isDirty()) {
            $planAccion->fk_usuActualiza_users = $usu;
            $planAccion->save();
        }
        $planAccion->proyectos()->detach();
        $proyectoIds = Arr::pluck($request->proyectoArr, 'id');

        $nuevosProyectos = [];
        foreach ($proyectoIds as $proyectoId) {
            $nuevosProyectos[$proyectoId] = ['fk_id_empresas' => $idEmpresa, 'fk_usuCrea_users' => $usu];
        }
        $planAccion->proyectos()->attach($nuevosProyectos);
    }
    public function desactivar(Request $request, PlanAccion $planAccion)
    {
        if (!$request->ajax()) return redirect('/');
        $planAccion->estado = 0;
        $planAccion->save();
    }
    public function activar(Request $request, PlanAccion $planAccion)
    {
        if (!$request->ajax()) return redirect('/');
        $planAccion->estado = 1;
        $planAccion->save();
    }
}
