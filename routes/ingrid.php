<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();

Route::group(['middleware' => 'auth'], function () {




    /*INICIO TARJETAS*/

    route::get('tarjetas', 'TarjetasController@index')->name('tarjetas');
    route::get('tarjetas/gridtarjetas', 'TarjetasController@gridTarjetas')->name('gridtarjetas');
    route::get('tarjetas/crear', 'TarjetasController@viewCrearTarjeta')->name('tarjetas.crear');
    route::post('tarjetas/crear', 'TarjetasController@crearTarjeta')->name('tarjetas.crearp');
    route::get('tarjetas/editar', 'TarjetasController@viewEditarTarjeta')->name('tarjetas.editar');
    route::post('tarjetas/editar/{id}', 'TarjetasController@editarTarjeta')->name('tarjetas.editarp');
    route::get('tarjetas/crearbloque', 'TarjetasController@viewCrearTarjetaBloque')->name('tarjetas.crearbloque');
    route::post('tarjetas/crearbloque', 'TarjetasController@crearTarjetaBloque')->name('tarjetas.crearbloquep');
    //route::get('tarjetas','TarjetasController@index')->name('tarjetas');


    /*FINALIZA TARJETAS*/
    /* INICIA CREACION TARJETAS BONO */
    route::get('creartarjetasBono', 'TarjetasBonoController@viewCrearTarjetaIndividual')->name('creartarjetasBono');
    route::post('bono.crearindividual', 'TarjetasBonoController@CrearTarjetaIndividual')->name('bono.crearindividual');
    route::get('autoCompleNumContrato', 'TarjetasBonoController@autoCompleNumContrato')->name('autoCompleNumContrato');
    route::get('getNombre', 'TarjetasBonoController@getNombre')->name('getNombre');
    route::get('creartarjetasBonoBloque', 'TarjetasBonoController@viewCrearTarjetaBloque')->name('creartarjetasBonoBloque');
    route::post('bono.crearbloque', 'TarjetasBonoController@CrearTarjetaBloque')->name('bono.crearbloque');
    /*FINALIZA CREACION TARJETAS BONO */

    /*INICIA CONSULTA TARJETA BONO*/
    Route::get('tarjetas/bono/consulta', 'TarjetasBonoController@consultaTarjetasBono')->name('consultabono');
    Route::get('tarjetas/bono/gridconsultatarjetabono', 'TarjetasBonoController@gridConsultaTarjetaBono')->name('gridconsultatarjetabono');
    Route::get('tarjetas/bono/editar/{id}', 'TarjetasBonoController@viewEditarBono')->name('bono.editar')->middleware('permissionshinobi:editar.fecha.bono');
    Route::post('tarjetas/bono/editar/{id}', 'TarjetasBonoController@editarBono')->name('bono.editarp')->middleware('permissionshinobi:editar.fecha.bono');
    Route::post('tarjetas/bono/activar', 'TarjetasBonoController@activarTarjetaBono')->name('tarjeta.bono.activar');
    Route::get('tarjetas/bono/consultainteligente','TarjetasBonoController@viewConsultaxContrato')->name('bono.consultaxcontrato');
    Route::post('tarjetas/bono/consultarporcontrato','TarjetasBonoController@ConsultaxContrato')->name('bono.consultaxcontratop');
    Route::post('tarjetas/bono/consultarporempresa','TarjetasBonoController@ConsultaxEmpresa')->name('bono.consultaxempresap');
    Route::post('tarjetas/bono/activarporcontrato','TarjetasBonoController@ActivarxContrato')->name('bono.activarxcontrato');
    /*FINALIZA CONSULTA TARJETA BONO*/

    /*INICIA REPORTES SALDOS VENCIDOS  */
    Route::get('reportes/saldosvencidos/consulta','ReportesController@viewSaldosVencidos')->name('reportes.saldosvencidos');
    Route::post('reportes/saldosvencidos/resultadoconsulta','ReportesController@consultarSaldosVencidos')->name('resultadosaldosvencidos');
    Route::get('reportes/saldosvencidos/exportarpdf','ReportesController@pdfSaldosVencidos')->name('pdfsaldosvencidos');
    Route::get('reportes/saldosvencidos/exportarexcel','ReportesController@excelSaldosVencidos')->name('excelsaldosvencidos');
    /*FINALIZA REPORTES SALDOS VENCIDOS*/

    /*INICIA REPORTES VENTAS DIARIAS POR ESTABLECIMIENTO  */
    Route::get('reportes/ventasdiariasxestablecimiento/consulta','ReportesController@viewVentasDiarias')->name('reportes.ventasdiarias');
    Route::get('select/establecimientos',"ReportesController@selectestablecimientos")->name('selectestablecimientos');
    Route::post('reportes/ventasdiariasxestablecimiento/resultadoconsulta','ReportesController@consultarVentasDiarias')->name('resultadoventasdiarias');
    Route::get('reportes/ventasdiariasxestablecimiento/exportarpdf','ReportesController@pdfVentasDiarias')->name('pdfventasdiarias');
    Route::get('reportes/ventasdiariasxestablecimiento/exportarexcel','ReportesController@excelVentasDiarias')->name('excelventasdiarias');
    /*FINALIZA REPORTES VENTAS DIARIAS POR ESTABLECIMIENTO*/

    /*INICIA REPORTES DATAFONOS POR ESTABLECIMIENTO */
    Route::get('reportes/datafonosxestablecimiento/consulta','ReportesController@viewDatafonosxEstablecimiento')->name('reportes.datafonosxestablecimientos');
    Route::get('select/establecimientos',"ReportesController@selectestablecimientos")->name('selectestablecimientos');
    Route::post('reportes/datafonosxestablecimiento/resultadoconsulta','ReportesController@consultarDatafonosxEstablecimiento')->name('resultadodatafonosxestablecimientos');
    Route::get('reportes/datafonosxestablecimiento/exportarpdf','ReportesController@pdfDatafonosxEstablecimiento')->name('pdfdatafonosxestablecimientos');
    Route::get('reportes/datafonosxestablecimiento/exportarexcel','ReportesController@excelDatafonosxEstablecimiento')->name('exceldatafonosxestablecimientos');
    /*FINALIZA REPORTES DATAFONOS POR ESTABLECIMIENTO*/

    /*INICIA REPORTES SALDOS POR TARJETA ACTIVA  */
    Route::get('reportes/saldotarjeta/consulta','ReportesController@viewSaldoTarjeta')->name('reportes.saldotarjeta');
    Route::post('reportes/saldotarjeta/resultadoconsulta','ReportesController@consultarSaldoTarjeta')->name('resultadosaldotarjeta');
    Route::get('reportes/saldotarjeta/exportarpdf','ReportesController@pdfSaldoTarjeta')->name('pdfsaldotarjeta');
    Route::get('reportes/saldotarjeta/exportarexcel','ReportesController@excelSaldoTarjeta')->name('excelsaldotarjeta');
    /*FINALIZA REPORTES SALDOS POR TARJETA ACTIVA*/

    /*INICIA REPORTE TRANSACCIONES POR DATAFONO*/
    Route::get('reportes/transaccionesxdatafono/consulta','ReportesController@viewTransaccionesxDatafono')->name('reportes.transaccionesxdatafono');
    Route::get('select/establecimientos',"ReportesController@selectestablecimientos")->name('selectestablecimientos');
    Route::post('reportes/transaccionesxdatafono/resultadoconsulta','ReportesController@consultarTransaccionesxDatafono')->name('resultadotransaccionesxdatafono');
    Route::get('reportes/transaccionesxdatafono/exportarpdf','ReportesController@pdfTransaccionesxDatafono')->name('pdftransaccionesxdatafono');
    Route::get('reportes/transaccionesxdatafono/exportarexcel','ReportesController@excelTransaccionesxDatafono')->name('exceltransaccionesxdatafono');
    /*FINALIZA REPORTE TRANSACCIONES POR DATAFONO*/

    /*INICIA REPORTE PROMEDIO POR DATAFONO*/
    Route::get('reportes/promedioxdatafono/consulta','ReportesController@viewPromedioxDatafono')->name('reportes.promedioxdatafono');
    Route::get('select/establecimientos',"ReportesController@selectestablecimientos")->name('selectestablecimientos');
    Route::post('reportes/promedioxdatafono/resultadoconsulta','ReportesController@consultarPromedioxDatafono')->name('resultadopromedioxdatafono');
    Route::get('reportes/promedioxdatafono/exportarpdf','ReportesController@pdfPromedioxDatafono')->name('pdfpromedioxdatafono');
    Route::get('reportes/promedioxdatafono/exportarexcel','ReportesController@excelPromedioxDatafono')->name('excelpromedioxdatafono');
    /*FINALIZA REPORTE PROMEDIO POR DATAFONO*/

    /*INICIA REPORTE MONTOS USADOS*/
    Route::get('reportes/montosusados/consulta','ReportesController@viewMontosUsados')->name('reportes.montosusados');
    Route::get('select/establecimientos',"ReportesController@selectestablecimientos")->name('selectestablecimientos');
    Route::post('reportes/montosusados/resultadoconsulta','ReportesController@consultarMontosUsados')->name('resultadomontosusados');
    Route::get('reportes/montosusados/exportarpdf','ReportesController@pdfMontosUsados')->name('pdfmontosusados');
    Route::get('reportes/montosusados/exportarexcel','ReportesController@excelMontosUsados')->name('excelmontosusados');
    /*FINALIZA REPORTE MONTOS USADOS*/

    /*INICIA REPORTE VENTAS POR SUCURSAL*/
    Route::get('reportes/ventasxsucursal/consulta','ReportesController@viewVentasxSucursal')->name('reportes.ventasxsucursal');
    Route::post('reportes/ventasxsucursal/resultadoconsulta','ReportesController@consultarVentasxSucursal')->name('resultadoventasxsucursal');
    Route::get('reportes/ventasxsucursal/exportarpdf','ReportesController@pdfVentasxSucursal')->name('pdfventasxsucursal');
    Route::get('reportes/ventasxsucursal/exportarexcel','ReportesController@excelVentasxSucursal')->name('excelventasxsucursal');
    /*FINALIZA REPORTE VENTAS POR SUCURSAL*/

    /*INICIA REPORTE VENTAS POR ESTABLECIMIENTO*/
    Route::get('reportes/ventasxestablecimiento/consulta','ReportesController@viewVentasxEstablecimiento')->name('reportes.ventasxestablecimiento');
    Route::post('reportes/ventasxestablecimiento/resultadoconsulta','ReportesController@consultarVentasxEstablecimiento')->name('resultadoventasxestablecimiento');
    Route::get('reportes/ventasxestablecimiento/exportarpdf','ReportesController@pdfVentasxEstablecimiento')->name('pdfventasxestablecimiento');
    Route::get('reportes/ventasxestablecimiento/exportarexcel','ReportesController@excelVentasxEstablecimiento')->name('excelventasxestablecimiento');
    /*FINALIZA REPORTE VENTAS POR ESTABLECIMIENTO*/

});