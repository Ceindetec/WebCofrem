<?php

namespace creditocofrem\Http\Controllers;

use Carbon\Carbon;
use creditocofrem\DetalleProdutos;
use creditocofrem\DetalleTransaccion;
use creditocofrem\Duplicado;
use creditocofrem\DuplicadoProductos;
use creditocofrem\Establecimientos;
use creditocofrem\HEstadoTransaccion;
use creditocofrem\Servicios;
use creditocofrem\Sucursales;
use creditocofrem\Tarjetas;
use creditocofrem\Terminales;
use creditocofrem\Transaccion;
use Illuminate\Http\Request;
use function MongoDB\BSON\toJSON;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Excel;
use PHPExcel_Worksheet_Drawing;

class ReportesController extends Controller
{
    /**
     * este metodo trae la vista para la generacion de reporte por primera vez
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewReportePrimeraVez()
    {
        return view('reportes.primeravez.primeravez');
    }

    /**
     * trae la vista parcial del resultado del reporte para tarjetas usadas por primera vez
     * @param Request $request Rango de fecha selecionado en el formulario
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function resultadoPrimeravez(Request $request)
    {
        $rangos = explode(" - ", $request->rango);

        $transacciones = $this->dataprimeravez($rangos, $request);

        $rango = ['fecha1' => $rangos[0], 'fecha2' => $rangos[1]];

        return view('reportes.primeravez.resultadoprimeravez', compact('transacciones', 'rango'));
    }

    private function dataprimeravez($rangos, $request)
    {

        $detallesUsadas = DetalleTransaccion::whereRaw("DETALLE_TRANSACCIONES.TRANSACCION_ID IN (select TRANSACCIONES.ID from TRANSACCIONES where FECHA BETWEEN ? AND ? AND tipo = ?)", [Carbon::createFromFormat("d/m/Y", $rangos[0]), Carbon::createFromFormat("d/m/Y", $rangos[1]), 'C'])
            ->groupBy("detalle_producto_id")
            ->select("detalle_producto_id")
            ->get();

        $arrayDetalles = [];
        foreach ($detallesUsadas as $detallesUsada) {
            array_push($arrayDetalles, $detallesUsada->detalle_producto_id);
        }

        $detallesAntes = DetalleTransaccion::whereRaw("DETALLE_TRANSACCIONES.TRANSACCION_ID IN (select TRANSACCIONES.ID from TRANSACCIONES where FECHA < ? AND tipo = ?)", [Carbon::createFromFormat("d/m/Y", $rangos[0]), 'C'])
            ->groupBy("detalle_producto_id")
            ->select("detalle_producto_id")
            ->get();

        $arrayDetallesAntes = [];
        foreach ($detallesAntes as $detallesAnte) {
            array_push($arrayDetallesAntes, $detallesAnte->detalle_producto_id);
        }

        $arrayFinal = [];
        foreach ($arrayDetalles as $arrayDetalle) {
            if (!in_array($arrayDetalle, $arrayDetallesAntes)) {
                array_push($arrayFinal, $arrayDetalle);
            }
        }
        $detalleProductos = DetalleProdutos::whereIn('id', $arrayFinal)->get();
        $regalos = [];
        $bonos = [];
        foreach ($detalleProductos as $detalleProducto) {
            if ($detalleProducto->factura != NULL) {
                array_push($regalos, $detalleProducto);
            } else if ($detalleProducto->contrato_emprs_id != NULL) {
                array_push($bonos, $detalleProducto);
            }
        }

        $transaRegalo = [];
        foreach ($regalos as $regalo) {
            $detalleTUsado = DetalleTransaccion::where('detalle_producto_id', $regalo->id)
                ->join('transacciones', 'transacciones.id', 'detalle_transacciones.transaccion_id')
                ->join("sucursales", "transacciones.sucursal_id", "sucursales.id")
                ->whereBetween('transacciones.fecha', [Carbon::createFromFormat("d/m/Y", $rangos[0]), Carbon::createFromFormat("d/m/Y", $rangos[1])])
                ->where('transacciones.tipo', 'C')
                ->orderBy('transacciones.fecha', 'asc')
                ->first();
            array_push($transaRegalo, $detalleTUsado);
        }

        $transaBono = [];
        foreach ($bonos as $bono) {
            $detalleTUsado = DetalleTransaccion::where('detalle_producto_id', $bono->id)
                ->join('transacciones', 'transacciones.id', 'detalle_transacciones.transaccion_id')
                ->whereBetween('transacciones.fecha', [Carbon::createFromFormat("d/m/Y", $rangos[0]), Carbon::createFromFormat("d/m/Y", $rangos[1])])
                ->where('transacciones.tipo', 'C')
                ->orderBy('transacciones.fecha', 'asc')->first();
            array_push($transaBono, $detalleTUsado);
        }
        return $respuesta = ["regalo" => ["detproducto" => $regalos, "dettransa" => $transaRegalo], "bono" => ["detproducto" => $bonos, "dettransa" => $transaBono]];
    }

    /**
     * permite exportar a pdf el resultado de las tarjetas usadas por primera vez
     * @param Request $request
     * @return mixed
     */
    public function exportarPdfPrimeravez(Request $request)
    {

        $rangos = [$request->fecha1, $request->fecha2];
        $transacciones = $this->dataprimeravez($rangos, $request);
        $data = ['transacciones' => $transacciones, 'rango' => $request->fecha1 . " - " . $request->fecha2];
        $pdf = \PDF::loadView('reportes.primeravez.pdfprimeravez', $data);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('primeravez.pdf');
    }

