<?php

namespace creditocofrem;

use Illuminate\Database\Eloquent\Model;

class TarjetaServicios extends Model
{
    //
    protected $fillable = [
        'numero_tarjeta', 'servicio_codigo', 'estado'
    ];

    public function getServicio()
    {
        return $this->belongsTo('creditocofrem\Servicios','servicio_codigo','codigo');
    }
}
