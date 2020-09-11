<?php

namespace App;

use App\Raic;
use App\User;
use App\Segmento;
use App\Pendiente;
use App\PlanAccion;
use Illuminate\Database\Eloquent\Model;

class ConfProyecto extends Model
{
    protected $table = 'proyectos';
    protected $fillable = ['proyecto', 'descripcion', 'correoProductOwner', 'fk_id_empresas', 'fk_respons_users', 'fk_usuCrea_users', 'fk_usuActualiza_users'];

    public function rol()
    {
        return $this->hasMany('App\UsuariosProyecto');
    }

    public function asociados()
    {
        return $this->belongsToMany(User::class, 'usuarios_proyectos', 'fk_id_proyectos', 'fk_usuAsoc_users');
    }
    
    public function planesAccion()
    {
        return $this->belongsToMany(PlanAccion::class, 'planes_accion_proyectos', 'fk_id_proyectos', 'fk_id_planes_accion');
    }

    public function segmentos()
    {
        return $this->belongsToMany(Segmento::class, 'proyectos_segmentos', 'fk_id_proyectos', 'fk_id_segmentos');
    }

    public function pendientes()
    {
        return $this->hasMany(Pendiente::class, 'fk_id_proyectos');
    }

    public function raics()
    {
        return $this->hasMany(Raic::class, 'fk_id_proyectos');
    }
    // METODOS DB
    public static function paraSelector ($empresaId)
    {
        return self::select('id', 'proyecto')->where('fk_id_empresas', '=', $empresaId)->get();
    }
}
