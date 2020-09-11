<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActividadEvidencia extends Model
{
    protected $table = 'actividades_evidencias';
    protected $fillable = ['evidencia', 'ruta', 'fk_id_actividades_seguimientos', 'fk_usuCrea_users'];
}
