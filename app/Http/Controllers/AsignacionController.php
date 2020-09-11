<?php

namespace App\Http\Controllers;

use App\User;
use App\Asignacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AsignacionController extends Controller
{
    public function userSelector(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $users = User::paraSelector(Session::get('id_empresa'));
        return ['users' => $users];
    }

    /**
     * Display a listing of the resource by pendiente.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPorPendiente(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $respuesta = Asignacion::join('users', 'asignaciones.fk_respons_users', '=', 'users.id')
        ->select('asignaciones.id', 'fechaLimite', 'asignacion', 'observacion', 'alCerrar', 'alAbrir', 'asignaciones.condicion', 'estado', 'fk_respons_users', 'nombre_completo');
        $where = [['fk_id_pendientes', '=', $request->fkIdPendientes]];
        if ($request->fkResponsUsers) $where[] = ['fk_respons_users', '=', $request->fkResponsUsers];
        if ($request->condicion) $where[] = ['asignaciones.condicion', '=', intval($request->condicion)];
        if (isset($request->estado)) $where[] = ['estado', '=', $request->estado];

        $asignaciones = $respuesta->where($where)->paginate($request->numRegs);

        $asignaciones->each(function ($asignacion) {
            $asignacion->alAbrir = nl2br($asignacion->alAbrir, false);
            $asignacion->alCerrar = str_replace('..', '</em>', str_replace('- Resp:', '- <em>Resp:', nl2br($asignacion->alCerrar, false)));
        });

        return [
            'pagination' => [
                'total'        => $asignaciones->total()
                , 'current_page' => $asignaciones->currentPage()
                , 'per_page'     => $asignaciones->perPage()
                , 'last_page'    => $asignaciones->lastPage()
                , 'from'         => $asignaciones->firstItem()
                , 'to'           => $asignaciones->lastItem()
            ]
            , 'asignaciones' => $asignaciones
        ];
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
        $usu = Auth::user()->id;
        
        $nuevaAsignacion = $request->all() + [
            'fk_id_empresas' => Session::get('id_empresa')
            , 'fk_usuCrea_users' => $usu
            , 'fk_usuActualiza_users' => $usu
        ];

        Asignacion::create($nuevaAsignacion);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Asignacion  $asignacion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Asignacion $asignacion)
    {
        if (!$request->ajax()) return redirect('/');
        if ($request->fechaLimite != $asignacion->fechaLimite)
            $asignacion->fechaLimite = $request->fechaLimite;

        if ($request->asignacion != $asignacion->asignacion)
            $asignacion->asignacion = $request->asignacion;
        
        if ($request->observacion != $asignacion->observacion)
            $asignacion->observacion = $request->observacion;
        
        if ($request->fk_respons_users != $asignacion->fk_respons_users)
            $asignacion->fk_respons_users = $request->fk_respons_users;

        if ($asignacion->isDirty()) {
            $asignacion->fk_usuCrea_users = Auth::user()->id;
            $asignacion->save();
        }
    }

    public function cerrar(Request $request, Asignacion $asignacion)
    {
        if (!$request->ajax()) return redirect('/');
        if (is_null($asignacion->alCerrar)) {
            $asignacion->alCerrar = "$request->fecha - Resp: $request->responsable..\n\n$request->observacionAl";
        } else {
            $asignacion->alCerrar .= "\n\n$request->fecha - Resp: $request->responsable..\n\n$request->observacionAl";
        }
        $asignacion->condicion = 'Cerrado';
        $asignacion->save();
    }
    
    public function abrir(Request $request, Asignacion $asignacion)
    {
        if (!$request->ajax()) return redirect('/');
        if (is_null($asignacion->alAbrir)) {
            $asignacion->alAbrir .= "$request->fecha\n\n$request->observacionAl";
        } else {
            $asignacion->alAbrir .= "\n\n$request->fecha\n\n$request->observacionAl";
        }
        $asignacion->condicion = 'Abierto';
        $asignacion->save();
    }

    public function desactivar(Request $request, Asignacion $asignacion)
    {
        if (!$request->ajax()) return redirect('/');
        $asignacion->estado = 0;
        $asignacion->save();
    }

    public function activar(Request $request, Asignacion $asignacion)
    {
        if (!$request->ajax()) return redirect('/');
        $asignacion->estado = 1;
        $asignacion->save();
    }
}