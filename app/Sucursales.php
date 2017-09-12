<?php

namespace creditocofrem;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Sucursales extends Model implements AuditableContract
{
    use Auditable;

    protected $fillable = [
        'nombre', 'latitud', 'longitud','municipio_codigo','email','telefono'
    ];

    public function getMunicipio()
    {
        return $this->belongsTo('creditocofrem\Municipios', 'municipio_codigo','codigo');
    }

    public function getEstablecimiento()
    {
        return $this->belongsTo('creditocofrem\Establecimientos', 'establecimiento_id','id');
    }
}
