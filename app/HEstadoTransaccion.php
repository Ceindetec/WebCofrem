<?php

namespace creditocofrem;

use Illuminate\Database\Eloquent\Model;

class HEstadoTransaccion extends Model
{
    public static $ESTADO_ACTIVO = "A";
    public static $ESTADO_INACTIVO = "I";


    protected $table = 'h_estado_transacciones';
}
