<?php

namespace App\Http\Controllers;

use App\CausaNovedad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CausaNovedadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $respuesta = CausaNovedad::select('id', 'causaNovedad', 'padre', 'estado');
        $where = [['padre', '=', $request->padre]];
        if ($request->leer != 1) { // ¿DEBE REPLANTEARSE ESTA CONDICIÓN?
            $where[] = ['fk_usuCrea_users', '=', Auth::user()->id];
        }
        $causasNovedades = $respuesta->where($where)->orderBy('causaNovedad')->withCount('causasNovedadesItems')->paginate($request->numRegs);

        $subcausasNovedades = CausaNovedad::select(DB::raw('count(*) as subniveles_count, padre'))
        ->whereIn('padre', $causasNovedades->pluck('id'))->groupBy('padre')->get();

        foreach ($causasNovedades as $causaNovedad) {
            $recuentoSubniveles = 0;
            foreach ($subcausasNovedades as $subcausaNovedad) {
                if ($subcausaNovedad->padre == $causaNovedad->id) {
                    $recuentoSubniveles = $subcausaNovedad->subniveles_count;
                }
            }
            $causaNovedad->setAttribute('subniveles_count', $recuentoSubniveles);
        }
        return [
            'pagination' => [
                'total'        => $causasNovedades->total()
                , 'current_page' => $causasNovedades->currentPage()
                , 'per_page'     => $causasNovedades->perPage()
                , 'last_page'    => $causasNovedades->lastPage()
                , 'from'         => $causasNovedades->firstItem()
                , 'to'           => $causasNovedades->lastItem()
            ]            
            , 'causasNovedades' => $causasNovedades
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
        $nuevaCausaNovedad = $request->all() + [
            #'fk_id_empresas' => Session::get('id_empresa'),
            'fk_usuCrea_users' => $usu
            , 'fk_usuActualiza_users' => $usu
        ];
        CausaNovedad::create($nuevaCausaNovedad);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CausaNovedad  $causaNovedad
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CausaNovedad $causaNovedad)
    {
        if (!$request->ajax()) return redirect('/');
        
        if ($request->causaNovedad != $causaNovedad->causaNovedad)
            $causaNovedad->causaNovedad = $request->causaNovedad;

        if ($causaNovedad->isDirty()) {
            $causaNovedad->fk_usuActualiza_users = Auth::user()->id;
            $causaNovedad->save();
        }
    }
    
    public function desactivar(Request $request, CausaNovedad $causaNovedad)
    {
        if (!$request->ajax()) return redirect('/');
        $causaNovedad->estado = 0;
        $causaNovedad->save();
    }
    public function activar(Request $request, CausaNovedad $causaNovedad)
    {
        if (!$request->ajax()) return redirect('/');
        $causaNovedad->estado = 1;
        $causaNovedad->save();
    }
}