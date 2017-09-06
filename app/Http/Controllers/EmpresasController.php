<?php

namespace creditocofrem\Http\Controllers;

use creditocofrem\Establecimientos;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class EmpresasController.php extends Controller
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
        return Datatables::of($empresas)
            ->addColumn('action', function ($empresas) {
                $acciones = '<div class="btn-group">';
                $acciones = $acciones . '<a href="' . route("empresa.editar", ["id" => $empresas->id]) . '" class="btn btn-xs btn-custom" ><i class="ti-pencil-alt"></i> Edit</a>';
                $acciones = $acciones . '<a class="btn btn-xs btn-primary" href="' . route("listsucursales", [$establecimientos->id]) . '"><i class="ti-layers-alt"></i> Sucursales</a>';
                $acciones = $acciones . '</div>';
                return $acciones;
            })
            ->make(true);
    }

    /**
     * retorna la vista del modal que permite crear nuevos establecimientos
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewCrearEstablecimiento()
    {
        return view('establecimientos.modalcrearestablecimientos');
    }

    /**
     * metodo que permite agregar un nuevo establecimiento al sistema
     * @param Request $request trae la informacion del formulario, para agregar el establecimiento
     * @return mixed returna una respuesta positiva o negativa dependiendo de la transaccion
     */
    public function crearEstablecimiento(Request $request)
    {
        $result = [];
        try {
            $validator = \Validator::make($request->all(), [
                'nit' => 'required|unique:establecimientos|max:11',
                'email' => 'required|unique:establecimientos',
            ]);

            if ($validator->fails()) {
                return $validator->errors()->all();
            }
            $establecimiento = new Establecimientos($request->all());
            $establecimiento->razon_social = strtoupper($establecimiento->razon_social);
            $establecimiento->estado = 'I';
            $establecimiento->save();
            $result['estado'] = true;
            $result['mensaje'] = 'El establecimiento ha sido creado satisfactoriamente';
        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible crear el establicimiento ' . $exception->getMessage();
        }
        return $result;
    }

    /**
     * trae la vista para editar establecimiento asi como agregar contractos y editarlos
     * @param Request $request trae la id del establecimiento a editar
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewEditarEstablecimiento($id)
    {
        $establecimiento = Establecimientos::find($id);
        if ($establecimiento == null) {
            return redirect()->back();
        }
        return view('establecimientos.editarestablecimiento', compact('establecimiento'));
    }

    /**
     * metodo que permite editar un establecimiento comercial
     * @param Request $request campos del establecimiento a editar
     * @return mixed
     */
    public function editarEstablecimiento(Request $request)
    {
        $result= [];
        try{
            $establecimiento = Establecimientos::find($request->getQueryString());
            if ($establecimiento->nit != $request->nit) {
                if ($establecimiento->email != $request->email) {
                    $validator = \Validator::make($request->all(), [
                        'nit' => 'required|unique:establecimientos|max:11',
                        'email' => 'required|unique:establecimientos',
                    ]);

                    if ($validator->fails()) {
                        return $validator->errors()->all();
                    }
                } else {
                    $validator = \Validator::make($request->all(), [
                        'nit' => 'required|unique:establecimientos|max:11',
                    ]);

                    if ($validator->fails()) {
                        return $validator->errors()->all();
                    }
                }
            } elseif ($establecimiento->email != $request->email) {
                $validator = \Validator::make($request->all(), [
                    'email' => 'required|unique:establecimientos',
                ]);
                if ($validator->fails()) {
                    return $validator->errors()->all();
                }
            }
            if ($request->estado == 'A') {
                $convenios = $establecimiento->convenios;
                if (count($convenios) == 0) {
                    $result['estado'] = false;
                    $result['mensaje'] = 'No es posible cambiar el estado de un establecimiento sin un convenio activo.';
                    $result['data'] = $establecimiento;
                    return $result;
                }
            }
            $establecimiento->update($request->all());
            $establecimiento->razon_social = strtoupper($establecimiento->razon_social);
            $establecimiento->save();
            $result['estado'] = true;
            $result['mensaje'] = 'El establecimiento actualizado satisfactorimante.';
            $result['data'] = $establecimiento;
        }catch (\Exception $exception){
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible editar el establecimiento. '.$exception->getMessage();
        }
        return $result;
    }
}

