<?php

namespace App;

use App\User;
use App\Empresa;
use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $table = 'documentos';
    protected $fillable = ['documento', 'ruta', 'fk_id_carpetas', 'fk_id_empresas', 'fk_usuCrea_users', 'fk'];

    public function carpeta()
    {
        return $this->belongsTo(Carpeta::class, 'fk_id_carpetas');
    }
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'fk_id_empresas');
    }
    public function usuCrea()
    {
        return $this->belongsTo(User::class, 'fk_usuCrea_users');
    }
}
