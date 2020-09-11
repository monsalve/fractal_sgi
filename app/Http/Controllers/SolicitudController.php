<?php

namespace App\Http\Controllers;

use App\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class SolicitudController extends Controller
{
    public function opcionesRegistradas(Request $request, Solicitud $solicitud)
    {
        if (!$request->ajax()) return redirect('/');

        $opciones = $solicitud->categoriasOpciones()->select('categorias_opciones.id', 'categorias_opciones.fk_id_categorias_archivos')->get()->makeHidden('pivot');
        return ['opcionesRegistradas' => $opciones];
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPorSegmentoArchivo(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        // SOLO SUPER PUEDE ELIMINAR SOLICITUDES
        // SOLO PUEDO ACTUALIAR SOLICITUDES SI SOY EL AUTOR Y EL REGISTRO ESTA ACTIVO

        $respuesta = Solicitud::join('segmentos_archivos', 'solicitudes.fk_id_segmentos_archivos', '=', 'segmentos_archivos.id')
        ->join('users AS usersRecibe', 'solicitudes.fk_recibe_users', '=', 'usersRecibe.id')
        ->join('users AS usersUsuCrea', 'solicitudes.fk_usuCrea_users', '=', 'usersUsuCrea.id')
        ->select(
            'solicitudes.id'
            , 'segmentos_archivos.archivo'
            , 'solicitudes.fk_usuCrea_users'
            , 'usersUsuCrea.nombre_completo AS usuario'
            , 'solicitudes.updated_at AS fecha'
            , 'fk_recibe_users'
            , 'usersRecibe.nombre_completo AS recibe'
            , 'solicitudes.ruta'
            , 'solicitudes.estado'
        );

        $where = [['fk_id_segmentos_archivos', '=', $request->fkIdSegmentosArchivos]];
        
        // DETERMINA SI SOLO PUEDE VER SUS SOLICITUDES
        $puedeVer = DB::table('cargos_archivos')->select('crear', 'ver')->where([['fk_id_cargos', '=', Auth::user()->fk_id_cargos], ['fk_id_segmentos_archivos', '=', $request->fkIdSegmentosArchivos]])->value('ver');
        if (!$puedeVer) $where[] = ['solicitudes.fk_usuCrea_users', '=', Auth::user()->id];

        $respuesta->where($where);
        $solicitudes = $respuesta->paginate($request->numRegs);

        $solicitudes->each(function ($solicitud, $key) {
            $solicitud->ruta = asset("docsSolicitudes/$solicitud->ruta");
        });

        return [
            'pagination' => [
                'total'        => $solicitudes->total()
                , 'current_page' => $solicitudes->currentPage()
                , 'per_page'     => $solicitudes->perPage()
                , 'last_page'    => $solicitudes->lastPage()
                , 'from'         => $solicitudes->firstItem()
                , 'to'           => $solicitudes->lastItem()
            ]
            , 'solicitudes' => $solicitudes
        ];
    }

    public function notificar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        
        User::find($request->destinatarioId)->persona->notify(new SolicitudNotificador(
            $request->observacion
            , Auth::user()->nombre_completo
            , Auth::user()->cargo->cargo
        ));
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
        $idEmpresa = Session::get('id_empresa');
        $carpetaEmpresa = $idEmpresa .'_emp';
        $dirEmpresa = public_path("docsSolicitudes/$carpetaEmpresa");
        if (!file_exists($dirEmpresa)) mkdir($dirEmpresa, 0777);

        $ruta = $request->ruta->store($carpetaEmpresa, 'docsSolicitudes');

        $nuevaSolicitud = [
            'ruta' => $ruta
            , 'fk_recibe_users' => $request->fk_recibe_users
            , 'fk_id_segmentos_archivos' => $request->fk_id_segmentos_archivos
            , 'fk_usuCrea_users' => $usu
            , 'fk_usuActualiza_users' => $usu
        ];
        $solicitud = Solicitud::create($nuevaSolicitud);

        $solicitud->categoriasOpciones()->attach(json_decode($request->opciones));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Solicitud  $solicitud
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Solicitud $solicitud)
    {
        if (!$request->ajax()) return redirect('/');
        
        if ($request->hasFile('ruta')) {
            $carpetaEmpresa = Session::get('id_empresa') .'_emp';
            Storage::disk('docsSolicitudes')->delete($solicitud->ruta);
            $solicitud->ruta = $request->ruta->store($carpetaEmpresa, 'docsSolicitudes');
        }

        if ($request->fk_recibe_users != $solicitud->fk_recibe_users)
            $solicitud->fk_recibe_users = $request->fk_recibe_users;
        
        if ($solicitud->isDirty()) {
            $solicitud->fk_usuActualiza_users = Auth::user()->id;
            $solicitud->save();
        }

        $solicitud->categoriasOpciones()->detach();
        $solicitud->categoriasOpciones()->attach(json_decode($request->opciones));
    }

    public function desactivar(Request $request, Solicitud $solicitud)
    {
        if (!$request->ajax()) return redirect('/');
        $solicitud->estado = 0;
        $solicitud->save();
    }
    public function activar(Request $request, Solicitud $solicitud)
    {
        if (!$request->ajax()) return redirect('/');
        $solicitud->estado = 1;
        $solicitud->save();
    }
    public function finalizar(Request $request, Solicitud $solicitud)
    {
        if (!$request->ajax()) return redirect('/');
        $solicitud->estado = 2;
        $solicitud->save();
    }
}
