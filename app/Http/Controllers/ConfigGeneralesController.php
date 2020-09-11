<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ConfigGenerales;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ConfigGeneralesController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        $id_empresa = $request->session()->get('id_empresa');
        
        if ($buscar==''){
            $configgenerales = ConfigGenerales::where('id','=',$id_empresa)->orderBy('id', 'desc')->paginate(6);
        }
        else{
            $configgenerales = ConfigGenerales::where($criterio, 'like', '%'. $buscar . '%')->where('id','=',$id_empresa)->orderBy('id', 'desc')->paginate(6);
        }
        return [
            'pagination' => [
                'total'        => $configgenerales->total(),
                'current_page' => $configgenerales->currentPage(),
                'per_page'     => $configgenerales->perPage(),
                'last_page'    => $configgenerales->lastPage(),
                'from'         => $configgenerales->firstItem(),
                'to'           => $configgenerales->lastItem(),
            ],
            'configgenerales' => $configgenerales,
        ];
    }

    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $id_usuario = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');

        $carpetaEmpresa = $id_empresa .'_empresa'; 
        $dirEmpresa = public_path("logos/$carpetaEmpresa");
        if (!file_exists($dirEmpresa)) mkdir($dirEmpresa, 0777);

        if(is_uploaded_file($_FILES['logo']['tmp_name']))
        {
            $nombreLogo = $_FILES['logo']['name'];
            if($_FILES['logo']['type'] == "image/jpg" || $_FILES['logo']['type'] == "image/jpeg" || $_FILES['logo']['type'] == "image/png")
            {
                copy($_FILES['logo']['tmp_name'],$dirEmpresa.'/'.$_FILES['logo']['name']);

                $archivo = [
                    'nombre' => $request->nombre,
                    'logo' => $nombreLogo,
                    'repre_legal' => $request->repre_legal,
                    'nit' => $request->nit,
                    'direccion' => $request->direccion,
                    'res_fact_elect' => $request->res_fact_elect,
                    'res_fact_pos' => $request->res_fact_pos,
                    'correo' => $request->correo,
                    'celular' => $request->celular,
                    'telefono' => $request->telefono,
                    'usu_crea' => $id_usuario,
                    'usu_actualiza' => $id_usuario
                ];

                ConfigGenerales::create($archivo);
            }
        }
    }

    public function update(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $id_usuario = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');

        $carpetaEmpresa = $id_empresa .'_empresa'; 
        $dirEmpresa = public_path("logos/$carpetaEmpresa");
        if (!file_exists($dirEmpresa)) mkdir($dirEmpresa, 0777);

        if(is_uploaded_file($_FILES['logo']['tmp_name']))
        {
            $nombreLogo = $_FILES['logo']['name'];
            if($_FILES['logo']['type'] == "image/jpg" || $_FILES['logo']['type'] == "image/jpeg" || $_FILES['logo']['type'] == "image/png")
            {
                copy($_FILES['logo']['tmp_name'],$dirEmpresa.'/'.$_FILES['logo']['name']);

                $archivo = [
                    'nombre' => $request->nombre,
                    'logo' => $nombreLogo,
                    'repre_legal' => $request->repre_legal,
                    'nit' => $request->nit,
                    'direccion' => $request->direccion,
                    'res_fact_elect' => $request->res_fact_elect,
                    'res_fact_pos' => $request->res_fact_pos,
                    'correo' => $request->correo,
                    'celular' => $request->celular,
                    'telefono' => $request->telefono,
                ];

                $config = ConfigGenerales::findOrFail($request->id);
                $config->update($archivo);
            }
        }
    }

    public function desactivar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $config = ConfigGenerales::findOrFail($request->id);
        $config->estado = '0';
        $config->save();
    }

    public function activar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $config = ConfigGenerales::findOrFail($request->id);
        $config->estado = '1';
        $config->save();
    }
}
