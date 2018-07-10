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

    Route::group(['middleware' => 'roleshinobi:admin'], function (){

        /*INICIO TARJETAS*/

        //vistas
        route::get('crearTarjetaRegalo','TarjetasRegaloController@crearTarjetaRegalo')->name('crearTarjetaRegalo');
        route::get('crearTarjetaBloque','TarjetasRegaloController@crearTarjetaBloque')->name('crearTarjetaBloque');

        /*INICIA CONSULTA TARJETA REGALO*/
        Route::get('tarjetas/regalo/consulta', 'TarjetasRegaloController@consultaTarjetasRegalo')->name('consultaregalo');
        Route::get('tarjetas/regalo/gridconsultatarjetaregalo', 'TarjetasRegaloController@gridConsulaTarjetaRegalo')->name('gridconsultatarjetaregalo');
        Route::get('tarjetas/regalo/editar/{id}','TarjetasRegaloController@viewEditarRegalo')->name('regalo.editar')->middleware('permissionshinobi:editar.monto.regalo');
        Route::post('tarjetas/regalo/editar/{id}','TarjetasRegaloController@editarRegalo')->middleware('permissionshinobi:editar.monto.regalo');
        Route::post('tarjetas/regalo/activar','TarjetasRegaloController@activarTarjetaRegalo')->name('tarjeta.regalo.activar');
        /*FINALIZA CONSULTA TARJETA REGALO*/

        /*INICIA CONSULTA AVNZADA TARJETA REGALO*/

        Route::get('tarjetas/regalo/consultaavanzada','TarjetasRegaloController@consultaTarjetasRegaloAvanzada')->name('consultaavan');
        Route::post('tarjetas/regalo/consultaxfactura','TarjetasRegaloController@restadoConsultaxFacturaRegalo')->name('reagalo.consultaxfactura');
        Route::post('tarjetas/regalo/activarxfactura','TarjetasRegaloController@activarxFacturaRegalo')->name('regalo.activarxcfactura');

        /*FIN CONSULTA AVNZADA TARJETA REGALO*/

        //procesos
        route::get('autoCompleNumTarjeta','TarjetasRegaloController@autoCompleNumTarjeta')->name('autoCompleNumTarjeta');
        route::post('addTarjetaRegalo','TarjetasRegaloController@addTarjetaRegalo')->name('addTarjetaRegalo');
        route::post('addTarjetaRegaloBloque','TarjetasRegaloController@addTarjetaRegaloBloque')->name('addTarjetaRegaloBloque');



        //route::get('tarjetas','TarjetasController@index')->name('tarjetas');

        /*FINALIZA TARJETAS*/

    });
});