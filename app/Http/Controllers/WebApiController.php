<?php

namespace creditocofrem\Http\Controllers;

use Illuminate\Http\Request;

class WebApiController extends Controller
{
    //

    public function comunicacion(Request $request){
        if($request->codigo == '02'){
            $result['estado'] = true;
            $result['mensaje'] = 'Comunicacion exitosa';
        }else{
            $result['estado'] = false;
            $result['mensaje'] = 'Error de comunicacion';
        }
        return $result;
    }
}
