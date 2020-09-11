<?php

namespace App;

use App\User;
use App\Modulo;
use App\RolDocumento;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'roles';
    protected $fillable = ['nombre', 'descripcion', 'empresa_id', 'usu_crea', 'fk_usuActualiza_users'];

    public function usuarios()
    {
        return $this->hasMany(User::class, 'idrol');
    }

    public function modulos()
    {
        return $this->belongsToMany(Modulo::class, 'roles_permisos', 'id_rol', 'id_modulo');
    }
}
