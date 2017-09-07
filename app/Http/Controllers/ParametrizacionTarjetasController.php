<?php

namespace creditocofrem\Http\Controllers;

use creditocofrem\AdminisTarjetas;
use creditocofrem\TipoTarjetas;
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
        $tipotarjetas = TipoTarjetas::all()->pluck('descripcion','codigo');
        $valorTarjeta = ValorTarjeta::where('estado','A')->first();
        return view('tarjetas.parametrizacion.parametrizacion', compact(['tipotarjetas','valorTarjeta']));
    }

    /**
     * metodo que permite agregar un valor al plastico de la tarjeta
     * @param Request $request
     * @return array
     */
    public function tarjetaCrearParametroValor(Request $request){
        $result = [];
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
            $result['estado'] = true;
            $result['mensaje'] = 'Valor ingresado satisfactoriamente';
        }catch (\Exception $exception){
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

    public function gridAdministracionTarjetas(){
        $administraciones = AdminisTarjetas::where('estado','A')->get();
        foreach ($administraciones as $administracione){
            $administracione->getTipoTarjeta;
        }

        return Datatables::of($administraciones)->make(true);

    }
}
