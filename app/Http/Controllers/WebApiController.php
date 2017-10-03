<?php

namespace creditocofrem\Http\Controllers;

use creditocofrem\Terminales;
use Facades\creditocofrem\Encript;
use Illuminate\Http\Request;

class WebApiController extends Controller
{
    //

    public function comunicacion(Request $request)
    {

        if ($request->codigo == '02') {
            $result['estado'] = true;
            $result['mensaje'] = 'Comunicacion exitosa';
        } else {
            $result['estado'] = false;
            $result['mensaje'] = 'Error de comunicacion';
        }

        $resultFinal['resultado'] = $result;

        return ($resultFinal);
    }



    public function validadTerminal(Request $request){
        $result = [];
        try{
            $terminal = Terminales::where('codigo',$request->codigo)->first();
            if(count($terminal)>0){
                $sucursal = $terminal->getSucursal;
                $establecimiento = $sucursal->getEstablecimiento;
                $data['nit'] = $establecimiento->nit;
                $data['razon_social'] = $establecimiento->razon_social;
                $data['sucursal_id'] = $sucursal->id;
                $data['nombre_sucursal'] = $sucursal->nombre;
                $data['ciudad'] = $sucursal->getMunicipio->descripcion;
                $data['direccion'] = $sucursal->direccion;
                $data['estado_sucursal'] = $sucursal->estado;
                $data['estado_terminal'] = $terminal->estado;
                $data['codigo_terminal'] = $terminal->codigo;
                $terminal->imei = $request->imei;
                $terminal->uid = $request->uid;
                $terminal->mac = $request->mac;
                $request->save();
                $result['estado'] = TRUE;
                $result['mensaje'] = 'Validacion exitosa';
                $result['data'] = $data;
            }else{
                $result['estado'] = FALSE;
                $result['mensaje'] = 'verifique el codigo de la terminal';
            }
        }catch (\Exception $exception){
            $result['estado'] = FALSE;
            $result['mensaje'] = 'Error de operacion';
        }
        return ['resultado'=>$result];
    }
}
