<?php

namespace App\Http\Controllers;

use App\AsignacionEvidencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class AsignacionEvidenciaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPorAsignacion (Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $evidencias = AsignacionEvidencia::select('id', 'evidencia', 'ruta', 'estado')->where('fk_id_asignaciones', '=', $request->fkIdAsignaciones)->get();

        $evidencias->each(function ($evidencia, $key) {
            $evidencia->ruta = asset("asignacionesEvidencias/$evidencia->ruta");
        });

        return ['evidencias' => $evidencias];
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
        $idEmpresa = Session::get('id_empresa');
        $carpetaEmpresa = $idEmpresa .'_emp';
        $dirEmpresa = public_path("asignacionesEvidencias/$carpetaEmpresa");
        if (!file_exists($dirEmpresa)) mkdir($dirEmpresa, 0777);

        $ruta = $request->ruta->store($carpetaEmpresa, 'asignacionesEvidencias');

        $nuevaEvidencia = [
            'evidencia' => $request->evidencia
            , 'fk_id_asignaciones' => $request->fk_id_asignaciones
            , 'ruta' => $ruta
            , 'fk_usuCrea_users' => $usu
            , 'fk_usuActualiza_users' => $usu
        ];

        AsignacionEvidencia::create($nuevaEvidencia);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AsignacionEvidencia  $evidencia
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AsignacionEvidencia $evidencia)
    {
        if (!$request->ajax()) return redirect('/');

        if ($request->evidencia != $evidencia->evidencia) $evidencia->evidencia = $request->evidencia;

        if ($request->hasFile('ruta')) {
            $carpetaEmpresa = Session::get('id_empresa') .'_emp';
            Storage::disk('asignacionesEvidencias')->delete($evidencia->ruta);
            $evidencia->ruta = $request->ruta->store($carpetaEmpresa, 'asignacionesEvidencias');
        }

        if ($evidencia->isDirty()) {
            $evidencia->fk_usuActualiza_users = Auth::user()->id;
            $evidencia->save();
        }
    }

    public function desactivar(Request $request, AsignacionEvidencia $evidencia)
    {
        if (!$request->ajax()) return redirect('/');
        $evidencia->estado = 0;
        $evidencia->save();
    }

    public function activar(Request $request, AsignacionEvidencia $evidencia)
    {
        if (!$request->ajax()) return redirect('/');
        $evidencia->estado = 1;
        $evidencia->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AsignacionEvidencia  $evidencia
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, AsignacionEvidencia $evidencia)
    {
        if (!$request->ajax()) return redirect('/');
        Storage::disk('asignacionesEvidencias')->delete($evidencia->ruta);
        $evidencia->delete();
    }
}
