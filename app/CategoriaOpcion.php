<?php

namespace App;

use App\Solicitud;
use Illuminate\Database\Eloquent\Model;

class CategoriaOpcion extends Model
{
    protected $table = 'categorias_opciones';
    protected $fillable = [
        'categoriaOpcion', 'fk_id_categorias_archivos', 'fk_usuCrea_users', 'fk_usuActualiza_users'
    ];

    public function categoriaArchivo()
    {
        return $this->belongsTo(categoriaArchivo::class, 'fk_id_categorias_archivos');
    }

    public function solicitudes()
    {
        return $this->belongsToMany(Solicitud::class, 'solicitudes_opciones', 'fk_id_categorias_opciones', 'fk_id_solicitudes');
    }

    public function usuCrea()
    {
        return $this->belongsTo(User::class, 'fk_usuCrea_users');
    }
    public function usuActualiza()
    {
        return $this->belongsTo(User::class, 'fk_usuActualiza_users');
    }
}