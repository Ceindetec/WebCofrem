<?php

namespace creditocofrem;

use Illuminate\Database\Eloquent\Model;

class TarjetasBono extends Model
{
    //
    protected $fillable = [
        'numero_tarjeta', 'tercero_id', 'fecha_creacion','fecha_inicio','fecha_vence','monto_inicial','monto_restante',
    ];
}
