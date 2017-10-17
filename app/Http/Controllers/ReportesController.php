<?php

namespace creditocofrem\Http\Controllers;

use Carbon\Carbon;
use creditocofrem\DetalleProdutos;
use creditocofrem\DetalleTransaccion;
use creditocofrem\HEstadoTransaccion;
use creditocofrem\Servicios;
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
                    $sheet->row(7, array('Numero tarjeta', 'Transacción', 'Valor', 'Sucursal', 'Terminal', 'Fecha'));
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

    public function viewReporteMontos()
    {
        $servicios = Servicios::pluck('descripcion', 'codigo');
        return view('reportes.montostarjetas.motosportarjetas', compact('servicios'));
    }

    public function resultadoMontosTarjetas(Request $request)
    {
        $result = [];
        try {
            $rangos = explode(" - ", $request->rango);
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
                        if(isset($regalo['detalles'][$contadorfecha]))
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
                        if(isset($bono['detalles'][$contadorfecha]))
                            $contadorfecha++;
                    }
                }
                $data = ['regalo' => $regalo, 'bono'=>$bono];
            }

        } catch (\Exception $exception) {
            dd($exception->getMessage());
        }
        return view('reportes.montostarjetas.parcialresultadomotosportarjeta', compact('data'));
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
                        $sheet->row(7, array('Numero tarjeta', 'Monto incial', 'Sobrante', 'Fecha activacion', 'Fecha vencimiento'));
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
                        $sheet->row($fila, array('Numero tarjeta', 'Monto incial', 'Sobrante', 'Fecha activacion', 'Fecha vencimiento'));
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
}
