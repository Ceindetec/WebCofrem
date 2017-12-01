<?php

namespace creditocofrem\Http\Controllers;

use creditocofrem\Establecimientos;
use creditocofrem\Municipios;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use creditocofrem\Empresas;
use creditocofrem\Departamentos;
use creditocofrem\TipoDocumento;
use SoapClient;


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
        foreach ($empresas as $emp) {
            $emp->getMunicipio->getDepartamento;
            $emp->getTipoDocumento;
        }
        // dd($empresas);
//
        return Datatables::of($empresas)
            ->addColumn('action', function ($empresas) {
                $acciones = '<div class="btn-group">';
                $acciones = $acciones . '<a href="' . route("empresa.editar", ["id" => $empresas->id]) . '" data-modal class="btn btn-xs btn-custom" ><i class="ti-pencil-alt"></i> Editar</a>';
                if($empresas->tipo == "A")
                {
                    $acciones = $acciones . '<a href="' . route("empresas.convenios", ["id" => $empresas->id]) . '" class="btn btn-xs btn-custom" ><i class="ti-layers-alt">Convenios</i> </a>'; //data-modal class="btn btn-xs btn-custom"
                }
                //$acciones = $acciones . '<a class="btn btn-xs btn-primary" href="' . route("listsucursales", [$establecimientos->id]) . '"><i class="ti-layers-alt"></i> Sucursales</a>';
                $acciones = $acciones . '</div>';
                return $acciones;
            })
            ->make(true);
    }

    /**
     * retorna la vista del modal que permite crear nuevas empresas
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewCrearEmpresa()
    {
        $departamentos = Departamentos::pluck('descripcion', 'codigo');
        $tipoDocu = TipoDocumento::pluck('equivalente', 'tip_codi');
        return view('empresas.modalcrearempresas', compact(['departamentos', 'tipoDocu']));
    }

    /**
     * metodo que permite agregar un nuevo establecimiento al sistema
     * @param Request $request trae la informacion del formulario, para agregar el establecimiento
     * @return mixed returna una respuesta positiva o negativa dependiendo de la transaccion
     */
    public function crearEmpresa(Request $request)//que datos son importantes que queden al crear la empresa
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
            //dd("empresa razon:  ".$empresa);
            if($empresa->tipo=="Afiliado")
                $empresa->tipo="A";
            else
                $empresa->tipo="T";
            $empresa->razon_social = strtoupper($empresa->razon_social);
            $empresa->save();
            $result['estado'] = true;
            $result['mensaje'] = 'La empresa ha sido creada satisfactoriamente';
        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible crear la empresa' . $exception->getMessage();
        }
        return $result;
    }

    //metodo para llamar a la vista para editar la info de la empresa
    public function viewEditarEmpresa(Request $request)
    {
        $empresa = Empresas::find($request->id);
        $depar = Municipios::find($empresa->municipio_codigo)->getDepartamento;
        $departamentos = Departamentos::pluck('descripcion', 'codigo');
        $tipoDocu = TipoDocumento::pluck('equivalente', 'tip_codi');
        return view('empresas.editarempresa', compact(['empresa', 'departamentos', 'depar','tipoDocu']));
    }

    /**
     * metodo que permite editar un establecimiento comercial
     * @param Request $request campos del establecimiento a editar
     * @return mixed
     */
    public function editarEmpresa(Request $request)
    {
        $result = [];
        try {
            $empresa = Empresas::find($request->getQueryString());
            if ($empresa->nit != $request->nit) {
                if ($empresa->email != $request->email) {
                    $validator = \Validator::make($request->all(), [
                        'nit' => 'required|unique:empresas|max:10',
                        'email' => 'required|unique:empresas',
                    ]);

                    if ($validator->fails()) {
                        return $validator->errors()->all();
                    }
                } else {
                    $validator = \Validator::make($request->all(), [
                        'nit' => 'required|unique:empresas|max:10',
                    ]);

                    if ($validator->fails()) {
                        return $validator->errors()->all();
                    }
                }
            } elseif ($empresa->email != $request->email) {
                $validator = \Validator::make($request->all(), [
                    'email' => 'required|unique:empresas',
                ]);
                if ($validator->fails()) {
                    return $validator->errors()->all();
                }
            }
            $empresa->update($request->all());
            if($request->tipo_name=="Afiliado")
                $empresa->tipo="A";
            else
                $empresa->tipo="T";
            $empresa->razon_social = strtoupper($empresa->razon_social);
            $empresa->save();
            $result['estado'] = true;
            $result['mensaje'] = 'La empresa ha sido actualizada satisfactorimante.';
            $result['data'] = $empresa;
        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible editar la empresa. ' . $exception->getMessage();
        }
        return $result;
    }
    /**
     * METODO para consultar datos de aportante con WS de SevenAs
     * @return mixed returna una objeto de tipo empresa, con los datos consultados (si existe) o vacia (si no la encontro)
     */
    public function consultarAportante(Request $request)
    {
        $empresa = new Empresas();
        $url = "http://192.168.0.188/WebServices4/WConsultasCajas.asmx?wsdl";
        try {
            $client = new SoapClient($url, array("trace" => 1, "exception" => 0));
            $result = $client->ConsultaAportante( [ "emp_codi" => 406, "tip_codi" => $request->tipo, "apo_coda" => $request->num ] );// 2  "41676254"
            if(isset($result->ConsultaAportanteResult->Aportante->TOAportante))
            {
                //dd("existe");
                $empresa->razon_social=$result->ConsultaAportanteResult->Aportante->TOAportante->Apo_razs;
                $empresa->representante_legal=$result->ConsultaAportanteResult->Aportante->TOAportante->Ter_noco;
                $municipio_name = $result->ConsultaAportanteResult->Aportante->TOAportante->Mun_nomb;
                $depto_name = $result->ConsultaAportanteResult->Aportante->TOAportante->Dep_nomb;
                $empresa->email=$result->ConsultaAportanteResult->Aportante->TOAportante->Dsu_mail;
                $empresa->telefono=$result->ConsultaAportanteResult->Aportante->TOAportante->Dsu_tele;
                $empresa->celular=$result->ConsultaAportanteResult->Aportante->TOAportante->Dsu_celu;
                $empresa->direccion=$result->ConsultaAportanteResult->Aportante->TOAportante->Dsu_dire;
                $empresa->tipo="A";
                //buscar departamento
                $depto = Departamentos::where("descripcion", $depto_name)->first();
                //dd($depto);
                if($depto != null) {
                    $muni = Municipios::where("descripcion", $municipio_name)->where("departamento_codigo", $depto->codigo)->first();
                    $empresa->municipio_codigo = (string) $muni->codigo;
                    $empresa->departamento_codigo = $depto->codigo;
                }
                else
                {
                   /* $muni = Municipios::where("descripcion", "VILLAVICENCIO")->where("departamento_codigo", $depto->codigo);
                    $empresa->municipio_codigo = $muni->codigo;
                    $empresa->departamento_codigo = $muni->departamento_codigo;*/
                }
                //buscar municipio
            }
            else
            {
                //dd("NO existe");
                $empresa->tipo="T";
            }

        } catch ( SoapFault $e ) {
            echo $e->getMessage();
        }
        return $empresa; //->razon_social
    }

}

