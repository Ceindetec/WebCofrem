<?php

namespace creditocofrem\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use creditocofrem\Htarjetas;
use creditocofrem\Tarjetas;
use creditocofrem\Servicios;
use creditocofrem\TarjetaServicios;
use Illuminate\Support\Facades\Auth;
use Facades\creditocofrem\Encript;
use Yajra\Datatables\Datatables;

class TarjetasBonoController extends Controller
{
    //Abre el formulario para la creacion individual de tarjetas bono
    public function viewCrearTarjetaIndividual()
    {
       // $servicios = Servicios::pluck('descripcion', 'codigo');
        return view('tarjetas.bono.individualmente');
    }
    public function crearTarjetaIndividual(Request $request)
    {
        $result = [];
        \DB::beginTransaction();
        try {
            $tarjetas = new Tarjetas($request->all());
            $result['estado'] = true;
            $result['mensaje'] = 'La tarjeta bono ha sido creada satisfactoriamente';
            //$paso = 1;
            //$result = $this->crearHtarjeta($tarjetas, 'C', $request->servicio_codigo);
            \DB::commit();
        }
        catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible crear la tarjeta bono' . $exception->getMessage();//. $exception->getMessage()
            \DB::rollBack();
        }
        return $result;
    }
}
