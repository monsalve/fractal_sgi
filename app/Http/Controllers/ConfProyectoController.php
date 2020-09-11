<?php

namespace App\Http\Controllers;

use App\User;
use App\Segmento;
use App\ConfProyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ConfProyectoController extends Controller
{
    public function listaSimple(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $respuesta = ConfProyecto::select('id', 'proyecto');
        $where = [];
        $where[] = ['fk_id_empresas', '=', Session::get('id_empresa')];
        if ($request->leer != 1) {
            $where[] = ['fk_usuCrea_users', '=', Auth::user()->id];
        }
        $proyectos = $respuesta->where($where)->orderBy('proyecto', 'asc')->get();
        return ['proyectos' => $proyectos];
    }
    
    public function index(Request $request)
    {
        if(!$request->ajax()) return redirect('/');
        $respuesta = ConfProyecto::join('users','proyectos.fk_respons_users','=','users.id')->select('proyectos.id','proyectos.proyecto','proyectos.descripcion', 'proyectos.correoProductOwner', 'proyectos.fk_respons_users', 'users.usuario', 'proyectos.estado');
        $where = [];
        $where[] = ['fk_id_empresas', '=', Session::get('id_empresa')];
        if ($request->leer != 1) {
            $where[] = ['proyectos.fk_usuCrea_users', '=', Auth::user()->id];
        }
        if($request->buscar){
            $where[] = ['proyectos.proyecto', 'LIKE', "%$request->buscar%"];
        }
        if(isset($request->Bestado)){
            $where[] = ['proyectos.estado', '=', $request->Bestado];
        }
        if($request->Bresponsable){
            $where[] = ['users.usuario', 'LIKE', "%$request->Bresponsable%"];
        }
        if(isset($request->Bparticipantes)){
            $arrIdParticipantes = DB::table('usuarios_proyectos')->where('fk_usuAsoc_users', '=', intval($request->Bparticipantes))->select('fk_id_proyectos')->get();
            $respuesta->whereIn('proyectos.id', $arrIdParticipantes->pluck('fk_id_proyectos'));
        }
        
        $proyectos = $respuesta->where($where)->orderBy('proyectos.id', 'desc')->paginate($request->numRegs);
        return [
            'pagination' => [
                'total'        => $proyectos->total()
                , 'current_page' => $proyectos->currentPage()
                , 'per_page'     => $proyectos->perPage()
                , 'last_page'    => $proyectos->lastPage()
                , 'from'         => $proyectos->firstItem()
                , 'to'           => $proyectos->lastItem()
            ]
            , 'proyectos' => $proyectos
        ];
    }
    public function selectResponsables(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $respon = User::where('condicion', '=', '1')
        ->select('id','usuario')
        ->orderBy('usuario', 'asc')->get();

        return ['responsables' => $respon];
    }

    public function msUsuAsociados(Request $request, $proyectoId)
    {
        if (!$request->ajax()) return redirect('/');
        $usuAsociados = DB::table('usuarios_proyectos')->join('users','usuarios_proyectos.fk_usuAsoc_users','=','users.id')
        ->select('usuarios_proyectos.fk_usuAsoc_users as id','users.usuario')
        ->where('fk_id_proyectos', '=', $proyectoId)
        ->orderBy('id', 'asc')->get();

        return ['msAsociados' => $usuAsociados];
    }
    public function selectSegmentos()
    {
        $segmentos = Segmento::select('id', 'segmento')->where([
            ['fk_id_empresas', '=', Session::get('id_empresa')],
            ['estado', '=', 1]
        ])->get();
        
        return ['segmentos' => $segmentos];
    }
    public function msSegAsociados(Request $request, $proyectoId)
    {
        if (!$request->ajax()) return redirect('/');
        $msSegmentos = DB::table('proyectos_segmentos')->join('segmentos','proyectos_segmentos.fk_id_segmentos','=','segmentos.id')
        ->select('proyectos_segmentos.fk_id_segmentos as id','segmentos.segmento')
        ->where('fk_id_proyectos', '=', $proyectoId)
        ->orderBy('id', 'asc')->get();

        return ['msSegmentos' => $msSegmentos];
    }
    public function listarUsuAsociados(Request $request, $proyectoId)
    {
        if (!$request->ajax()) return redirect('/');
        $respon = DB::table('usuarios_proyectos')->join('users','usuarios_proyectos.fk_usuAsoc_users','=','users.id')
        ->join('proyectos','usuarios_proyectos.fk_id_proyectos','=','proyectos.id')
        ->select('usuarios_proyectos.fk_usuAsoc_users as id','users.nombre_completo')
        ->where('fk_id_proyectos', '=', $proyectoId)
        ->orderBy('id', 'asc')->get();

        return ['asociados' => $respon];
    }

    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        try{
            DB::beginTransaction();
        
            $idEmpresa = Session::get('id_empresa');
            $usu = Auth::user()->id;

            $nuevoProyecto = $request->all() + [
                'fk_id_empresas' => $idEmpresa,
                'fk_usuCrea_users' => $usu,
                'fk_usuActualiza_users' => $usu,
            ];
            $proyecto = ConfProyecto::create($nuevoProyecto);

            $valores = $request->value;
            $nuevosAsociados = [];
            foreach($valores as $valor)
            {
                $nuevosAsociados[$valor['id']] = ['fk_id_empresas' => $idEmpresa, 'fk_usuCrea_users' => $usu];
            }
            $proyecto->asociados()->attach($nuevosAsociados);

            $segmentos = $request->valueSegmentos;
            $nuevosSegmentos = [];
            foreach($segmentos as $segmento)
            {
                $nuevosSegmentos[$segmento['id']] = ['fk_id_empresas' => $idEmpresa, 'fk_usuCrea_users' => $usu];
            }
            $proyecto->segmentos()->attach($nuevosSegmentos);
            
            DB::commit();
        } catch (Exception $e){
            DB::rollBack();
        }
    }
    public function update(Request $request, ConfProyecto $proyecto){
        if (!$request->ajax()) return redirect('/');
        try{
            DB::beginTransaction();

            $idEmpresa = Session::get('id_empresa');
            $usu = Auth::user()->id;

            if ($request->proyecto != $proyecto->proyecto) {
                $proyecto->proyecto = $request->proyecto;
            }
            if ($request->descripcion != $proyecto->descripcion) {
                $proyecto->descripcion = $request->descripcion;
            }
            if ($request->fk_respons_users != $proyecto->fk_respons_users) {
                $proyecto->fk_respons_users = $request->fk_respons_users;
            }
            if ($request->correoProductOwner != $proyecto->correoProductOwner) {
                $proyecto->correoProductOwner = $request->correoProductOwner;
            }
            if ($proyecto->isDirty()) {
                $proyecto->fk_usuActualiza_users = $usu;
                $proyecto->save();
            }

            $proyecto->asociados()->detach();
            $proyecto->segmentos()->detach();

            $valores = $request->value;
            $segmentos = $request->valueSegmentos;

            $asociados = [];
            foreach($valores as $valor)
            {
                $asociados[$valor['id']] = ['fk_id_empresas' => $idEmpresa, 'fk_usuCrea_users' => $usu];
            }
            $proyecto->asociados()->attach($asociados);

            $nuevosSegmentos = [];
            foreach($segmentos as $segmento)
            {
                $nuevosSegmentos[$segmento['id']] = ['fk_id_empresas' => $idEmpresa, 'fk_usuCrea_users' => $usu];
            }
            $proyecto->segmentos()->attach($nuevosSegmentos);

            DB::commit();
        } catch (Exception $e){
            DB::rollBack();
        }
    }
    public function desactivar(Request $request, ConfProyecto $proyecto)
    {
        if (!$request->ajax()) return redirect('/');
        $proyecto->estado = 0;
        $proyecto->save();
    }

    public function activar(Request $request,  ConfProyecto $proyecto)
    {
        if (!$request->ajax()) return redirect('/');
        $proyecto->estado = 1;
        $proyecto->save();
    }
    public function finalizar(Request $request,  ConfProyecto $proyecto)
    {
        if (!$request->ajax()) return redirect('/');
        $proyecto->estado = 2;
        $proyecto->save();
    }
    
}
