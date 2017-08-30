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

        /*INICIO ESTABLECIMIENTOS*/

        route::get('establecimientos','EstablecimientosController@index')->name('establecimientos');
        route::get('establecimientos/gridestablecimientos','EstablecimientosController@gridEstablecimientos')->name('gridestablecimientos');
        route::get('establecimientos/crear','EstablecimientosController@viewCrearEstablecimiento')->name('establecimiento.crear');
        route::post('establecimientos/crear','EstablecimientosController@crearEstablecimiento')->name('establecimiento.crearp');
        route::get('establecimientos/editar','EstablecimientosController@viewEditarEstablecimiento')->name('establecimiento.editar');
        route::post('establecimientos/editar','EstablecimientosController@editarEstablecimiento')->name('establecimiento.editarp');

        /*FINALIZA ESTABLECIMIENTOS*/

    });


    /*combobox*/
    Route::get('select/permisos',"HomeController@selectpermisos")->name('selectpermisos');

    Route::get('select/roles',"HomeController@selectroles")->name('selectroles');



});

Route::get('datatable_es', 'IdiomaDataTableController@espanol')->name('datatable_es');

Route::get('/', function () {
    return redirect('/login');
});