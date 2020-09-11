<?php

namespace App;

use App\User;
use App\RaicContingencia;
use Illuminate\Database\Eloquent\Model;

class RaicEvidencia extends Model
{
    protected $table = 'raics_evidencias';
    protected $fillable = [
        'evidencia'
        , 'ruta'
        , 'fk_id_raics_contingencias'
        , 'fk_usuCrea_users'
        , 'fk_usuActualiza_users'
    ];
    public function raicContingenica ()
    {
        return $this->belongsTo(RaicContingencia::class, 'fk_id_raics_contingencias');
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
