<?php

namespace App;

use App\User;
use App\Empresa;
use App\UsuarioDocumento;
use Illuminate\Database\Eloquent\Model;

class UsuarioCarpeta extends Model
{
    protected $table = 'usuarios_carpetas';
    protected $fillable = [
        'carpeta', 'descripcion', 'fk_id_empresas', 'fk_id_users', 'fk_usuCrea_users', 'fk_usuActualiza_users'
    ];
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'fk_id_empresas');
    }
    public function usuario()
    {
        return $this->belongsTo(User::class, 'fk_id_users');
    }
    public function usuariosDocumentos ()
    {
        return $this->hasMany(UsuarioDocumento::class, 'fk_id_usuarios_carpetas');
    }
    // DE AUDITORÍA
    public function usuarioCrea()
    {
        return $this->belongsTo(User::class, 'fk_usuCrea_users');
    }
    public function usuarioActualiza()
    {
        return $this->belongsTo(User::class, 'fk_usuActualiza_users');
    }
}