<?php

namespace App;

use App\CargoDocumento;
use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    protected $table = 'cargos';
    protected $fillable = [
        'cargo', 'descripcion', 'fk_id_empresas', 'fk_usuCrea_users', 'fk_usuActualiza_users'
    ];
    
    public function cargoDocumentos()
    {
        return $this->hasMany(CargoDocumento::class, 'fk_id_cargos');//->select('id', 'documento', 'fk_id_roles');
    }

    public function usuarios()
    {
        return $this->hasMany(User::class, 'fk_id_cargos');
    }
}