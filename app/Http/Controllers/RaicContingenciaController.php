<?php

namespace App\Http\Controllers;

use App\User;
use App\RaicContingencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class RaicContingenciaController extends Controller
{
    // Para cargar el selector con busqueda al registrar/actualizar una contingencia
    public function usuariosContingencia(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $usuarios = User::select('id', 'nombre_completo')->where('empresas_id', '=', Session::get('id_empresa'))->get();
        return ['usuarios' => $usuarios];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPorRaic(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $raicContingencias = RaicContingencia::join('users', 'fk_respons_users', '=', 'users.id')
        ->select('raics_contingencias.id'
            , 'consecutivo'
            , 'accion'
            , 'fechaLimite'
            , 'fk_respons_users'
            , 'nombre_completo'
            , 'observacionAlAbrir'
            , 'observacionAlCerrar'
            , 'raics_contingencias.condicion'
            , 'raics_contingencias.estado'
        )->where('fk_id_raics', '=', $request->fkIdRaics)->get();

        $raicContingencias->each(function ($raicContingencia) {
            $raicContingencia->observacionAlAbrir = nl2br($raicContingencia->observacionAlAbrir, false);
            $raicContingencia->observacionAlCerrar = str_replace('..', '</em>', str_replace('- Resp:', '- <em>Resp:', nl2br($raicContingencia->observacionAlCerrar, false)));
        });
        
        return ['raicContingencias' => $raicContingencias];
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
        $nuevaRaicContingencia = $request->all() + [
            'fk_id_empresas' => Session::get('id_empresa')
            , 'fk_usuCrea_users' => $usu
            , 'fk_usuActualiza_users' => $usu
        ];

        RaicContingencia::create($nuevaRaicContingencia);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RaicContingencia  $raicContingencia
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RaicContingencia $raicContingencia)
    {
        if (!$request->ajax()) return redirect('/');

        if ($request->consecutivo != $raicContingencia->consecutivo)
            $raicContingencia->consecutivo = $request->consecutivo;

        if ($request->accion != $raicContingencia->accion)
            $raicContingencia->accion = $request->accion;

        if ($request->fechaLimite != $raicContingencia->fechaLimite)
            $raicContingencia->fechaLimite = $request->fechaLimite;

        if ($request->fk_respons_users != $raicContingencia->fk_respons_users)
            $raicContingencia->fk_respons_users = $request->fk_respons_users;

        if ($raicContingencia->isDirty()) {
            $raicContingencia->fk_usuActualiza_users = Auth::user()->id;
            $raicContingencia->save();
        }
    }

    public function cerrar(Request $request, RaicContingencia $raicContingencia)
    {
        if (!$request->ajax()) return redirect('/');
        if (is_null($raicContingencia->observacionAlCerrar)) {
            $raicContingencia->observacionAlCerrar = "$request->fecha - Resp: $request->responsable..\n\n$request->observacion";
        } else {
            $raicContingencia->observacionAlCerrar .= "\n\n$request->fecha - Resp: $request->responsable..\n\n$request->observacion";
        }
        $raicContingencia->condicion = 'Cerrado';
        $raicContingencia->save();
    }
    public function abrir(Request $request, RaicContingencia $raicContingencia)
    {
        if (!$request->ajax()) return redirect('/');
        if (is_null($raicContingencia->observacionAlAbrir)) {
            $raicContingencia->observacionAlAbrir .= "$request->fecha\n\n$request->observacion";
        } else {
            $raicContingencia->observacionAlAbrir .= "\n\n$request->fecha\n\n$request->observacion";
        }
        $raicContingencia->condicion = 'Abierto';
        $raicContingencia->save();
    }
    public function desactivar(Request $request, RaicContingencia $raicContingencia)
    {
        if (!$request->ajax()) return redirect('/');
        $raicContingencia->estado = 0;
        $raicContingencia->save();
    }

    public function activar(Request $request, RaicContingencia $raicContingencia)
    {
        if (!$request->ajax()) return redirect('/');
        $raicContingencia->estado = 1;
        $raicContingencia->save();
    }
}
