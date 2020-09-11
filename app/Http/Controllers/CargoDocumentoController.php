<?php

namespace App\Http\Controllers;

use App\CargoDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CargoDocumentoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $where = [];
        $respuesta = CargoDocumento::select('id', 'documento', 'estado');
        if ($request->fk_id_cargos) {
            $where[] = ['fk_id_cargos' , '=', $request->fk_id_cargos];
        }
        if ($request->documento) {
            $where[] = ['documento' , '=', $request->documento];
        }
        if (isset($request->estado)) {
            $where[] = ['estado' , '=', $request->estado];
        }

        $cargoDocumentos = $respuesta->where($where)->get();

        return ['cargoDocumentos' => $cargoDocumentos];
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
        $nuevoDocumento = $request->all() + [
            'fk_id_empresas' => Session::get('id_empresa')
            , 'fk_usuCrea_users' => $usu
            , 'fk_usuActualiza_users' => $usu
        ];
        CargoDocumento::create($nuevoDocumento);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  CargoDocumento  $cargoDocumento
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CargoDocumento $cargoDocumento)
    {
        if (!$request->ajax()) return redirect('/');

        if ($request->documento != $cargoDocumento->documento)
            $cargoDocumento->documento = $request->documento;
            
        if ($cargoDocumento->isDirty()) {
            $cargoDocumento->fk_usuActualiza_users = Auth::user()->id;
            $cargoDocumento->save();
        }
    }
    public function desactivar(Request $request, CargoDocumento $cargoDocumento)
    {
        if (!$request->ajax()) return redirect('/');
        if ($cargoDocumento->usuariosDocumentos()->count()) return ['hayDocumentos' => true];
        $cargoDocumento->estado = 0;
        $cargoDocumento->save();
        return ['hayDocumentos' => false];
    }
    public function activar(Request $request, CargoDocumento $cargoDocumento)
    {
        if (!$request->ajax()) return redirect('/');
        $cargoDocumento->estado = 1;
        $cargoDocumento->save();
    }
}
