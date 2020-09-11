<?php

namespace App\Http\Controllers;

use App\RaicEvidencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class RaicEvidenciaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPorContingencia(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $raicEvidencias = RaicEvidencia::select('id', 'evidencia', 'ruta')->where('fk_id_raics_contingencias', '=', $request->fkIdRaicsContingencias)->get();

        $raicEvidencias->each(function ($evidencia, $key) {
            $evidencia->ruta = asset("raicsEvidencias/$evidencia->ruta");
        });

        return ['raicEvidencias' => $raicEvidencias];
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
        $dirEmpresa = public_path("raicsEvidencias/$carpetaEmpresa");
        if (!file_exists($dirEmpresa)) mkdir($dirEmpresa, 0777);

        $ruta = $request->ruta->store($carpetaEmpresa, 'raicsEvidencias');

        $nuevaRaicEvidencia = [
            'evidencia' => $request->evidencia
            , 'fk_id_raics_contingencias' => $request->fk_id_raics_contingencias
            , 'ruta' => $ruta
            , 'fk_usuCrea_users' => $usu
            , 'fk_usuActualiza_users' => $usu
        ];

        RaicEvidencia::create($nuevaRaicEvidencia);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RaicEvidencia  $raicEvidencia
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RaicEvidencia $raicEvidencia)
    {
        if (!$request->ajax()) return redirect('/');

        if ($request->evidencia != $raicEvidencia->evidencia)
            $raicEvidencia->evidencia = $request->evidencia;

        if ($request->hasFile('ruta')) {
            $carpetaEmpresa = Session::get('id_empresa') .'_emp';
            Storage::disk('raicsEvidencias')->delete($raicEvidencia->ruta);
            $raicEvidencia->ruta = $request->ruta->store($carpetaEmpresa, 'raicsEvidencias');
        }

        if ($raicEvidencia->isDirty()) {
            $raicEvidencia->fk_usuActualiza_users = Auth::user()->id;
            $raicEvidencia->save();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\RaicEvidencia  $raicEvidencia
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, RaicEvidencia $raicEvidencia)
    {
        if (!$request->ajax()) return redirect('/');
        Storage::disk('raicsEvidencias')->delete($raicEvidencia->ruta);
        $raicEvidencia->delete();
    }
}
