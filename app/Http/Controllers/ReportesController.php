<?php

namespace creditocofrem\Http\Controllers;

use Carbon\Carbon;
use creditocofrem\DetalleProdutos;
use creditocofrem\DetalleTransaccion;
use creditocofrem\Establecimientos;
use creditocofrem\HEstadoTransaccion;
use creditocofrem\Servicios;
use creditocofrem\Sucursales;
use creditocofrem\Tarjetas;
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
        $transacciones = Transaccion::whereBetween('fecha', [Carbon::createFromFormat("d/m/Y", $rangos[0]), Carbon::createFromFormat("d/m/Y", $rangos[1])])
            ->where('tipo', 'C')
            ->groupBy('numero_tarjeta')
            ->select('numero_tarjeta')
            ->get();
        $arrayTarjetas = [];
        foreach ($transacciones as $transaccione) {
            array_push($arrayTarjetas, $transaccione->numero_tarjeta);
        }
        $transacciones2 = Transaccion::whereDate('fecha', '<', Carbon::createFromFormat("d/m/Y", $rangos[0])->addDays(-1))
            ->whereIn('numero_tarjeta', $arrayTarjetas)
            ->where('tipo', 'C')
            ->groupBy('transacciones.numero_tarjeta')
            ->select('transacciones.numero_tarjeta')
            ->get();
        $arrayTarjetas2 = [];
        foreach ($transacciones2 as $transaccion) {
            array_push($arrayTarjetas2, $transaccion->numero_tarjeta);
        }
        $arrayFinal = [];
        foreach ($arrayTarjetas as $arrayTarjeta) {
            if (!in_array($arrayTarjeta, $arrayTarjetas2)) {
                array_push($arrayFinal, $arrayTarjeta);
            }
        }
        $arrayIds = [];
        foreach ($arrayFinal as $item) {
            $id = Transaccion::where('numero_tarjeta', $item)
                ->where('tipo', 'C')
                ->orderBy('fecha', 'asc')
                ->select('id')
                ->first();
            array_push($arrayIds, $id->id);
        }
        $transacciones = Transaccion::whereIn('id', $arrayIds)
            ->where('tipo', 'C')
            ->get();
        $rango = ['fecha1' => $rangos[0], 'fecha2' => $rangos[1]];
        return view('reportes.primeravez.resultadoprimeravez', compact('transacciones', 'rango'));
    }

    /**
     * permite exportar a pdf el resultado de las tarjetas usadas por primera vez
     * @param Request $request
     * @return mixed
     */
    public function exportarPdfPrimeravez(Request $request)
    {
        $transacciones = Transaccion::whereBetween('fecha', [Carbon::createFromFormat("d/m/Y", $request->fecha1), Carbon::createFromFormat("d/m/Y", $request->fecha2)])
            ->where('tipo', 'C')
            ->groupBy('numero_tarjeta')
            ->select('numero_tarjeta')
            ->get();
        $arrayTarjetas = [];
        foreach ($transacciones as $transaccione) {
            array_push($arrayTarjetas, $transaccione->numero_tarjeta);
        }
        $transacciones2 = Transaccion::whereDate('fecha', '<', Carbon::createFromFormat("d/m/Y", $request->fecha1)->addDays(-1))
            ->whereIn('numero_tarjeta', $arrayTarjetas)
            ->where('tipo', 'C')
            ->groupBy('transacciones.numero_tarjeta')
            ->select('transacciones.numero_tarjeta')
            ->get();
        $arrayTarjetas2 = [];
        foreach ($transacciones2 as $transaccion) {
            array_push($arrayTarjetas2, $transaccion->numero_tarjeta);
        }
        $arrayFinal = [];
        foreach ($arrayTarjetas as $arrayTarjeta) {
            if (!in_array($arrayTarjeta, $arrayTarjetas2)) {
                array_push($arrayFinal, $arrayTarjeta);
            }
        }
        $arrayIds = [];
        foreach ($arrayFinal as $item) {
            $id = Transaccion::where('numero_tarjeta', $item)
                ->where('tipo', 'C')
                ->orderBy('fecha', 'asc')
                ->select('id')
                ->first();
            array_push($arrayIds, $id->id);
        }
        $transacciones = Transaccion::whereIn('id', $arrayIds)
            ->where('tipo', 'C')
            ->get();
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
        $transacciones = Transaccion::whereBetween('fecha', [Carbon::createFromFormat("d/m/Y", $request->fecha1), Carbon::createFromFormat("d/m/Y", $request->fecha2)])
            ->where('tipo', 'C')
            ->groupBy('numero_tarjeta')
            ->select('numero_tarjeta')
            ->get();
        $arrayTarjetas = [];
        foreach ($transacciones as $transaccione) {
            array_push($arrayTarjetas, $transaccione->numero_tarjeta);
        }
        $transacciones2 = Transaccion::whereDate('fecha', '<', Carbon::createFromFormat("d/m/Y", $request->fecha1)->addDays(-1))
            ->whereIn('numero_tarjeta', $arrayTarjetas)
            ->where('tipo', 'C')
            ->groupBy('transacciones.numero_tarjeta')
            ->select('transacciones.numero_tarjeta')
            ->get();
        $arrayTarjetas2 = [];
        foreach ($transacciones2 as $transaccion) {
            array_push($arrayTarjetas2, $transaccion->numero_tarjeta);
        }
        $arrayFinal = [];
        foreach ($arrayTarjetas as $arrayTarjeta) {
            if (!in_array($arrayTarjeta, $arrayTarjetas2)) {
                array_push($arrayFinal, $arrayTarjeta);
            }
        }
        $arrayIds = [];
        foreach ($arrayFinal as $item) {
            $id = Transaccion::where('numero_tarjeta', $item)
                ->where('tipo', 'C')
                ->orderBy('fecha', 'asc')
                ->select('id')
                ->first();
            array_push($arrayIds, $id->id);
        }
        $transacciones = Transaccion::whereIn('id', $arrayIds)
            ->where('tipo', 'C')
            ->get();


        \Excel::create('ExcelTarjetasPrimeraVez', function ($excel) use ($request, $transacciones) {
            $fecha1 = $request->fecha1;
            $fecha2 = $request->fecha2;
            $rango = $fecha1 . " - " . $fecha2;
            $transacciones;
            $excel->sheet('tarjetaPrimeraVez', function ($sheet) use ($transacciones, $rango) {
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
                if (sizeof($transacciones) > 0) {
                    $sheet->row(7, array('Numero tarjeta', 'TransacciÃ³n', 'Valor', 'Sucursal', 'Terminal', 'Fecha'));
                    $sheet->row(7, function ($row) {
                        $row->setBackground('#f2f2f2');
                    });
                    foreach ($transacciones as $miresul) {
                        $sheet->row($fila,
                            array($miresul->numero_tarjeta,
                                $miresul->numero_transaccion,
                                $miresul->valorTransacion[0]->total,
                                $miresul->getSucursal->nombre,
                                $miresul->codigo_terminal,
                                $miresul->fecha,
                            ));
                        $fila++;
                    }
                } else
                    $sheet->row($fila, array('No hay resultados'));
                $fila++;
                $fila++;
            });
        })->export('xls');

    }

    /**
     * trae la vista para generar el reporte de servicios activados descriminados por fecha de activacion y montos
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewReporteMontos()
    {
        $servicios = Servicios::pluck('descripcion', 'codigo');
        return view('reportes.montostarjetas.motosportarjetas', compact('servicios'));
    }

    /**
     * genera la previzualizacion del reporte de servicios activados descriminados por fecha de activacion y montos
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function resultadoMontosTarjetas(Request $request)
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
    public function exportarPdfMotosPorTarjeta(Request $request)
    {
        $rangos = [$request->fecha1,$request->fecha2];
        $data = $this->dataReporteActivadosPorMontos($rangos, $request);
        $data = array_merge($data, ['rango' => $request->fecha1 . " - " . $request->fecha2]);
        $pdf = \PDF::loadView('reportes.montostarjetas.pdfmontosportarjeta', $data);
        //$pdf->setPaper('A4', 'landscape');
        return $pdf->download('activacionesporponto - '.Carbon::now()->format('d-m-Y').'.pdf');
    }

    /**
     * METODO QUE PERMITE EXPORTAR A EXCEL EL REPORTE DE SERVICIOS ACTIVADOS POR FECGA DE ACTIVACION Y MONTOS
     * @param Request $request
     */
    public function exportarExcelMontosPorTarjeta(Request $request){
        $rangos = [$request->fecha1,$request->fecha2];
        $data = $this->dataReporteActivadosPorMontos($rangos, $request);
        \Excel::create('ExcelMontosPorTarjeta', function ($excel) use ($request, $data) {
            $fecha1 = $request->fecha1;
            $fecha2 = $request->fecha2;
            $rango = $fecha1 . " - " . $fecha2;
            if(count($data['regalo'])>0){
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
                        foreach ($data['regalo']['detalles'] as $detalles){
                            foreach ($detalles as $montos){
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
                                foreach ($montos as $monto){
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
                    } else
                        $sheet->row($fila, array('No hay resultados'));
                    $fila++;
                    $fila++;
                });
            }
            if(count($data['bono'])>0){
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
                        foreach ($data['bono']['detalles'] as $detalles){
                            foreach ($detalles as $montos){
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
                                foreach ($montos as $monto){
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
    private function dataReporteActivadosPorMontos($rangos, $request)
    {
        $fechaini = "";
        $regalo = [];
        $bono = [];
        if ($request->servicios == 'T') {
            $montosregalo = DetalleProdutos::where('factura', '<>', NULL)
                ->whereBetween('fecha_activacion', [Carbon::createFromFormat("d/m/Y", $rangos[0]), Carbon::createFromFormat("d/m/Y", $rangos[1])])
                ->where('estado', DetalleProdutos::$ESTADO_ACTIVO)
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
                            ->where('estado', DetalleProdutos::$ESTADO_ACTIVO)
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
                ->where('estado', DetalleProdutos::$ESTADO_ACTIVO)
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
                            ->where('estado', DetalleProdutos::$ESTADO_ACTIVO)
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
                ->where('estado', DetalleProdutos::$ESTADO_ACTIVO)
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
                            ->where('estado', DetalleProdutos::$ESTADO_ACTIVO)
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
                ->where('estado', DetalleProdutos::$ESTADO_ACTIVO)
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
                            ->where('estado', DetalleProdutos::$ESTADO_ACTIVO)
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
    public function viewSaldosvencidos()
    {
        return view('reportes.saldosvencidos.saldosvencidos');
    }

    /*
     * Funcion consultar saldos vencidos
     * - segun el tipo: consultar tarjetas bono, regalo o las dos.
     * -
     */
    public function consultarSaldosVencidos(Request $request)
    {
        $rangos = explode(" - ", $request->rango);
        $tiposervicio = $request->tipo;
        $resultadob = array();
        $resultador = array();
        if ($tiposervicio == "B" || $tiposervicio == "T") {
            $detallesb = DetalleProdutos::join('tarjeta_servicios', 'detalle_produtos.numero_tarjeta', 'tarjeta_servicios.numero_tarjeta')
                ->whereBetween('detalle_produtos.fecha_vencimiento', [Carbon::createFromFormat("d/m/Y", $rangos[0]), Carbon::createFromFormat("d/m/Y", $rangos[1])])
                ->where('tarjeta_servicios.servicio_codigo', Tarjetas::$CODIGO_SERVICIO_BONO)
                ->select('detalle_produtos.*')
                ->get();
            $listaDetallesb = [];
            foreach ($detallesb as $detalle) {
                $gasto = 0;
                $dtransacciones = DetalleTransaccion::where('detalle_producto_id', $detalle->id)->get();
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
                ->select('detalle_produtos.*')
                ->get();
            foreach ($detallesr as $detalle) {
                $gasto = 0;
                $dtransacciones = DetalleTransaccion::where('detalle_producto_id', $detalle->id)->get();
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
    public function pdfSaldosVencidos(Request $request)
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
    public function excelSaldosVencidos(Request $request)
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
                $objDrawing->setPath(public_path('images/logo_mini.png')); //your image path
                $objDrawing->setCoordinates('A1');
                $objDrawing->setWorksheet($sheet);
                $sheet->setWidth(array(
                    'A' => 30,
                    'B' => 20,
                    'C' => 20,
                    'D' => 20,
                    'E' => 20,
                ));

                $sheet->row(2, array('', 'REPORTE DE SALDOS VENCIDOS'));
                $sheet->row(2, function ($row) {
                    $row->setBackground('#4CAF50');
                });

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
    public function viewVentasDiarias()
    {
        $establecimientos=Establecimientos::pluck('razon_social', 'id');
        return view('reportes.ventasdiariasxestablecimiento.ventasdiarias',compact('establecimientos'));
    }
    /*
     * Funcion consultar saldos vencidos
     * - segun el tipo: consultar tarjetas bono, regalo o las dos.
     * -
     */
    public function consultarVentasDiarias(Request $request)
    {
        $rangos = explode(" - ", $request->rango);
        $resultado=array();
        $lista_esta=$request->establecimientos;
        $establecimientos=Establecimientos::wherein('id',$request->establecimientos)
            ->orderby('razon_social','asc')->get();
        $sucursales=Sucursales::wherein('establecimiento_id',$request->establecimientos)
            ->orderby('nombre','asc')->get();
        if($sucursales!=null)
        {
            foreach ($sucursales as $sucursale) {

                $dtransacciones = DetalleTransaccion::join('h_estado_transacciones','detalle_transacciones.transaccion_id','h_estado_transacciones.transaccion_id')
                    ->join('transacciones','detalle_transacciones.transaccion_id','transacciones.id')
                    ->where('h_estado_transacciones.estado','!=', HEstadoTransaccion::$ESTADO_INACTIVO)
                    ->where('transacciones.sucursal_id',$sucursale->id)
                    ->whereBetween('transacciones.fecha', [Carbon::createFromFormat("d/m/Y", $rangos[0]), Carbon::createFromFormat("d/m/Y", $rangos[1])])
                    ->select('transacciones.fecha as fecha', DB::raw('SUM(detalle_transacciones.valor) as venta'))
                    ->groupBy('detalle_transacciones.transaccion_id','transacciones.fecha','detalle_transacciones.valor')
                    ->orderBy('transacciones.fecha', 'asc')
                    ->get();
                $fechaanterior="";
                $venta=0;
                foreach ($dtransacciones as $dtransaccione) {
                    $fechaactual=$dtransaccione->fecha;
                    if($fechaanterior=="")
                        $fechaanterior=$fechaactual;
                    if($fechaanterior != $fechaactual)
                    {
                        //insertar registro con valor de $venta
                        $resultado[] = array('establecimiento' => $sucursale->establecimiento_id,
                            'sucursal' => $sucursale->id,
                            'fecha' => $fechaanterior,
                            'venta' => $venta,
                        );
                        //tomar valores actuales
                        $venta=0+($dtransaccione->venta);
                        $fechaanterior=$fechaactual;
                    }
                    else{
                        //sumar acumulado a $venta
                        $venta+=$dtransaccione->venta;
                        //fechaanterior=actual
                        $fechaanterior=$fechaactual;
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
        $rango =['fecha1'=> $rangos[0], 'fecha2'=>$rangos[1]];
        return view('reportes.ventasdiariasxestablecimiento.parcialventasdiarias', compact('resultado','rango','lista_esta','establecimientos','sucursales'));
    }
    public function selectestablecimientos(Request $request)
    {
       // $establecimientos=Establecimientos::pluck('razon_social', 'id');
        $variable=strtoupper($request->term);
        //dd($variable);
        $establecimientos = Establecimientos::where('razon_social', 'like', '%'.$variable.'%')->get();
        return $establecimientos;
    }
    /*
    * FUNCION GENERAR PDF para ventas diarias por establecimiento
    * Exporta a pdf, los resultados por dia de las ventas por sucursal
    */
    public function pdfVentasDiarias(Request $request)
    {
       // dd($request->lista_esta);
        $establecimientos=Establecimientos::wherein('id',$request->lista_esta)->orderby('razon_social','asc')->get();
        $sucursales=Sucursales::wherein('establecimiento_id',$request->lista_esta)->orderby('nombre','asc')->get();
        //dd($establecimientos);
        $data = ['resultado'=>$request->resultado, 'establecimientos'=>$establecimientos, 'sucursales'=>$sucursales, 'rango'=>$request->fecha1." - ".$request->fecha2];
        $pdf = \PDF::loadView('reportes.ventasdiariasxestablecimiento.pdfventasdiarias', $data);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('ventasdiarias.pdf');
    }
    /*
     * FUNCION GENERAR EXCEL para ventas diarias por establecimiento
     * Exporta a excel, los resultados por dia de las ventas por sucursal
     */
    public function excelVentasDiarias(Request $request)
    {
        $establecimientos=Establecimientos::wherein('id',$request->lista_esta)->orderby('razon_social','asc')->get();
        $sucursales=Sucursales::wherein('establecimiento_id',$request->lista_esta)->orderby('nombre','asc')->get();

        \Excel::create('ExcelVentasDiarias', function($excel) use($request,$establecimientos,$sucursales) {
            $resultado = $request->resultado;
            $fecha1= $request->fecha1;
            $fecha2= $request->fecha2;
            $rango=$fecha1." - ".$fecha2;
            $num_esta=0;
            //FOR ESTABLECIMIENTOS POR CADA UNO CREAR UNA PESTAÃ‘A
            if(sizeof($establecimientos)>0)
            {
                foreach($establecimientos as $establecimiento)
                {
                    $num_esta++;
                        //titulo <h5>Establecimiento: {{$establecimiento->razon_social}}</h5>
                        $excel->sheet('Est'.$num_esta, function($sheet) use($resultado, $establecimiento, $sucursales, $rango)
                        {
                            $haysucursal=0;
                        $hoy=Carbon::now();
                        $objDrawing = new PHPExcel_Worksheet_Drawing;
                        $objDrawing->setPath(public_path('images/logo_mini.png')); //your image path
                        $objDrawing->setCoordinates('A1');
                        $objDrawing->setWorksheet($sheet);
                        $sheet->setWidth(array(
                            'A'     =>  30,
                            'B'     =>  20,
                            'C'     =>  20,
                            'D'     =>  10,
                            'E'     =>  10,
                            'F'     =>  10,
                        ));
                        $sheet->row(2, array('','REPORTE DE VENTAS DIARIAS PARA ESTABLECIMIENTO '.$establecimiento->razon_social));
                        $sheet->row(2, function ($row) {
                            $row->setBackground('#4CAF50');
                        });

                        $sheet->row(3, array('','Rango:',$rango,'',''));
                        $sheet->row(4, array('','Fecha:',$hoy,'',''));
                        $fila=6;
                    foreach($sucursales as $sucursale)
                    {
                        $cant=0;
                        if($sucursale->establecimiento_id==$establecimiento->id)
                        {
                            $haysucursal++;
                            //titulo <h5>{{$sucursale->nombre}}</h5>
                            if (sizeof($resultado) > 0)
                            {
                                //inicia tabla
                                /*campos:
                                    <th>Fecha</th>
                                    <th>Venta</th>*/
                                $subtotal = 0;
                                $sheet->row($fila, array('Sucursal: '.$sucursale->nombre));
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
                                foreach ($resultado as $miresul)
                                {
                                    if ($miresul["establecimiento"] == $establecimiento->id && $miresul["sucursal"] == $sucursale->id)
                                    {
                                        $cant++;
                                        //insertar los valores
                                        /*<td>{{$miresul["fecha"]}}</td>
                                        <td>{{$miresul["venta"]}}</td>
                                        $subtotal+=$miresul["venta"];  */
                                            $sheet->row($fila, array($miresul["fecha"], $miresul["venta"]));
                                            $fila++;
                                            $subtotal += $miresul["venta"];
                                        /*} else
                                            $sheet->row($fila, array('No hay resultados'));*/

                                    }//cierra if

                                } //cierra foreach
                                $sheet->row($fila, array('Total', $subtotal));
                                $fila++;
                                $fila++;
                                //mostrar subtotal $subtotal
                                //finaliza tabla
                            }//cierra if
                            if ($cant == 0)
                                $sheet->row($fila, array('No hay registros'));
                        }//cierra if sucursal id
                    }//cierra foreach
                    if($haysucursal==0)
                        $sheet->row($fila, array('No existen sucursales'));
                    /// mostrar No existen sucursales
                        });        //CIERRA PESTAÃ‘A
                } //finaliza foreach
            } //finaliza if
        })->export('xls');

    }
    /*
     * FINALIZA REPORTE VENTAS DIARIAS POR ESTABLECIMIENTO
     */
}
