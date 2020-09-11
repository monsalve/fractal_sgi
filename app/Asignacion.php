<?php

namespace App;

use App\User;
use App\Empresa;
use App\Pendiente;
use App\AsignacionEvidencia;
use Illuminate\Database\Eloquent\Model;

class Asignacion extends Model
{
    protected $table = 'asignaciones';
    protected $fillable = [
        'fechaLimite'
        , 'asignacion'
        , 'observacion'
        , 'fk_id_empresas'
        , 'fk_id_pendientes'
        , 'fk_respons_users'
        , 'fk_usuCrea_users'
        , 'fk_usuActualiza_users'
    ];

    public function empresa ()
    {
        return $this->belongsTo(Empresa::class, 'fk_id_empresas');
    }
    public function pendiente ()
    {
        return $this->belongsTo(Pendiente::class, 'fk_id_pendientes');
    }

    public function responsable ()
    {
        return $this->belongsTo(User::class, 'fk_respons_users');
    }
    
    public function usuCrea ()
    {
        return $this->belongsTo(User::class, 'fk_usuCrea_users');
    }

    public function usuActualiza ()
    {
        return $this->belongsTo(User::class, 'fk_usuActualiza_users');
    }

    public function evidencias ()
    {
        return $this->hasMany(AsignacionEvidencia::class, 'fk_id_asignaciones');
    }
}
