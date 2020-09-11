<?php

namespace App\Http\Controllers;

use App\UsuarioCarpeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UsuarioCarpetaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $respuesta = UsuarioCarpeta::select('id', 'carpeta', 'descripcion', 'estado');
        $where = [];
        $where[] = ['fk_id_users', '=', $request->fk_id_users];
        if ($request->carpeta) {
            $where[] = ['carpeta', 'LIKE', "%$request->carpeta%"];
        }
        
        $carpetas = $respuesta->where($where)->orderBy('carpeta')->paginate(8);
        
        $carpetas->each(function ($item, $key) {
            if (is_null($item->descripcion))$item->descripcion = '';
        });
        
        return [
            'pagination' => [
                'total'           => $carpetas->total()
                , 'current_page'  => $carpetas->currentPage()
                , 'per_page'      => $carpetas->perPage()
                , 'last_page'     => $carpetas->lastPage()
                , 'from'          => $carpetas->firstItem()
                , 'to'            => $carpetas->lastItem()
            ]
            , 'carpetas' => $carpetas
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
        $usuario = Auth::user()->id;
        $nuevaCarpeta = $request->all() + [
            'fk_id_empresas'          => Session::get('id_empresa')
            , 'fk_usuCrea_users'      => $usuario
            , 'fk_usuActualiza_users' => $usuario
        ];
        UsuarioCarpeta::create($nuevaCarpeta);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UsuarioCarpeta  $usuarioCarpeta
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UsuarioCarpeta $usuarioCarpeta)
    {
        if (!$request->ajax()) return redirect('/');
        if ($request->carpeta != $usuarioCarpeta->carpeta) {
            $usuarioCarpeta->carpeta = $request->carpeta;
        }
        if ($request->descripcion != $usuarioCarpeta->descripcion) {
            $usuarioCarpeta->descripcion = $request->descripcion;
        }
        if ($usuarioCarpeta->isDirty()) {
            $usuarioCarpeta->fk_usuActualiza_users = Auth::user()->id;
            $usuarioCarpeta->save();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\UsuarioCarpeta  $usuarioCarpeta
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, UsuarioCarpeta $usuarioCarpeta)
    {
        if (!$request->ajax()) return redirect('/');
        $usuarioCarpeta->delete();
    }
}
