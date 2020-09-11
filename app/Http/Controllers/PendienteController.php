<?php

namespace App\Http\Controllers;

use App\Pendiente;
use App\ConfProyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class PendienteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $respuesta = Pendiente::join('proyectos', 'pendientes.fk_id_proyectos', '=', 'proyectos.id')
        ->select('pendientes.id', 'fecha', 'pendiente', 'observacion', 'fk_id_proyectos', 'proyecto', 'condicion', 'pendientes.estado', 'pendientes.fk_usuCrea_users');
        $where = [
            ['pendientes.fk_id_empresas', '=', Session::get('id_empresa')]
        ];
        if ($request->pendiente) $where[] = ['pendiente', 'LIKE', "%$request->pendiente%"];
        if ($request->condicion) $where[] = ['condicion', '=', intval($request->condicion)];
        if (isset($request->estado)) $where[] = ['pendientes.estado', '=', $request->estado];
        if ($request->fkIdProyectos) $where[] = ['fk_id_proyectos', '=', $request->fkIdProyectos];
        if ($request->leer != 1) $where[] = ['pendientes.fk_usuCrea_users', '=', Auth::user()->id];

        $pendientes = $respuesta->where($where)->paginate($request->numRegs);

        return [
            'pagination' => [
                'total'        => $pendientes->total()
                , 'current_page' => $pendientes->currentPage()
                , 'per_page'     => $pendientes->perPage()
                , 'last_page'    => $pendientes->lastPage()
                , 'from'         => $pendientes->firstItem()
                , 'to'           => $pendientes->lastItem()
            ]
            , 'pendientes' => $pendientes
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

        $nuevoPendiente = $request->all() + [
            'fk_id_empresas' => Session::get('id_empresa')
            , 'fk_usuCrea_users' => $usu
            , 'fk_usuActualiza_users' => $usu
        ];

        Pendiente::create($nuevoPendiente);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Pendiente  $pendiente
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pendiente $pendiente)
    {
        if (!$request->ajax()) return redirect('/');
        
        if ($request->fecha != $pendiente->fecha)
            $pendiente->fecha = $request->fecha;
        
        if ($request->pendiente != $pendiente->pendiente)
            $pendiente->pendiente = $request->pendiente;
        
        if ($request->observacion != $pendiente->observacion)
            $pendiente->observacion = $request->observacion;

        if ($request->fk_id_proyectos != $pendiente->fk_id_proyectos)
            $pendiente->fk_id_proyectos = $request->fk_id_proyectos;

        if ($pendiente->isDirty()) {
            $pendiente->fk_usuCrea_users = Auth::user()->id;
            $pendiente->save();
        }
    }

    public function cerrar(Request $request, Pendiente $pendiente)
    {
        if (!$request->ajax()) return redirect('/');
        $pendiente->condicion = 'Cerrado';
        $pendiente->save();
    }
    
    public function abrir(Request $request, Pendiente $pendiente)
    {
        if (!$request->ajax()) return redirect('/');
        $pendiente->condicion = 'Abierto';
        $pendiente->save();
    }
    
    public function desactivar(Request $request, Pendiente $pendiente)
    {
        if (!$request->ajax()) return redirect('/');
        $pendiente->estado = 0;
        $pendiente->save();
    }

    public function activar(Request $request, Pendiente $pendiente)
    {
        if (!$request->ajax()) return redirect('/');
        $pendiente->estado = 1;
        $pendiente->save();
    }
    
    public function cierreValidar(Request $request, Pendiente $pendiente)
    {
        if (!$request->ajax()) return redirect('/');
        $indCierre = -1;
        if ($pendiente->asignaciones()->where('estado', '=', 1)->count()) $indCierre = 0;
        if ($pendiente->asignaciones()->where([['condicion', '=', 'Abierto'], ['estado', '=', 1]])->count()) $indCierre = 1;

        return ['indicaCierre' => $indCierre];
    }

    public function proyectoSelector (Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $proyectos = ConfProyecto::paraSelector(Session::get('id_empresa'));
        return ['proyectos' => $proyectos];
    }
}
