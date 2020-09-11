<?php

namespace App\Http\Controllers;

use App\UsuarioDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class UsuarioDocumentoController extends Controller
{
    /*public function basicoListarRolDocumentos(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $rolDocumentos = DB::table('roles_documentos')->where([
            ['fk_id_empresas', '=', Session::get('id_empresa')]
            , ['fk_id_roles' , '=', $request->rolId]
            , ['estado', '=', 1]
        ])->select('id', 'documento')->orderBy('documento')->get();

        return ['rolDocumentos' => $rolDocumentos];
    }*/
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $documentos = UsuarioDocumento::where('fk_id_usuarios_carpetas', '=', $request->fk_id_usuarios_carpetas)->select('id', 'documento', 'ruta', 'fk_id_cargos_documentos')->paginate(8);
        $documentos->each(function ($item, $key) {
            $item->ruta = asset("usuDocumentos/$item->ruta");
            if (is_null($item->fk_id_cargos_documentos)) $item->fk_id_cargos_documentos = '';
        });
        return [
            'pagination' => [
                'total'           => $documentos->total()
                , 'current_page'  => $documentos->currentPage()
                , 'per_page'      => $documentos->perPage()
                , 'last_page'     => $documentos->lastPage()
                , 'from'          => $documentos->firstItem()
                , 'to'            => $documentos->lastItem()
            ]
            , 'documentos' => $documentos
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
        $carpetaUsuario = $request->fk_id_users .'_usu';
        $dirUsuario = public_path("usuDocumentos/$carpetaUsuario");
        if (!file_exists($dirUsuario)) mkdir($dirUsuario, 0777);

        $ruta = $request->ruta->store($carpetaUsuario, 'usuDocumentos');

        $nuevoDocumento = [
            'documento' => $request->documento
            , 'ruta' => $ruta
            , 'fk_id_empresas' => Session::get('id_empresa')
            , 'fk_id_usuarios_carpetas' => $request->fk_id_usuarios_carpetas
            , 'fk_usuCrea_users' => $usu
            , 'fk_usuActualiza_users' => $usu
        ];
        if ($request->fk_id_cargos_documentos) $nuevoDocumento['fk_id_cargos_documentos'] = $request->fk_id_cargos_documentos;
        
        UsuarioDocumento::create($nuevoDocumento);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UsuarioDocumento  $usuarioDocumento
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UsuarioDocumento $usuarioDocumento)
    {
        if (!$request->ajax()) return redirect('/');
        if ($request->documento != $usuarioDocumento->documento) {
            $usuarioDocumento->documento = $request->documento;
        }
        if ($request->hasFile('ruta')) {
            $carpetaUsuario = $request->fk_id_users .'_usu';
            Storage::disk('usuDocumentos')->delete($usuarioDocumento->ruta);
            $usuarioDocumento->ruta = $request->ruta->store($carpetaUsuario, 'usuDocumentos');
        }
        if ($request->fk_id_cargos_documentos != $usuarioDocumento->fk_id_cargos_documentos) {
            $usuarioDocumento->fk_id_cargos_documentos = $request->fk_id_cargos_documentos;
        }
        if ($usuarioDocumento->isDirty()) {
            $usuarioDocumento->fk_usuActualiza_users = Auth::user()->id;
            $usuarioDocumento->save();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\UsuarioDocumento  $usuarioDocumento
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, UsuarioDocumento $usuarioDocumento)
    {
        if (!$request->ajax()) return redirect('/');
        Storage::disk('usuDocumentos')->delete($usuarioDocumento->ruta);
        $usuarioDocumento->delete();
    }
}