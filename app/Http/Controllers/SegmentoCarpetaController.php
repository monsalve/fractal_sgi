<?php

namespace App\Http\Controllers;

use App\SegmentoCarpeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SegmentoCarpetaController extends Controller
{
    public function listaSimpleAgrupadaPorSegmento(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $segmentosCarpetas = SegmentoCarpeta::select('id', 'carpeta', 'fk_id_segmentos')->where('fk_id_empresas', '=', Session::get('id_empresa'))
        ->orderBy('fk_id_segmentos')->orderBy('carpeta')->get()->groupBy('fk_id_segmentos');

        return ['segmentosCarpetas' => $segmentosCarpetas];
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $respuesta = SegmentoCarpeta::select(
            'segmentos_carpetas.id',
            'carpeta',
            'descripcion',
            'fk_id_tipos_carpetas',
            'tipoCarpeta',
            'segmentos_carpetas.estado'
        )->join('tipos_carpetas', 'fk_id_tipos_carpetas', '=', 'tipos_carpetas.id');

        $where = [];
        $where[] = ['segmentos_carpetas.fk_id_empresas', '=', Session::get('id_empresa')];
        $where[] = ['fk_id_segmentos', '=', $request->fkIdSegmentos];
        if ($request->leer != 1) {
            $where[] = ['segmentos_carpetas.fk_usuCrea_users', '=', Auth::user()->id];
        }
        if ($request->carpeta) {
            $where[] = ['carpeta', 'LIKE', "%$request->carpeta%"];
        }
        if (isset($request->fkIdTiposCarpetas)) {
            $where[] = ['fk_id_tipos_carpetas', '=', $request->fkIdTiposCarpetas];
        }

        $carpetas = $respuesta->where($where)->orderBy('segmentos_carpetas.id', 'desc')->paginate($request->numRegs);
        $carpetas->each(function ($item, $key) {
            if (is_null($item->descripcion))$item->descripcion = '';
        });
        return [
            'pagination' => [
                'total'        => $carpetas->total(),
                'current_page' => $carpetas->currentPage(),
                'per_page'     => $carpetas->perPage(),
                'last_page'    => $carpetas->lastPage(),
                'from'         => $carpetas->firstItem(),
                'to'           => $carpetas->lastItem(),
            ],
            'carpetas' => $carpetas
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
        $carpeta = $request->all() + [
            'fk_id_empresas' => $request->session()->get('id_empresa'),
            'fk_usuCrea_users' => $usu,
            'fk_usuActualiza_users' => $usu,
        ];
        SegmentoCarpeta::create($carpeta);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SegmentoCarpeta  $carpeta
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SegmentoCarpeta $carpeta)
    {
        if (!$request->ajax()) return redirect('/');
        if ($request->carpeta != $carpeta->carpeta)
            $carpeta->carpeta = $request->carpeta;
            
        if ($request->descripcion != $carpeta->descripcion)
            $carpeta->descripcion = $request->descripcion;
            
        if ($request->fk_id_tipos_carpetas != $carpeta->fk_id_tipos_carpetas)
            $carpeta->fk_id_tipos_carpetas = $request->fk_id_tipos_carpetas;
            
        if ($carpeta->isDirty()) {
            $carpeta->fk_usuActualiza_users = Auth::user()->id;
            $carpeta->save();
        }
    }

    public function desactivar(Request $request, SegmentoCarpeta $carpeta)
    {
        if (!$request->ajax()) return redirect('/');
        $carpeta->estado = 0;
        $carpeta->save();
    }

    public function activar(Request $request, SegmentoCarpeta $carpeta)
    {
        if (!$request->ajax()) return redirect('/');
        $carpeta->estado = 1;
        $carpeta->save();
    }
}
