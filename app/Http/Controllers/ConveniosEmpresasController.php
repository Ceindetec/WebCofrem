<?php

namespace creditocofrem\Http\Controllers;

use creditocofrem\ConveniosEmp;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use creditocofrem\Empresas;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ConveniosEmpresasController extends Controller
{
    //
    public function index($id)
    {
        //$empresa_id = $id;
        $empresa = Empresas::find($id);
        return view('empresas.convenios.listaconvenios', compact('empresa'));//, compact('empresa')
    }
    /**
     * metodo que carga la grid, en donde se listan todas las empresas de la red cofrem
     * @return retorna el arreglo de tal manera que el datatable lo entienda
     */
    public function gridConvenios(Request $request)
    {
        $conveniosemp = ConveniosEmp::where('empresa_id',$request->id)->get();//
        return Datatables::of($conveniosemp)
            ->addColumn('action', function ($conveniosemp) {
                $acciones = '<div class="btn-group">';
                $acciones = $acciones . '<a href="' . route("empresas.convenio.editar", ["id" => $conveniosemp->id]) . '" data-modal class="btn btn-xs btn-custom" ><i class="ti-pencil-alt"></i> Editar</a>';
                $acciones = $acciones . '</div>';
                return $acciones;
            })
            ->make(true);
    }
    /**
     * retorna la vista del modal que permite crear un nuevo convenio de empresa
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewCrearConvenio(Request $request)
    {
        $empresa = Empresas::find($request->id);
        return view('empresas.convenios.modalcrearconvenio', compact(['empresa']));
    }
    /**
     * metodo que permite agregar un nuevo convenio a una empresa
     * @param Request $request trae la informacion del formulario, para agregar el establecimiento
     * @return mixed returna una respuesta positiva o negativa dependiendo de la transaccion
     */
    public function crearConvenio(Request $request)
    {
        $result = [];
        DB::beginTransaction();
        try {
            $empresa = Empresas::find($request->id);
            $convenioemp = new ConveniosEmp($request->all());
            //$next = ConveniosEmp::max('numero_convenio');//select('numero_convenio')->orderBy('numero_convenio','DESC')->first()
            $next = ConveniosEmp::selectraw("max(to_number(regexp_substr(numero_convenio, '\d+'))) numero_convenio")->get();
            $next = $next[0]->numero_convenio; //(int)
            $next++;
            $validator = \Validator::make(['numero_convenio'=>$next], [
                'numero_convenio' => 'required|unique:convenios_emps',
            ]);

            if ($validator->fails()) {
                DB::rollBack();
                $result['estado'] = false;
                $result['mensaje'] = 'El número de convenio ya esta en uso';
                return $result;
            }
            //dd($next);
            $convenioemp->numero_convenio=$next;
            //guardar archivo
            $ruta = "documentos/conveniosempresas/";
            $new_name = $ruta . $empresa->id . "_" . $convenioemp->numero_convenio . ".pdf";
            $fichero = $request->file('archivo');
            //dd($fichero);
            $nombre_file = $fichero->getClientOriginalName();
            $extensiones = explode(".", $nombre_file);
            $extension = end($extensiones);
            if ($extension == "pdf") {
                if(copy($_FILES['archivo']['tmp_name'], $new_name)) {
                    //inactivar los demas convenios que existan
                    ConveniosEmp::where('empresa_id', $request->id)->where('estado', '<>', ConveniosEmp::$ESTADO_CONVENIO_INACTIVO)->update(['estado' => ConveniosEmp::$ESTADO_CONVENIO_INACTIVO]);

                    $convenioemp->pdf=$new_name;
                    $convenioemp->empresa_id=$request->id;
                    $convenioemp->estado=ConveniosEmp::$ESTADO_CONVENIO_INACTIVO;

                    if ($convenioemp->fecha_inicio <= Carbon::now()->format('d/m/Y')) {
                        $convenioemp->estado = ConveniosEmp::$ESTADO_CONVENIO_ACTIVO;
                    } else {
                        $convenioemp->estado = ConveniosEmp::$ESTADO_CONVENIO_PENDIENTE;
                    }
                    $convenioemp->fecha_inicio = Carbon::createFromFormat('d/m/Y', $convenioemp->fecha_inicio);
                    $convenioemp->fecha_fin = Carbon::createFromFormat('d/m/Y', $convenioemp->fecha_fin);
                    $convenioemp->save();
                    $result['estado'] = true;
                    $result['mensaje'] = 'El convenio de empresa ha sido creado satisfactoriamente con el número '.$next;
                    DB::commit();
                }
                else{
                    $result['estado'] = false;
                    $result['mensaje'] = 'Ocurrio un error al intentar subir el archivo';
                }
            }
            else
            {
                $result['estado'] = false;
                $result['mensaje'] = 'El archivo no es de tipo PDF';
            }

        } catch (\Exception $exception) {
            DB::rollBack();
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible crear el convenio de empresa ' . $exception->getMessage();
        }
        return $result;
    }
    //metodo para llamar a la vista para editar la info del convenio de la empresa
    public function viewEditarConvenio(Request $request)
    {
        $convenio_emp = ConveniosEmp::find($request->id);
        $empresa = Empresas::where('id',$convenio_emp->empresa_id)->first();
        $fecha1= Carbon::parse($convenio_emp->fecha_inicio);
        $convenio_emp->fecha_inicio = $fecha1->format('d/m/Y');
        $fecha2= Carbon::parse($convenio_emp->fecha_fin);
        $convenio_emp->fecha_fin = $fecha2->format('d/m/Y');
        return view('empresas.convenios.modaleditarconvenio', compact(['empresa', 'convenio_emp']));
    }
    /**
     * metodo que permite editar un convenio de empresa: editar el tipo, la fecha de terminacion y si lo desea el archivo PDF
     * @param Request $request campos del convenio a actualizar
     * @return mixed
     */
    public function editarConvenio(Request $request)
    {
        $result = [];
        DB::beginTransaction();
        try {
            $convenioemp = ConveniosEmp::find($request->id);
            $empresa = Empresas::where('id',$convenioemp->empresa_id)->first();
            $next = $convenioemp->numero_convenio;

            $fichero = $request->file('archivo');
            if($fichero != null) {
                $ruta = "documentos/conveniosempresas/";
                $new_name = $ruta . $empresa->id . "_" . $convenioemp->numero_convenio . ".pdf";
                $nombre_file = $fichero->getClientOriginalName();
                $extensiones = explode(".", $nombre_file);
                $extension = end($extensiones);
                if ($extension == "pdf") {
                    if (copy($_FILES['archivo']['tmp_name'], $new_name)) {

                        $convenioemp->pdf = $new_name;
                        $convenioemp->fecha_fin = Carbon::createFromFormat('d/m/Y', $request->fecha_fin);
                        $convenioemp->tipo = $request->tipo;
                        $convenioemp->save();
                        $result['estado'] = true;
                        $result['mensaje'] = 'El convenio de empresa ha sido actualizado satisfactoriamente';
                        DB::commit();
                    } else {
                        $result['estado'] = false;
                        $result['mensaje'] = 'Ocurrio un error al intentar subir el archivo';
                        DB::rollBack();
                    }
                } else {
                    $result['estado'] = false;
                    $result['mensaje'] = 'El archivo no es de tipo PDF';
                    DB::rollBack();
                }
            }
            else{
                $convenioemp->fecha_fin = Carbon::createFromFormat('d/m/Y', $request->fecha_fin);
                $convenioemp->tipo = $request->tipo;
                $convenioemp->save();
                $result['estado'] = true;
                $result['mensaje'] = 'El convenio de empresa ha sido actualizado satisfactoriamente';
                DB::commit();
            }

        } catch (\Exception $exception) {
            DB::rollBack();
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible actualizar el convenio de empresa ' . $exception->getMessage();
        }
        return $result;
    }
}
