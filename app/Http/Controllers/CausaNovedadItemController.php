<?php

namespace App\Http\Controllers;

use App\CausaNovedad;
use App\CausaNovedadItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CausaNovedadItemController extends Controller
{
    public function buscarCausa(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $niveles = CausaNovedad::select('id', 'causaNovedad', 'padre')->get();
        $causa = CausaNovedadItem::select('causaNovedadItem', 'fk_id_causas_novedades')->where('codigo', '=', $request->codigo)->first();

        $mensaje = 'Causa no registrada';
        if ($causa) {
            $idNivel = $causa->fk_id_causas_novedades;
            $mensaje = $causa->causaNovedadItem;
            foreach ($niveles as $nivel) {
                if ($nivel->id == $idNivel) {
                    $mensaje = "$nivel->causaNovedad > $mensaje";
                    if (!$nivel->padre)break;
                    $idNivel = $nivel->padre;
                }
            }
        }
        return ['mensaje' => $mensaje];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPorCausaNovedad(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $causasNovedadesItems = CausaNovedadItem::select('id', 'codigo', 'causaNovedadItem')
        ->where('fk_id_causas_novedades', '=', $request->fk_id_causas_novedades)->paginate($request->numRegs);

        return [
            'pagination' => [
                'total'        => $causasNovedadesItems->total(),
                'current_page' => $causasNovedadesItems->currentPage(),
                'per_page'     => $causasNovedadesItems->perPage(),
                'last_page'    => $causasNovedadesItems->lastPage(),
                'from'         => $causasNovedadesItems->firstItem(),
                'to'           => $causasNovedadesItems->lastItem(),
            ],
            'causasNovedadesItems' => $causasNovedadesItems
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
        $nuevaCausaNovedadItem = $request->all() + [
            'fk_usuCrea_users' => $usu
            , 'fk_usuActualiza_users' => $usu
        ];
        CausaNovedadItem::create($nuevaCausaNovedadItem);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CausaNovedadItem  $causaNovedadItem
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CausaNovedadItem $causaNovedadItem)
    {
        if (!$request->ajax()) return redirect('/');

        if ($request->codigo != $causaNovedadItem->codigo)
            $causaNovedadItem->codigo = $request->codigo;

        if ($request->causaNovedadItem != $causaNovedadItem->causaNovedadItem)
            $causaNovedadItem->causaNovedadItem = $request->causaNovedadItem;
            
        if ($causaNovedadItem->isDirty()) {
            $causaNovedadItem->fk_usuActualiza_users = Auth::user()->id;
            $causaNovedadItem->save();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CausaNovedadItem  $causaNovedadItem
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, CausaNovedadItem $causaNovedadItem)
    {
        if (!$request->ajax()) return redirect('/');
        $causaNovedadItem->delete();
    }
}
