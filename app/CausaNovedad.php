<?php

namespace App;

use App\CausaNovedadItem;
use Illuminate\Database\Eloquent\Model;

class CausaNovedad extends Model
{
    protected $table = 'causas_novedades';
    protected $fillable = ['causaNovedad', 'padre', 'fk_usuCrea_users', 'fk_usuActualiza_users'];

    public function causasNovedadesItems()
    {
        return $this->hasMany(CausaNovedadItem::class, 'fk_id_causas_novedades');
    }
}
