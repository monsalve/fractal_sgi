<?php

namespace App;

use App\User;
use App\Empresa;
use App\SegmentoCarpeta;
use Illuminate\Database\Eloquent\Model;

class Segmento extends Model
{
    protected $table = 'segmentos';
    protected $fillable = [
        'segmento', 'tipoSegmento', 'observacion', 'fk_id_empresas', 'fk_usuCrea_users', 'fk_usuActualiza_users'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'fk_id_empresas');
    }

    public function usuCrea()
    {
        return $this->belongsTo(User::class, 'fk_usuCrea_users');
    }
    
    public function carpetas ()
    {
        return $this->hasMany(SegmentoCarpeta::class, 'fk_id_segmentos');
    }
}
