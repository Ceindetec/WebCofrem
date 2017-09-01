<?php

namespace creditocofrem\Http\Controllers;

use creditocofrem\Sucursales;
use creditocofrem\Terminales;
use Facades\creditocofrem\Encript;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class TerminalesController extends Controller
{
    /**
     * metodo que me trae la vista donde se me listan las terminales de una sucursal
     * @param $id id de la sucursal
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index($id)
    {
        $sucursal = Sucursales::find($id);
        if ($sucursal == null) {
            return redirect()->back();
        }
        return view('establecimientos.terminales.listaterminales', compact('sucursal'));
    }

    /**
     * metodo que me carga el datatable que me lista las terminales de una sucursal
     * @param Request $request trae la id de la sucursal
     * @return mixed
     */
    public function gridTerminales(Request $request)
    {
        $terminales = Terminales::where('sucursal_id', $request->id)->get();

        return Datatables::of($terminales)
            ->addColumn('action', function ($terminales) {
                $acciones = '<div class="btn-group">';
                $acciones = $acciones . '<a href="' . route("terminal.editar", ["id" => $terminales->id]) . '" data-modal="modal-lg" class="btn btn-xs btn-custom" ><i class="ti-pencil-alt"></i> Edit</a>';
                if ($terminales->estado == "A")
                    $acciones = $acciones . '<bottom class="btn btn-xs btn-danger" onclick="cambiarEstado(' . $terminales->id . ')"><i class="mdi mdi-close-circle"></i> Inactivar</bottom>';
                else
                    $acciones = $acciones . '<bottom class="btn btn-xs btn-primary" onclick="cambiarEstado(' . $terminales->id . ')"><i class="mdi mdi-checkbox-marked-circle"></i> Activar</bottom>';
                $acciones = $acciones . '</div>';
                return $acciones;
            })
            ->make(true);
    }

    /**
     * metodo que me muestra el modal para agregar una nueva terminal
     * @param Request $request trae la id de la sucursal para saber a cual agregar una nueva terminal
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewCrearTerminal(Request $request)
    {
        $sucursal_id = $request->id;
        return view('establecimientos.terminales.modalcrearterminal', compact('sucursal_id'));
    }

    /**
     * metodo que crea una nueva terminal en una sucursal
     * @param Request $request datos de la terminal a crear
     * @return array
     */
    public function crearTerminal(Request $request)
    {
        $result = [];
        try {
            $validator = \Validator::make($request->all(), [
                'numero_activo' => 'required|unique:terminales'
            ]);

            if ($validator->fails()) {
                return $validator->errors()->all();
            }

            $ultimaTerminal = Terminales::select([\DB::raw('max(codigo) as codigo')])->first();
            if ($ultimaTerminal->codigo == null) {
                $codigo = '000000000000001';
            } else {
                $codigo = intval($ultimaTerminal->codigo);
                $codigo++;
                $largo = strlen($codigo);
                for ($i = 0; $i < (15 - $largo); $i++) {
                    $codigo = "0" . $codigo;
                }
            }
            $terminal = new Terminales();
            $terminal->codigo = $codigo;
            $terminal->numero_activo = $request->numero_activo;
            $terminal->celular = $request->celular;
            $terminal->password = Encript::encryption($request->password);
            $terminal->sucursal_id = $request->getQueryString();
            $terminal->save();
            $result['estado'] = true;
            $result['mensaje'] = 'Terminal creada satisfactoriamente';
        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible crear la terminal ' . $exception->getMessage();
        }
        return $result;
    }

    /**
     * trae el modal para editar una terminal
     * @param Request $request la id de la terminal que se quiere editar
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewEditarTerminal(Request $request)
    {
        $terminal = Terminales::find($request->id);
        return view('establecimientos.terminales.modaleditarterminal', compact('terminal'));
    }


    /**
     * metodo usado para editar una terminal especifica
     * @param Request $request id de la terminal que se quiere editar
     * @return array
     */
    public function editarTerminal(Request $request)
    {
        $result = [];
        try {
            $terminal = Terminales::find($request->getQueryString());

            if ($request->numero_activo != $terminal->numero_activo) {
                $validator = \Validator::make($request->all(), [
                    'numero_activo' => 'required|unique:terminales'
                ]);

                if ($validator->fails()) {
                    return $validator->errors()->all();
                }
            }

            $terminal->numero_activo = $request->numero_activo;
            $terminal->celular = $request->celular;
            if ($request->password != "") {
                $terminal->password = Encript::encryption($request->password);
            }

            $terminal->save();
            $result['estado'] = true;
            $result['mensaje'] = 'Terminal creada satisfactoriamente';
        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible crear la terminal ' . $exception->getMessage();
        }
        return $result;
    }

    public function cambiarEstadoTerminal(Request $request)
    {
        $result = [];
        try {
            $terminal = Terminales::find($request->id);

            if ($terminal->estado == 'A')
                $terminal->estado = 'I';
            else
                $terminal->estado = 'A';

            $terminal->save();
            $result['estado'] = true;
            $result['mensaje'] = 'Se cambio de estado satisfactoriamente';

        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible cambiar el estado ' . $exception->getMessage();
        }

        return $result;

    }
}
