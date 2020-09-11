<?php

namespace App;

use App\User;
use App\Cargo;
use App\Empresa;
use App\Solicitud;
use App\SegmentoCarpeta;
use App\CategoriaArchivo;
use Illuminate\Database\Eloquent\Model;

class SegmentoArchivo extends Model
{
    protected $table = 'segmentos_archivos';
    protected $fillable = [
        'archivo', 'codigo', 'versionamiento', 'ruta', 'gestionable', 'fk_id_segmentos_carpetas', 'fk_id_empresas', 'fk_usuCrea_users', 'fk_usuActualiza_users'
    ];
    
    public function carpeta()
    {
        return $this->belongsTo(SegmentoCarpeta::class, 'fk_id_segmentos_carpetas');
    }

    public function cargos()
    {
        return $this->belongsToMany(Cargo::class, 'cargos_archivos', 'fk_id_segmentos_archivos', 'fk_id_cargos');
    }

    public function categoriasArchivos()
    {
        return $this->hasMany(CategoriaArchivo::class, 'fk_id_segmentos_archivos');
    }
    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'fk_id_segmentos_archivos');
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
