<?php

namespace creditocofrem\Http\Controllers;

use creditocofrem\Establecimientos;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class EstablecimientosController extends Controller
{
    /**
     * trae la vista donde se listan todos los establecimientos de la red cofrem
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(){
        return view('establecimientos.listaestablecimientos');
    }

    /**
     * metodo que carga la grid, en donde se listan todos los establecimientos de la red cofrem
     * @return retorna el arreglo de tal manera que el datatable lo entienda
     */
    public function gridEstablecimientos(){
        $establecimientos = Establecimientos::all();
        return Datatables::of($establecimientos)
            ->addColumn('action', function ($establecimientos) {
                $acciones = '<a href="' . route("establecimiento.editar", ["id" => $establecimientos->id]) . '" data-modal="modal-lg" class="btn btn-xs btn-custom" ><i class="glyphicon glyphicon-edit"></i> Edit</a>';
                $acciones = $acciones . ' ' . '<button class="btn btn-xs btn-danger" onclick="eliminar(' . $establecimientos->id . ')"><i class="glyphicon glyphicon-remove"></i> Eliminar</button>';
                return $acciones;
            })
            ->make(true);
    }

    /**
     * retorna la vista del modal que permite crear nuevos establecimientos
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewCrearEstablecimiento(){
        return view('establecimientos.modalcrearestablecimientos');
    }

    /**
     * metodo que permite agregar un nuevo establecimiento al sistema
     * @param Request $request trae la informacion del formulario, para agregar el establecimiento
     * @return mixed returna una respuesta positiva o negativa dependiendo de la transaccion
     */
    public function crearEstablecimiento(Request $request){
        $validator = \Validator::make($request->all(), [
            'nit' => 'required|unique:establecimientos|max:11',
            'email' => 'required|unique:establecimientos',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }

        $establecimiento = new Establecimientos();
        $establecimiento->create($request->all());
        $result['estado'] = true;
        $result['mensaje'] = 'El establecimiento ha sido creado satisfactoriamente';
        return $result;
    }

    /**
     * trae la vista para el modal que tendra el formulario de editar establecimiento
     * @param Request $request trae la id del establecimiento a editar
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewEditarEstablecimiento(Request $request){
        $establecimiento = Establecimientos::find($request->id);
        return view('establecimientos.modaleditarestablecimientos', compact('establecimiento'));
    }

    public function  editarEstablecimiento(Request $request){
        $establecimiento = Establecimientos::find($request->getQueryString());
        if($establecimiento->nit != $request->nit){
            if($establecimiento->email != $request->email){
                $validator = \Validator::make($request->all(), [
                    'nit' => 'required|unique:establecimientos|max:11',
                    'email' => 'required|unique:establecimientos',
                ]);

                if ($validator->fails()) {
                    return $validator->errors()->all();
                }
            }else{
                $validator = \Validator::make($request->all(), [
                    'nit' => 'required|unique:establecimientos|max:11',
                ]);

                if ($validator->fails()) {
                    return $validator->errors()->all();
                }
            }
        }elseif($establecimiento->email != $request->email){
            $validator = \Validator::make($request->all(), [
                'email' => 'required|unique:establecimientos',
            ]);
            if ($validator->fails()) {
                return $validator->errors()->all();
            }
        }

        $establecimiento->update($request->all());
        $result['estado'] = true;
        $result['mensaje'] = 'El establecimiento actualizado satisfactorimante.';
        return $result;
    }
}
