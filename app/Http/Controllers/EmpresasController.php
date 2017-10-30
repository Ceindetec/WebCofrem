<?php

namespace creditocofrem\Http\Controllers;

use creditocofrem\Establecimientos;
use creditocofrem\Municipios;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use creditocofrem\Empresas;
use creditocofrem\Departamentos;

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
        }
        // dd($empresas);
//
        return Datatables::of($empresas)
            ->addColumn('action', function ($empresas) {
                $acciones = '<div class="btn-group">';
                $acciones = $acciones . '<a href="' . route("empresa.editar", ["id" => $empresas->id]) . '" data-modal class="btn btn-xs btn-custom" ><i class="ti-pencil-alt"></i> Editar</a>';
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
        return view('empresas.modalcrearempresas', compact(['departamentos']));
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
        return view('empresas.editarempresa', compact(['empresa', 'departamentos', 'depar']));
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

}

