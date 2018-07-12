<?php

namespace creditocofrem;

use Illuminate\Database\Eloquent\Model;

class Transaccion extends Model
{
    public static $TIPO_ADMINISTRATIVO = "A";
    public static $TIPO_CONSUMO = "C";

    protected $table = 'transacciones';

    public function getSucursal(){
       return $this->belongsTo('creditocofrem\Sucursales','sucursal_id','id');
    }

    public function valorTransacion(){
        return $this->hasMany('creditocofrem\DetalleTransaccion','transaccion_id','id')->select([\DB::raw('SUM(valor) as total')]);
    }

}
