<?php

namespace App\Http\Controllers;

use App\Cargo;
use App\SegmentoArchivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class SegmentoArchivoController extends Controller
{
    public function listaSimpleConPermisoCrearAgrupadaPorCarpeta(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        
        $respuesta = SegmentoArchivo::leftJoin('cargos_archivos', 'cargos_archivos.fk_id_segmentos_archivos', '=', 'segmentos_archivos.id')
        ->select('segmentos_archivos.id', 'archivo', 'ruta', 'fk_id_segmentos_carpetas', DB::raw('IFNULL(crear, 0) AS crear'))
        ->where([
            ['fk_id_empresas', '=', Session::get('id_empresa')]
            , ['segmentos_archivos.gestionable', '=', 1]
            , ['fk_id_cargos', '=', Auth::user()->fk_id_cargos]
        ])->orderBy('fk_id_segmentos_carpetas')->orderBy('archivo')->get();

        $segmentosArchivos = $respuesta->each(function ($segmentoArchivo, $key) {
            $segmentoArchivo->ruta = asset("docsSegmentos/$segmentoArchivo->ruta");
        })->groupBy('fk_id_segmentos_carpetas');

        return ['segmentosArchivos' => $segmentosArchivos];
    }
    
    public function cargosActivos(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $cargos = Cargo::select(DB::raw('id, cargo, 0 AS crear, 0 AS ver'))
        ->where([['fk_id_empresas', '=', Session::get('id_empresa')], ['cargos.estado', '=', 1]])->get();

        return ['cargos' => $cargos];
    }
    public function indexPermisos(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $permisos = DB::table('cargos_archivos')->select(
            'cargos_archivos.crear'
            , 'cargos_archivos.ver'
            , 'cargos_archivos.fk_id_cargos'
        )->where('fk_id_segmentos_archivos', '=', $request->fkIdSegmentosArchivos)->get();

        return ['permisos' => $permisos];
    }

    public function storePermisos(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $usu = Auth::user()->id;
        $archivo = SegmentoArchivo::find($request->fkIdSegmentosArchivos);
        $archivo->cargos()->detach();
        $archivo->cargos()->attach(collect($request->permisosArr)->mapWithKeys(function ($item) use ($usu) {
            return [ $item['id'] => [ 'crear' => $item['crear'], 'ver' => $item['ver'], 'fk_usuCrea_users' => $usu ] ];
        }));
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $idEmpresa = Session::get('id_empresa');
        $respuesta = SegmentoArchivo::select('id', 'archivo', 'codigo', 'versionamiento', 'ruta', 'descripcion', 'gestionable', 'estado');
        $where = [];

        #echo (is_int($request->fkIdCarpetasSegmentos)) ? 'ES UN NUMERO': 'ES UNA CADENA';exit; // EL RESULTADO ES CADENA, LOS ATRIBUTOS DE LA PETICION SE ENVIAN COMO CADENAS
        $where[] = ['fk_id_segmentos_carpetas', '=', intval($request->fkIdSegmentosCarpetas)];
        $where[] = ['fk_id_empresas', '=', $idEmpresa];
        if ($request->leer != 1) {
            $where[] = ['fk_usuCrea_users', '=', Auth::user()->id];
        }
        if ($request->textoCol) {
            $where[] = [$request->nombreCol, 'LIKE', "%$request->textoCol%"];
        }

        $archivos = $respuesta->where($where)->orderBy('id', 'desc')->paginate($request->numRegs);
        $archivos->each(function ($archivo, $key) {
            $archivo->ruta = asset("docsSegmentos/$archivo->ruta");
            if (is_null($archivo->descripcion))$archivo->descripcion = '';
        });
        return [
            'pagination' => [
                'total'        => $archivos->total(),
                'current_page' => $archivos->currentPage(),
                'per_page'     => $archivos->perPage(),
                'last_page'    => $archivos->lastPage(),
                'from'         => $archivos->firstItem(),
                'to'           => $archivos->lastItem(),
            ],
            'archivos' => $archivos
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
        $idEmpresa = Session::get('id_empresa');
        $carpetaEmpresa = $idEmpresa .'_empresa';
        $dirEmpresa = public_path("docsSegmentos/$carpetaEmpresa");
        if (!file_exists($dirEmpresa)) mkdir($dirEmpresa, 0777);

        $ruta = $request->ruta->store($carpetaEmpresa, 'docsSegmentos');

        $gestionable = json_decode($request->gestionable);
        $nuevoArchivo = [
            'archivo' => $request->archivo
            , 'codigo' => $request->codigo
            , 'versionamiento' => $request->versionamiento
            , 'ruta' => $ruta
            , 'gestionable' => $gestionable
            , 'fk_id_segmentos_carpetas' => $request->fk_id_segmentos_carpetas
            , 'fk_id_empresas' => $idEmpresa
            , 'fk_usuCrea_users' => $usu
            , 'fk_usuActualiza_users' => $usu
        ];
        if ($request->descripcion) {
            $nuevoArchivo['descripcion'] = $request->descripcion;
        }
        $archivo = SegmentoArchivo::create($nuevoArchivo);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SegmentoArchivo  $archivo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SegmentoArchivo $archivo)
    {
        if (!$request->ajax()) return redirect('/');

        if ($request->archivo != $archivo->archivo)
            $archivo->archivo = $request->archivo;

        if ($request->codigo != $archivo->codigo)
            $archivo->codigo = $request->codigo;

        if ($request->versionamiento != $archivo->versionamiento)
            $archivo->versionamiento = $request->versionamiento;

        if ($request->hasFile('ruta')) {
            $carpetaEmpresa = Session::get('id_empresa') .'_empresa';
            //$dirEmpresa = public_path("docsSegmentos/$carpetaEmpresa");
            Storage::disk('docsSegmentos')->delete($archivo->ruta);
            $archivo->ruta = $request->ruta->store($carpetaEmpresa, 'docsSegmentos');
        }

        if ($request->descripcion != $archivo->descripcion)
            $archivo->descripcion = $request->descripcion;
        
        $gestionable = json_decode($request->gestionable);
        if ($gestionable != $archivo->gestionable)
            $archivo->gestionable = $gestionable;
            
        if ($archivo->isDirty()) {
            $archivo->fk_usuActualiza_users = Auth::user()->id;
            $archivo->save();
        }
    }

    public function desactivar(Request $request, SegmentoArchivo $archivo)
    {
        if (!$request->ajax()) return redirect('/');
        $archivo->estado = 0;
        $archivo->save();
    }

    public function activar(Request $request, SegmentoArchivo $archivo)
    {
        if (!$request->ajax()) return redirect('/');
        $archivo->estado = 1;
        $archivo->save();
    }
}