    /**
     * metodo que permite exportar a excel el reporte
     * @param Request $request
     */
    public function exportarExcelPrimeravez(Request $request)
    {
        $rangos = [$request->fecha1, $request->fecha2];
        $transacciones = $this->dataprimeravez($rangos, $request);

        \Excel::create('ExcelTarjetasPrimeraVez', function ($excel) use ($request, $transacciones) {
            $fecha1 = $request->fecha1;
            $fecha2 = $request->fecha2;
            $rango = $fecha1 . " - " . $fecha2;

            $excel->sheet('regalo', function ($sheet) use ($transacciones, $rango) {
                $hoy = Carbon::now();
                $objDrawing = new PHPExcel_Worksheet_Drawing;
                $objDrawing->setPath(public_path('images/logo.png')); //your image path
                $objDrawing->setHeight(50);
                $objDrawing->setCoordinates('A1');
                $objDrawing->setWorksheet($sheet);
                $objDrawing->setOffsetY(10);
                $sheet->setWidth(array(
                    'A' => 30,
                    'B' => 20,
                    'C' => 20,
                    'D' => 30,
                    'E' => 20,
                    'F' => 20,
                    'G' => 20,
                    'H' => 20,
                ));

                $sheet->setMergeColumn(array(
                    'columns' => array('A'),
                    'rows' => array(
                        array(1, 4),
                    )
                ));

                $sheet->row(1, array('', 'REPORTE TARJETAS USADAS POR PRIMERA VEZ'));
                $sheet->row(1, function ($row) {
                    $row->setBackground('#4CAF50');
                });

                $sheet->cells('A1:A4', function ($cells) {
                    $cells->setBackground('#FFFFFF');
                });

                $sheet->setBorder('A1:A4', 'thin');

                $sheet->row(3, array('', 'Rango:', $rango, '', ''));
                $sheet->row(4, array('', 'Fecha:', $hoy, '', ''));

                $fila = 8;
                if (count($transacciones["regalo"]["detproducto"]) > 0) {
                    $sheet->row(7, array('Numero tarjeta', 'Monto inicial', 'Factura', 'Transacción', 'Codigo Terminal', 'Sucursal', 'Valor', 'Fecha'));
                    $sheet->row(7, function ($row) {
                        $row->setBackground('#f2f2f2');
                    });
                    for ($i = 0; $i < count($transacciones["regalo"]["detproducto"]); $i++) {
                        $sheet->row($fila,
                            array(
                                $transacciones["regalo"]["detproducto"][$i]->numero_tarjeta,
                                $transacciones["regalo"]["detproducto"][$i]->monto_inicial,
                                $transacciones["regalo"]["detproducto"][$i]->factura,
                                $transacciones["regalo"]["dettransa"][$i]->numero_transaccion,
                                $transacciones["regalo"]["dettransa"][$i]->codigo_terminal,
                                $transacciones["regalo"]["dettransa"][$i]->nombre,
                                $transacciones["regalo"]["dettransa"][$i]->valor,
                                $transacciones["regalo"]["dettransa"][$i]->fecha
                            ));
                        $fila++;
                    }
                } else
                    $sheet->row($fila, array('No hay resultados'));
                $fila++;
                $fila++;
            });
            $excel->sheet('Bono empresarial', function ($sheet) use ($transacciones, $rango) {
                $hoy = Carbon::now();
                $objDrawing = new PHPExcel_Worksheet_Drawing;
                $objDrawing->setPath(public_path('images/logo.png')); //your image path
                $objDrawing->setHeight(50);
                $objDrawing->setCoordinates('A1');
                $objDrawing->setWorksheet($sheet);
                $objDrawing->setOffsetY(10);
                $sheet->setWidth(array(
                    'A' => 30,
                    'B' => 20,
                    'C' => 20,
                    'D' => 30,
                    'E' => 20,
                    'F' => 20,
                    'G' => 20,
                    'H' => 20,
                ));

                $sheet->setMergeColumn(array(
                    'columns' => array('A'),
                    'rows' => array(
                        array(1, 4),
                    )
                ));

                $sheet->row(1, array('', 'REPORTE TARJETAS USADAS POR PRIMERA VEZ'));
                $sheet->row(1, function ($row) {
                    $row->setBackground('#4CAF50');
                });

                $sheet->cells('A1:A4', function ($cells) {
                    $cells->setBackground('#FFFFFF');
                });

                $sheet->setBorder('A1:A4', 'thin');

                $sheet->row(3, array('', 'Rango:', $rango, '', ''));
                $sheet->row(4, array('', 'Fecha:', $hoy, '', ''));

                $fila = 8;
                if (count($transacciones["bono"]["detproducto"]) > 0) {
                    $sheet->row(7, array('Numero tarjeta', 'Monto inicial', 'Factura', 'Transacción', 'Codigo Terminal', 'Sucursal', 'Valor', 'Fecha'));
                    $sheet->row(7, function ($row) {
                        $row->setBackground('#f2f2f2');
                    });
                    for ($i = 0; $i < count($transacciones["bono"]["detproducto"]); $i++) {
                        $sheet->row($fila,
                            array(
                                $transacciones["bono"]["detproducto"][$i]->numero_tarjeta,
                                $transacciones["bono"]["detproducto"][$i]->monto_inicial,
                                $transacciones["bono"]["detproducto"][$i]->factura,
                                $transacciones["bono"]["dettransa"][$i]->numero_transaccion,
                                $transacciones["bono"]["dettransa"][$i]->codigo_terminal,
                                $transacciones["bono"]["dettransa"][$i]->nombre,
                                $transacciones["bono"]["dettransa"][$i]->valor,
                                $transacciones["bono"]["dettransa"][$i]->fecha
                            ));
                        $fila++;
                    }
                } else
                    $sheet->row($fila, array('No hay resultados'));
                $fila++;
                $fila++;
            });
        })->
export('xls');

}

/**
 * trae la vista para generar el reporte de servicios activados descriminados por fecha de activacion y montos
 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
 */
public
function viewReporteMontos()
{
    $servicios = Servicios::pluck('descripcion', 'codigo');
    return view('reportes.montostarjetas.motosportarjetas', compact('servicios'));
}

/**
 * genera la previzualizacion del reporte de servicios activados descriminados por fecha de activacion y montos
 * @param Request $request
 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
 */
public
function resultadoMontosTarjetas(Request $request)
{
    $result = [];
    try {
        $rangos = explode(" - ", $request->rango);
        $data = $this->dataReporteActivadosPorMontos($rangos, $request);
        $rango = ['fecha1' => $rangos[0], 'fecha2' => $rangos[1]];
        $tipoServicio = $request->servicios;
    } catch (\Exception $exception) {
        $data = ['regalo' => [], 'bono' => []];
    }
    return view('reportes.montostarjetas.parcialresultadomotosportarjeta', compact('data', 'rango', 'tipoServicio'));
}

/**
 * genera la previzualizacion del reporte de servicios activados descriminados por fecha de activacion y montos
 * @param Request $request
 * @return mixed
 */
public
function exportarPdfMotosPorTarjeta(Request $request)
{
    $rangos = [$request->fecha1, $request->fecha2];
    $data = $this->dataReporteActivadosPorMontos($rangos, $request);
    $data = array_merge($data, ['rango' => $request->fecha1 . " - " . $request->fecha2]);
    $pdf = \PDF::loadView('reportes.montostarjetas.pdfmontosportarjeta', $data);
    //$pdf->setPaper('A4', 'landscape');
    return $pdf->download('activacionesporponto - ' . Carbon::now()->format('d-m-Y') . '.pdf');
}

/**
 * METODO QUE PERMITE EXPORTAR A EXCEL EL REPORTE DE SERVICIOS ACTIVADOS POR FECHA DE ACTIVACION Y MONTOS
 * @param Request $request
 */
public
function exportarExcelMontosPorTarjeta(Request $request)
{
    $rangos = [$request->fecha1, $request->fecha2];
    $data = $this->dataReporteActivadosPorMontos($rangos, $request);
    \Excel::create('ExcelMontosPorTarjeta', function ($excel) use ($request, $data) {
        $fecha1 = $request->fecha1;
        $fecha2 = $request->fecha2;
        $rango = $fecha1 . " - " . $fecha2;
        if (count($data['regalo']) > 0) {
            $excel->sheet('Regalo', function ($sheet) use ($data, $rango) {
                $hoy = Carbon::now();
                $objDrawing = new PHPExcel_Worksheet_Drawing;
                $objDrawing->setPath(public_path('images/logo.png')); //your image path
                $objDrawing->setHeight(50);
                $objDrawing->setCoordinates('A1');
                $objDrawing->setWorksheet($sheet);
                $objDrawing->setOffsetY(10);
                $sheet->setWidth(array(
                    'A' => 30,
                    'B' => 20,
                    'C' => 20,
                    'D' => 30,
                ));

                $sheet->setMergeColumn(array(
                    'columns' => array('A'),
                    'rows' => array(
                        array(1, 4),
                    )
                ));

                $sheet->row(1, array('', 'REPORTE SERVICIOS REGALO ACTIVADOS POR MONTOS'));
                $sheet->row(1, function ($row) {
                    $row->setBackground('#4CAF50');
                });

                $sheet->cells('A1:A4', function ($cells) {
                    $cells->setBackground('#FFFFFF');
                });

                $sheet->setBorder('A1:A4', 'thin');

                $sheet->row(3, array('', 'Rango:', $rango, ''));
                $sheet->row(4, array('', 'Fecha:', $hoy, ''));

                $fila = 7;
                if (sizeof($data['regalo']['detalles']) > 0) {
                    foreach ($data['regalo']['detalles'] as $detalles) {
                        foreach ($detalles as $montos) {
                            $sheet->row($fila, array('FECHA', $montos[0]->fecha_activacion, 'MONTO INICIAL', $montos[0]->monto_inicial));
                            $sheet->row($fila, function ($row) {
                                $row->setBackground('#4CAF50');
                            });
                            $fila++;
                            $sheet->row($fila, array('Numero tarjeta', 'Monto inicial', 'Factura', 'Usuario'));
                            $sheet->row($fila, function ($row) {
                                $row->setBackground('#f2f2f2');
                            });
                            $fila++;
                            foreach ($montos as $monto) {
                                $sheet->row($fila,
                                    array(
                                        $monto->numero_tarjeta,
                                        $monto->monto_inicial,
                                        $monto->factura,
                                        $monto->getUser->name
                                    ));
                                $fila++;
                            }
                            $fila++;
                        }
                    }
                } else {
                    $sheet->row($fila, array('No hay resultados'));
                }
                $fila++;
                $fila++;
            });
        }
        if (count($data['bono']) > 0) {
            $excel->sheet('Bonos Empresariales', function ($sheet) use ($data, $rango) {
                $hoy = Carbon::now();
                $objDrawing = new PHPExcel_Worksheet_Drawing;
                $objDrawing->setPath(public_path('images/logo.png')); //your image path
                $objDrawing->setHeight(50);
                $objDrawing->setCoordinates('A1');
                $objDrawing->setWorksheet($sheet);
                $objDrawing->setOffsetY(10);
                $sheet->setWidth(array(
                    'A' => 30,
                    'B' => 20,
                    'C' => 20,
                    'D' => 30,
                ));

                $sheet->setMergeColumn(array(
                    'columns' => array('A'),
                    'rows' => array(
                        array(1, 4),
                    )
                ));

                $sheet->row(1, array('', 'REPORTE SERVICIOS BONO EMPRESARIAL ACTIVADOS POR MONTOS'));
                $sheet->row(1, function ($row) {
                    $row->setBackground('#4CAF50');
                });

                $sheet->cells('A1:A4', function ($cells) {
                    $cells->setBackground('#FFFFFF');
                });

                $sheet->setBorder('A1:A4', 'thin');

                $sheet->row(3, array('', 'Rango:', $rango, ''));
                $sheet->row(4, array('', 'Fecha:', $hoy, ''));

                $fila = 7;
                if (sizeof($data['bono']['detalles']) > 0) {
                    foreach ($data['bono']['detalles'] as $detalles) {
                        foreach ($detalles as $montos) {
                            $sheet->row($fila, array('FECHA', $montos[0]->fecha_activacion, 'MONTO INICIAL', $montos[0]->monto_inicial));
                            $sheet->row($fila, function ($row) {
                                $row->setBackground('#4CAF50');
                            });
                            $fila++;
                            $sheet->row($fila, array('Numero tarjeta', 'Monto inicial', 'Numero de contrato', 'Usuario'));
                            $sheet->row($fila, function ($row) {
                                $row->setBackground('#f2f2f2');
                            });
                            $fila++;
                            foreach ($montos as $monto) {
                                $sheet->row($fila,
                                    array(
                                        $monto->numero_tarjeta,
                                        $monto->monto_inicial,
                                        $monto->getContrato->n_contrato,
                                        $monto->getUser->name
                                    ));
                                $fila++;
                            }
                            $fila++;
                        }
                    }
                } else
                    $sheet->row($fila, array('No hay resultados'));
                $fila++;
                $fila++;
            });
        }
    })->export('xls');
}

/**
 * metodo reusable para obtener la informacion para el reporte de activacion de servicios discriminados por montos
 * @param $rangos
 * @param $request
 * @return array
 */
private
function dataReporteActivadosPorMontos($rangos, $request)
{
    $fechaini = "";
    $regalo = [];
    $bono = [];
    if ($request->servicios == 'T') {
        $montosregalo = DetalleProdutos::where('factura', '<>', NULL)
            ->whereBetween('fecha_activacion', [Carbon::createFromFormat("d/m/Y", $rangos[0]), Carbon::createFromFormat("d/m/Y", $rangos[1])])
            ->where('estado', '<>', DetalleProdutos::$ESTADO_ANULADO)
            ->groupBy('monto_inicial')
            ->select('monto_inicial')
            ->get();
        if (count($montosregalo) > 0) {
            $fechaini = Carbon::createFromFormat("d/m/Y", $rangos[0]);
            $contadorfecha = 0;
            $contador = 0;
            while ($fechaini <= Carbon::createFromFormat("d/m/Y", $rangos[1])) {
                foreach ($montosregalo as $monto) {
                    $detalle = DetalleProdutos::where('factura', '<>', NULL)
                        ->whereDate('fecha_activacion', $fechaini->toDateString())
                        ->where('monto_inicial', $monto->monto_inicial)
                        ->where('estado', '<>', DetalleProdutos::$ESTADO_ANULADO)
                        ->get();
                    if (count($detalle) > 0) {
                        $regalo['detalles'][$contadorfecha][$contador] = $detalle;
                        $contador++;
                    }
                }
                $fechaini = $fechaini->addDay();
                if (isset($regalo['detalles'][$contadorfecha]))
                    $contadorfecha++;
            }
        }
        $montosbono = DetalleProdutos::where('contrato_emprs_id', '<>', NULL)
            ->whereBetween('fecha_activacion', [Carbon::createFromFormat("d/m/Y", $rangos[0]), Carbon::createFromFormat("d/m/Y", $rangos[1])])
            ->where('estado', '<>', DetalleProdutos::$ESTADO_ANULADO)
            ->groupBy('monto_inicial')
            ->select('monto_inicial')
            ->get();
        if (count($montosbono) > 0) {
            $fechaini = Carbon::createFromFormat("d/m/Y", $rangos[0]);
            $contadorfecha = 0;
            $contador = 0;
            while ($fechaini <= Carbon::createFromFormat("d/m/Y", $rangos[1])) {
                foreach ($montosbono as $monto) {
                    $detalle = DetalleProdutos::where('factura', '<>', NULL)
                        ->whereDate('fecha_activacion', $fechaini->toDateString())
                        ->where('monto_inicial', $monto->monto_inicial)
                        ->where('estado', '<>', DetalleProdutos::$ESTADO_ANULADO)
                        ->get();
                    if (count($detalle) > 0) {
                        $bono['detalles'][$contadorfecha][$contador] = $detalle;
                        $contador++;
                    }
                }
                $fechaini = $fechaini->addDay();
                if (isset($bono['detalles'][$contadorfecha]))
                    $contadorfecha++;
            }
        }
    } else if ($request->servicios == Tarjetas::$CODIGO_SERVICIO_REGALO) {
        $montosregalo = DetalleProdutos::where('factura', '<>', NULL)
            ->whereBetween('fecha_activacion', [Carbon::createFromFormat("d/m/Y", $rangos[0]), Carbon::createFromFormat("d/m/Y", $rangos[1])])
            ->where('estado', '<>', DetalleProdutos::$ESTADO_ANULADO)
            ->groupBy('monto_inicial')
            ->select('monto_inicial')
            ->get();
        if (count($montosregalo) > 0) {
            $fechaini = Carbon::createFromFormat("d/m/Y", $rangos[0]);
            $contadorfecha = 0;
            $contador = 0;
            while ($fechaini <= Carbon::createFromFormat("d/m/Y", $rangos[1])) {
                foreach ($montosregalo as $monto) {
                    $detalle = DetalleProdutos::where('factura', '<>', NULL)
                        ->whereDate('fecha_activacion', $fechaini->toDateString())
                        ->where('monto_inicial', $monto->monto_inicial)
                        ->where('estado', '<>', DetalleProdutos::$ESTADO_ANULADO)
                        ->get();
                    if (count($detalle) > 0) {
                        $regalo['detalles'][$contadorfecha][$contador] = $detalle;
                        $contador++;
                    }
                }
                $fechaini = $fechaini->addDay();
                if (isset($regalo['detalles'][$contadorfecha]))
                    $contadorfecha++;
            }
        }
    } else if ($request->servicios == Tarjetas::$CODIGO_SERVICIO_BONO) {
        $montosbono = DetalleProdutos::where('contrato_emprs_id', '<>', NULL)
            ->whereBetween('fecha_activacion', [Carbon::createFromFormat("d/m/Y", $rangos[0]), Carbon::createFromFormat("d/m/Y", $rangos[1])])
            ->where('estado', '<>', DetalleProdutos::$ESTADO_ANULADO)
            ->groupBy('monto_inicial')
            ->select('monto_inicial')
            ->get();
        if (count($montosbono) > 0) {
            $fechaini = Carbon::createFromFormat("d/m/Y", $rangos[0]);
            $contadorfecha = 0;
            $contador = 0;
            while ($fechaini <= Carbon::createFromFormat("d/m/Y", $rangos[1])) {
                foreach ($montosbono as $monto) {
                    $detalle = DetalleProdutos::where('factura', '<>', NULL)
                        ->whereDate('fecha_activacion', $fechaini->toDateString())
                        ->where('monto_inicial', $monto->monto_inicial)
                        ->where('estado', '<>', DetalleProdutos::$ESTADO_ANULADO)
                        ->get();
                    if (count($detalle) > 0) {
                        $bono['detalles'][$contadorfecha][$contador] = $detalle;
                        $contador++;
                    }
                }
                $fechaini = $fechaini->addDay();
                if (isset($bono['detalles'][$contadorfecha]))
                    $contadorfecha++;
            }
        }
    }
    return ['regalo' => $regalo, 'bono' => $bono];
}

/**
 * INICIA REPORTE SALDOS VENCIDOS
 * LLamar a la vista de consulta de saldos vencidos
 */
public
function viewSaldosvencidos()
{
    return view('reportes.saldosvencidos.saldosvencidos');
}

/*
 * Funcion RECURSIVA que retorna listado DE tarjeta DUPLICADO
 * la lista contiene los numero de tarjeta asociados
 */
public
function consultarDuplicados($numtarjeta, $listado)
{
    //buscar si la tarjeta tiene duppicado, si SI, agregar al array listado.
    $duplicado = Duplicado::where('newtarjeta', $numtarjeta)->first();
    if ($duplicado != null) {
        //$listado[] = array('numero_tarjeta' => $duplicado->newtarjeta);
        array_push($listado, $duplicado->oldtarjeta);
        // dd($duplicado->newtarjeta);
        $resultado = $this->consultarDuplicados($duplicado->oldtarjeta, $listado);
        if ($resultado != null)
            $listado = $resultado;
        return $listado;
    } else
        return null;
}

/*
 * Funcion RECURSIVA que retorna listado DE productos DUPLICADO
 * la lista contiene los numero de tarjeta asociados
 */
public
function consultarDuplicadoProductos($numproducto, $listado)
{
    //buscar si el producto tiene duppicado, si SI, agregar al array listado.
    $duplicado = DuplicadoProductos::where('newproducto', $numproducto)->first();
    if ($duplicado != null) {
        array_push($listado, $duplicado->oldproducto);
        $resultado = $this->consultarDuplicados($duplicado->oldproducto, $listado);
        if ($resultado != null)
            $listado = $resultado;
        return $listado;
    } else
        return null;
}

/*
 * Funcion consultar saldos vencidos
 * - segun el tipo: consultar tarjetas bono, regalo o las dos.
 * -
 */
public
function consultarSaldosVencidos(Request $request)
{
    $rangos = explode(" - ", $request->rango);
    $tiposervicio = $request->tipo;
    $resultadob = array();
    $resultador = array();

    if ($tiposervicio == "B" || $tiposervicio == "T") {
        $detallesb = DetalleProdutos::join('tarjeta_servicios', 'detalle_produtos.numero_tarjeta', 'tarjeta_servicios.numero_tarjeta')
            ->whereBetween('detalle_produtos.fecha_vencimiento', [Carbon::createFromFormat("d/m/Y", $rangos[0]), Carbon::createFromFormat("d/m/Y", $rangos[1])])
            ->where('tarjeta_servicios.servicio_codigo', Tarjetas::$CODIGO_SERVICIO_BONO)
            ->where('detalle_produtos.estado', '<>', 'N')
            ->select('detalle_produtos.*')
            ->get();
        foreach ($detallesb as $detalle) {
            $gasto = 0;
            //buscar Duplicados
            $listado = [];
            array_push($listado, $detalle->id);
            $respuesta = $this->consultarDuplicadoProductos($detalle->id, $listado);
            if ($respuesta != null)
                $listado = $respuesta;
            $dtransacciones = DetalleTransaccion::wherein('detalle_producto_id', $listado)->get();
            //finaliza ajuste para duplicados
            //$dtransacciones = DetalleTransaccion::where('detalle_producto_id', $detalle->id)->get();
            foreach ($dtransacciones as $dtransaccione) {
                $htransaccion = DB::table('h_estado_transacciones')->where('transaccion_id', $dtransaccione->transaccion_id)->orderBy('id', 'desc')->first();
                if ($htransaccion->estado == HEstadoTransaccion::$ESTADO_ACTIVO)
                    $gasto += $dtransaccione->valor;
            }
            if ($gasto < $detalle->monto_inicial) //si hay saldo
            {
                $sobrante = $detalle->monto_inicial - $gasto;
                $resultadob[] = array('numero_tarjeta' => $detalle->numero_tarjeta,
                    'monto_inicial' => $detalle->monto_inicial,
                    'sobrante' => $sobrante,
                    'fecha_activacion' => $detalle->fecha_activacion,
                    'fecha_vencimiento' => $detalle->fecha_vencimiento,
                );
            }
        }
    }
    if ($tiposervicio == "R" || $tiposervicio == "T") {
        $detallesr = DetalleProdutos::join('tarjeta_servicios', 'detalle_produtos.numero_tarjeta', 'tarjeta_servicios.numero_tarjeta')
            ->whereBetween('detalle_produtos.fecha_vencimiento', [Carbon::createFromFormat("d/m/Y", $rangos[0]), Carbon::createFromFormat("d/m/Y", $rangos[1])])
            ->where('tarjeta_servicios.servicio_codigo', Tarjetas::$CODIGO_SERVICIO_REGALO)
            ->where('detalle_produtos.estado', '<>', 'N')
            ->select('detalle_produtos.*')
            ->get();
        foreach ($detallesr as $detalle) {
            $gasto = 0;
            //buscar Duplicados
            $listado = [];
            array_push($listado, $detalle->id);
            $respuesta = $this->consultarDuplicadoProductos($detalle->id, $listado);
            if ($respuesta != null)
                $listado = $respuesta;
            $dtransacciones = DetalleTransaccion::wherein('detalle_producto_id', $listado)->get();
            //finaliza ajuste para duplicados
            //$dtransacciones = DetalleTransaccion::where('detalle_producto_id', $detalle->id)->get();
            foreach ($dtransacciones as $dtransaccione) {
                $htransaccion = DB::table('h_estado_transacciones')->where('transaccion_id', $dtransaccione->transaccion_id)->orderBy('id', 'desc')->first();
                if ($htransaccion->estado == HEstadoTransaccion::$ESTADO_ACTIVO)
                    $gasto += $dtransaccione->valor;
            }
            if ($gasto < $detalle->monto_inicial) //si hay saldo
            {
                $sobrante = $detalle->monto_inicial - $gasto;
                $resultador[] = array('numero_tarjeta' => $detalle->numero_tarjeta,
                    'monto_inicial' => $detalle->monto_inicial,
                    'sobrante' => $sobrante,
                    'fecha_activacion' => $detalle->fecha_activacion,
                    'fecha_vencimiento' => $detalle->fecha_vencimiento,
                );
            }
        }
    }
    $rango = ['fecha1' => $rangos[0], 'fecha2' => $rangos[1]];
    return view('reportes.saldosvencidos.parcialresultadosaldosvencidos', compact('resultadob', 'resultador', 'rango', 'tiposervicio'));
}

/*
 * FUNCION GENERAR PDF para Saldos Vencidos
 * Exporta en formato pdf, los resultados
 * de tarjetas bono y/o regalo que se vencieron y quedaron con saldo
 */
public
function pdfSaldosVencidos(Request $request)
{
    $data = ['resultadob' => $request->resultadob, 'resultador' => $request->resultador, 'tiposervicio' => $request->tiposervicio, 'rango' => $request->fecha1 . " - " . $request->fecha2];
    $pdf = \PDF::loadView('reportes.saldosvencidos.pdfsaldosvencidos', $data);

    $pdf->setPaper('A4', 'landscape');
    return $pdf->download('saldosvencidos.pdf');
}

/*
 * FUNCION GENERAR EXCEL para saldos vencidos
 * Exporta a excel, los resultados de las tarjetas
 * bono y/o regalo que se vencieron y quedaron con saldo
 */
public
function excelSaldosVencidos(Request $request)
{
    \Excel::create('ExcelSaldosVencidos', function ($excel) use ($request) {
        $resultadob = $request->resultadob;
        $resultador = $request->resultador;
        $fecha1 = $request->fecha1;
        $fecha2 = $request->fecha2;
        $rango = $fecha1 . " - " . $fecha2;
        $tiposervicio = $request->tiposervicio;

        $excel->sheet('SaldosVencidos', function ($sheet) use ($resultadob, $resultador, $rango, $tiposervicio) {
            $hoy = Carbon::now();
            $objDrawing = new PHPExcel_Worksheet_Drawing;
            $objDrawing->setPath(public_path('images/logo.png')); //your image path
            $objDrawing->setHeight(50);
            $objDrawing->setCoordinates('A1');
            $objDrawing->setWorksheet($sheet);
            $objDrawing->setOffsetY(10);
            $sheet->setWidth(array(
                'A' => 30,
                'B' => 20,
                'C' => 20,
                'D' => 20,
                'E' => 20,
                'F' => 20,
            ));

            $sheet->setMergeColumn(array(
                'columns' => array('A'),
                'rows' => array(
                    array(1, 4),
                )
            ));
            $sheet->row(2, array('', 'REPORTE DE SALDOS VENCIDOS'));
            $sheet->row(2, function ($row) {
                $row->setBackground('#4CAF50');
            });
            $sheet->cells('A1:A4', function ($cells) {
                $cells->setBackground('#FFFFFF');
            });

            $sheet->setBorder('A1:A4', 'thin');

            $sheet->row(3, array('', 'Rango:', $rango, '', ''));
            $sheet->row(4, array('', 'Fecha:', $hoy, '', ''));
            if ($tiposervicio != "R") {
                $sheet->row(6, array('TARJETAS BONO'));
                $sheet->row(6, function ($row) {
                    $row->setBackground('#4CAF50');
                });

                $fila = 8;
                if (sizeof($resultadob) > 0) {
                    $sheet->row(7, array('Número tarjeta', 'Monto inicial', 'Sobrante', 'Fecha activación', 'Fecha vencimiento'));
                    $sheet->row(7, function ($row) {
                        $row->setBackground('#f2f2f2');
                    });
                    foreach ($resultadob as $miresul) {
                        $sheet->row($fila, array($miresul["numero_tarjeta"], $miresul["monto_inicial"], $miresul["sobrante"], $miresul["fecha_activacion"], $miresul["fecha_vencimiento"]));
                        $fila++;
                    }
                } else
                    $sheet->row($fila, array('No hay resultados'));
                $fila++;
                $fila++;
            } else
                $fila = 6;
            if ($tiposervicio != "B") {
                $sheet->row($fila, array('TARJETAS REGALO'));
                $sheet->row($fila, function ($row) {
                    $row->setBackground('#4CAF50');
                });
                $fila++;
                if (sizeof($resultador) > 0) {
                    $sheet->row($fila, array('Número tarjeta', 'Monto inicial', 'Sobrante', 'Fecha activación', 'Fecha vencimiento'));
                    $sheet->row($fila, function ($row) {
                        $row->setBackground('#f2f2f2');
                    });
                    $fila++;
                    foreach ($resultador as $miresul) {
                        $sheet->row($fila, array($miresul["numero_tarjeta"], $miresul["monto_inicial"], $miresul["sobrante"], $miresul["fecha_activacion"], $miresul["fecha_vencimiento"]));
                        $fila++;
                    }
                } else
                    $sheet->row($fila, array('No hay resultados'));
            }
        });
    })->export('xls');
}

/*
 * FINALIZA REPORTES SALDOS VENCIDOS
 */
/*
 * INICIA REPORTE VENTAS DIARIAS POR ESTABLECIMIENTO
 */
public
function viewVentasDiarias()
{
    $establecimientos = Establecimientos::pluck('razon_social', 'id');
    return view('reportes.ventasdiariasxestablecimiento.ventasdiarias', compact('establecimientos'));
}

/*
 * Funcion consultar ventas diarias
 * - segun el tipo: consultar tarjetas bono, regalo o las dos.
 * -
 */
public
function consultarVentasDiarias(Request $request)
{
    $rangos = explode(" - ", $request->rango);
    $resultado = array();
    $lista_esta = $request->establecimientos;
    if (sizeof($lista_esta) > 0) {
        $establecimientos = Establecimientos::wherein('id', $request->establecimientos)
            ->orderby('razon_social', 'asc')->get();
    } else {
        $establecimientos = Establecimientos::orderby('razon_social', 'asc')->get();
        $lista_esta = [];
        foreach ($establecimientos as $establecimiento) {
            array_push($lista_esta, $establecimiento->id);
        }
    }
    $sucursales = Sucursales::wherein('establecimiento_id', $lista_esta)
        ->orderby('nombre', 'asc')->get();//$request->establecimientos
    if ($sucursales != null) {
        foreach ($sucursales as $sucursale) {
            $dtransacciones = DetalleTransaccion::join('h_estado_transacciones', 'detalle_transacciones.transaccion_id', 'h_estado_transacciones.transaccion_id')
                ->join('transacciones', 'detalle_transacciones.transaccion_id', 'transacciones.id')
                ->where('h_estado_transacciones.estado', '<>', HEstadoTransaccion::$ESTADO_INACTIVO)
                ->whereraw('"DETALLE_TRANSACCIONES"."TRANSACCION_ID" NOT IN (select "TRANSACCION_ID" FROM "H_ESTADO_TRANSACCIONES" WHERE "ESTADO"=?)', [HEstadoTransaccion::$ESTADO_INACTIVO])
                ->where('transacciones.sucursal_id', $sucursale->id)
                ->whereBetween('transacciones.fecha', [Carbon::createFromFormat("d/m/Y", $rangos[0]), Carbon::createFromFormat("d/m/Y", $rangos[1])])
                ->select('transacciones.fecha as fecha', DB::raw('SUM(detalle_transacciones.valor) as venta'))
                ->groupBy('detalle_transacciones.transaccion_id', 'transacciones.fecha', 'detalle_transacciones.valor')
                ->orderBy('transacciones.fecha', 'asc')
                ->get();
            $fechaanterior = "";
            $venta = 0;
            foreach ($dtransacciones as $dtransaccione) {
                $dt = Carbon::parse($dtransaccione->fecha);
                $fechaactual = $dt->year . "-" . $dt->month . "-" . $dt->day;
                if ($fechaanterior == "")
                    $fechaanterior = $fechaactual;
                if ($fechaanterior != $fechaactual) {
                    //insertar registro con valor de $venta
                    $resultado[] = array('establecimiento' => $sucursale->establecimiento_id,
                        'sucursal' => $sucursale->id,
                        'fecha' => $fechaanterior,
                        'venta' => $venta,
                    );
                    //tomar valores actuales
                    $venta = 0 + ($dtransaccione->venta);
                    $fechaanterior = $fechaactual;
                } else {
                    //sumar acumulado a $venta
                    $venta += $dtransaccione->venta;
                    //fechaanterior=actual
                    $fechaanterior = $fechaactual;
                }
            }
            $resultado[] = array('establecimiento' => $sucursale->establecimiento_id,
                'sucursal' => $sucursale->id,
                'fecha' => $fechaanterior,
                'venta' => $venta,
            );
        }
    }
    // dd($resultado);
    $rango = ['fecha1' => $rangos[0], 'fecha2' => $rangos[1]];
    return view('reportes.ventasdiariasxestablecimiento.parcialventasdiarias', compact('resultado', 'rango', 'lista_esta', 'establecimientos', 'sucursales'));
}

public
function selectestablecimientos(Request $request)
{
    // $establecimientos=Establecimientos::pluck('razon_social', 'id');
    $variable = strtoupper($request->term);
    $establecimientos = Establecimientos::where('razon_social', 'like', '%' . $variable . '%')->get();
    return $establecimientos;
}

/*
* FUNCION GENERAR PDF para ventas diarias por establecimiento
* Exporta a pdf, los resultados por dia de las ventas por sucursal
*/
public
function pdfVentasDiarias(Request $request)
{
    $establecimientos = Establecimientos::wherein('id', $request->lista_esta)->orderby('razon_social', 'asc')->get();
    $sucursales = Sucursales::wherein('establecimiento_id', $request->lista_esta)->orderby('nombre', 'asc')->get();
    $data = ['resultado' => $request->resultado, 'establecimientos' => $establecimientos, 'sucursales' => $sucursales, 'rango' => $request->fecha1 . " - " . $request->fecha2];
    $pdf = \PDF::loadView('reportes.ventasdiariasxestablecimiento.pdfventasdiarias', $data);
    $pdf->setPaper('A4', 'landscape');
    return $pdf->download('ventasdiarias.pdf');
}

/*
 * FUNCION GENERAR EXCEL para ventas diarias por establecimiento
 * Exporta a excel, los resultados por dia de las ventas por sucursal
 */
public
function excelVentasDiarias(Request $request)
{
    $establecimientos = Establecimientos::wherein('id', $request->lista_esta)->orderby('razon_social', 'asc')->get();
    $sucursales = Sucursales::wherein('establecimiento_id', $request->lista_esta)->orderby('nombre', 'asc')->get();

    \Excel::create('ExcelVentasDiarias', function ($excel) use ($request, $establecimientos, $sucursales) {
        $resultado = $request->resultado;
        $fecha1 = $request->fecha1;
        $fecha2 = $request->fecha2;
        $rango = $fecha1 . " - " . $fecha2;
        $num_esta = 0;
        //FOR ESTABLECIMIENTOS POR CADA UNO CREAR UNA PESTAÑA
        if (sizeof($establecimientos) > 0) {
            foreach ($establecimientos as $establecimiento) {
                $num_esta++;
                //titulo <h5>Establecimiento: {{$establecimiento->razon_social}}</h5>
                $excel->sheet('Est' . $num_esta, function ($sheet) use ($resultado, $establecimiento, $sucursales, $rango) {
                    $haysucursal = 0;
                    $hoy = Carbon::now();
                    $objDrawing = new PHPExcel_Worksheet_Drawing;
                    $objDrawing->setPath(public_path('images/logo.png')); //your image path
                    $objDrawing->setHeight(50);
                    $objDrawing->setCoordinates('A1');
                    $objDrawing->setWorksheet($sheet);
                    $objDrawing->setOffsetY(10);
                    $sheet->setWidth(array(
                        'A' => 30,
                        'B' => 20,
                        'C' => 20,
                        'D' => 20,
                        'E' => 20,
                        'F' => 20,
                    ));
                    $sheet->setMergeColumn(array(
                        'columns' => array('A'),
                        'rows' => array(
                            array(1, 4),
                        )
                    ));
                    $sheet->row(2, array('', 'REPORTE DE VENTAS DIARIAS PARA ESTABLECIMIENTO ' . $establecimiento->razon_social));
                    $sheet->row(2, function ($row) {
                        $row->setBackground('#4CAF50');
                    });
                    $sheet->cells('A1:A4', function ($cells) {
                        $cells->setBackground('#FFFFFF');
                    });

                    $sheet->setBorder('A1:A4', 'thin');
                    $sheet->row(3, array('', 'Rango:', $rango, '', ''));
                    $sheet->row(4, array('', 'Fecha:', $hoy, '', ''));
                    $fila = 6;
                    foreach ($sucursales as $sucursale) {
                        $cant = 0;
                        if ($sucursale->establecimiento_id == $establecimiento->id) {
                            $haysucursal++;
                            //titulo <h5>{{$sucursale->nombre}}</h5>
                            if (sizeof($resultado) > 0) {
                                //inicia tabla
                                /*campos:
                                    <th>Fecha</th>
                                    <th>Venta</th>*/
                                $subtotal = 0;
                                $sheet->row($fila, array('Sucursal: ' . $sucursale->nombre));
                                $sheet->row($fila, function ($row) {
                                    $row->setBackground('#4CAF50');
                                });
                                $fila++;
                                $fila++;
                                //if (sizeof($resultado) > 0) {
                                $sheet->row($fila, array('Fecha', 'Venta'));
                                $sheet->row($fila, function ($row) {
                                    $row->setBackground('#f2f2f2');
                                });
                                $fila++;
                                foreach ($resultado as $miresul) {
                                    if ($miresul["establecimiento"] == $establecimiento->id && $miresul["sucursal"] == $sucursale->id) {
                                        $cant++;
                                        //insertar los valores
                                        /*<td>{{$miresul["fecha"]}}</td>
                                        <td>{{$miresul["venta"]}}</td>
                                        $subtotal+=$miresul["venta"];  */
                                        $venta_name = '$ ' . number_format($miresul["venta"], 2, ',', '.');
                                        $sheet->row($fila, array($miresul["fecha"], $venta_name));
                                        $fila++;
                                        $subtotal += $miresul["venta"];
                                        /*} else
                                            $sheet->row($fila, array('No hay resultados'));*/

                                    }//cierra if

                                } //cierra foreach
                                $subtotal_name = '$ ' . number_format($subtotal, 2, ',', '.');
                                $sheet->row($fila, array('Total', $subtotal_name));
                                $fila++;
                                $fila++;
                                //mostrar subtotal $subtotal
                                //finaliza tabla
                            }//cierra if
                            if ($cant == 0)
                                $sheet->row($fila, array('No hay registros'));
                        }//cierra if sucursal id
                    }//cierra foreach
                    if ($haysucursal == 0)
                        $sheet->row($fila, array('No existen sucursales'));
                    /// mostrar No existen sucursales
                });        //CIERRA PESTAÑA
            } //finaliza foreach
        } //finaliza if
    })->export('xls');
}

/*
 * FINALIZA REPORTE VENTAS DIARIAS POR ESTABLECIMIENTO
 */

/*
 * INICIA REPORTE DATAFONOS POR ESTABLECIMIENTO
 */
public
function viewDatafonosxEstablecimiento()
{
    $establecimientos = Establecimientos::pluck('razon_social', 'id');
    return view('reportes.datafonosxestablecimiento.datafonosxestablecimiento', compact('establecimientos'));
}

/*
 * Funcion consultar datafonos por establecimientos
 * - segun el tipo: consultar tarjetas bono, regalo o las dos.
 * -
 */
public
function consultarDatafonosxEstablecimiento(Request $request)
{
    $resultado = array();
    $resumen = array();
    $lista_esta = $request->establecimientos;
    /*$establecimientos = Establecimientos::wherein('id', $request->establecimientos)
        ->orderby('razon_social', 'asc')->get();*/
    if (sizeof($lista_esta) > 0) {
        $establecimientos = Establecimientos::wherein('id', $request->establecimientos)
            ->orderby('razon_social', 'asc')->get();
    } else {
        $establecimientos = Establecimientos::orderby('razon_social', 'asc')->get();
        $lista_esta = [];
        foreach ($establecimientos as $establecimiento) {
            array_push($lista_esta, $establecimiento->id);
        }
    }

    $sucursales = Sucursales::wherein('establecimiento_id', $lista_esta)
        ->orderby('nombre', 'asc')->get();
    if ($sucursales != null) {
        foreach ($sucursales as $sucursale) {
            $terminales = Terminales::where('sucursal_id', $sucursale->id)
                ->orderBy('codigo', 'asc')
                ->get();

            foreach ($terminales as $terminal) {
                if ($terminal->estado == "A")
                    $name_estado = "Activa";
                else
                    $name_estado = "Inactiva";
                $resultado[] = array('establecimiento' => $sucursale->establecimiento_id,
                    'sucursal' => $sucursale->id,
                    'codigo' => $terminal->codigo,
                    'numero_activo' => $terminal->numero_activo,
                    'estado' => $name_estado,
                );

            }

        }
        //obtener subtotales de estados de datafonos por establecimiento
        foreach ($establecimientos as $establecimiento) {
            $tactivas = 0;
            $tinactivas = 0;
            foreach ($resultado as $resul) {
                if ($resul['establecimiento'] == $establecimiento->id) {
                    if ($resul['estado'] == "Activa")//Terminales::$ESTADO_TERMINAL_ACTIVA
                        $tactivas++;
                    else
                        $tinactivas++;
                }
            }
            $resumen[] = array('establecimiento' => $establecimiento->id,
                'tactivas' => $tactivas,
                'tinactivas' => $tinactivas,
            );
        }
        //dd($resumen);
    }
    return view('reportes.datafonosxestablecimiento.parcialresultadodxe', compact('resultado', 'lista_esta', 'establecimientos', 'sucursales', 'resumen'));
}

/*
* FUNCION GENERAR PDF para datafonos por establecimiento
* Exporta a pdf, los datos y estado de datafonos por sucursal
*/
public
function pdfDatafonosxEstablecimiento(Request $request)
{
    // dd($request->lista_esta);
    $establecimientos = Establecimientos::wherein('id', $request->lista_esta)->orderby('razon_social', 'asc')->get();
    $sucursales = Sucursales::wherein('establecimiento_id', $request->lista_esta)->orderby('nombre', 'asc')->get();
    //dd($establecimientos);
    $data = ['resultado' => $request->resultado, 'establecimientos' => $establecimientos, 'sucursales' => $sucursales, 'resumen' => $request->resumen];
    $pdf = \PDF::loadView('reportes.datafonosxestablecimiento.pdfdxe', $data);
    $pdf->setPaper('A4', 'landscape');
    return $pdf->download('RelacionDatafonos.pdf');
}

/*
 * FUNCION GENERAR EXCEL para datafonos por establecimiento
 * Exporta a excel, datos y estado de datafonos por sucursal
 */
public
function excelDatafonosxEstablecimiento(Request $request)
{
    $establecimientos = Establecimientos::wherein('id', $request->lista_esta)->orderby('razon_social', 'asc')->get();
    $sucursales = Sucursales::wherein('establecimiento_id', $request->lista_esta)->orderby('nombre', 'asc')->get();

    \Excel::create('ExcelDatafonos', function ($excel) use ($request, $establecimientos, $sucursales) {
        $resultado = $request->resultado;
        $resumen = $request->resumen;
        $num_esta = 0;
        //FOR ESTABLECIMIENTOS POR CADA UNO CREAR UNA PESTAÑA
        if (sizeof($establecimientos) > 0) {
            foreach ($establecimientos as $establecimiento) {
                $num_esta++;
                //titulo <h5>Establecimiento: {{$establecimiento->razon_social}}</h5>
                $excel->sheet('Est' . $num_esta, function ($sheet) use ($resultado, $establecimiento, $sucursales, $resumen) {
                    $haysucursal = 0;
                    $hoy = Carbon::now();
                    $objDrawing = new PHPExcel_Worksheet_Drawing;
                    $objDrawing->setPath(public_path('images/logo.png')); //your image path
                    $objDrawing->setHeight(50);
                    $objDrawing->setCoordinates('A1');
                    $objDrawing->setWorksheet($sheet);
                    $objDrawing->setOffsetY(10);
                    $sheet->setWidth(array(
                        'A' => 30,
                        'B' => 20,
                        'C' => 20,
                        'D' => 20,
                        'E' => 20,
                        'F' => 20,
                    ));
                    $sheet->setMergeColumn(array(
                        'columns' => array('A'),
                        'rows' => array(
                            array(1, 4),
                        )
                    ));
                    $sheet->row(2, array('', 'REPORTE DE DATAFONOS DEL ESTABLECIMIENTO: ' . $establecimiento->razon_social));
                    $sheet->row(2, function ($row) {
                        $row->setBackground('#4CAF50');
                    });
                    $sheet->cells('A1:A4', function ($cells) {
                        $cells->setBackground('#FFFFFF');
                    });

                    $sheet->setBorder('A1:A4', 'thin');

                    //$sheet->row(3, array('','Rango:',$rango,'',''));
                    $sheet->row(3, array('', 'Fecha:', $hoy, '', ''));
                    $fila = 5;
                    foreach ($sucursales as $sucursale) {
                        $cant = 0;
                        if ($sucursale->establecimiento_id == $establecimiento->id) {
                            foreach ($resumen as $resum) {
                                if ($resum['establecimiento'] == $establecimiento->id) {
                                    $sheet->row($fila, array('Terminales Activas: ' . $resum['tactivas'], 'Terminales Inactivas: ' . $resum['tinactivas']));
                                    $sheet->row($fila, function ($row) {
                                        $row->setBackground('#f2f2f2');
                                    });
                                    $fila++;
                                    $fila++;
                                }
                            }
                            $haysucursal++;
                            if (sizeof($resultado) > 0) {
                                $subtotal = 0;
                                $sheet->row($fila, array('Sucursal: ' . $sucursale->nombre));
                                $sheet->row($fila, function ($row) {
                                    $row->setBackground('#4CAF50');
                                });
                                $fila++;
                                $fila++;
                                $sheet->row($fila, array('Código', 'Activo', 'Estado'));
                                $sheet->row($fila, function ($row) {
                                    $row->setBackground('#f2f2f2');
                                });
                                $fila++;
                                foreach ($resultado as $miresul) {
                                    if ($miresul["establecimiento"] == $establecimiento->id && $miresul["sucursal"] == $sucursale->id) {
                                        $cant++;
                                        $sheet->row($fila, array($miresul["codigo"], $miresul["numero_activo"], $miresul["estado"]));
                                        $fila++;
                                    }//cierra if
                                } //cierra foreach
                                $fila++;
                            }//cierra if
                            if ($cant == 0)
                                $sheet->row($fila, array('No hay registros'));
                        }//cierra if sucursal id
                    }//cierra foreach
                    if ($haysucursal == 0)
                        $sheet->row($fila, array('No existen sucursales'));
                });        //CIERRA PESTAÑA
            } //finaliza foreach
        } //finaliza if
    })->export('xls');
}

/*
 * FINALIZA REPORTE DATAFONOS POR ESTABLECIMIENTO
 */
/**
 * INICIA REPORTE SALDOS TARJETA
 * LLamar a la vista de consulta de saldos de cada servicio activo de una tarjeta
 */
public
function viewSaldoTarjeta()
{
    return view('reportes.saldotarjeta.saldotarjeta');
}

/*
 * Funcion consultar saldos de los servicios activos de una tarjeta
 */
/**
 * @param Request $request
 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
 */
public
function consultarSaldoTarjeta(Request $request)
{
    $numero_tarjeta = $request->numero_tarjeta;
    $codigo = $numero_tarjeta;
    while (strlen($codigo) < 6) {
        $codigo = "0" . $codigo;
    }
    $numero_tarjeta = $codigo;
    $resultado = array();
    $listado = [];
    array_push($listado, $numero_tarjeta);
    $respuesta = $this->consultarDuplicados($numero_tarjeta, $listado);
    if ($respuesta != null)
        $listado = $respuesta;
    $detalles = DetalleProdutos::wherein('numero_tarjeta', $listado)
        ->where('estado', DetalleProdutos::$ESTADO_ACTIVO)
        ->get();
    foreach ($detalles as $detalle) {
        $gasto = 0;
        $listadod = [];
        array_push($listadod, $detalle->id);
        $respuesta = $this->consultarDuplicadoProductos($detalle->id, $listadod);
        if ($respuesta != null)
            $listadod = $respuesta;
        $dtransacciones = DetalleTransaccion::wherein('detalle_producto_id', $listadod)->get();//$detalle->id
        foreach ($dtransacciones as $dtransaccione) {
            $htransaccion = DB::table('h_estado_transacciones')->where('transaccion_id', $dtransaccione->transaccion_id)->orderBy('id', 'desc')->first();
            if ($htransaccion->estado == HEstadoTransaccion::$ESTADO_ACTIVO)
                $gasto += $dtransaccione->valor;
        }
        $sobrante = $detalle->monto_inicial - $gasto;
        $sobrante = '$ ' . number_format($sobrante, 2, ',', '.');
        $monto = $detalle->monto_inicial;
        $monto = '$ ' . number_format($monto, 2, ',', '.');
        $tiposervicio = 'Bono';
        if ($detalle->contrato_emprs_id == null)
            $tiposervicio = 'Regalo';
        $resultado[] = array('monto_inicial' => $monto,
            'saldo' => $sobrante,
            'tipo_servicio' => $tiposervicio,
            'fecha_vencimiento' => $detalle->fecha_vencimiento,
        );
    }
    return view('reportes.saldotarjeta.parcialsaldotarjeta', compact('resultado', 'numero_tarjeta'));
}

/*
 * FUNCION GENERAR PDF para Saldos de tarjeta
 * Exporta en formato pdf, los resultados
 * de los saldos de los servicios activos de una tarjeta
 */
public
function pdfSaldoTarjeta(Request $request)
{
    $data = ['resultado' => $request->resultado, 'numero_tarjeta' => $request->numero_tarjeta];
    $pdf = \PDF::loadView('reportes.saldotarjeta.pdfsaldotarjeta', $data);

    $pdf->setPaper('A4', 'landscape');
    return $pdf->download('SaldosTarjeta.pdf');
}

/*
 * FINALIZA REPORTES SALDOS POR TARJETA ACTIVA
 */

/*
 * INICIA REPORTE DE NUMERO DE TRANSACCIONES POR DATAFONO POR ESTABLECIMIENTO Y SUCURSAL
 */
public
function viewTransaccionesxDatafono()
{
    $establecimientos = Establecimientos::pluck('razon_social', 'id');
    return view('reportes.transaccionesxdatafono.transaccionesxdatafono', compact('establecimientos'));
}

/*
 * Funcion consultar transacciones por datafono por establecimientos
 */
public
function consultarTransaccionesxDatafono(Request $request)
{
    $resultado = array();
    $resumen = array();
    $rangos = explode(" - ", $request->rango);
    $lista_esta = $request->establecimientos;
    if (sizeof($lista_esta) > 0) {
        $establecimientos = Establecimientos::wherein('id', $request->establecimientos)
            ->orderby('razon_social', 'asc')->get();
    } else {
        $establecimientos = Establecimientos::orderby('razon_social', 'asc')->get();
        $lista_esta = [];
        foreach ($establecimientos as $establecimiento) {
            array_push($lista_esta, $establecimiento->id);
        }
    }
    $sucursales = Sucursales::wherein('establecimiento_id', $lista_esta)
        ->orderby('nombre', 'asc')->get();
    if ($sucursales != null) {
        foreach ($sucursales as $sucursale) {
            $subtotal = 0;
            $terminales = Terminales::where('sucursal_id', $sucursale->id)
                ->orderBy('codigo', 'asc')
                ->get();

            foreach ($terminales as $terminal) {
                $totaltranx = Transaccion::where('codigo_terminal', $terminal->codigo)
                    ->whereBetween('fecha', [Carbon::createFromFormat("d/m/Y", $rangos[0]), Carbon::createFromFormat("d/m/Y", $rangos[1])])
                        ->whereraw('"ID" NOT IN (select "TRANSACCION_ID" FROM "H_ESTADO_TRANSACCIONES" WHERE "ESTADO"=?)',[HEstadoTransaccion::$ESTADO_INACTIVO])
                    ->count();
                if ($terminal->estado == Terminales::$ESTADO_TERMINAL_ACTIVA)
                    $name_estado = "Activo";
                else
                    $name_estado = "Inactivo";
                $resultado[] = array('establecimiento' => $sucursale->establecimiento_id,
                    'sucursal' => $sucursale->id,
                    'terminal' => $terminal->codigo,
                    'total' => $totaltranx,
                    'estado' => $name_estado,
                );
                $subtotal += $totaltranx;
            }
            $resumen[] = array('establecimiento' => $sucursale->establecimiento_id,
                'sucursal' => $sucursale->id,
                'total' => $subtotal,
            );
        }
    }
    $rango = ['fecha1' => $rangos[0], 'fecha2' => $rangos[1]];
    return view('reportes.transaccionesxdatafono.parcialresultadotxd', compact('resultado', 'lista_esta', 'establecimientos', 'sucursales', 'resumen', 'rango'));
}

/*
* FUNCION GENERAR PDF para transacciones por datafonos por establecimiento
* Exporta a pdf, cantidad de tranx por datafonos por sucursal
*/
public
function pdfTransaccionesxDatafono(Request $request)
{
    // dd($request->lista_esta);
    $establecimientos = Establecimientos::wherein('id', $request->lista_esta)->orderby('razon_social', 'asc')->get();
    $sucursales = Sucursales::wherein('establecimiento_id', $request->lista_esta)->orderby('nombre', 'asc')->get();
    //dd($establecimientos);
    $data = ['resultado' => $request->resultado, 'establecimientos' => $establecimientos, 'sucursales' => $sucursales, 'resumen' => $request->resumen, 'rango' => $request->fecha1 . " - " . $request->fecha2];
    $pdf = \PDF::loadView('reportes.transaccionesxdatafono.pdftxd', $data);
    $pdf->setPaper('A4', 'landscape');
    return $pdf->download('TransaccionesDatafonos.pdf');
}

/*
 * FUNCION GENERAR EXCEL para transacciones por datafono por establecimiento
 * Exporta a excel, cantidad de tranx de datafonos por sucursal
 */
public
function excelTransaccionesxDatafono(Request $request)
{
    $establecimientos = Establecimientos::wherein('id', $request->lista_esta)->orderby('razon_social', 'asc')->get();
    $sucursales = Sucursales::wherein('establecimiento_id', $request->lista_esta)->orderby('nombre', 'asc')->get();

    \Excel::create('ExcelTxDatafonos', function ($excel) use ($request, $establecimientos, $sucursales) {
        $resultado = $request->resultado;
        $resumen = $request->resumen;
        $rango = $request->fecha1 . " - " . $request->fecha2;
        $num_esta = 0;
        //FOR ESTABLECIMIENTOS POR CADA UNO CREAR UNA PESTAÑA
        if (sizeof($establecimientos) > 0) {
            foreach ($establecimientos as $establecimiento) {
                $num_esta++;
                //titulo <h5>Establecimiento: {{$establecimiento->razon_social}}</h5>
                $excel->sheet('Est' . $num_esta, function ($sheet) use ($resultado, $establecimiento, $sucursales, $resumen, $rango) {
                    $haysucursal = 0;
                    $hoy = Carbon::now();
                    $objDrawing = new PHPExcel_Worksheet_Drawing;
                    $objDrawing->setPath(public_path('images/logo.png')); //your image path
                    $objDrawing->setHeight(50);
                    $objDrawing->setCoordinates('A1');
                    $objDrawing->setWorksheet($sheet);
                    $objDrawing->setOffsetY(10);
                    /*$objDrawing = new PHPExcel_Worksheet_Drawing;
                    $objDrawing->setPath(public_path('images/logo_mini.png')); //your image path
                    $objDrawing->setCoordinates('A1');
                    $objDrawing->setWorksheet($sheet);*/
                    $sheet->setWidth(array(
                        'A' => 30,
                        'B' => 20,
                        'C' => 20,
                        'D' => 20,
                        'E' => 20,
                        'F' => 20,
                    ));

                    $sheet->setMergeColumn(array(
                        'columns' => array('A'),
                        'rows' => array(
                            array(1, 4),
                        )
                    ));
                    $sheet->row(1, array('', 'REPORTE DE TRANSACCIONES POR DATAFONO DEL ESTABLECIMIENTO: ' . $establecimiento->razon_social));
                    $sheet->row(1, function ($row) {
                        $row->setBackground('#4CAF50');
                    });
                    $sheet->cells('A1:A4', function ($cells) {
                        $cells->setBackground('#FFFFFF');
                    });

                    $sheet->setBorder('A1:A4', 'thin');

                    $sheet->row(3, array('', 'Fecha:', $hoy, '', ''));
                    $sheet->row(4, array('', 'Rango:', $rango, '', ''));
                    $fila = 5;
                    foreach ($sucursales as $sucursale) {
                        $cant = 0;
                        if ($sucursale->establecimiento_id == $establecimiento->id) {
                            $haysucursal++;
                            if (sizeof($resultado) > 0) {
                                $subtotal = 0;
                                $sheet->row($fila, array('Sucursal: ' . $sucursale->nombre));
                                $sheet->row($fila, function ($row) {
                                    $row->setBackground('#4CAF50');
                                });
                                $fila++;
                                $fila++;
                                $sheet->row($fila, array('Código del datafono', 'Estado Actual', 'No. de transacciones'));
                                $sheet->row($fila, function ($row) {
                                    $row->setBackground('#f2f2f2');
                                });
                                $fila++;
                                foreach ($resultado as $miresul) {
                                    if ($miresul["establecimiento"] == $establecimiento->id && $miresul["sucursal"] == $sucursale->id) {
                                        $cant++;
                                        $sheet->row($fila, array($miresul["terminal"], $miresul["estado"], $miresul["total"]));
                                        $fila++;
                                    }//cierra if
                                } //cierra foreach
                                $fila++;
                                foreach ($resumen as $resum) {
                                    if ($resum['sucursal'] == $sucursale->id) {
                                        $sheet->row($fila, array('Total transacciones: ', $resum['total']));
                                        $sheet->row($fila, function ($row) {
                                            $row->setBackground('#f2f2f2');
                                        });
                                        $fila++;
                                        $fila++;
                                    }
                                }
                            }//cierra if
                            if ($cant == 0)
                                $sheet->row($fila, array('No hay registros'));
                        }//cierra if sucursal id
                    }//cierra foreach
                    if ($haysucursal == 0)
                        $sheet->row($fila, array('No existen sucursales'));
                });        //CIERRA PESTAÑA
            } //finaliza foreach
        } //finaliza if
    })->export('xls');
}

/*
 * FINALIZA REPORTE TRANX X DATAFONO POR ESTABLECIMIENTO
 */

/*
 * INICIA REPORTE DE PROMEDIO DE TRANSACCIONES POR DATAFONO POR ESTABLECIMIENTO Y SUCURSAL
 */
public
function viewPromedioxDatafono()
{
    $establecimientos = Establecimientos::pluck('razon_social', 'id');
    return view('reportes.promedioxdatafono.promedioxdatafono', compact('establecimientos'));
}

/*
 * Funcion consultar transacciones por datafono por establecimientos
 */
public
function consultarPromedioxDatafono(Request $request)
{
    $resultado = array();
    $resumen = array();
    $rangos = explode(" - ", $request->rango);
        list($dia1,$mes1,$ano1) = explode("/",$rangos[0]);
        $datetime1 = $ano1 . "-" . $mes1 . "-" .  $dia1;
        list($dia2,$mes2,$ano2) = explode("/",$rangos[1]);
        $datetime2 = $ano2 . "-" . $mes2 . "-" .  $dia2;
        $datetime1 = strtotime($datetime1);
        $datetime2 = strtotime($datetime2);
        $interval=abs ($datetime2 - $datetime1);
        $interval = $interval / (60 * 60 * 24 );
    $lista_esta = $request->establecimientos;
    if (sizeof($lista_esta) > 0) {
        $establecimientos = Establecimientos::wherein('id', $request->establecimientos)
            ->orderby('razon_social', 'asc')->get();
    } else {
        $establecimientos = Establecimientos::orderby('razon_social', 'asc')->get();
        $lista_esta = [];
        foreach ($establecimientos as $establecimiento) {
            array_push($lista_esta, $establecimiento->id);
        }
    }
    $sucursales = Sucursales::wherein('establecimiento_id', $lista_esta)
        ->orderby('nombre', 'asc')->get();
    if ($sucursales != null) {
            $lista_sucu = [];
            foreach ($sucursales as $sucursale)
            {
                array_push($lista_sucu, $sucursale->id);
            }
            $terminales = Terminales::wherein('sucursal_id', $lista_sucu)
                ->orderBy('codigo', 'asc')
                ->get();
            foreach ($terminales as $terminal) {
                    $subtotal = 0;
                    $contdias = 0;
                    $lasucursal = Sucursales::where('id',$terminal->sucursal_id)->first();
                    $dtransacciones = DetalleTransaccion::join('h_estado_transacciones', 'detalle_transacciones.transaccion_id', 'h_estado_transacciones.transaccion_id')
                        ->join('transacciones', 'detalle_transacciones.transaccion_id', 'transacciones.id')
                        ->where('h_estado_transacciones.estado', '<>', HEstadoTransaccion::$ESTADO_INACTIVO)
                        ->whereraw('"DETALLE_TRANSACCIONES"."TRANSACCION_ID" NOT IN (select "TRANSACCION_ID" FROM "H_ESTADO_TRANSACCIONES" WHERE "ESTADO"=?)',[HEstadoTransaccion::$ESTADO_INACTIVO])
                        ->where('transacciones.codigo_terminal', $terminal->codigo)
                        ->whereBetween('transacciones.fecha', [Carbon::createFromFormat("d/m/Y", $rangos[0]), Carbon::createFromFormat("d/m/Y", $rangos[1])])
                        ->select('transacciones.fecha as fecha', DB::raw('SUM(detalle_transacciones.valor) as venta'))
                        ->groupBy('detalle_transacciones.transaccion_id', 'transacciones.fecha', 'detalle_transacciones.valor')
                        ->orderBy('transacciones.fecha', 'asc')
                        ->get();
                    $fechaanterior = "";
                    $venta = 0;
                    foreach ($dtransacciones as $dtransaccione) {
                        $dt = Carbon::parse($dtransaccione->fecha);
                        $fechaactual = $dt->year . "-" . $dt->month . "-" . $dt->day;
                        if ($fechaanterior == "")
                            $fechaanterior = $fechaactual;
                        if ($fechaanterior != $fechaactual) {
                            //insertar registro con valor de $venta
                            $resultado[] = array('establecimiento' => $lasucursal->establecimiento_id,
                                'sucursal' => $terminal->sucursal_id,
                                'terminal' => $terminal->codigo,
                                'fecha' => $fechaanterior,
                                'venta' => $venta,
                            );
                            //tomar valores actuales
                            $venta = 0 + ($dtransaccione->venta);
                            $fechaanterior = $fechaactual;
                            $contdias++;
                            $subtotal+=$venta;
                        } else {
                            //sumar acumulado a $venta
                            $venta += $dtransaccione->venta;
                            //fechaanterior=actual
                            $fechaanterior = $fechaactual;
                        }
                    }
                    if($fechaanterior!="")
                    {
                        $resultado[] = array('establecimiento' => $lasucursal->establecimiento_id,
                            'sucursal' => $terminal->sucursal_id,
                            'terminal' => $terminal->codigo,
                            'fecha' => $fechaanterior,
                            'venta' => $venta,
                        );
                        $contdias++;
                        $subtotal+=$venta;
                    }
                if ($terminal->estado == Terminales::$ESTADO_TERMINAL_ACTIVA)
                    $name_estado = "Activo";
                else
                    $name_estado = "Inactivo";
                    if($interval<1)
                        $interval=1;
                    $promedio = $subtotal / $interval; //$contdias
                    $resumen[] = array('establecimiento' => $lasucursal->establecimiento_id,
                        'sucursal' => $terminal->sucursal_id,
                        'terminal' => $terminal->codigo,
                        'estado' => $name_estado,
                        'promedio' => $promedio,
                        'suma' => $subtotal,
                        'totaldias' => $contdias,
                    );
                }
        }
        $rango = ['fecha1' => $rangos[0], 'fecha2' => $rangos[1]];
        return view('reportes.promedioxdatafono.parcialresultadopxd', compact('resultado', 'lista_esta', 'establecimientos', 'sucursales', 'resumen','rango','terminales'));
}

/*
* FUNCION GENERAR PDF para promedio por datafonos por establecimiento
    * Exporta a pdf, promedio de ventas por datafonos por sucursal
    */
    public function pdfPromedioxDatafono(Request $request)
    {
        // dd($request->lista_esta);
        $establecimientos = Establecimientos::wherein('id', $request->lista_esta)->orderby('razon_social', 'asc')->get();
        $sucursales = Sucursales::wherein('establecimiento_id', $request->lista_esta)->orderby('nombre', 'asc')->get();
        $lista_sucu = [];
        foreach ($sucursales as $sucursale)
        {
            array_push($lista_sucu, $sucursale->id);
        }
        $terminales = Terminales::wherein('sucursal_id', $lista_sucu)
               ->orderBy('codigo', 'asc')
               ->get();

    //dd($establecimientos);
        $data = ['resultado' => $request->resultado, 'establecimientos' => $establecimientos, 'sucursales' => $sucursales, 'terminales' => $terminales, 'resumen' => $request->resumen, 'rango' => $request->fecha1 . " - " . $request->fecha2];
        $pdf = \PDF::loadView('reportes.promedioxdatafono.pdfpxd', $data);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('PromedioConsumo.pdf');
    }
    /*
     * FUNCION GENERAR EXCEL para promedio por datafono por establecimiento
     * Exporta a excel, promedio de ventas por datafono por sucursal
     */
    public function excelPromedioxDatafono(Request $request)
    {
        $establecimientos = Establecimientos::wherein('id', $request->lista_esta)->orderby('razon_social', 'asc')->get();
        $sucursales = Sucursales::wherein('establecimiento_id', $request->lista_esta)->orderby('nombre', 'asc')->get();
        $lista_sucu = [];
        foreach ($sucursales as $sucursale)
        {
            array_push($lista_sucu, $sucursale->id);
        }
        $terminales = Terminales::wherein('sucursal_id', $lista_sucu)
            ->orderBy('codigo', 'asc')
            ->get();
        \Excel::create('ExcelPromDatafonos', function ($excel) use ($request, $establecimientos, $sucursales, $terminales) {
            $resultado = $request->resultado;
            $resumen = $request->resumen;
            $rango = $request->fecha1 ." - ".$request->fecha2;
            $num_esta = 0;
            //FOR ESTABLECIMIENTOS POR CADA UNO CREAR UNA PESTAÑA
            if (sizeof($establecimientos) > 0) {
                foreach ($establecimientos as $establecimiento) {
                    $num_esta++;
                    $excel->sheet('Est' . $num_esta, function ($sheet) use ($resultado, $establecimiento, $sucursales, $resumen, $rango, $terminales) {
                        $haysucursal = 0;
                        $hayterminal=0;
                        $hoy = Carbon::now();
                        $objDrawing = new PHPExcel_Worksheet_Drawing;
                        $objDrawing->setPath(public_path('images/logo.png')); //your image path
                        $objDrawing->setHeight(50);
                        $objDrawing->setCoordinates('A1');
                        $objDrawing->setWorksheet($sheet);
                        $objDrawing->setOffsetY(10);
                        $sheet->setWidth(array(
                            'A' => 30,
                            'B' => 20,
                            'C' => 20,
                            'D' => 20,
                            'E' => 20,
                            'F' => 20,
                        ));
                        $sheet->setMergeColumn(array(
                            'columns' => array('A'),
                            'rows' => array(
                                array(1, 4),
                            )
                        ));
                        $sheet->row(1, array('', 'REPORTE DE CONSUMO PROMEDIO POR DATAFONO DEL ESTABLECIMIENTO: ' . $establecimiento->razon_social));
                        $sheet->row(1, function ($row) {
                            $row->setBackground('#4CAF50');
                        });
                        $sheet->cells('A1:A4', function ($cells) {
                            $cells->setBackground('#FFFFFF');
                        });
                        $sheet->setBorder('A1:A4', 'thin');
                        $sheet->row(3, array('', 'Fecha:', $hoy, '', ''));
                        $sheet->row(4, array('', 'Rango:', $rango, '', ''));
                        $fila = 5;
                        foreach ($sucursales as $sucursale) {
                            //$cant = 0;
                            if ($sucursale->establecimiento_id == $establecimiento->id) {
                                $haysucursal++;
                               // if (sizeof($resultado) > 0) {
                                    $subtotal = 0;
                                    $sheet->row($fila, array('Sucursal: ' . $sucursale->nombre));
                                    $sheet->row($fila, function ($row) {
                                        $row->setBackground('#4CAF50');
                                    });
                                    $fila++;
                                    foreach ($terminales as $terminale) {
                                        $cant = 0;
                                        if($sucursale->id == $terminale->sucursal_id) {
                                            $hayterminal++;
                                            $sheet->row($fila, array('Código del datafono: ' . $terminale->codigo));
                                            $sheet->row($fila, function ($row) {
                                                $row->setBackground('#4CAF50');
                                            });
                                            $fila++;
                                            $fila++;
                                            if (sizeof($resultado) > 0) {
                                            $sheet->row($fila, array('Fecha', 'Total'));
                                            $sheet->row($fila, function ($row) {
                                                $row->setBackground('#f2f2f2');
                                            });
                                            $fila++;
                                            foreach ($resultado as $miresul) {
                                                if ($miresul["establecimiento"] == $establecimiento->id && $miresul["sucursal"] == $sucursale->id && $miresul["terminal"]==$terminale->codigo) {
                                                    $cant++;
                                                    $venta_name = '$ '.number_format( $miresul["venta"], 2, ',', '.');
                                                    $sheet->row($fila, array($miresul["fecha"], $venta_name));
                                                    $fila++;
                                                }//cierra if
                                            } //cierra foreach
                                            if ($cant == 0)
                                                $sheet->row($fila, array('No hay registros'));
                                            $fila++;
                                            foreach ($resumen as $resum) {
                                                if ($resum['terminal'] == $terminale->codigo) {
                                                    $prom_name = '$ '.number_format( $resum["promedio"], 2, ',', '.');
                                                    $sheet->row($fila, array('Estado actual: ', $resum['estado'],'Promedio diario: ', $prom_name));
                                                    $sheet->row($fila, function ($row) {
                                                        $row->setBackground('#f2f2f2');
                                                    });
                                                    $fila++;
                                                    $fila++;
                                                }
                                            }
                                            }//cierra if

                                        }//cierra if (terminal)
                                    }//cierra for terminales
                                if ($hayterminal == 0)
                                    $sheet->row($fila, array('No existen datafonos'));
                            }//cierra if sucursal id
                        }//cierra foreach
                        if ($haysucursal == 0)
                            $sheet->row($fila, array('No existen sucursales'));
                    });        //CIERRA PESTAÑA
                } //finaliza foreach
            } //finaliza if
        })->export('xls');
    }
    /*
     * FINALIZA REPORTE CONSUMO PROMEDIO X DATAFONO POR ESTABLECIMIENTO
     */
    /*
     * INICIA REPORTE DE MONTOS USADOS
     */
    public function viewMontosUsados()
    {
        //$establecimientos = Establecimientos::pluck('razon_social', 'id');
        return view('reportes.montosusados.montosusados');
    }
    /*
     * Funcion consultar montos usados
     * - consultar productos de tarjetas que registran transacciones  dentro del rango de fecha seleccionado
     * -
     */
    public function consultarMontosUsados(Request $request)
    {
        $rangos = explode(" - ", $request->rango);
        $tiposervicio = $request->tipo;
        $resultadob = array();
        $resultador = array();
        $resumen = array();
        $subtotalb = 0;
        $subtotalr = 0;
        $total = 0;

        if ($tiposervicio == "B" || $tiposervicio == "T") {
            $detallesb = DetalleProdutos::where('contrato_emprs_id','<>',null)
                ->whereRaw('id IN (SELECT detalle_producto_id FROM detalle_transacciones WHERE transaccion_id IN (SELECT id FROM transacciones WHERE fecha between ? and ?) )',[Carbon::createFromFormat("d/m/Y", $rangos[0]), Carbon::createFromFormat("d/m/Y", $rangos[1])])
                ->where('estado', '<>', 'N')
                ->get();
            foreach ($detallesb as $detalle) {
                $gasto = 0;
                //buscar Duplicados
                $listado = [];
                array_push($listado, $detalle->id);
                $respuesta = $this->consultarDuplicadoProductos($detalle->id, $listado);
                if ($respuesta != null)
                    $listado = $respuesta;
                $dtransacciones = DetalleTransaccion::join('transacciones', 'detalle_transacciones.transaccion_id', 'transacciones.id')
                    ->wherein('detalle_transacciones.detalle_producto_id', $listado)
                    ->whereBetween('transacciones.fecha', [Carbon::createFromFormat("d/m/Y", $rangos[0]), Carbon::createFromFormat("d/m/Y", $rangos[1])])
                    ->get();
                foreach ($dtransacciones as $dtransaccione) {
                    $htransaccion = DB::table('h_estado_transacciones')->where('transaccion_id', $dtransaccione->transaccion_id)->orderBy('id', 'desc')->first();
                    if ($htransaccion->estado == HEstadoTransaccion::$ESTADO_ACTIVO)
                        $gasto += $dtransaccione->valor;
                }
                    $sobrante = $detalle->monto_inicial - $gasto;
                    if ($detalle->estado == DetalleProdutos::$ESTADO_ACTIVO)
                        $name_estado = "Activa";
                    else
                        $name_estado = "Inactiva";
                    $resultadob[] = array('numero_tarjeta' => $detalle->numero_tarjeta,
                        'monto' => $detalle->monto_inicial,
                        'gasto' => $gasto,
                        'saldo' => $sobrante,
                        'vencimiento' => $detalle->fecha_vencimiento,
                        'estado' => $name_estado,
                    );
                    $subtotalb++;
            }
        }
        if ($tiposervicio == "R" || $tiposervicio == "T") {
            $detallesr = DetalleProdutos::where('factura','<>',null)
                ->whereRaw('id IN (SELECT detalle_producto_id FROM detalle_transacciones WHERE transaccion_id IN (SELECT id FROM transacciones WHERE fecha between ? and ?) )',[Carbon::createFromFormat("d/m/Y", $rangos[0]), Carbon::createFromFormat("d/m/Y", $rangos[1])])
                ->where('estado', '<>', 'N')
                ->get();
            foreach ($detallesr as $detalle) {
                $gasto = 0;
                //buscar Duplicados
                $listado = [];
                array_push($listado, $detalle->id);
                $respuesta = $this->consultarDuplicadoProductos($detalle->id, $listado);
                if ($respuesta != null)
                    $listado = $respuesta;
                $dtransacciones = DetalleTransaccion::join('transacciones', 'detalle_transacciones.transaccion_id', 'transacciones.id')
                    ->wherein('detalle_transacciones.detalle_producto_id', $listado)
                    ->whereBetween('transacciones.fecha', [Carbon::createFromFormat("d/m/Y", $rangos[0]), Carbon::createFromFormat("d/m/Y", $rangos[1])])
                    ->get();
                foreach ($dtransacciones as $dtransaccione) {
                    $htransaccion = DB::table('h_estado_transacciones')->where('transaccion_id', $dtransaccione->transaccion_id)->orderBy('id', 'desc')->first();
                    if ($htransaccion->estado == HEstadoTransaccion::$ESTADO_ACTIVO)
                        $gasto += $dtransaccione->valor;
                }
                    $sobrante = $detalle->monto_inicial - $gasto;
                    if ($detalle->estado == DetalleProdutos::$ESTADO_ACTIVO)
                        $name_estado = "Activa";
                    else
                        $name_estado = "Inactiva";
                    $resultador[] = array('numero_tarjeta' => $detalle->numero_tarjeta,
                        'monto' => $detalle->monto_inicial,
                        'gasto' => $gasto,
                        'saldo' => $sobrante,
                        'vencimiento' => $detalle->fecha_vencimiento,
                        'estado' => $name_estado,
                    );
                    $subtotalr++;
            }
        }
        $total = $subtotalb + $subtotalr;
        $resumen[] = array('totalr' => $subtotalr,
            'totalb' => $subtotalb,
            'total' => $total,
        );
        $rango = ['fecha1' => $rangos[0], 'fecha2' => $rangos[1]];
        return view('reportes.montosusados.parcialresultadomontosusados', compact('resultadob', 'resultador', 'rango', 'tiposervicio', 'resumen'));
    }
    /*
     * FUNCION GENERAR PDF para montos Usados
     * Exporta en formato pdf, los resultados
     * de productos de tarjetas que tuvieron transacciones en un rango de fecha
     */
    public function pdfMontosUsados(Request $request)
    {
        $data = ['resultadob' => $request->resultadob, 'resultador' => $request->resultador, 'tiposervicio' => $request->tiposervicio, 'rango' => $request->fecha1 . " - " . $request->fecha2, 'resumen' => $request->resumen];
        $pdf = \PDF::loadView('reportes.montosusados.pdfmontosusados', $data);

        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('montosusados.pdf');
    }

