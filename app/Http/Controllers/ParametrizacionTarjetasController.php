<?php

namespace creditocofrem\Http\Controllers;

use creditocofrem\AdminisTarjetas;
use creditocofrem\CuenContaTarjeta;
use creditocofrem\Departamentos;
use creditocofrem\PagaPlastico;
use creditocofrem\Servicios;
use creditocofrem\Tarjetas;
use creditocofrem\ValorTarjeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class ParametrizacionTarjetasController extends Controller
{
    //
    /**
     * metodo que trae la vista para parametrizar las tarjetas
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewParametrosTarjetas()
    {
        $tipotarjetas = Servicios::all()->pluck('descripcion', 'codigo');
        $valorTarjeta = ValorTarjeta::where('estado', 'A')->first();
        if ($valorTarjeta != null) {
            if (!strrpos($valorTarjeta->valor, '.')) {
                $valorTarjeta->valor = $valorTarjeta->valor . ',00';
            }
        }
        return view('tarjetas.parametrizacion.parametrizacion', compact(['tipotarjetas', 'valorTarjeta']));
    }

    /**
     * metodo que permite agregar un valor al plastico de la tarjeta
     * @param Request $request
     * @return array
     */
    public function tarjetaCrearParametroValor(Request $request)
    {
        $result = [];
        \DB::beginTransaction();
        try {
            $existe = ValorTarjeta::all();
            $newvalor = new ValorTarjeta();
            if (count($existe) > 0) {
                ValorTarjeta::where('estado', 'A')->update(['estado' => 'I']);
                $newvalor->valor = str_replace(",", ".", str_replace(".", "", $request->valor));
                $newvalor->estado = 'A';
            } else {
                $newvalor->valor = str_replace(",", ".", str_replace(".", "", $request->valor));
                $newvalor->estado = 'A';
            }
            $newvalor->save();
            \DB::commit();
            $result['estado'] = true;
            $result['mensaje'] = 'Valor ingresado satisfactoriamente';
        } catch (\Exception $exception) {
            \DB::rollback();
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible ingresar el valor ' . $exception->getMessage();
        }
        return $result;
    }

    /**
     * permite agregar valores de administracion para las tarjetas
     * @param Request $request
     * @return array
     */
    public function tarjetaCrearParametroAdministracion(Request $request, $codigo)
    {
        $result = [];
        try {
            $existe = AdminisTarjetas::all();
            if (count($existe) > 0) {
                $oldAdministraciones = AdminisTarjetas::where('estado', 'A')->where('servicio_codigo', $codigo)->get();
                foreach ($oldAdministraciones as $oldAdministracione) {
                    if ($oldAdministracione->porcentaje == $request->porcentaje) {
                        $result['estado'] = false;
                        $result['mensaje'] = 'Ya exite este porcentaje de administracion para este tipo de tarjeta';
                        return $result;
                    }
                }
            }
            if($codigo == Tarjetas::$CODIGO_SERVICIO_REGALO){
                $existePararegalo = AdminisTarjetas::where('estado', 'A')->where('servicio_codigo', $codigo)->first();
                if($existePararegalo != NULL){
                    $result['estado'] = false;
                    $result['mensaje'] = 'Existe una parametrizacion activa para este producto';
                    return $result;
                }
            }
            $parametro = new AdminisTarjetas($request->all());
            $parametro->servicio_codigo = $codigo;
            $parametro->save();
            $result['estado'] = true;
            $result['mensaje'] = 'Administracion agregada satisfactoriamente';
        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible agregar la administracion ' . $exception->getMessage();
        }
        return $result;
    }

    /**
     * metodo encargado de llenar la grid con las configuraciones de administracion existentes para una tarjeta
     * @return mixed
     */
    public function gridAdministracionTarjetas($codigo)
    {
        $administraciones = AdminisTarjetas::where('estado', 'A')->where('servicio_codigo', $codigo)->get();
        foreach ($administraciones as $administracione) {
            $administracione->getTipoTarjeta;
        }

        return Datatables::of($administraciones)
            ->addColumn('action', function ($rangos) {
                $acciones = '<div class="btn-group">';
                $acciones = $acciones . '<button class="btn btn-xs btn-danger" onclick="eliminarAdministracion(' . $rangos->id . ')" ><i class="fa fa-trash"></i> Eliminar</button>';
                $acciones = $acciones . '</div>';
                return $acciones;
            })->make(true);

    }

    /**
     * metodo que permite eliminar(cambiar de estado) una parametricacion de porcentaje de administracion
     * @param Request $request
     */
    public function tarjetaEliminarParametroAdministracion(Request $request)
    {
        $result = [];
        try {
            $administracion = AdminisTarjetas::find($request->id);
            $administracion->estado = 'I';
            $administracion->save();
            $result['estado'] = true;
            $result['mensaje'] = 'Eliminado parametro de administracion satisfactoriamente';
        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'Eliminado parametro de administracion satisfactoriamente';
        }
        return $result;
    }


    /**
     * metodo encargado de tarer la vista parcial de parametrizacion de tarjetas
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getViewParametrizarServicio(Request $request)
    {
        $servicio = Servicios::where('codigo', $request->codigo)->first();
        $departamentos = Departamentos::all()->pluck('descripcion', 'codigo');
        //dd($servicio);
        if ($servicio->tipo == 'P') {
            return view('tarjetas.parametrizacion.partialparametrizacionproducto', compact(['servicio', 'departamentos']));
        } else {

        }
    }

    /**
     * metodo que me carga el historial del cambio del valor del plastico
     * @return mixed
     */
    public function gridValorPlastico()
    {
        $valorPlasticos = ValorTarjeta::all();
        return Datatables::of($valorPlasticos)->make(true);
    }

    public function tarjetaCrearParametroPagaplastico(Request $request, $codigo)
    {
        $result = [];
        DB::beginTransaction();
        try {
            $existePaga = PagaPlastico::where('estado', 'A')->where('servicio_codigo', $codigo)->first();
            if (count($existePaga) > 0) {
                $existePaga->estado = 'I';
                $existePaga->save();
            }
            $newPagaPlastico = new PagaPlastico();
            $newPagaPlastico->pagaplastico = $request->pagaplatico;
            $newPagaPlastico->estado = 'A';
            $newPagaPlastico->servicio_codigo = $codigo;
            $newPagaPlastico->save();
            $result['estado'] = true;
            $result['mensaje'] = 'Actualizado satisfactoriamente';
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible actualizar el pago de platico ' . $exception->getMessage();
        }
        return $result;
    }

    /**
     * metodo que alimenta la parametrica para saber si un servicio paga o no plastico
     * @param $codigo el codigo del servicio
     * @return mixed
     */
    public function gridServicioPagaPlastico($codigo)
    {
        $pagaPlastico = PagaPlastico::where('servicio_codigo', $codigo)->get();
        return Datatables::of($pagaPlastico)->make(true);
    }

    /**
     * metodo que agrega una cuenta contable a el servicio de bono y tarjeta regalo
     * @param Request $request
     * @param $codigo el codigo del servicio
     * @return array
     */
    public function tarjetaCrearParametroCuentaRB(Request $request, $codigo)
    {
        $result = [];
        DB::beginTransaction();
        try {
            if ($codigo != 'A') {
                CuenContaTarjeta::where('estado', 'A')->where('servicio_codigo', $codigo)->update(['estado' => 'I']);
            } else {
                $existeCuenta = CuenContaTarjeta::where('estado', 'A')
                    ->where('servicio_codigo', $codigo)
                    ->where('municipio_codigo', $request->municipio_codigo)
                    ->update(['estado' => 'I']);
            }
            $newCutenta = new CuenContaTarjeta();
            $newCutenta->estado = 'A';
            $newCutenta->servicio_codigo = $codigo;
            $newCutenta->cuenta = $request->cuentacontable;
            $newCutenta->municipio_codigo = $codigo != 'A' ? '50001' : $request->municipio_codigo;
            $newCutenta->save();
            $result['estado'] = true;
            $result['mensaje'] = 'Se actualizado la cuenta contable satisfactoriamente.';
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible actualizar la cuenta contable. ' . $exception->getMessage();
        }
        return $result;
    }

    /**
     * metodo que me carga la grid, que me muestra las cuentas contables que tenga un servicio
     * @param $codigo el codigo del servicio
     * @return mixed
     */
    public function gridParametrosCuentasContables($codigo)
    {
        $cuentas = CuenContaTarjeta::where('servicio_codigo', $codigo)->get();
        foreach ($cuentas as $cuenta) {
            $cuenta->getMunicipio;
        }
        return Datatables::of($cuentas)->make(true);
    }
}
