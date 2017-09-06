<?php

namespace creditocofrem;

use Illuminate\Database\Eloquent\Model;

class TarjetasRegalo extends Model
{
    //
    protected $fillable = [
        'numero_tarjeta','fecha_creacion','fecha_activacion','fecha_vence','monto_inicial','monto_restante',
    ];
}
