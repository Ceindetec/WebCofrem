<?php

namespace creditocofrem;

use Illuminate\Database\Eloquent\Model;

class Duplicado extends Model
{
    //
    protected $fillable = [
        'oldtarjeta', 'newtarjeta',
    ];
}
