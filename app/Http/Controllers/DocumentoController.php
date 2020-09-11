<?php

namespace App\Http\Controllers;

use App\Documento;
use Illuminate\Http\Request;

class DocumentoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!$request->ajax()) return redirect('/');
        $respuesta = Documento::select('id', 'documento', 'ruta', 'estado');

        $where = [['fk_id_empresas', '=', Session::get('id_empresa')]];
        if ($request->leer != 1) $where[] = ['fk_usuCrea_users', '=', Auth::user()->id];
        $respuesta->where($where);
        $documentos = $respuesta->orderBy('id', 'desc')->paginate(10);
        $documentos->each(function ($item, $key) {
            $item->ruta = asset("docs/$item->ruta");
        });

        return [
            'pagination' => [
                'total'        => $documentos->total()
                , 'current_page' => $documentos->currentPage()
                , 'per_page'     => $documentos->perPage()
                , 'last_page'    => $documentos->lastPage()
                , 'from'         => $documentos->firstItem()
                , 'to'           => $documentos->lastItem()
            ],
            'documentos' => $documentos
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
        $idEmpresa = Session::get('id_empresa');
        $carpetaEmpresa = $idEmpresa .'_empresa';
        $dirEmpresa = public_path("docs/$carpetaEmpresa");
        if (!file_exists($dirEmpresa)) mkdir($dirEmpresa, 0777);

        $ruta = $request->ruta->store($carpetaEmpresa, 'docs');

        $documento = [
            'documento' => $request->documento
            , 'ruta' => $ruta
            , 'fk_id_carpetas' => $request->fk_id_carpetas
            , 'fk_id_empresas' => $idEmpresa
            , 'fk_usuCrea_users' => Auth::user()->id
        ];

        Documento::create($documento);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Documento  $documento
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Documento $documento)
    {
        if (!$request->ajax()) return redirect('/');
        if ($request->documento != $documento->documento) {
            $documento->documento = $request->documento;
        }
        if ($request->hasFile('ruta')) {
            $carpetaEmpresa = Session::get('id_empresa') .'_empresa';
            $dirEmpresa = public_path("docs/$carpetaEmpresa");
            Storage::disk('docs')->delete($documento->ruta);
            $documento->ruta = $request->ruta->store($carpetaEmpresa, 'docs');
        }
        if ($documento->isDirty()) {
            $documento->fk_usuActualiza_users = Auth::user()->id;
            $documento->save();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Documento  $documento
     * @return \Illuminate\Http\Response
     */
    public function destroy(Documento $documento)
    {
        if (!$request->ajax()) return redirect('/');        
    }
}