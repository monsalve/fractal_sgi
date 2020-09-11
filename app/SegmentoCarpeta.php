<?php

namespace App;

use App\User;
use App\Empresa;
use App\Segmento;
use App\TipoCarpeta;
use App\SegmentoArchivo;
use Illuminate\Database\Eloquent\Model;

class SegmentoCarpeta extends Model
{
    protected $table = 'segmentos_carpetas';
    protected $fillable = ['carpeta', 'descripcion', 'fk_id_empresas', 'fk_id_segmentos', 'fk_id_tipos_carpetas', 'fk_usuCrea_users', 'fk_usuActualiza_users'];
    
    public function empresa ()
    {
        return $this->belongsTo(Empresa::class, 'fk_id_empresas');
    }

    public function segmento ()
    {
        return $this->belongsTo(Segmento::class, 'fk_id_segmentos');
    }

    public function tipoCarpeta ()
    {
        return $this->belongsTo(TipoCarpeta::class, 'fk_id_tipos_carpetas');
    }

    public function usuCrea ()
    {
        return $this->belongsTo(User::class, 'fk_usuCrea_users');
    }

    public function archivos ()
    {
        return $this->hasMany(SegmentoArchivo::class, 'fk_id_segmentos_carpetas');
    }
}