<?php

namespace App;

use App\User;
use App\Empresa;
use App\RaicFuente;
use App\ConfProyecto;
use App\CausaNovedadItem;
use App\RaicContingencia;
use App\RaicTipoDesviacion;
use Illuminate\Database\Eloquent\Model;

class Raic extends Model
{
    protected $table = 'raics';
    protected $fillable = [
        'rpDocumento'
        , 'rpNombresCompletos'
        , 'rpCargo'
        , 'rpAreaTrabajo'
        , 'jiDocumento'
        , 'jiNombresCompletos'
        , 'jiCargo'
        , 'supDocumento'
        , 'supNombresCompletos'
        , 'fechaReporte'
        , 'areaTrabajo'
        , 'fechaEvento'
        , 'sitioEspecifico'
        , 'prioridad'
        , 'descripcionCorta'
        , 'descripcionEvento'
        , 'tipoEvento'
        , 'haySeguimiento'
        , 'condicion'
        , 'estado'
        , 'fk_id_empresas'
        , 'fk_id_proyectos'
        , 'fk_id_raics_fuentes'
        , 'fk_id_raics_tipos_desviaciones'
        , 'fk_piJefInmed_users'
        , 'fk_piRepPor_users'
        , 'fk_piSuperv_users'
        , 'fk_usuCrea_users'
        , 'fk_usuActualiza_users'
    ];
    public function empresa ()
    {
        return $this->belongsTo(Empresa::class, 'fk_id_empresas');
    }
    public function proyecto ()
    {
        return $this->belongsTo(ConfProyecto::class, 'fk_id_proyectos');
    }
    public function raicFuente ()
    {
        return $this->belongsTo(RaicFuente::class, 'fk_id_raics_fuentes');
    }
    public function raicTipoDesviacion ()
    {
        return $this->belongsTo(RaicTipoDesviacion::class, 'fk_id_raics_tipos_desviaciones');
    }
    public function piJefInmed ()
    {
        return $this->belongsTo(User::class, 'fk_piJefInmed_users');
    }
    public function piRepPor ()
    {
        return $this->belongsTo(User::class, 'fk_piRepPor_users');
    }
    public function piSuperv ()
    {
        return $this->belongsTo(User::class, 'fk_piSuperv_users');
    }

    public function usuCrea ()
    {
        return $this->belongsTo(User::class, 'fk_usuCrea_users');
    }
    public function usuActualiza ()
    {
        return $this->belongsTo(User::class, 'fk_usuActualiza_users');
    }

    public function causasNovedadesItems ()
    {
        return $this->belongsToMany(CausaNovedadItem::class, 'causas_items_raics', 'fk_id_raics', 'fk_id_causas_items');
    }

    public function contingencias ()
    {
        return $this->hasMany(RaicContingencia::class, 'fk_id_raics');
    }
}
