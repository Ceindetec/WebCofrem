<?php

namespace creditocofrem;

use Illuminate\Database\Eloquent\Model;

class DetalleTransaccion extends Model
{
    public static $DESCRIPCION_ADMINISTRACION = "A";
    public static $DESCRIPCION_PLASTICO = "P";
    public static $DESCRIPCION_CONSUMO = "C";

    protected $table = "detalle_transacciones";
}
