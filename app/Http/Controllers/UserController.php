<?php

namespace App\Http\Controllers;

use App\Rol;
use App\User;
use App\Cargo;
use App\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Builder;

class UserController extends Controller
{
    public function miId(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        return ['id' => Auth::user()->id];
    }

    public function listaSimple(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $usuarios = User::select('id', 'nombre_completo')->where('empresas_id', '=', Session::get('id_empresa'))->orderBy('nombre_completo')->get();
        return ['usuarios' => $usuarios];
    }
    
    public function listaSimpleExcluido (Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $usuarios = User::select('id', 'nombre_completo')->where([
            ['empresas_id', '=', Session::get('id_empresa')]
            , ['id', '!=', Auth::user()->id]
        ])->orderBy('nombre_completo')->get();        
        return ['usuarios' => $usuarios];
    } 
    
    private static function getCargosConDocumentos()
    {
        $cargosConDocumentos = Cargo::where([
            ['fk_id_empresas', '=', Session::get('id_empresa')]
            , ['cargos.estado', '=', 1]
        ])->select('id')->with(['cargoDocumentos' => function ($query) {
            $query->select('id', 'documento', 'fk_id_cargos')->orderBy('documento');
        }])/*->withCount('rolDocumentos as rolDocumentos_count')*/->orderBy('cargo')->get()->keyBy('id');
        
        return $cargosConDocumentos;
    }
    public function basicoListarCargosConDocumentos(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        return ['cargosConDocumentos' => self::getCargosConDocumentos()];
    }

    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        

        $respuesta = User::join('personas','users.id','=','personas.id')
        ->join('cargos', 'users.fk_id_cargos', '=', 'cargos.id')
        ->select(
            'personas.id'
            , 'nombre_completo'
            , 'nombre1'
            , 'nombre2'
            , 'apellido1'
            , 'apellido2'
            , 'tipo_documento'
            , 'num_documento'
            , 'fec_nac'
            , 'sexo'
            , 'direccion'
            , 'telefono'
            , 'email'
            , 'logueable'
            , 'usuario'
            , 'condicion'
            , 'fk_id_cargos'
            , 'cargo'
            , 'idrol'
        );
        if ($buscar != '') {
            $respuesta->where('personas.'.$criterio, 'like', "%$buscar%");
        }
        $personas = $respuesta->with(['usuariosCarpetas' => function ($query) {
            $query->select('id', 'carpeta', 'fk_id_users')->with(['usuariosDocumentos' => function ($query) {
                $query->select('id', 'documento', 'ruta', 'fk_id_cargos_documentos', 'fk_id_usuarios_carpetas')
                ->whereNotNull('fk_id_cargos_documentos')->orderBy('documento');
            }]);
        }])->orderBy('id', 'desc')->paginate($request->numRegs);

        $cargosConDocumentos = self::getCargosConDocumentos();
        
