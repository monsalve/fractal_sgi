<?php

namespace App;

use App\UsuarioDocumento;
use Illuminate\Database\Eloquent\Model;

class CargoDocumento extends Model
{
    protected $table = 'cargos_documentos';
    protected $fillable = ['id','documento', 'fk_id_empresas', 'fk_id_cargos', 'fk_usuCrea_users', 'fk_usuActualiza_users'];

    public function usuariosDocumentos()
    {
        return $this->hasMany(UsuarioDocumento::class, 'fk_id_cargos_documentos');
    }
}
