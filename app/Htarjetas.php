<?php

namespace creditocofrem;

use Illuminate\Database\Eloquent\Model;

class Htarjetas extends Model
{
    //
    protected $fillable = [
        'motivo','estado','fecha','user_id','hora','tarjetas_id',
    ];
}