    /*
     * FUNCION GENERAR EXCEL para Montos Usados
     * Exporta a excel, los resultados de las tarjetas (los productos)
     *  que tuvieron transacciones en un rango de fecha seleccionado
     */
    public function excelMontosUsados(Request $request)
    {
        \Excel::create('ExcelMontosUsados', function ($excel) use ($request) {
            $resultadob = $request->resultadob;
            $resultador = $request->resultador;
            $resumen = $request->resumen;
            $fecha1 = $request->fecha1;
            $fecha2 = $request->fecha2;
            $rango = $fecha1 . " - " . $fecha2;
            $tiposervicio = $request->tiposervicio;

            $excel->sheet('MontosUsados', function ($sheet) use ($resultadob, $resultador, $rango, $tiposervicio, $resumen) {
                $hoy = Carbon::now();
                $objDrawing = new PHPExcel_Worksheet_Drawing;
                $objDrawing->setPath(public_path('images/logo.png')); //your image path
                $objDrawing->setHeight(50);
                $objDrawing->setCoordinates('A1');
                $objDrawing->setWorksheet($sheet);
                $objDrawing->setOffsetY(10);
                $sheet->setWidth(array(
                    'A' => 30,
                    'B' => 20,
                    'C' => 20,
                    'D' => 20,
                    'E' => 20,
                    'F' => 20,
                ));
                $sheet->setMergeColumn(array(
                    'columns' => array('A'),
                    'rows' => array(
                        array(1, 4),
                    )
                ));
                $sheet->row(2, array('', 'REPORTE DE MONTOS USADOS'));
                $sheet->row(2, function ($row) {
                    $row->setBackground('#4CAF50');
                });
                $sheet->cells('A1:A4', function ($cells) {
                    $cells->setBackground('#FFFFFF');
                });
                $sheet->setBorder('A1:A4', 'thin');
                $sheet->row(3, array('', 'Rango:', $rango, '', ''));
                $sheet->row(4, array('', 'Fecha:', $hoy, '', ''));
                if ($tiposervicio != "R") {
                    $sheet->row(6, array('TARJETAS BONO'));
                    $sheet->row(6, function ($row) {
                        $row->setBackground('#4CAF50');
                    });
                    $fila = 8;
                    if (sizeof($resultadob) > 0) {
                        $sheet->row(7, array('Número tarjeta', 'Monto inicial', 'Monto usado', 'Saldo', 'Fecha vencimiento', 'Estado'));
                        $sheet->row(7, function ($row) {
                            $row->setBackground('#f2f2f2');
                        });
                        foreach ($resultadob as $miresul) {
                            $monto_name = '$ '.number_format( $miresul["monto"], 2, ',', '.');
                            $gasto_name = '$ '.number_format( $miresul["gasto"], 2, ',', '.');
                            $saldo_name = '$ '.number_format( $miresul["saldo"], 2, ',', '.');
                            $sheet->row($fila, array($miresul["numero_tarjeta"], $monto_name, $gasto_name, $saldo_name, $miresul["vencimiento"], $miresul["estado"]));
                            $fila++;
                        }
                    } else
                        $sheet->row($fila, array('No hay resultados'));
                    $fila++;
                    foreach($resumen as $resum) {
                        $sheet->row($fila, array("Total productos de tarjeta bono:", $resum["totalb"]));
                        $sheet->row($fila, function ($row) {
                            $row->setBackground('#f2f2f2');
                        });
                    }
                    $fila++;
                    $fila++;
                } else
                    $fila = 6;
                if ($tiposervicio != "B") {
                    $sheet->row($fila, array('TARJETAS REGALO'));
                    $sheet->row($fila, function ($row) {
                        $row->setBackground('#4CAF50');
                    });
                    $fila++;
                    if (sizeof($resultador) > 0) {
                        $sheet->row($fila, array('Número tarjeta', 'Monto inicial', 'Monto usado', 'Saldo', 'Fecha vencimiento', 'Estado'));
                        $sheet->row($fila, function ($row) {
                            $row->setBackground('#f2f2f2');
                        });
                        $fila++;
                        foreach ($resultador as $miresul) {
                            $monto_name = '$ '.number_format( $miresul["monto"], 2, ',', '.');
                            $gasto_name = '$ '.number_format( $miresul["gasto"], 2, ',', '.');
                            $saldo_name = '$ '.number_format( $miresul["saldo"], 2, ',', '.');
                            $sheet->row($fila, array($miresul["numero_tarjeta"], $monto_name, $gasto_name, $saldo_name, $miresul["vencimiento"], $miresul["estado"]));
                            $fila++;
                        }
                    } else
                        $sheet->row($fila, array('No hay resultados'));
                    $fila++;
                    foreach($resumen as $resum) {
                        $sheet->row($fila, array("Total productos de tarjeta regalo:", $resum["totalr"]));
                        $sheet->row($fila, function ($row) {
                            $row->setBackground('#f2f2f2');
                        });
                    }
                    $fila++;
                    $fila++;
                }
                foreach($resumen as $resum) {
                    $sheet->row($fila, array("Total productos encontrados:", $resum["total"]));
                    $sheet->row($fila, function ($row) {
                        $row->setBackground('#f2f2f2');
                    });
                }
            });
        })->export('xls');
    }
    /*
     * FINALIZA REPORTES MONTOS USADOS
     */

