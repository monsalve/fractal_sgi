<?php

namespace App;

use App\SegmentoArchivo;
use Illuminate\Database\Eloquent\Model;

class CategoriaArchivo extends Model
{
    protected $table = 'categorias_archivos';
    protected $fillable = [
        'categoriaArchivo', 'fk_id_segmentos_archivos', 'fk_id_empresas', 'fk_usuCrea_users', 'fk_usuActualiza_users'
    ];

    public function segmentosArchivos()
    {
        return $this->belongsTo(SegmentoArchivo::class, 'fk_id_segmentos_archivos');
    }
    public function categoriasOpciones()
    {
        return $this->hasMany(CategoriaOpcion::class, 'fk_id_categorias_archivos');
    }
}
