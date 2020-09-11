<?php

namespace App;

use App\User;
use App\Empresa;
use App\Segmento;
use App\PlanAccion;
use App\ConfProyecto;
use App\ActividadSeguimiento;
use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    protected $table = 'actividades';
    protected $fillable = [
        'actividad'
        , 'tipoActividad'
        , 'ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'
        , 'diaMesLimite'
        , 'ponderacion'
        , 'fk_id_empresas'
        , 'fk_planAsoc_planes_accion'
        , 'fk_id_planes_accion'
        , 'fk_id_proyectos'
        , 'fk_id_segmentos'
        , 'fk_usuCrea_users'
        , 'fk_usuActualiza_users'
    ];

    public function planAccion ()
    {
        return $this->belongsTo(PlanAccion::class, 'fk_id_planes_accion');
    }
    public function proyecto ()
    {
        return $this->belongsTo(ConfProyecto::class, 'fk_id_proyectos');
    }
    public function segmento ()
    {
        return $this->belongsTo(Segmento::class, 'fk_id_segmentos');
    }

    public function responsables ()
    {
        return $this->belongsToMany(User::class, 'usuarios_actividades', 'fk_id_actividades', 'fk_id_users');
    }

    public function empresa ()
    {
        return $this->belongsTo(Empresa::class, 'fk_id_empresas');
    }

    public function seguimientos ()
    {
        return $this->hasMany(ActividadSeguimiento::class, 'fk_id_actividades');
    }
}
