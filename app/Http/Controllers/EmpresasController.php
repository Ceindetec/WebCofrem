<?php

namespace creditocofrem\Http\Controllers;

use creditocofrem\Establecimientos;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use creditocofrem\Empresas;

class EmpresasController extends Controller
{
    /**
     * trae la vista donde se listan todas las empresas de la red cofrem
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('empresas.listaempresas');
    }

    /**
     * metodo que carga la grid, en donde se listan todas las empresas de la red cofrem
     * @return retorna el arreglo de tal manera que el datatable lo entienda
     */
    public function gridEmpresas()
    {
        $empresas = Empresas::all();
        foreach ($empresas as $empresa){
            $empresa->municipio;
        }
        dd($empresas);
        return Datatables::of($empresas)
            ->addColumn('action', function ($empresas) {
                $acciones = '<div class="btn-group">';
                $acciones = $acciones . '<a href="' . route("empresa.editar", ["id" => $empresas->id]) . '" class="btn btn-xs btn-custom" ><i class="ti-pencil-alt"></i> Edit</a>';
                //$acciones = $acciones . '<a class="btn btn-xs btn-primary" href="' . route("listsucursales", [$establecimientos->id]) . '"><i class="ti-layers-alt"></i> Sucursales</a>';
                $acciones = $acciones . '</div>';
                return $acciones;
            })
            ->make(true);
    }

    /**
     * retorna la vista del modal que permite crear nuevos establecimientos
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewCrearEmpresa()
    {
        return view('empresas.modalcrearempresas');
    }

    /**
     * metodo que permite agregar un nuevo establecimiento al sistema
     * @param Request $request trae la informacion del formulario, para agregar el establecimiento
     * @return mixed returna una respuesta positiva o negativa dependiendo de la transaccion
     */
    public function crearEmpresa(Request $request)
    {
        $result = [];
        try {
            $validator = \Validator::make($request->all(), [
                'nit' => 'required|unique:empresas|max:11',
                'email' => 'required|unique:empresas',
            ]);

            if ($validator->fails()) {
                return $validator->errors()->all();
            }
            $empresa = new Empresas($request->all());
            $empresa->razon_social = strtoupper($empresa->razon_social);
            $empresa->estado = 'I';
            $empresa->save();
            $result['estado'] = true;
            $result['mensaje'] = 'El establecimiento ha sido creado satisfactoriamente';
        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible crear el establicimiento ' . $exception->getMessage();
        }
        return $result;
    }

}

