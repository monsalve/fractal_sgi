<?php

namespace App\Http\Controllers;

use App\Objetivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ObjetivoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $respuesta = Objetivo::join('users', 'fk_respons_users', '=', 'users.id')->select('objetivos.id', 'objetivo', 'descripcion', 'clasificacion', 'anio', 'fk_respons_users', 'users.nombre_completo', 'objetivos.estado');
        $where = [];
        $where[] = ['fk_id_empresas', '=', Session::get('id_empresa')];
        if ($request->leer != 1)
            $where[] = ['fk_usuCrea_users', '=', Auth::user()->id];
        
        if ($request->objetivo)
            $where[] = ['objetivo', 'LIKE', "%$request->objetivo%"];
            
        if ($request->descripcion)
            $where[] = ['descripcion', 'LIKE', "%$request->descripcion%"];

        if (isset($request->estado))
            $where[] = ['objetivos.estado', '=', $request->estado];
        
        $objetivos = $respuesta->where($where)->orderBy('objetivos.estado', 'desc')->paginate($request->numRegs);

        return [
            'pagination' => [
                'total'        => $objetivos->total(),
                'current_page' => $objetivos->currentPage(),
                'per_page'     => $objetivos->perPage(),
                'last_page'    => $objetivos->lastPage(),
                'from'         => $objetivos->firstItem(),
                'to'           => $objetivos->lastItem(),
            ],
            'objetivos' => $objetivos
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
        $nuevoObjetivo = $request->all() + [
            'fk_id_empresas' => Session::get('id_empresa'),
            'fk_usuCrea_users' => $usu,
            'fk_usuActualiza_users' => $usu,
        ];
        Objetivo::create($nuevoObjetivo);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Objetivo  $objetivo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Objetivo $objetivo)
    {
        if (!$request->ajax()) return redirect('/');

        if ($request->objetivo != $objetivo->objetivo)
            $objetivo->objetivo = $request->objetivo;

        if ($request->descripcion != $objetivo->descripcion)
            $objetivo->descripcion = $request->descripcion;

        if ($request->clasificacion != $objetivo->clasificacion)
            $objetivo->clasificacion = $request->clasificacion;

        if ($request->anio != $objetivo->anio)
            $objetivo->anio = $request->anio;
            
        if ($request->fk_respons_users != $objetivo->fk_respons_users)
            $objetivo->fk_respons_users = $request->fk_respons_users;

        if ($objetivo->isDirty()) {
            $objetivo->fk_usuActualiza_users = Auth::user()->id;
            $objetivo->save();
        }
    }
    public function desactivar(Request $request, Objetivo $objetivo)
    {
        if (!$request->ajax()) return redirect('/');
        $objetivo->estado = 0;
        $objetivo->save();
    }
    public function activar(Request $request, Objetivo $objetivo)
    {
        if (!$request->ajax()) return redirect('/');
        $objetivo->estado = 1;
        $objetivo->save();
    }
}