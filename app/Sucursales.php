<?php

namespace creditocofrem;

use Illuminate\Database\Eloquent\Model;

class Sucursales extends Model
{
    protected $fillable = [
        'nombre', 'latitud', 'longitud','municipio_codigo'
    ];

    public function getMunicipio()
    {
        return $this->belongsTo('creditocofrem\Municipios', 'municipio_codigo','codigo');
    }
}
