<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rol;
use App\RolPermisos;
use App\Modulo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RolController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;

        $respuesta = Rol::select('id', 'nombre', 'estado');

        if ($buscar) {
            $respuesta->where('nombre', 'LIKE', "%$buscar%");
        }
        if (isset($request->estado)) {
            $respuesta->where('estado', '=', $request->estado);
        }

        $roles = $respuesta->orderBy('id', 'desc')->get();
        
        $roles2 = Rol::select('id','nombre','estado')->groupBy('nombre')->orderBy('id','desc')->paginate(6);

        return [
            'pagination' => [
                'total'        => $roles2->total(),
                'current_page' => $roles2->currentPage(),
                'per_page'     => $roles2->perPage(),
                'last_page'    => $roles2->lastPage(),
                'from'         => $roles2->firstItem(),
                'to'           => $roles2->lastItem(),
            ],
            // 'page' => $request->page,
            // 'buscar' => $buscar,
            // 'criterio' => $criterio,
            'roles' => $roles,
            'roles2' => $roles2
        ];
    }

    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $id_usuario = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');

        $roles = new Rol();
        $roles->nombre = $request->nombre;
        $roles->empresa_id = $id_empresa;
        $roles->usu_crea = $id_usuario;
        $roles->fk_usuActualiza_users = $id_usuario;
        $roles->save();

        $modulos = $request->modulos;

        foreach($modulos as $mod)
        {
            $permisos = new RolPermisos();
            $permisos->id_rol = $roles->id;
            $permisos->id_modulo = $mod['id'];
            $ban = 0;

            if(isset($mod['lectura']) && $mod['lectura']==true)
            {
                $permisos->lectura = 1;
                $ban = 1;
            }

            if(isset($mod['escritura']) && $mod['escritura']==true)
            {
                $permisos->escritura = 1;
                $ban = 1;
            }

            if(isset($mod['edicion']) && $mod['edicion']==true)
            {
                $permisos->edicion = 1;
                $ban = 1;
            }

            if(isset($mod['anular']) && $mod['anular']==true)
            {
                $permisos->anular = 1;
                $ban = 1;
            }

            if(isset($mod['imprimir']) && $mod['imprimir']==true)
            {
                $permisos->imprimir = 1;
                $ban = 1;
            }
            
            $permisos->usu_crea = $id_usuario;

            if($ban==1)
            {
                $permisos->save();
            }

        }
    }

    public function update(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $id_usuario = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');

        $rol = Rol::findOrFail($request->id);

        if ($request->nombre != $rol->nombre)
            $rol->nombre = $request->nombre;

        if ($rol->isDirty()) {
            $rol->fk_usuActualiza_users = $id_usuario;
            $rol->save();
        }
        $rol->modulos()->detach();

        $modulos = $request->modulos;

        foreach($modulos as $mod)
        {
            $permisos = new RolPermisos();
            $permisos->id_rol = $rol->id;
            $permisos->id_modulo = $mod['id'];
            $ban = 0;

            if(isset($mod['lectura']) && $mod['lectura']==true)
            {
                $permisos->lectura = 1;
                $ban = 1;
            }

            if(isset($mod['escritura']) && $mod['escritura']==true)
            {
                $permisos->escritura = 1;
                $ban = 1;
            }

            if(isset($mod['edicion']) && $mod['edicion']==true)
            {
                $permisos->edicion = 1;
                $ban = 1;
            }

            if(isset($mod['anular']) && $mod['anular']==true)
            {
                $permisos->anular = 1;
                $ban = 1;
            }

            if(isset($mod['imprimir']) && $mod['imprimir']==true)
            {
                $permisos->imprimir = 1;
                $ban = 1;
            }

            $permisos->usu_crea = $id_usuario;

            if($ban)
            {
                $permisos->save();
            }
        }
    }

    public function listarPermisos(Request $request){
        if (!$request->ajax()) return redirect('/');

        $id_rol = $request->id_rol;
        
        $total = array();
        $modu = array();
        $permi = array();
        $permisos = array();

        $modulos = Modulo::select('id','nombre', 'tipo', 'padre')->where([['tipo','=','1'], ['estado','=','1']])->orderBy('nombre', 'asc')->get();

        foreach($modulos as $mod)
        {
            $total[] = $mod;
            $modulos2= Modulo::select('id','nombre', 'tipo', 'padre')->where([
                ['tipo', '=', 2]
                , ['padre', '=', $mod['id']]
                , ['estado', '=', 1]
            ])->orderBy('nombre','asc')->get();
            foreach($modulos2 as $mod2)
            {
                $total[] = $mod2;
            }
        }

        if($id_rol!='' && $id_rol!=null && $id_rol!=0)
        {
            foreach($total as $t)
            {
                $modu = array(
                    'id' => $t['id'],
                    'nombre' => $t['nombre'],
                    'tipo' => $t['tipo'],
                    'padre' => $t['padre'],
                );

                $permisos = RolPermisos::where([['id_rol','=',$id_rol],['id_modulo', '=', $t['id']]])->first();
                
                if($permisos)
                {
                    $permi = [
                        'lectura' => $permisos->lectura
                        , 'escritura' => $permisos->escritura
                        , 'edicion' => $permisos->edicion
                        , 'anular' => $permisos->anular
                        , 'imprimir' => $permisos->imprimir
                    ];
                }
                else
                {
                    $permi = [
                        'lectura' => ''
                        , 'escritura' => ''
                        , 'edicion' => ''
                        , 'anular' => ''
                        , 'imprimir' => ''
                    ];
                }

                $respuesta[] = array_merge($modu, $permi);
            }
        }
        else
        {
            foreach ($total as $rolPermiso) {
                $rolPermiso['lectura'] = '';
                $rolPermiso['escritura'] = '';
                $rolPermiso['edicion'] = '';
                $rolPermiso['anular'] = '';
                $rolPermiso['imprimir'] = '';
            }
            $respuesta = $total;
        }
        
        return [
            'permisos' => $respuesta,
        ];
    }

    public function selectRol(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $roles = Rol::where('estado', '=', '1')
        ->select('id','nombre')
        ->orderBy('nombre', 'asc')->get();

        return ['roles' => $roles];
    }

    public function desactivar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $rol = Rol::findOrFail($request->id);
        if ($rol->usuarios->count()) return ['hayUsuarios' => true]; 
        $rol->estado = '0';
        $rol->save();
        return ['hayUsuarios' => false];
    }

    public function activar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $roles = Rol::findOrFail($request->id);
        $roles->estado = '1';
        $roles->save();
    }
}
