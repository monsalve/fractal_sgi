<?php

namespace App;

use App\User;
use App\CategoriaOpcion;
use App\SegmentoArchivo;
use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    protected $table = 'solicitudes';
    protected $fillable = ['ruta', 'fk_id_segmentos_archivos', 'fk_recibe_users', 'fk_usuCrea_users', 'fk_usuActualiza_users'];

    public function segmentoArchivo()
    {
        return $this->belongsTo(SegmentoArchivo::class, 'fk_id_segmentos_archivos');
    }
    public function usuarioRecibe()
    {
        return $this->belongsTo(User::class, 'fk_recibe_users');
    }

    public function categoriasOpciones()
    {
        return $this->belongsToMany(CategoriaOpcion::class, 'solicitudes_opciones', 'fk_id_solicitudes', 'fk_id_categorias_opciones');
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
