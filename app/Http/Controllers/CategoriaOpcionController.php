<?php

namespace App\Http\Controllers;

use App\CategoriaOpcion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CategoriaOpcionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPorCategoriaArchivo(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $categoriasOpciones = CategoriaOpcion::select('id','categoriaOpcion','estado')->where('fk_id_categorias_archivos', '=', $request->fkIdCategoriasArchivos)->get();
        return ['categoriasOpciones' => $categoriasOpciones];
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
        $nuevaCategoriaOpcion = $request->all() + [
            'fk_usuCrea_users' => $usu
            , 'fk_usuActualiza_users' => $usu
        ];
        
        CategoriaOpcion::create($nuevaCategoriaOpcion);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CategoriaOpcion  $categoriaOpcion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CategoriaOpcion $categoriaOpcion)
    {
        if (!$request->ajax()) return redirect('/');

        if ($request->categoriaOpcion != $categoriaOpcion->categoriaOpcion)
            $categoriaOpcion->categoriaOpcion = $request->categoriaOpcion;

        if ($categoriaOpcion->isDirty()) {
            $categoriaOpcion->fk_usuActualiza_users = Auth::user()->id;
            $categoriaOpcion->save();
        }
    }

    public function destroy(Request $request, CategoriaOpcion $categoriaOpcion)
    {
        if (!$request->ajax()) return redirect('/');
        $categoriaOpcion->delete();
    }
}
