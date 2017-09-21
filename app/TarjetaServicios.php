<?php

namespace creditocofrem;

use Illuminate\Database\Eloquent\Model;

class TarjetaServicios extends Model
{
    //
    public static $ESTADO_INACTIVO = "I";
    public static $ESTADO_ACTIVO = "A";

    protected $fillable = [
        'numero_tarjeta', 'servicio_codigo', 'estado'
    ];

    public function getServicio()
    {
        return $this->belongsTo('creditocofrem\Servicios','servicio_codigo','codigo');
    }
}
