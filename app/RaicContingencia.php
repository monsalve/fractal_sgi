<?php

namespace App;

use App\Raic;
use App\User;
use App\Empresa;
use Illuminate\Database\Eloquent\Model;

class RaicContingencia extends Model
{
    protected $table = 'raics_contingencias';
    protected $fillable = [
        'consecutivo'
        , 'accion'
        , 'fechaLimite'
        , 'fk_id_empresas'
        , 'fk_id_raics'
        , 'fk_respons_users'
        , 'fk_usuCrea_users'
        , 'fk_usuActualiza_users'
    ];
    public function empresa ()
    {
        return $this->belongsTo(Empresa::class, 'fk_id_empresas');
    }
    public function raic ()
    {
        return $this->belongsTo(Raic::class, 'fk_id_raics');
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
}
