<?php

namespace App\Http\Controllers;

use App\Segmento;
use App\ArchivoSegmento;
use App\CarpetaSegmento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SegmentoController extends Controller
{
    public function listaSimple(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $respuesta = Segmento::select('id', 'segmento');
        $where = [];
        $where[] = ['fk_id_empresas', '=', Session::get('id_empresa')];
        if ($request->leer != 1) {
            $where[] = ['fk_usuCrea_users', '=', Auth::user()->id];
        }
        $segmentos = $respuesta->where($where)->orderBy('segmento', 'asc')->get();
        return ['segmentos' => $segmentos];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        
        $respuesta = Segmento::select('id', 'segmento', 'tipoSegmento', 'observacion', 'estado');
        
        $where = [];
        $where[] = ['fk_id_empresas', '=', Session::get('id_empresa')];
        if ($request->leer != 1) {
            $where[] = ['fk_usuCrea_users', '=', Auth::user()->id];
        }
        if ($request->segmento) {
            $where[] = ['segmento', 'LIKE', "%$request->segmento%"];
        }
        if (isset($request->indiceTipoSegmento)) {
            $where[] = ['tipoSegmento', '=', intval($request->indiceTipoSegmento)];
        }

        $segmentos = $respuesta->where($where)->orderBy('id', 'desc')->paginate($request->numRegs);
        return [
            'pagination' => [
                'total'        => $segmentos->total(),
                'current_page' => $segmentos->currentPage(),
                'per_page'     => $segmentos->perPage(),
                'last_page'    => $segmentos->lastPage(),
                'from'         => $segmentos->firstItem(),
                'to'           => $segmentos->lastItem(),
            ],
            'segmentos' => $segmentos
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
        $nuevoSegmento = $request->all() + [
            'fk_id_empresas' => $request->session()->get('id_empresa'),
            'fk_usuCrea_users' => $usu,
            'fk_usuActualiza_users' => $usu,
        ];

        Segmento::create($nuevoSegmento);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Segmento  $segmento
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Segmento $segmento)
    {
        if (!$request->ajax()) return redirect('/');
        if ($request->segmento != $segmento->segmento)
            $segmento->segmento = $request->segmento;

        if ($request->tipoSegmento != $segmento->tipoSegmento)
            $segmento->tipoSegmento = $request->tipoSegmento;
            
        if ($request->observacion != $segmento->observacion)
            $segmento->observacion = $request->observacion;
            
        if ($segmento->isDirty()) {
            $segmento->fk_usuActualiza_users = Auth::user()->id;
            $segmento->save();
        }
    }

    public function desactivar(Request $request, Segmento $segmento)
    {
        if (!$request->ajax()) return redirect('/');
        $segmento->estado = 0;
        $segmento->save();
    }

    public function activar(Request $request, Segmento $segmento)
    {
        if (!$request->ajax()) return redirect('/');
        $segmento->estado = 1;
        $segmento->save();
    }
}
