<?php

namespace creditocofrem;

use Illuminate\Database\Eloquent\Model;

class Tarjetas extends Model
{
    //
    protected $fillable = [
        'numero_tarjeta', 'tarjeta_codigo', 'cambioclave', 'password','persona_id','estado'
    ];

    public function getTarjetaServicios()
    {
        return $this->hasMany('creditocofrem\TarjetaServicios','numero_tarjeta','numero_tarjeta');
    }
}
