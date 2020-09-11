<?php

namespace App;

use App\User;
use App\Asignacion;
use Illuminate\Database\Eloquent\Model;

class AsignacionEvidencia extends Model
{
    protected $table = 'asignaciones_evidencias';
    protected $fillable = [
        'evidencia'
        , 'ruta'
        , 'fk_id_asignaciones'
        , 'fk_usuCrea_users'
        , 'fk_usuActualiza_users'
    ];

    public function asignacion ()
    {
        return $this->belongsTo(Asignacion::class, 'fk_id_asignaciones');
    }

    public function usuCrea ()
    {
        return $this->belongsTo(User::class, 'fk_usuCrea_users');
    }
    public function usuActualiza ()
    {
        return $this->belongsTo(User::class, 'fk_usuActualiza_users');
    }
}
