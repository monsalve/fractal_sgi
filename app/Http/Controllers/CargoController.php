<?php

namespace App\Http\Controllers;

use App\Cargo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CargoController extends Controller
{
    public function listaSimple(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $cargos = Cargo::select('id', 'cargo')->where([
            ['estado', '=', 1]
            , ['fk_id_empresas', '=', Session::get('id_empresa')]
        ])->orderBy('cargo')->get();

        return ['cargos' => $cargos];
    }
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $respuesta = Cargo::select('id','cargo','descripcion','estado');
        $where = [];
        $where[] = ['fk_id_empresas', '=', Session::get('id_empresa')];
        $order = 'id';
        if ($request->leer != 1) {
            $where[] = ['fk_usuCrea_users', '=', Auth::user()->id];
        }
        if($request->textoCol) {
            $order = 'cargo';
            $where[] = [$request->nombreCol, 'LIKE', "%$request->textoCol%"];
        }
        $cargos = $respuesta->where($where)->orderBy($order, 'asc')->paginate($request->numRegs);

        return [
            'pagination' => [
                'total'        => $cargos->total(),
                'current_page' => $cargos->currentPage(),
                'per_page'     => $cargos->perPage(),
                'last_page'    => $cargos->lastPage(),
                'from'         => $cargos->firstItem(),
                'to'           => $cargos->lastItem(),
            ],
            'cargos' => $cargos
        ];
    }
    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $usu = Auth::user()->id;
        $nuevoCargo = $request->all() + [
            'fk_id_empresas' => Session::get('id_empresa'),
            'fk_usuCrea_users' => $usu,
            'fk_usuActualiza_users' => $usu,
        ];
        Cargo::create($nuevoCargo);
    }
    public function update(Request $request, Cargo $cargo)
    {
        if (!$request->ajax()) return redirect('/');
        
        if ($request->cargo != $cargo->cargo)
            $cargo->cargo = $request->cargo;

        if ($request->descripcion != $cargo->descripcion)
            $cargo->descripcion = $request->descripcion;

        if ($cargo->isDirty()) {
            $cargo->fk_usuActualiza_users = Auth::user()->id;
            $cargo->save();
        }
    }
    public function desactivar(Request $request, Cargo $cargo)
    {
        if (!$request->ajax()) return redirect('/');
        if($cargo->usuarios()->count()) return ['hayUsuarios' => true];
        $cargo->estado = 0;
        $cargo->save();
        return ['hayUsuarios' => false];
    }
    public function activar(Request $request, Cargo $cargo)
    {
        if (!$request->ajax()) return redirect('/');
        $cargo->estado = 1;
        $cargo->save();
    }
}
