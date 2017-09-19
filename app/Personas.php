<?php

namespace creditocofrem;

use Illuminate\Database\Eloquent\Model;

class Personas extends Model
{
    //
    protected $fillable = [
        'identificacion', 'nombres', 'apellidos'
    ];
}