    /*
    * INICIA REPORTE VENTAS POR SUCURSAL
    */
    public function viewVentasxSucursal()
    {
        $establecimientos = Establecimientos::pluck('razon_social', 'id');
        return view('reportes.ventasxsucursal.ventasxsucursal', compact('establecimientos'));
    }
    /*
     * Funcion consultar ventas x sucursal
     * -
     */
    public function consultarVentasxSucursal(Request $request)
    {
        $rangos = explode(" - ", $request->rango);
        $resultado = array();
        $lista_esta = $request->establecimientos;
        if (sizeof($lista_esta) > 0) {
            $establecimientos = Establecimientos::wherein('id', $request->establecimientos)
                ->orderby('razon_social', 'asc')->get();
        } else {
            $establecimientos = Establecimientos::orderby('razon_social', 'asc')->get();
            $lista_esta = [];
            foreach ($establecimientos as $establecimiento) {
                array_push($lista_esta, $establecimiento->id);
            }
        }
        $sucursales = Sucursales::wherein('establecimiento_id', $lista_esta)
            ->orderby('nombre', 'asc')->get();//$request->establecimientos
        if ($sucursales != null) {
            foreach ($sucursales as $sucursale) {
                $dtransacciones = DetalleTransaccion::join('h_estado_transacciones', 'detalle_transacciones.transaccion_id', 'h_estado_transacciones.transaccion_id')
                    ->join('transacciones', 'detalle_transacciones.transaccion_id', 'transacciones.id')
                    ->where('h_estado_transacciones.estado', '<>', HEstadoTransaccion::$ESTADO_INACTIVO)
                    ->whereraw('"DETALLE_TRANSACCIONES"."TRANSACCION_ID" NOT IN (select "TRANSACCION_ID" FROM "H_ESTADO_TRANSACCIONES" WHERE "ESTADO"=?)', [HEstadoTransaccion::$ESTADO_INACTIVO])
                    ->where('transacciones.sucursal_id', $sucursale->id)
                    ->whereBetween('transacciones.fecha', [Carbon::createFromFormat("d/m/Y", $rangos[0]), Carbon::createFromFormat("d/m/Y", $rangos[1])])
                    ->select('transacciones.fecha as fecha', DB::raw('SUM(detalle_transacciones.valor) as venta'))
                    ->groupBy('detalle_transacciones.transaccion_id', 'transacciones.fecha', 'detalle_transacciones.valor')
                    ->orderBy('transacciones.fecha', 'asc')
                    ->get();
                $fechaanterior = "";
                $venta = 0;
                $cantidad = 0;
                foreach ($dtransacciones as $dtransaccione) {
                    $cantidad++;
                    $venta +=  $dtransaccione->venta;
                }
                $resultado[] = array('establecimiento' => $sucursale->establecimiento_id,
                    'sucursal' => $sucursale->id,
                    'cantidad' => $cantidad,
                    'venta' => $venta,
                );
            }
        }
        // dd($resultado);
        $rango = ['fecha1' => $rangos[0], 'fecha2' => $rangos[1]];
        return view('reportes.ventasxsucursal.parcialventasxsucursal', compact('resultado', 'rango', 'lista_esta', 'establecimientos', 'sucursales'));
    }
    /*
    * FUNCION GENERAR PDF para ventas por sucursal
    * Exporta a pdf, los resultados de las ventas por sucursal
    */
    public function pdfVentasxSucursal(Request $request)
    {
        $establecimientos = Establecimientos::wherein('id', $request->lista_esta)->orderby('razon_social', 'asc')->get();
        $sucursales = Sucursales::wherein('establecimiento_id', $request->lista_esta)->orderby('nombre', 'asc')->get();
        $data = ['resultado' => $request->resultado, 'establecimientos' => $establecimientos, 'sucursales' => $sucursales, 'rango' => $request->fecha1 . " - " . $request->fecha2];
        $pdf = \PDF::loadView('reportes.ventasxsucursal.pdfventasxsucursal', $data);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('ventasxsucursal.pdf');
    }
    /*
     * FUNCION GENERAR EXCEL para ventas por sucursal
     * Exporta a excel, los resultados de las ventas por sucursal
     */
    public function excelVentasxSucursal(Request $request)
    {
        $establecimientos = Establecimientos::wherein('id', $request->lista_esta)->orderby('razon_social', 'asc')->get();
        $sucursales = Sucursales::wherein('establecimiento_id', $request->lista_esta)->orderby('nombre', 'asc')->get();
        $canttotal = 5;
        $ventatotal = 0;
        \Excel::create('ExcelVentasxSucursal', function ($excel) use ($request, $establecimientos, $sucursales, $canttotal, $ventatotal) {
            $resultado = $request->resultado;
            $fecha1 = $request->fecha1;
            $fecha2 = $request->fecha2;
            $rango = $fecha1 . " - " . $fecha2;
            $num_esta = 0;
            //FOR ESTABLECIMIENTOS POR CADA UNO CREAR UNA PESTAÑA
            if (sizeof($establecimientos) > 0) {

                foreach ($establecimientos as $establecimiento) {
                    $num_esta++;
                    //titulo <h5>Establecimiento: {{$establecimiento->razon_social}}</h5>
                    $excel->sheet('Est' . $num_esta, function ($sheet) use ($resultado, $establecimiento, $sucursales, $rango, $canttotal, $ventatotal) {
                        $haysucursal = 0;
                        $cantest = 0;
                        $ventaest = 0;
                        $hoy = Carbon::now();
                        $objDrawing = new PHPExcel_Worksheet_Drawing;
                        $objDrawing->setPath(public_path('images/logo.png')); //your image path
                        $objDrawing->setHeight(50);
                        $objDrawing->setCoordinates('A1');
                        $objDrawing->setWorksheet($sheet);
                        $objDrawing->setOffsetY(10);
                        $sheet->setWidth(array(
                            'A' => 30,
                            'B' => 20,
                            'C' => 20,
                            'D' => 20,
                            'E' => 20,
                            'F' => 20,
                        ));
                        $sheet->setMergeColumn(array(
                            'columns' => array('A'),
                            'rows' => array(
                                array(1, 4),
                            )
                        ));
                        $sheet->row(2, array('', 'REPORTE DE VENTAS PARA ESTABLECIMIENTO ' . $establecimiento->razon_social . ' - NIT: ' . $establecimiento->nit));
                        $sheet->row(2, function ($row) {
                            $row->setBackground('#4CAF50');
                        });
                        $sheet->cells('A1:A4', function ($cells) {
                            $cells->setBackground('#FFFFFF');
                        });
                        $sheet->setBorder('A1:A4', 'thin');
                        $sheet->row(3, array('', 'Rango:', $rango, '', ''));
                        $sheet->row(4, array('', 'Fecha:', $hoy, '', ''));
                        $fila = 6;
                        foreach ($sucursales as $sucursale) {
                            $cant = 0;
                            if ($sucursale->establecimiento_id == $establecimiento->id) {
                                $haysucursal++;
                                if (sizeof($resultado) > 0) {
                                    $subtotal = 0;
                                    $sheet->row($fila, array('Sucursal','Cantidad de transacciones', 'Monto'));
                                    $sheet->row($fila, function ($row) {
                                        $row->setBackground('#f2f2f2');
                                    });
                                    $fila++;
                                    foreach ($resultado as $miresul) {
                                        if ($miresul["establecimiento"] == $establecimiento->id && $miresul["sucursal"] == $sucursale->id) {
                                            $cant++;
                                            $venta_name = '$ ' . number_format($miresul["venta"], 2, ',', '.');
                                            $sheet->row($fila, array($sucursale->nombre, $miresul["cantidad"], $venta_name));
                                            $fila++;
                                            $cantest += $miresul["cantidad"];
                                            $ventaest += $miresul["venta"];
                                        }//cierra if
                                    } //cierra foreach
                                    $ventaest_name = '$ '.number_format( $ventaest, 2, ',', '.');
                                    $canttotal = $canttotal + $cantest;
                                    $ventatotal = $ventatotal + $ventaest;
                                    $sheet->row($fila, array('Total', $cantest, $ventaest_name));
                                    $fila++;
                                    $fila++;
                                    //finaliza tabla
                                }//cierra if
                                if ($cant == 0)
                                    $sheet->row($fila, array('No hay registros'));
                            }//cierra if sucursal id
                        }//cierra foreach
                        if ($haysucursal == 0)
                            $sheet->row($fila, array('No existen sucursales'));
                        /// mostrar No existen sucursales
                    });        //CIERRA PESTAÑA
                } //finaliza foreach
                /*$excel->sheet('Resumen', function ($sheet) use ($resultado, $establecimiento, $sucursales, $rango, $canttotal, $ventatotal, $establecimientos) {
                $ventatotal_name = '$ '.number_format( $ventatotal, 2, ',', '.');
                $sheet->row(4, array('Total establecimientos','Total sucursales','Cantidad de transacciones', 'Monto'));
                $sheet->row(4, function ($row) {
                    $row->setBackground('#f2f2f2');
                });
                    $sheet->row(5, array(sizeof($establecimientos),sizeof($sucursales),$canttotal, $ventatotal_name));
                });        //CIERRA PESTAÑA*/
            } //finaliza if
        })->export('xls');
    }

    /*
     * FINALIZA REPORTE VENTAS POR SUCURSAL
     */

}
