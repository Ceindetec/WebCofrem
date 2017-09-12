<?php

namespace creditocofrem;

use Illuminate\Database\Eloquent\Model;

class Tarjetas extends Model
{
    //
    protected $fillable = [
        'numero_tarjeta', 'tarjeta_codigo', 'cambioclave', 'password',
    ];
}
