<?php

namespace creditocofrem;

use Illuminate\Database\Eloquent\Model;

class Transaccion extends Model
{
    public static $TIPO_ADMINISTRATIVO = "A";
    public static $TIPO_CONSUMO = "C";

    protected $table = 'transacciones';

}
