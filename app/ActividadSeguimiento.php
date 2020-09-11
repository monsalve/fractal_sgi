<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActividadSeguimiento extends Model
{
    protected $table = 'actividades_seguimientos';
    protected $fillable = ['aplica', 'mes', 'fechaReporte', 'observacion', 'fk_id_actividades', 'fk_usuCrea_users', 'fk_usuActualiza_users'];
}