        $personas->each(function ($persona, $personaInd) use ($cargosConDocumentos) {
            $obliSubidos = 0;
            $persona->usuariosCarpetas->each(function ($usuarioCarpeta, $usuarioCarpetaInd) use (&$obliSubidos) {
                $carpeta = $usuarioCarpeta->carpeta;
                $usuarioCarpeta->usuariosDocumentos->each(function ($usuarioDocumento, $usuarioDocumentoInd) use ($carpeta) {
                    $usuarioDocumento->setAttribute('nombre_ruta', "$carpeta / $usuarioDocumento->documento");
                    $usuarioDocumento->ruta = asset("usuDocumentos/$usuarioDocumento->ruta");
                });
                $obliSubidos += $usuarioCarpeta->usuariosDocumentos->count();
            });
            $docsCargo = $cargosConDocumentos[$persona->fk_id_cargos]->cargoDocumentos->count();
            $persona->setAttribute('indicadorDocsPends', $obliSubidos.' / '.$docsCargo)->setAttribute('documentosPendientes_count', $docsCargo - $obliSubidos);
        });
        //->with(['usuariosDocumentos' => function ($query) {$query->select('usuarios_documentos.id, documento, ruta')->whereNotNull('fk_id_roles_documentos')->orderBy('id');
        //return is_object($personas)? 'Es objeto': 'No lo es';//return $personas[0]->usuariosDocumentos;
        return [
            'pagination' => [
                'total'          => $personas->total()
                , 'current_page' => $personas->currentPage()
                , 'per_page'     => $personas->perPage()
                , 'last_page'    => $personas->lastPage()
                , 'from'         => $personas->firstItem()
                , 'to'           => $personas->lastItem()
            ],
            'personas' => $personas
        ];
    }

    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        try{
            DB::beginTransaction();

            $persona = new Persona;
            $persona->nombre1 = $request->nombre1;
            $persona->nombre2 = $request->nombre2;
            $persona->apellido1 = $request->apellido1;
            $persona->apellido2 = $request->apellido2;
            $persona->tipo_documento = $request->tipo_documento;
            $persona->num_documento = $request->num_documento;
            $persona->fec_nac = $request->fec_nac;
            $persona->sexo = $request->sexo;
            $persona->direccion = $request->direccion;
            $persona->telefono = $request->telefono;
            $persona->email = $request->email;
            // Datos obligatorios
            $persona->regimen = 'Comun';
            $persona->reside = 'Sin residencia';
            $persona->tipo_persona = 'Natural';
            $persona->save();

            $user = new User;
            $user->id = $persona->id;
            $user->nombre_completo = $persona->nombre1. ($persona->nombre2? ' '.$persona->nombre2:' ') .$persona->apellido1. ($persona->apellido2? ' '.$persona->apellido2:'');
            $user->fk_id_cargos = $request->cargoId;
            $user->personas_id = $persona->id;
            $user->logueable = $request->logueable;
            if ($user->logueable) {
                if ($request->usuario)
                    $user->usuario = $request->usuario;
                
                if ($request->password)
                    $user->password = bcrypt($request->password);
            }
            $user->empresas_id = Session::get('id_empresa');
            $usu = Auth::user()->id;
            $user->fk_usuCrea_users = $usu;
            $user->fk_usuActualiza_users = $usu;
            $user->save();

            DB::commit();
        } catch (Exception $e){
            DB::rollBack();
        }
    }

    public function update(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        try{
            DB::beginTransaction();

            $persona = Persona::findOrFail($request->id);

            if ($request->nombre1 != $persona->nombre1)
                $persona->nombre1 = $request->nombre1;

            if ($request->nombre2 != $persona->nombre2)
                $persona->nombre2 = $request->nombre2;

            if ($request->apellido1 != $persona->apellido1)
                $persona->apellido1 = $request->apellido1;

            if ($request->apellido2 != $persona->apellido2)
                $persona->apellido2 = $request->apellido2;

            if ($request->tipo_documento != $persona->tipo_documento)
                $persona->tipo_documento = $request->tipo_documento;

            if ($request->num_documento != $persona->num_documento)
                $persona->num_documento = $request->num_documento;

            if ($request->fec_nac != $persona->fec_nac)
                $persona->fec_nac = $request->fec_nac;

            if ($request->sexo != $persona->sexo)
                $persona->sexo = $request->sexo;

            if ($request->direccion != $persona->direccion)
                $persona->direccion = $request->direccion;

            if ($request->telefono != $persona->telefono)
                $persona->telefono = $request->telefono;

            if ($request->email != $persona->email)
                $persona->email = $request->email;

            if ($persona->isDirty())
                $persona->save();
            
            $user = User::findOrFail($request->id);
                
            $nombreCompleto = $persona->nombre1. ($persona->nombre2? ' '.$persona->nombre2:' ') .$persona->apellido1. ($persona->apellido2? ' '.$persona->apellido2:'');
            if ($user->nombre_completo != $nombreCompleto)
                $user->nombre_completo = $nombreCompleto;

            if ($request->logueable != $user->logueable) {
                $user->logueable = $request->logueable;
            }

            if ($user->logueable) {
                if ($request->usuario != $user->usuario)
                    $user->usuario = $request->usuario;

                if ($request->password)
                    $user->password = bcrypt($request->password);
            } else {
                if ($user->usuario)
                    $user->usuario = '';

                if ($user->password)
                    $user->password = '';
            }

            if ($user->isDirty()) {
                $user->fk_usuActualiza_users = Auth::user()->id;
                $user->save();
            }

            DB::commit();
        } catch (Exception $e){
            DB::rollBack();
        }
    }

    public function updateRol(Request $request, User $user)
    {
        if (!$request->ajax()) return redirect('/');

        if ($request->rolId != $user->idrol)
            $user->idrol = $request->rolId;

        if ($user->isDirty()) {
            $user->fk_usuActualiza_users;
            $user->save();
        }
    }
    
    public function desactivar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $user = User::findOrFail($request->id);
        $user->condicion = '0';
        $user->save();
    }

    public function activar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $user = User::findOrFail($request->id);
        $user->condicion = '1';
        $user->save();
    }
}
