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

//DB::listen(function ($query){
//    echo "<pre>{$query->sql}</pre>";
//});


Route::get("permi",function (){
    $permisos = \Caffeinated\Shinobi\Models\Permission::all()->pluck("id");

    return $permisos;
});



Auth::routes();

Route::group(['middleware' => 'auth'], function () {


    Route::group(['middleware' => 'roleshinobi:admin'], function (){

        /* INICIO ADMINISTRACION DE USUARIOS */
        Route::get('/usuarios', 'HomeController@index')->name('usuarios');
        Route::get('gridusuarios', 'HomeController@gridusuarios')->name('gridusuarios');
        Route::get('usuario/crear', 'HomeController@viewCrearUsuario')->name('usuario.crear');
        Route::post('usuario/crearp', 'HomeController@pCrearUsuario')->name('usuario.crearp');
        Route::get('usuario/editar', 'HomeController@viewEditarUsuario')->name('usuario.editar');
        Route::get('usuario/detalleroles', 'HomeController@usuarioDetalleRolles')->name('detalleroles');
        Route::post('usuario/eliminar', 'HomeController@eliminarUsuario')->name('usuario.eliminar');
        Route::post('usuario/editarp', 'HomeController@pEditarUsuario')->name('usuario.editarp');
        Route::post('usuario/agrerol', 'HomeController@usuarioAgregarRol')->name('usuario.agrerol');
        Route::get('usuario/gridroles', 'HomeController@gridrolesusuario')->name('gridrolesusuario');
        Route::post('usuario/eliminarrolusuario', 'HomeController@eliminarRolUsuario')->name('eliminarrolusuario');
        /* FINALIZA ADMINISTRACION DE USUARIOS */

        /* INICIA ADMINISTRACION DE ROLES*/
        Route::get('roles','HomeController@viewroles')->name('roles');
        Route::get('gridroles', 'HomeController@gridroles')->name('gridroles');
        Route::post('roles/agregar', 'HomeController@agrerol')->name('agrerol');
        Route::get('roles/detalle', 'HomeController@rolespermisos')->name('detallerpermisos');
        Route::post('roles/eliminar', 'HomeController@eliminarrol')->name('eliminarrol');
        Route::get('roles/editar', 'HomeController@viewEditarRol')->name('editar.rol');
        Route::post('roles/editarp', 'HomeController@editarrolp')->name('editar.rolp');
        Route::post('roles/agrepermisos', 'HomeController@rolAgregarPermiso')->name('rol.agrepermiso');
        Route::post('roles/eliminarpermisorol', 'HomeController@eliminarPermisoRol')->name('eliminarpermisorol');
        Route::get('gridpermisosrol', 'HomeController@gridpermisosrol')->name('gridpermisosrol');
        /*TERMINA ADMINISTRACION DE ROLES*/

        /*INICIA ADMINISTRACION DE PERMISOS */
        Route::get('permisos', 'HomeController@viewpermisos')->name('permisos');
        Route::get('gridpermisos', 'HomeController@gridpermisos')->name('gridpermisos');
        Route::post('permiso/agregar', 'HomeController@agrepermiso')->name('agrepermiso');
        Route::post('permiso/eliminar', 'HomeController@eliminapermiso')->name('eliminarpermiso');
        Route::get('permiso/editar', 'HomeController@vieweditarpermiso')->name('editar.permiso');
        Route::post('permiso/editarp', 'HomeController@editarpermiso')->name('editar.permisop');
        /*TERMINA ADMINISTRACION DE PERMISO*/

    });


    /*INICIO ESTABLECIMIENTOS*/

    route::get('establecimientos','EstablecimientosController@index')->name('establecimientos');
    route::get('establecimientos/gridestablecimientos','EstablecimientosController@gridEstablecimientos')->name('gridestablecimientos');
    route::get('establecimientos/crear','EstablecimientosController@viewCrearEstablecimiento')->name('establecimiento.crear')->middleware('permissionshinobi:crear.establecimiento');
    route::post('establecimientos/crear','EstablecimientosController@crearEstablecimiento')->name('establecimiento.crearp')->middleware('permissionshinobi:crear.establecimiento');;
    route::get('establecimientos/editar/{id}','EstablecimientosController@viewEditarEstablecimiento')->name('establecimiento.editar');
    route::post('establecimientos/editar','EstablecimientosController@editarEstablecimiento')->name('establecimiento.editarp')->middleware('permissionshinobi:editar.establecimiento');

    /*FINALIZA ESTABLECIMIENTOS*/

    /*INICIA SUCURSALES ESTABLECIMIENTOS*/

    Route::get('listsucursales/{id}','SucursalesController@index')->name('listsucursales');
    Route::get('gridsuscursales','SucursalesController@gridSuscursales')->name('gridsuscursales');
    Route::get('sucursal/crear','SucursalesController@viewCrearSucursal')->name('sucursal.crear')->middleware('permissionshinobi:agregar.sucursal');
    Route::post('sucursal/crear','SucursalesController@crearSucursal')->name('sucursal.crearp')->middleware('permissionshinobi:agregar.sucursal');
    Route::get('marketsucursales','SucursalesController@getMarketSucursales')->name('marketsucursales');
    Route::get('sucursal/editar','SucursalesController@viewEditarSucursal')->name('sucursal.editar');
    Route::post('sucursal/editar','SucursalesController@editarSucursal')->name('sucursal.editarp')->middleware('permissionshinobi:editar.sucursal');

    /*FINALIZA SUCURSALES ESTABLECIMIENTOS*/

    /*INICIA TERMINALES*/

    Route::get('listterminales/{id}','TerminalesController@index')->name('listterminales');
    Route::get('gridterminales','TerminalesController@gridTerminales')->name('gridterminales');
    Route::get('terminal/crear','TerminalesController@viewCrearTerminal')->name('terminal.crear')->middleware('permissionshinobi:agregar.terminal');
    Route::post('terminal/crear','TerminalesController@crearTerminal')->name('terminal.crearp')->middleware('permissionshinobi:agregar.terminal');
    Route::get('terminal/editar','TerminalesController@viewEditarTerminal')->name('terminal.editar')->middleware('permissionshinobi:editar.terminal');
    Route::post('terminal/editar','TerminalesController@editarTerminal')->name('terminal.editarp')->middleware('permissionshinobi:editar.terminal');
    Route::post('terminal/cambiarestado','TerminalesController@cambiarEstadoTerminal')->name('terminal.cambiarestado')->middleware('permissionshinobi:estado.terminal');

    Route::group(['middleware' => 'permissionshinobi:trasladar.terminal'], function () {
        Route::get('terminal/traslados', 'TerminalesController@viewListTerminalTraslados')->name('traladoterminal');
        Route::get('terminal/gridterminalestraslado', 'TerminalesController@gridTerminalesTraslado')->name('gridterminalestraslado');
        Route::get('terminal/viewtrasladoterminal/{id}', 'TerminalesController@viewTrasladoTerminal')->name('viewtrasladoterminal');
        Route::post('terminal/trasladarp', 'TerminalesController@trasladarTerminal')->name('terminal.trasladarp');
    });

    /*FINALIZA TERMINALES*/

    /*INICIA CONVENIOS ESTABLECIMIENTOS*/
    Route::get('gridconveniosestablecimiento','ConveniosEstablecimientosController@gridConveniosEstablecimiento')->name('gridconveniosestablecimiento');
    Route::get('convenio/crear/{id}','ConveniosEstablecimientosController@viewCrearConvenio')->name('convenio.crear');
    Route::post('convenio/crear/{id}','ConveniosEstablecimientosController@crearConvenio')->name('convenio.crearp');
    Route::get('coveniosesta/reglas/{id}','ConveniosEstablecimientosController@viewReglasConvenioEstablecimiento')->name('coveniosesta.reglas');
    Route::get('gridrangosconvenio/{id}','ConveniosEstablecimientosController@gridRangosConvenio')->name('gridrangosconvenio');
    Route::post('actualizar/plazo/{id}','ConveniosEstablecimientosController@actualizarPlazoPagoConvenio')->name('actualizar.plazo');
    Route::post('actualizar/frecuencia/{id}','ConveniosEstablecimientosController@actualizarFrecuenciaCorteConvenio')->name('actualizar.frecuencia');
    Route::post('conveni/.nuevorango/{id}','ConveniosEstablecimientosController@nuevoRongoConvenio')->name('convenio.nuevorango');
    Route::post('convenio/ramgo/eliminar','ConveniosEstablecimientosController@eliminarRangoConvenio')->name('convenio.ramgo.eliminar');
    Route::get('convenios/historial/{id}','ConveniosEstablecimientosController@historialConvenioEstablecimiento')->name('convenios.historial');
    Route::get('gridhitorialrangos/{id}','ConveniosEstablecimientosController@gridHitorialRangos')->name('gridHitorialrangos');
    Route::get('gridfrecuencia/{id}','ConveniosEstablecimientosController@gridFrecuencia')->name('gridFrecuencia');
    Route::get('gridplazos/{id}','ConveniosEstablecimientosController@gridPlazos')->name('gridPlazos');

    /*FINALIZA CONVENIOS ESTABLECIMIENTOS*/

    /*INICIA PARAMETRIZACION TARJETAS*/
    Route::group(['middleware' => 'permissionshinobi:parametrizar.producto'], function () {
        Route::get('tarjetas/parametrizacion', 'ParametrizacionTarjetasController@viewParametrosTarjetas')->name('tarjetas.parametros');
        Route::post('tarjeta/parametrovalor/crear', 'ParametrizacionTarjetasController@tarjetaCrearParametroValor')->name('tarjeta.parametro.valor');
        Route::post('tarjeta/parametroadministracion/crear/{codigo}', 'ParametrizacionTarjetasController@tarjetaCrearParametroAdministracion')->name('tarjeta.parametro.administracion');
        Route::get('gridadministraciontarjetas/{codigo}', 'ParametrizacionTarjetasController@gridAdministracionTarjetas')->name('gridadministraciontarjetas');
        Route::post('tarjeta/parametro/administracion/eliminar', 'ParametrizacionTarjetasController@tarjetaEliminarParametroAdministracion')->name('tarjeta.parametro.administracion.eliminar');
        Route::get('tarjeta/parametrizacion/servicio', 'ParametrizacionTarjetasController@getViewParametrizarServicio')->name('viewparametrizarservicio');
        Route::get('tarjeta/parametrizacion/gridvalorplatico', 'ParametrizacionTarjetasController@gridValorPlastico')->name('gridvalorplatico');
        Route::post('tarjeta/parametro/pagaplastico/{codigo}', 'ParametrizacionTarjetasController@tarjetaCrearParametroPagaplastico')->name('tarjeta.parametro.pagaplastico');
        Route::get('tarjeta/parametro/pagaplastico/gridpagaplastico/{codigo}', 'ParametrizacionTarjetasController@gridServicioPagaPlastico')->name('gridpagaplastico');
        Route::post('tarjeta/parametro/cuentacontablerb/crear/{codigo}', 'ParametrizacionTarjetasController@tarjetaCrearParametroCuentaRB')->name('tarjeta.parametro.cuentaRB');
        Route::get('tarjeta/parametro/gridcuentascontables/{codigo}', 'ParametrizacionTarjetasController@gridParametrosCuentasContables')->name('gridcuentascontables');
    });

    /*FINALIZA PARAMETRIZACION TARJETAS*/


    /*INICIA GESTION DE TAARJETA*/
    Route::get('targetas/gestionar/{id}','TarjetasController@gestionarTarjeta')->name('gestionarTarjeta');
    /*FINALIZA GESTION DE TARJETA*/

    /*INICIA DUPLICADO DE TARJETAS*/
    Route::group(['middleware' => 'permissionshinobi:duplicar.tarjeta'], function () {
        Route::get('tarjetas/duplicar', 'TarjetasController@viewDuplicarTarjeta')->name('tarjetas.duplicar');
        Route::get('tarjetas/duplicar/gridtarjetasduplicar', 'TarjetasController@gridTarjetasDuplicar')->name('gridtarjetasduplicar');
        Route::get('tarjetas/duplicar/modal/{id}', 'TarjetasController@viewModalDuplicarTarjeta')->name('tarjetas.modalduplicar');
        Route::post('tarjetas/duplicar/modal/{id}', 'TarjetasController@duplicarTarjeta');
        Route::get('tarjetas/duplicar/autocomplete', 'TarjetasController@autoCompleteTarjetaDuplicado')->name('tarjetas.duplicado.autocomplete');
    });
    /*FINALIZA DUPLICADO DE TARJETAS*/

    /*INICIA MODULO REPORTES*/
    Route::get('reportes/rprimeravez','ReportesController@viewReportePrimeraVez')->name('rprimeravez');
    Route::post('reportes/resultadoprimeravez','ReportesController@resultadoPrimeravez')->name('resultadoprimeravez');
    Route::get('reportes/exportarpdfprimeravez','ReportesController@exportarPdfPrimeravez')->name('exportarpdfprimeravez');
    Route::get('reportes/exportarexcelprimeravez','ReportesController@exportarExcelPrimeravez')->name('exportarexcelprimeravez');


    Route::get('reportes/motostarjetas','ReportesController@viewReporteMontos')->name('reportes.montostarjetas');
    Route::post('reportes/resultadomotostarjetas','ReportesController@resultadoMontosTarjetas')->name('reportes.resultadomotostarjetas');
    Route::get('reportes/exportarpdfmotosportarjeta','ReportesController@exportarPdfMotosPorTarjeta')->name('reportes.exportarpdfmotosportarjeta');
    Route::get('reportes/exportarexcelmontosportarjeta','ReportesController@exportarExcelMontosPorTarjeta')->name('reportes.exportarexcelmontosportarjeta');



    /*combobox*/
    Route::get('select/permisos',"HomeController@selectpermisos")->name('selectpermisos');

    Route::get('select/roles',"HomeController@selectroles")->name('selectroles');



});

Route::get('municipios', function (\Illuminate\Http\Request $request){
    return \DB::table('municipios')->where('departamento_codigo',$request->data)->get();
})->name('municipios');

Route::get('getsucursales', function (\Illuminate\Http\Request $request){
    return \DB::table('sucursales')->where('establecimiento_id',$request->data)->get();
})->name('sucursales');


Route::get('datatable_es', 'IdiomaDataTableController@espanol')->name('datatable_es');

Route::get('/', function () {
    return redirect('/login');
});

Route::get('jsonpermisos', function (){
   return \DB::table('permissions')->select('name','slug','description')->get();
});


Route::get('pruebawebservi', function (){
    $url = "http://192.168.0.188/WebServices4/WConsultasCajas.asmx?wsdl";
    try {
        $client = new SoapClient($url, array("trace" => 1, "exception" => 0));
        $result = $client->ConsultaAportante( [ "emp_codi" => 406, "tip_codi" => 1, "apo_coda" => "892000146" ] );
       return dd($result->ConsultaAportanteResult->Aportante->TOAportante);
    } catch ( SoapFault $e ) {
        echo $e->getMessage();
    }
});