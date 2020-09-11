<?php

namespace App;

use App\User;
use App\Empresa;
use App\Segmento;
use App\ConfProyecto;
use Illuminate\Database\Eloquent\Model;

class PlanAccion extends Model
{
    protected $table = 'planes_accion';
    protected $fillable = [
        'planAccion', 'anio', 'puntaje', 'fk_id_empresas', 'fk_respons_users', 'fk_id_segmentos', 'fk_usuCrea_users', 'fk_usuActualiza_users'
    ];

    public function proyectos ()
    {
        return $this->belongsToMany(ConfProyecto::class, 'planes_accion_proyectos', 'fk_id_planes_accion', 'fk_id_proyectos');
    }
    public function responsable ()
    {
        return $this->belongsTo(User::class, 'fk_respons_users');
    }
    public function segmento ()
    {
        return $this->belongsTo(Segmento::class, 'fk_id_segmentos');
    }

    public function empresa ()
    {
        return $this->belongsTo(Empresa::class, 'fk_id_empresas');
    }
}