<?php

namespace creditocofrem\Http\Controllers;

use creditocofrem\Tarjetas;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class TarjetasController extends Controller
{
    //se listan las tarjetas registradas
    public function index(){
        return view('tarjetas.listatarjetas');
    }

    //carga la grid
    public function gridTarjetas(){
        $tarjetas = Tarjetas::all();
        return Datatables::of($tarjetas)
            ->addColumn('action', function ($tarjetas) {
              //  $acciones = '<a href="' . route("tarjeta.editar", ["id" => $tarjetas->id]) . '" data-modal="modal-lg" class="btn btn-xs btn-custom" ><i class="glyphicon glyphicon-edit"></i> Edit</a>';
                $acciones =  '<button class="btn btn-xs btn-danger" onclick="eliminar(' . $tarjetas->id . ')"><i class="glyphicon glyphicon-remove"></i> Eliminar</button>';
                return $acciones;
            })
            ->make(true);
    }
    //vista modal para crear tarjetas
    public function viewCrearTarjeta(){
        return view('tarjetas.modalcreartarjetas');
    }

    //metodo para agregar nueva tarjeta: insertar en bd
    public function crearTarjeta(Request $request){
        //dd($request->all());
        $tarjetas = new Tarjetas($request->all());
        //dd($tarjetas);
       // $tarjetas->create($request->all());
        $ultimos = substr($tarjetas->numero_tarjeta, -4);
        $tarjetas->password=bcrypt($ultimos);
        $tarjetas->save();
        $result['estado'] = true;
        $result['mensaje'] = 'La tarjeta ha sido creada satisfactoriamente';
        return $result;
    }
}
