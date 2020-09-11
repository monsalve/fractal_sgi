<?php

namespace App;

use App\Raic;
use App\User;
use Illuminate\Database\Eloquent\Model;

class RaicTipoDesviacion extends Model
{
    protected $table = 'raics_tipos_desviaciones';
    protected $fillable = [
        'raicTipoDesviacion', 'fk_usuCrea_users', 'fk_usuActualiza_users'
    ];

    public function raics()
    {
        return $this->hasMany(Raic::class, 'fk_id_raics_tipos_desviaciones');
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
