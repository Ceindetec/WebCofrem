<?php

namespace creditocofrem;

use Illuminate\Database\Eloquent\Model;

class Establecimientos extends Model
{
    protected $fillable = [
        'nit', 'razon_social', 'email', 'telefono','celular',
    ];
}
