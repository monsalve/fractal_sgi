<?php

namespace App;

use App\User;
use App\Empresa;
use App\CargoDocumento;
use App\UsuarioCarpeta;
use Illuminate\Database\Eloquent\Model;

class UsuarioDocumento extends Model
{
    protected $table = 'usuarios_documentos';
    protected $fillable = [
        'documento', 'ruta', 'fk_id_empresas', 'fk_id_cargos_documentos', 'fk_id_usuarios_carpetas', 'fk_usuCrea_users', 'fk_usuActualiza_users'
    ];
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'fk_id_empresas');
    }
    public function cargoDocumento()
    {
        return $this->belongsTo(CargoDocumento::class, 'fk_id_cargos_documentos');
    }
    public function usuarioCarpeta()
    {
        return $this->belongsTo(UsuarioCarpeta::class, 'fk_id_usuarios_carpetas');
    }
    // AUDITORÍA
    public function usuarioCrea()
    {
        return $this->belongsTo(User::class, 'fk_usuCrea_users');
    }
    public function usuarioActualiza()
    {
        return $this->belongsTo(User::class, 'fk_usuActualiza_users');
    }
}