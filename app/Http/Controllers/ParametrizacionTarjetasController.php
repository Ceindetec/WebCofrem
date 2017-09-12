<?php

namespace creditocofrem\Http\Controllers;

use creditocofrem\AdminisTarjetas;
use creditocofrem\Servicios;
use creditocofrem\ValorTarjeta;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class ParametrizacionTarjetasController extends Controller
{
    //
    /**
     * metodo que trae la vista para parametrizar las tarjetas
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewParametrosTarjetas(){
        $tipotarjetas = Servicios::all()->pluck('descripcion','codigo');
        $valorTarjeta = ValorTarjeta::where('estado','A')->first();
        if($valorTarjeta != null){
            if(!strrpos($valorTarjeta->valor,'.')){
                $valorTarjeta->valor= $valorTarjeta->valor.',00';
            }
        }
        return view('tarjetas.parametrizacion.parametrizacion', compact(['tipotarjetas','valorTarjeta']));
    }

    /**
     * metodo que permite agregar un valor al plastico de la tarjeta
     * @param Request $request
     * @return array
     */
    public function tarjetaCrearParametroValor(Request $request){
        $result = [];
        \DB::beginTransaction();
        try{
            $existe = ValorTarjeta::all();
            $newvalor = new ValorTarjeta();
            if(count($existe)>0){
                $oldValor = ValorTarjeta::where('estado','A')->first();
                $oldValor->estado = 'I';
                $oldValor->save();
                $newvalor->valor = str_replace(",",".",str_replace(".","",$request->valor));
                $newvalor->estado = 'A';
            }else{
                $newvalor->valor = str_replace(",",".",str_replace(".","",$request->valor));
                $newvalor->estado = 'A';
            }
            $newvalor->save();
            \DB::commit();
            $result['estado'] = true;
            $result['mensaje'] = 'Valor ingresado satisfactoriamente';
        }catch (\Exception $exception){
            \DB::rollback();
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible ingresar el valor '.$exception->getMessage();
        }
        return $result;
    }

    /**
     * permite agregar valores de administracion para las tarjetas
     * @param Request $request
     * @return array
     */
    public function tarjetaCrearParametroAdministracion(Request $request){
        $result = [];
        try{
            $existe = AdminisTarjetas::all();
            if(count($existe)>0){
                $oldAdministraciones = AdminisTarjetas::where('estado','A')->where('tarjeta_codigo',$request->tarjeta_codigo)->get();
                foreach ($oldAdministraciones as $oldAdministracione){
                    if($oldAdministracione->porcentaje == $request->porcentaje){
                        $result['estado'] = false;
                        $result['mensaje'] = 'Ya exite este porcentaje de administracion para este tipo de tarjeta';
                        return $result;
                    }
                }
            }
           $parametro = new AdminisTarjetas($request->all());
           $parametro->save();
            $result['estado'] = true;
            $result['mensaje'] = 'Administracion agregada satisfactoriamente';
        }catch (\Exception $exception){
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible agregar la administracion '.$exception->getMessage();
        }
        return $result;
    }

    /**
     * metodo encargado de llenar la grid con las configuraciones de administracion existentes para una tarjeta
     * @return mixed
     */
    public function gridAdministracionTarjetas(){
        $administraciones = AdminisTarjetas::where('estado','A')->get();
        foreach ($administraciones as $administracione){
            $administracione->getTipoTarjeta;
        }

        return Datatables::of($administraciones)
            ->addColumn('action', function ($rangos) {
                    $acciones = '<div class="btn-group">';
                    $acciones = $acciones . '<button class="btn btn-xs btn-danger" onclick="eliminarAdministracion('.$rangos->id.')" ><i class="fa fa-trash"></i> Eliminar</button>';
                    $acciones = $acciones . '</div>';
                    return $acciones;
            })->make(true);

    }

    /**
     * metodo que permite eliminar(cambiar de estado) una parametricacion de porcentaje de administracion
     * @param Request $request
     */
    public function tarjetaEliminarParametroAdministracion(Request $request){
        $result = [];
        try{
            $administracion = AdminisTarjetas::find($request->id);
            $administracion->estado = 'I';
            $administracion->save();
            $result['estado'] = true;
            $result['mensaje'] = 'Eliminado parametro de administracion satisfactoriamente';
        }catch (\Exception $exception){
            $result['estado'] = false;
            $result['mensaje'] = 'Eliminado parametro de administracion satisfactoriamente';
        }
        return $result;
    }
}
