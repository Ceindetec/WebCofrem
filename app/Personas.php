<?php

namespace creditocofrem;

use Illuminate\Database\Eloquent\Model;

class Personas extends Model
{
    //
    public static $TIPO_PERSONA_AFILIADO = "A";
    protected $fillable = [
        'identificacion', 'nombres', 'apellidos',
    ];
}
