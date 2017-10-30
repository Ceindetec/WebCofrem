<?php

namespace creditocofrem\Http\Controllers;

use creditocofrem\AdminisTarjetas;
use creditocofrem\Contratos_empr;
use Illuminate\Http\Request;
use creditocofrem\Establecimientos;
use Yajra\Datatables\Datatables;
use creditocofrem\Empresas;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;


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
                $acciones = $acciones . '<a href="' . route("contrato.editar", ["id" => $contratos->id]) . '" data-modal class="btn btn-xs btn-custom" ><i class="ti-pencil-alt"></i> Editar</a>';
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

        \DB::beginTransaction();
        $result = [];
        try {

            $validator = \Validator::make($request->all(), [
                'n_contrato' => 'required|unique:contratos_emprs|max:11',
            ], [
                'n_contrato.unique' => 'El contrato ya existe'
            ]);

            if ($validator->fails()) {
                return $validator->errors()->all();
            }

            $empresa = Empresas::where("nit", $request->nit)->first();

            if ($empresa != null) {
                $ruta = "contratos_pdf/";
                $num_contrato = $request->n_contrato;
                $new_name = $ruta . $num_contrato . ".pdf";
                $fichero = $request->file('pdf');
                $contrato = new Contratos_empr($request->all());
                $contrato->fecha = Carbon::createFromFormat('d/m/Y', $request->fecha);
                $contrato->valor_contrato = str_replace('.','',$request->valor_contrato);
                $contrato->valor_impuesto = str_replace('.','',$request->valor_impuesto);

                //  $contrato->n_contrato = strtoupper($contrato->n_contrato);
                $contrato->empresa_id = $empresa->id;
                $contrato->cons_mensual = $request->consumo;

                if($fichero != NULL){
                    $nombre_file = $fichero->getClientOriginalName();
                    $extensiones = explode(".", $nombre_file);
                    $extension = end($extensiones);
                    if ($extension == "pdf") {
                        copy($_FILES['pdf']['tmp_name'], $new_name);
                        $contrato->pdf = $new_name;

                    } else {
                        $result['estado'] = false;
                        $result['mensaje'] = 'El archivo debe tener extensión pdf';//. $exception->getMessage()
                    }
                }

                $contrato->save();
                //si es correcta la transaccion:
                $result['estado'] = true;
                $result['mensaje'] = 'El contrato fue creado';//. $exception->getMessage()
                \DB::commit();

            } else {
                $result['estado'] = false;
                $result['mensaje'] = 'No fue posible crear el contrato, la empresa no existe ';//. $exception->getMessage()
            }
        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible crear el contrato, la empresa no existe ' . $exception->getMessage();//. $exception->getMessage()
            \DB::rollBack();
        }
        return $result;
    }


    //metodo para llamar a la vista para editar la info del contrato
    public function viewEditarContrato(Request $request)
    {
        $contrato = Contratos_empr::find($request->id);
        $administracion = AdminisTarjetas::pluck('porcentaje', 'id');
        $empresa = Empresas::where('id', $contrato->empresa_id)->first();
        //  $fecha=$contrato->fecha;
        //  $fecha->toDateString();

        return view('empresas.contratos.editarcontratos', compact(['contrato', 'administracion', 'empresa']));

    }

    /**
     * metodo que permite editar un contrato
     */
    public function editarContrato(Request $request, $id)
    {
        $result = [];
        try {

            //$contrato = Contratos_empr::where('n_contrato', $request->n_contrato)->first();
            $contrato = Contratos_empr::find($id);
            $empresa = Empresas::where("nit", $request->nit)->first();
            //  dd($id);

            $contrato->valor_contrato = $request->valor_contrato;
            $contrato->valor_impuesto = $request->valor_impuesto;
            $contrato->dias_consumo = $request->consumo;
            $contrato->fecha = Carbon::createFromFormat('d/m/Y', $request->fecha);
            $ruta = "contratos_pdf/";
            $num_contrato = $request->n_contrato;
            $new_name = $ruta . $num_contrato . ".pdf";
            $fichero = $request->file('pdf');
            // dd($fichero);
            if ($fichero != null) {
                $nombre_file = $fichero->getClientOriginalName();
                $extensiones = explode(".", $nombre_file);
                $extension = end($extensiones);

                if ($extension == "pdf") {

                    copy($_FILES['pdf']['tmp_name'], $new_name);
                    $contrato->update($request->all());
                    $contrato->fecha = Carbon::createFromFormat('d/m/Y', $request->fecha);

                    //  $contrato->n_contrato = strtoupper($contrato->n_contrato);
                    $contrato->empresa_id = $empresa->id;
                    $contrato->cons_mensual = $request->consumo;

                    $contrato->pdf = $new_name;
                    // dd($contrato);
                }
            }

            //  copy($_FILES['pdf']['tmp_name'], $new_name);

            //  $contrato->pdf = $request->pdf;

            $contrato->save();
            $result['estado'] = true;
            $result['mensaje'] = 'El contrato ha sido actualizado satisfactorimante.';
            $result['data'] = $contrato;
        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible editar el contrato. ' . $exception->getMessage();
        }
        return $result;
    }


}
