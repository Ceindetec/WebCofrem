<?php

namespace creditocofrem;

use Illuminate\Database\Eloquent\Model;

class CuenContaTarjeta extends Model
{
    //

    public function getMunicipio(){
        return $this->belongsTo('creditocofrem\Municipios','municipio_codigo','codigo');
    }
}
