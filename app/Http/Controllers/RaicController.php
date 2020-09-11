<?php

namespace App\Http\Controllers;

use App\Raic;
use App\User;
use App\RaicFuente;
use App\CausaNovedad;
use App\CausaNovedadItem;
use App\RaicTipoDesviacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class RaicController extends Controller
{
    public function usuarioId(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        return ['id' => Auth::user()->id];
    }
    // RETORNA LOS USUARIOS DE 'Reportado por <select>...</select>' EN EL FILTRO DE RAICS
    public function usuariosFiltro(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $id_empresa = Session::get('id_empresa');
        $usuariosEnRaic = Raic::select('rpDocumento', 'rpNombresCompletos')->where('fk_id_empresas', '=', $id_empresa)->whereNotNull('rpDocumento')->groupBy('rpDocumento');
        // https://laravel.com/docs/6.x/queries#unions
        // https://dev.mysql.com/doc/refman/8.0/en/union.html
        // SE DECIDIÓ NO APLICAR UNIQUE
        $usuarios = User::select('id as numId', 'nombre_completo')->where('empresas_id', '=', $id_empresa)->union($usuariosEnRaic)->get();

        return ['usuarios' => $usuarios];
    }
    // RETORNA LA LISTA DE USUARIOS PARA SELECCIONAR EN EL MODAL IMPLICADOS
    public function usuarios(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $select = [ 'users.id', 'num_documento', 'nombre_completo' ];
        $orWhere = [
            ['num_documento', 'LIKE', "%$request->filtro%", 'or']
            , ['nombre_completo', 'LIKE', "%$request->filtro%", 'or']
        ];
        $whereNotIn = [];

        $respuesta = User::join('personas', 'users.personas_id', '=', 'personas.id');

        if ($request->hayCargo) {
            $select[] = 'cargo';
            $orWhere[] = ['cargo', 'LIKE', "%$request->filtro%", 'or'];
            $respuesta->join('cargos', 'users.fk_id_cargos', '=', 'cargos.id');
        }
        // EXCLUYE EL SELECCIONADO PREVIAMENTE SI EXISTE
        if ($request->{"pi$request->tipoImpli"} != '') {
            $whereNotIn[] = $request->{"pi$request->tipoImpli"};
        }
        // EXCLUYE Jefe inmediato Y Supervisor SELECCIONADOS SI SE BUSCA A Reportado por
        if ($request->tipoImpli == 'RepPor') {
            if ($request->piJefInmed) {
                $whereNotIn[] = $request->piJefInmed;
            }
            if ($request->piSuperv) {
                $whereNotIn[] = $request->piSuperv;
            }
        }
        // EXCLUYE A Reportado por SELECCIONADO SI SE BUSCA A Jefe inmediato o Supervisor
        if ($request->tipoImpli == 'JefInmed' || $request->tipoImpli == 'Superv') {
            if ($request->piRepPor) {
                $whereNotIn[] = $request->piRepPor;
            }
        }
        
        if ($whereNotIn) {
            $respuesta->whereNotIn('users.id', $whereNotIn);
        }
        
        $usuarios = $respuesta->select($select)->where('empresas_id', '=', Session::get('id_empresa'))
        ->where($orWhere)->orderBy('nombre_completo')->get();
        
        return ['usuarios' => $usuarios];
    }

    public function fuentesRep (Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $fuentesRep = RaicFuente::select('id', 'raicFuente')->get();

        return ['fuentesRep' => $fuentesRep];
    }

    public function tiposDesviaciones (Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $tiposDesviaciones = RaicTipoDesviacion::select('id', 'raicTipoDesviacion')->get();

        return ['tiposDesviaciones' => $tiposDesviaciones];
    }

    public function cbi (Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        // $fkIdRaics = $request->fk_id_raics;
        $causas = CausaNovedad::select('id', 'causaNovedad', 'padre')->orderBy('padre')->orderBy('causaNovedad')->get();
        $items = CausaNovedadItem::select('id', 'causaNovedadItem', 'fk_id_causas_novedades')->orderBy('causaNovedadItem')->orderBy('fk_id_causas_novedades')->get();
        /*CausaNovedadItem::select('causas_novedades_items.id', 'causaNovedadItem', 'fk_id_raics', 'fk_id_causas_novedades')
        ->leftJoin('causas_items_raics', function ($leftJoin) use ($fkIdRaics) {
            $leftJoin->on('causas_novedades_items.id', '=', 'causas_items_raics.fk_id_causas_items')
            ->where('fk_id_raics', '=', $fkIdRaics);
        })->orderBy('causaNovedadItem')->orderBy('fk_id_causas_novedades')->get();*/

        $padres4Arr = $padres3Arr = $cbi3Arr = $padres2Arr = $cbi2Arr = $cbiArr = [];
        $padre = 0;
        foreach ($items as $item) {
            if ($item->fk_id_causas_novedades != $padre) {
                $padre = $item->fk_id_causas_novedades;
                $padres4Arr[] = $padre;
            }
            $itemsArr[$item->fk_id_causas_novedades][] = [
                'id' => $item->id
                , 'item' => $item->causaNovedadItem
                , 'marcado' => FALSE//$item->fk_id_raics ? TRUE : FALSE
            ];
        }
        foreach ($causas->whereIn('id', $padres4Arr) as $causa) {
            $padres3Arr[] = $causa->padre;
            $cbi3Arr[$causa->padre][] = [
                'causa' => $causa->causaNovedad
                , 'items' => $itemsArr[$causa->id]
            ];
        }
        foreach ($causas->whereIn('id', $padres3Arr) as $causa) {
            $padres2Arr[] = $causa->padre;
            $cbi2Arr[$causa->padre][] = [
                'causa' => $causa->causaNovedad
                , 'subcausas' => $cbi3Arr[$causa->id]
            ];
        }
        foreach ($causas->whereIn('id', $padres2Arr) as $causa) {
            $cbiArr[] = [
                'causa' => $causa->causaNovedad
                , 'subcausas' => $cbi2Arr[$causa->id]
            ];
        }

        return ['causasBasInmed' => $cbiArr];
    }

    public function cbiMarcados (Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $items = DB::table('causas_items_raics')->select('fk_id_causas_items')->where('fk_id_raics', '=', $request->fk_id_raics)->get();

        return ['items' => $items];
    }
    // RETORNA LOS IMPLICADOS QUE HAYAN SIDO ELEGIDOS DESDE EL MODAL DE IMPLICADOS
    public function implicadosObtener (Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $respuesta = User::join('personas', 'users.personas_id', '=', 'personas.id')->join('cargos', 'users.fk_id_cargos', '=', 'cargos.id')
        ->select('users.id', 'num_documento', 'nombre_completo', 'cargo');
        $whereIn = [];
        if ($request->fk_piRepPor_users) {
            $whereIn[] = $request->fk_piRepPor_users;
        }
        if ($request->fk_piJefInmed_users) {
            $whereIn[] = $request->fk_piJefInmed_users;
        }
        if ($request->fk_piSuperv_users) {
            $whereIn[] = $request->fk_piSuperv_users;
        }
        $raicImplicados = $respuesta->whereIn('users.id', $whereIn)->get()->keyBy('id');

        return ['raicImplicados' => $raicImplicados];
    }
    
    public function contingenciasExistentes (Request $request, Raic $raic)
    {
        if (!$request->ajax()) return redirect('/');
        $abiertas = -1;
        if ($raic->contingencias()->where('estado', '=', 1)->count()) $abiertas = 0;
        if ($raic->contingencias()->where([['condicion', '=', 'Abierto'], ['estado', '=', 1]])->count()) $abiertas = 1;
        
        return ['abiertas' => $abiertas];
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index (Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $respuesta = Raic::join('proyectos', 'fk_id_proyectos', '=', 'proyectos.id')
        ->join('raics_fuentes', 'fk_id_raics_fuentes', '=', 'raics_fuentes.id')
        ->select(
            // Campos para mostrar en la tabla
            'raics.id'
            , 'fk_piRepPor_users'
            , 'rpDocumento'
            , 'rpNombresCompletos'
            , 'fk_piJefInmed_users'
            , 'fk_piSuperv_users'
            , 'supDocumento'
            , 'supNombresCompletos'
            , 'fechaReporte'
            , 'areaTrabajo'
            , 'fechaEvento'
            , 'descripcionCorta'
            , 'descripcionEvento'
            , 'fk_id_proyectos'
            , 'proyecto'
            , 'fk_id_raics_fuentes'
            , 'raicFuente'
            , 'haySeguimiento'
            , 'condicion'
            , 'raics.estado'
            , 'raics.fk_usuCrea_users'
            // Campos solicitados para cargar el formulario de actualizar
            , 'rpCargo'
            , 'rpAreaTrabajo'
            , 'jiDocumento'
            , 'jiNombresCompletos'
            , 'jiCargo'
            , 'tipoEvento'
            , 'fk_id_raics_tipos_desviaciones'
            , 'sitioEspecifico'
            , 'prioridad'
        );
        
        $where = [['raics.fk_id_empresas', '=', Session::get('id_empresa')]];
        if ($request->descripcionCorta) {
            $where[] = ['descripcionCorta', 'LIKE', "%$request->descripcionCorta%"];
        }

        if ($request->fechaReporte) {
            $where[] = ['fechaReporte', '=', $request->fechaReporte];
        }

        if ($request->fkIdProyecto) {
            $where[] = ['fk_id_proyectos', '=', $request->fkIdProyecto];
        }

        $respuesta->where($where);

        if ($request->reportadoPor) {
            $reportadoPor = $request->reportadoPor;
            $respuesta->where( function ($query) use ($reportadoPor) {
                $query->where('fk_piRepPor_users', '=', $reportadoPor)->orWhere('rpDocumento', '=', $reportadoPor);
            });
        }
        
        $raics = $respuesta->with('piRepPor:id,nombre_completo,personas_id'
        , 'piRepPor.persona:id,num_documento'
        , 'piSuperv:id,nombre_completo,personas_id'
        , 'piSuperv.persona:id,num_documento')->paginate($request->numRegs);

        return [
            'pagination' => [
                'total'        => $raics->total()
                , 'current_page' => $raics->currentPage()
                , 'per_page'     => $raics->perPage()
                , 'last_page'    => $raics->lastPage()
                , 'from'         => $raics->firstItem()
                , 'to'           => $raics->lastItem()
            ]
            , 'raics' => $raics
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
        $excluye = ['itemsMarcados'];
        $excluye += !$request->fk_piRepPor_users? ['fk_piRepPor_users'] : ['rpDocumento', 'rpNombresCompletos', 'rpCargo'];
        $excluye += !$request->fk_piJefInmed_users? ['fk_piJefInmed_users'] : ['jiDocumento', 'jiNombresCompletos', 'jiCargo'];
        $excluye += !$request->fk_piSuperv_users? ['fk_piSuperv_users'] : ['supDocumento', 'supNombresCompletos'];
        $nuevoRAIC = $request->except($excluye) + [
            'fk_id_empresas' => Session::get('id_empresa')
            , 'fk_usuCrea_users' => $usu
            , 'fk_usuActualiza_users' => $usu
        ];
        // SI ENTRA, condicion PASA A Cerrado
        if ($request->haySeguimiento == 'No') $nuevoRAIC['condicion'] = 2;

        $raic = Raic::create($nuevoRAIC);

        $itemsMarcadosArr = $request->itemsMarcados;
        $nuevosItems = [];
        foreach ($itemsMarcadosArr as $itemMarcado) {
            $nuevosItems[$itemMarcado] = [
                'fk_id_raics' => $raic->id
                , 'fk_usuCrea_users' => $usu
            ];
        }
        $raic->causasNovedadesItems()->attach($nuevosItems);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Raic  $raic
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Raic $raic)
    {
        if (!$request->ajax()) return redirect('/');
        $usu = Auth::user()->id;

        // Reportado por
        if ($request->rpDocumento != $raic->rpDocumento)
            $raic->rpDocumento = $request->rpDocumento;

        if ($request->rpNombresCompletos != $raic->rpNombresCompletos)
            $raic->rpNombresCompletos = $request->rpNombresCompletos;

        if ($request->rpCargo != $raic->rpCargo)
            $raic->rpCargo = $request->rpCargo;

        if ($request->fk_piRepPor_users != $raic->fk_piRepPor_users) {
            $raic->fk_piRepPor_users = NULL;
            if ($request->fk_piRepPor_users) {
                $raic->fk_piRepPor_users = $request->fk_piRepPor_users;
                $raic->rpDocumento = $raic->rpNombresCompletos = $raic->rpCargo = NULL;
            }
        }

        if ($request->rpAreaTrabajo != $raic->rpAreaTrabajo)
            $raic->rpAreaTrabajo = $request->rpAreaTrabajo;

        // Jefe inmediato
        if ($request->jiDocumento != $raic->jiDocumento)
            $raic->jiDocumento = $request->jiDocumento;

        if ($request->jiNombresCompletos != $raic->jiNombresCompletos)
            $raic->jiNombresCompletos = $request->jiNombresCompletos;

        if ($request->jiCargo != $raic->jiCargo)
            $raic->jiCargo = $request->jiCargo;

        if ($request->fk_piJefInmed_users != $raic->fk_piJefInmed_users) {
            $raic->fk_piJefInmed_users = NULL;
            if ($request->fk_piJefInmed_users) {
                $raic->fk_piJefInmed_users = $request->fk_piJefInmed_users;
                $raic->jiDocumento = $raic->jiNombresCompletos = $raic->jiCargo = NULL;
            }
        }

        // Supervisor
        if ($request->supDocumento != $raic->supDocumento)
            $raic->supDocumento = $request->supDocumento;

        if ($request->supNombresCompletos != $raic->supNombresCompletos)
            $raic->supNombresCompletos = $request->supNombresCompletos;

        if ($request->fk_piSuperv_users != $raic->fk_piSuperv_users) {
            $raic->fk_piSuperv_users = NULL;
            if ($request->fk_piSuperv_users) {
                $raic->fk_piSuperv_users = $request->fk_piSuperv_users;
                $raic->supDocumento = $raic->supNombresCompletos = NULL;
            }
        }

        if ($request->fk_id_raics_fuentes != $raic->fk_id_raics_fuentes)
            $raic->fk_id_raics_fuentes = $request->fk_id_raics_fuentes;

        if ($request->fk_id_proyectos != $raic->fk_id_proyectos)
            $raic->fk_id_proyectos = $request->fk_id_proyectos;

        if ($request->tipoEvento != $raic->tipoEvento)
            $raic->tipoEvento = $request->tipoEvento;

        if ($request->fechaReporte != $raic->fechaReporte)
            $raic->fechaReporte = $request->fechaReporte;

        if ($request->fk_id_raics_tipos_desviaciones != $raic->fk_id_raics_tipos_desviaciones)
            $raic->fk_id_raics_tipos_desviaciones = $request->fk_id_raics_tipos_desviaciones;

        if ($request->areaTrabajo != $raic->areaTrabajo)
            $raic->areaTrabajo = $request->areaTrabajo;

        if ($request->fechaEvento != $raic->fechaEvento)
            $raic->fechaEvento = $request->fechaEvento;

        if ($request->sitioEspecifico != $raic->sitioEspecifico)
            $raic->sitioEspecifico = $request->sitioEspecifico;

        if ($request->prioridad != $raic->prioridad)
            $raic->prioridad = $request->prioridad;

        if ($request->descripcionCorta != $raic->descripcionCorta)
            $raic->descripcionCorta = $request->descripcionCorta;

        if ($request->descripcionEvento != $raic->descripcionEvento)
            $raic->descripcionEvento = $request->descripcionEvento;

        if ($request->haySeguimiento != $raic->haySeguimiento) {
            $raic->haySeguimiento = $request->haySeguimiento;
            /*if ($request->haySeguimiento == 'No') {
                if ($raic->contingencias()->count() == 0)
                    $raic->haySeguimiento = $request->haySeguimiento;
            } else {
                $raic->haySeguimiento = $request->haySeguimiento;
            }*/
        }

        if ($raic->isDirty()) {
            $raic->fk_usuActualiza_users = $usu;
            $raic->save();
        }
        
        $raic->causasNovedadesItems()->detach();
        $itemsMarcadosArr = $request->itemsMarcados;
        $nuevosItems = [];
        foreach ($itemsMarcadosArr as $itemMarcado) {
            $nuevosItems[$itemMarcado] = [
                'fk_id_raics' => $raic->id
                , 'fk_usuCrea_users' => $usu
            ];
        }
        $raic->causasNovedadesItems()->attach($nuevosItems);
    }

    public function cerrar(Request $request, Raic $raic)
    {
        if (!$request->ajax()) return redirect('/');
        $raic->condicion = 'Cerrado';
        $raic->save();
    }
    
    public function abrir(Request $request, Raic $raic)
    {
        if (!$request->ajax()) return redirect('/');
        $raic->condicion = 'Abierto';
        $raic->save();
    }

    public function desactivar(Request $request, Raic $raic)
    {
        if (!$request->ajax()) return redirect('/');
        $raic->estado = 0;
        $raic->save();
    }

    public function activar(Request $request, Raic $raic)
    {
        if (!$request->ajax()) return redirect('/');
        $raic->estado = 1;
        $raic->save();
    }
}
