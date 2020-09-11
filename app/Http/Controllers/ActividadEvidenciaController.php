<?php

namespace App\Http\Controllers;

use App\ActividadEvidencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ActividadEvidenciaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $actividad_evidencias = ActividadEvidencia::select('id', 'evidencia', 'ruta')
        ->where('fk_id_actividades_seguimientos', '=', $request->fkIdActividadSeguimiento)
        ->orderBy('evidencia')->get();

        $actividad_evidencias->each(function ($item, $key) {
            $item->ruta = asset("docsEvidencias/$item->ruta");
        });
        return ['seguimientoEvidencias' => $actividad_evidencias];
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
        $carpetaSeg = $request->fk_id_actividades_seguimientos .'_seg';
        $dirSeg = public_path("docsEvidencias/$carpetaSeg");
        if (!file_exists($dirSeg)) mkdir($dirSeg, 0777);
        
        //var_dump($request->ruta);exit;
        $ruta = $request->ruta->store($carpetaSeg, 'docsEvidencias');

        $nuevoArchivo = $request->except('ruta') + [
            'ruta' => $ruta
            , 'fk_usuCrea_users' => Auth::user()->id
        ];
        ActividadEvidencia::create($nuevoArchivo);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ActividadEvidencia  $actividadEvidencia
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, ActividadEvidencia $actividadEvidencia)
    {
        if (!$request->ajax()) return redirect('/');
        Storage::disk('docsEvidencias')->delete($actividadEvidencia->ruta);
        $actividadEvidencia->delete();
    }
}
