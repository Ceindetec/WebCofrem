<?php

namespace creditocofrem\Http\Controllers;

use creditocofrem\AdminisTarjetas;
use creditocofrem\Contratos_empr;
use Illuminate\Http\Request;
use creditocofrem\Establecimientos;
use Yajra\Datatables\Datatables;
use creditocofrem\Empresas;


class ContratosController extends Controller
{
    /**
     * trae la vista donde se listan todas las empresas de la red cofrem
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('empresas.contratos.listacontratos');
    }

    /**
     * metodo que carga la grid, en donde se listan todos los contartos de la red cofrem
     * @return retorna el arreglo de tal manera que el datatable lo entienda
     */
    public function gridContratos()
    {
        $contratos = Contratos_empr::all();
        foreach ($contratos as $contr) {
            $contr->getEmpresa;
            // }
            // dd($empresas);
        }
            return Datatables::of($contratos)
                ->addColumn('action', function ($contratos) {
                    $acciones = '<div class="btn-group">';
                    // $acciones = $acciones . '<a href="' . route("contrato.editar", ["id" => $empresas->id]) . '" data-modal class="btn btn-xs btn-custom" ><i class="ti-pencil-alt"></i> Editar</a>';
                    $acciones = $acciones . '</div>';
                    return $acciones;
                })
                ->make(true);
    }

    /**
     * retorna la vista del modal que permite crear nuevos contratos
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewCrearContrato()
    {
        $administracion = AdminisTarjetas::pluck('porcentaje', 'id');
        return view('empresas.contratos.modalcrearcontratos', compact(['administracion']));

    }

    public function crearContrato(Request $request)
    {
        dd('hjgjhg');

        \DB::beginTransaction();
        $result = [];
        try {
            $validator = \Validator::make($request->all(), [
                'n_contratos' => 'required|unique:contratos_emprs|max:11',
            ]);

            if ($validator->fails()) {
                return $validator->errors()->all();
            }

            $contrato = Empresas::where("nit", $request->empresa_id)->first();//revisar los parametros de formulario, como saber si el dato ingresado es uno ya creado
            //dd($contrato);
            if($contrato != null) {
                $contrato = new Contratos_empr($request->all());
                //var_dump($empresa); revisar que parametros necesita para la creacion de la empresa
                $contrato->n_contrato = strtoupper($contrato->n_contrato);
                $contrato->save();
                //si es correcta la transaccion:
                $result['estado'] = true;
                $result['mensaje'] = 'El contrato fue creado';//. $exception->getMessage()
                \DB::commit();
            }
        }catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible crear el contrato, la empresa no existe ' . $exception->getMessage();//. $exception->getMessage()
            \DB::rollBack();
        }
        return $result;

    }


}
