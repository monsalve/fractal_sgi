<?php

namespace App;

use App\Rol;
use App\Cargo;
use App\Empresa;
use App\Persona;
use App\UsuarioCarpeta;
use App\UsuarioDocumento;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'usuario', 'nombre_completo', 'password', 'condicion', 'logueable', 'fk_id_cargos', 'idrol', 'empresas_id', 'personas_id'
    ];
    
    public $timestamps = false;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function cargo ()
    {
        return $this->belongsTo(Cargo::class, 'fk_id_cargos');
    }
    public function rol ()
    {
        return $this->belongsTo(Rol::class, 'idrol');
    }

    public function empresa ()
    {
        return $this->belongsTo(Empresa::class, 'empresas_id');
    }
    
    public function persona ()
    {
        return $this->belongsTo(Persona::class, 'personas_id');
    }

    public function usuariosCarpetas ()
    {
        return $this->hasMany(UsuarioCarpeta::class, 'fk_id_users');
    }
    public function usuariosDocumentos ()
    {
        return $this->hasManyThrough(UsuarioDocumento::class, UsuarioCarpeta::class, 'fk_id_users', 'fk_id_usuarios_carpetas');
    }

    // METODOS DB
    public static function paraSelector ($empresaId)
    {
        return self::select('id', 'nombre_completo')->where('empresas_id', '=', $empresaId)->get();
    }
}
