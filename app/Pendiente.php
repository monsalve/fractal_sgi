<?php

namespace App;

use App\User;
use App\Empresa;
use App\Asignacion;
use App\ConfProyecto;
use App\AsignacionEvidencia;
use Illuminate\Database\Eloquent\Model;

class Pendiente extends Model
{
    protected $table = 'pendientes';
    protected $fillable = [
        'fecha'
        , 'pendiente'
        , 'observacion'
        , 'fk_id_empresas'
        , 'fk_id_proyectos'
        , 'fk_usuCrea_users'
        , 'fk_usuActualiza_users'
    ];

    public function empresa ()
    {
        return $this->belongsTo(Empresa::class, 'fk_id_empresas');
    }

    public function proyecto ()
    {
        return $this->belongsTo(ConfProyecto::class, 'fk_id_proyectos');
    }

    public function usuCrea ()
    {
        return $this->belongsTo(User::class, 'fk_usuCrea_users');
    }

    public function usuActualiza ()
    {
        return $this->belongsTo(User::class, 'fk_usuActualiza_users');
    }

    public function asignaciones ()
    {
        return $this->hasMany(Asignacion::class, 'fk_id_pendientes');
    }

    public function asignacionesEvidencias ()
    {
        return $this->hasManyThrough(AsignacionEvidencia::class, Asignacion::class, 'fk_id_pendientes', 'fk_id_asignaciones');
    }
}
