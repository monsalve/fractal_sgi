<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ActividadSeguimiento;
use Illuminate\Support\Facades\Auth;

class ActividadSeguimientoController extends Controller
{
    public function show(Request $request, $actividadSeguimientoId)
    {
        if (!$request->ajax()) return redirect('/');
        // DEBE SER first Y NO get -> MEJOR MANIPULAR UN OBJETO QUE UNA COLECCIÓN
        $actividadSeguimiento = ActividadSeguimiento::select('aplica', 'fechaReporte', 'observacion')->where('id', '=', $actividadSeguimientoId)->first();
        return ['actividadSeguimiento' => $actividadSeguimiento];
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
        $nuevoSeguimiento = $request->except('noAplica') + [
            'aplica' => !$request->noAplica
            , 'fk_usuCrea_users' => $usu
            , 'fk_usuActualiza_users' => $usu
        ];
        $actividadSeguimiento = ActividadSeguimiento::create($nuevoSeguimiento);
        return ['idSeguimiento' => $actividadSeguimiento->id];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ActividadSeguimiento  $actividadSeguimiento
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ActividadSeguimiento $actividadSeguimiento)
    {
        if (!$request->ajax()) return redirect('/');
        
        if (!$request->noAplica != $actividadSeguimiento->aplica)
            $actividadSeguimiento->aplica = !$request->noAplica;

        if ($request->fechaReporte != $actividadSeguimiento->fechaReporte)
            $actividadSeguimiento->fechaReporte = $request->fechaReporte;

        if ($request->fechaReporte != $actividadSeguimiento->fechaReporte)
            $actividadSeguimiento->fechaReporte = $request->fechaReporte;

        if ($request->observacion != $actividadSeguimiento->observacion)
            $actividadSeguimiento->observacion = $request->observacion;
        
        if ($actividadSeguimiento->isDirty()) {
            $actividadSeguimiento->fk_usuActualiza_users = Auth::user()->id;
            $actividadSeguimiento->save();
        }
    }
}
