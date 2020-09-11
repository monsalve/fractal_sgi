<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CausaNovedadItem extends Model
{
    protected $table = 'causas_novedades_items';
    protected $fillable = ['codigo', 'causaNovedadItem', 'fk_id_causas_novedades', 'fk_usuCrea_users', 'fk_usuActualiza_users'];
}
