<?php

namespace creditocofrem\Http\Controllers;

use creditocofrem\Departamentos;
use creditocofrem\Establecimientos;
use creditocofrem\Municipios;
use creditocofrem\Sucursales;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class SucursalesController extends Controller
{
    /**
     * trae la vista de la lista de sucursales que pertenecen a un establecimiento
     * @param $id id del establecimiento
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index($id){
        $establecimiento = Establecimientos::find($id);
        return view('establecimientos.sucursales.listasucursales', compact('establecimiento'));
    }

    /**
     * metodo usuado para cargar la lista de surcursales en el datatable
     * @param Request $request trae la id del establecimiento
     * @return mixed
     */
    public function gridSuscursales(Request $request){
        $sucursales = Sucursales::where('establecimiento_id',$request->id)->get();
        foreach ($sucursales as $sucursale){
            $sucursale->getMunicipio;
        }
        return Datatables::of($sucursales)
            ->addColumn('action', function ($sucursales) {
                $acciones = '<div class="btn-group">';
                $acciones = $acciones.'<a href="' . route("sucursal.editar", ["id" => $sucursales->id]) . '" data-modal="modal-lg" class="btn btn-xs btn-custom" ><i class="ti-pencil-alt"></i> Edit</a>';
                $acciones = $acciones.'<a class="btn btn-xs btn-primary" href="'.route("listsucursales", [$sucursales->id]).'"><i class="ti-layers-alt"></i> Terminales</a>';
                $acciones = $acciones.'</div>';
                return $acciones;
            })
            ->make(true);
    }

    /**
     * trae la vista que se mostrara en el modal de crear sucursal
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewCrearSucursal(Request $request){
        $establecimiento_id = $request->id;
        $departamentos = Departamentos::pluck('descripcion','codigo');
        return view('establecimientos.sucursales.modalcrearsucursal', compact(['establecimiento_id','departamentos']));
    }

    /**
     * metodo que crea una sucursal
     * @param Request $request datos que trae del formulario para crear sucursal
     * @return array
     */
    public function crearSucursal(Request $request){
        $result=[];
        try{
            $sucursal = new Sucursales($request->all());
            $sucursal->nombre = strtoupper($sucursal->nombre);
            $sucursal->establecimiento_id = $request->getQueryString();
            $sucursal->direccion = trim($request->vp).' '.trim($request->nv).' #'.trim($request->n1).'-'.trim($request->n2).' '.trim($request->complemento);
            $sucursal->estado = 'A';
            $sucursal->password = $request->password;
            if($sucursal->save()){
                $result['estado']= true;
                $result['mensaje'] = 'sucursal creada satisfactoriamente';
            }else{
                $result['estado']= false;
                $result['mensaje'] = 'No fue posible crear la sucursal';
            }
        }catch (\Exception $exception){
            $result['estado']= false;
            $result['mensaje'] = 'No fue posible crear la sucursal '.$exception->getMessage();
        }
        return $result;
    }

    /**
     * metodo que trae los marcadores para mostrar en google maps
     * @param Request $request
     * @return \Illuminate\Support\Collection
     */
    public function getMarketSucursales(Request $request){
       $sucursales = Sucursales::where('establecimiento_id',$request->id)->select(['latitud','longitud','nombre'])->get();
       return $sucursales;
    }

    /**
     * metodo que trae la vista con la informacion de la sucursal a editar
     * @param Request $request id de la sucursal a editar
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewEditarSucursal(Request $request){
       $sucursal = Sucursales::find($request->id);
       $depar = Municipios::find($sucursal->municipio_codigo)->getDepartamento;
       $departamentos = Departamentos::pluck('descripcion','codigo');
       return view('establecimientos.sucursales.modaleditarsucursal', compact(['sucursal','departamentos','depar']));
    }

    public function editarSucursal(Request $request){
        $result=[];
        try{
            $sucursal = Sucursales::find($request->getQueryString());
            $sucursal->nombre = strtoupper($sucursal->nombre);
            $sucursal->direccion = trim($request->vp).' '.trim($request->nv).' #'.trim($request->n1).'-'.trim($request->n2).' '.trim($request->complemento);
            $sucursal->estado = $request->estado; /*tener presente que cuando se creen terminales si esta pasa a inactiva se debe inactivar sus terminales*/
            if($request->password !=""){
                $sucursal->password = $request->password;
            }
            if($sucursal->save()){
                $result['estado']= true;
                $result['mensaje'] = 'sucursal creada satisfactoriamente';
            }else{
                $result['estado']= false;
                $result['mensaje'] = 'No fue posible crear la sucursal';
            }
        }catch (\Exception $exception){
            $result['estado']= false;
            $result['mensaje'] = 'No fue posible crear la sucursal '.$exception->getMessage();
        }
        return $result;
    }
}
