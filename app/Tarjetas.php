<?php

namespace creditocofrem;

use Illuminate\Database\Eloquent\Model;

class Tarjetas extends Model
{
    //
    protected $fillable = [
        'numero_tarjeta', 'tipo', 'cambioclave', 'password',
    ];
}
