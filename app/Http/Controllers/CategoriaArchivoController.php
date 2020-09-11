<?php

namespace App\Http\Controllers;

use App\CategoriaArchivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CategoriaArchivoController extends Controller
{
    public function listaSimplePorSegmentoArchivoMasOpcionesListaSimple(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $categoriasConOpciones = CategoriaArchivo::select('id', 'categoriaArchivo', DB::raw('"" AS seleccionado'))->has('categoriasOpciones')
        ->where('fk_id_segmentos_archivos', '=', $request->fkIdSegmentosArchivos)
        ->with(['categoriasOpciones' => function ($query) {
            $query->select('id', 'categoriaOpcion', 'fk_id_categorias_archivos')->orderBy('categoriaOpcion');
        }])->orderBy('categoriaArchivo')->get();

        return ['categoriasConOpciones' => $categoriasConOpciones];
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $categoriasArchivos = CategoriaArchivo::select('id','categoriaArchivo','estado')->where('fk_id_segmentos_archivos', '=', $request->fkIdSegmentosArchivos)->get();
        return ['categoriasArchivos' => $categoriasArchivos];
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
        $nuevaCategoriaArchivo = $request->all() + [
            'fk_id_empresas' => Session::get('id_empresa')
            , 'fk_usuCrea_users' => $usu
            , 'fk_usuActualiza_users' => $usu
        ];
        CategoriaArchivo::create($nuevaCategoriaArchivo);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CategoriaArchivo  $categoriaArchivo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CategoriaArchivo $categoriaArchivo)
    {
        if (!$request->ajax()) return redirect('/');

        if ($request->categoriaArchivo != $categoriaArchivo->categoriaArchivo)
            $categoriaArchivo->categoriaArchivo = $request->categoriaArchivo;

        if ($categoriaArchivo->isDirty()) {
            $categoriaArchivo->fk_usuActualiza_users = Auth::user()->id;
            $categoriaArchivo->save();
        }
    }

    public function destroy(Request $request, CategoriaArchivo $categoriaArchivo)
    {
        if (!$request->ajax()) return redirect('/');
        $categoriaArchivo->categoriasOpciones()->delete();
        $categoriaArchivo->delete();
    }
}
