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

        route::get('crearTarjetaRegalo','TarjetasRegaloController@crearTarjetaRegalo')->name('crearTarjetaRegalo');
        route::post('addTarjetaRegalo','TarjetasRegaloController@addTarjetaRegalo')->name('addTarjetaRegalo');
        route::get('autoCompleNumTarjeta','TarjetasRegaloController@autoCompleNumTarjeta')->name('autoCompleNumTarjeta');
        //route::get('tarjetas','TarjetasController@index')->name('tarjetas');

        /*FINALIZA TARJETAS*/

    });
});