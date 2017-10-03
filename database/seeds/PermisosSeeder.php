<?php

use Illuminate\Database\Seeder;

class PermisosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('permissions')->insert([
            ["name" => "Agregar convenio establecimiento", "slug" => "agre.convenio.esta", "description" => "Permite agregar un convenio a un establecimiento"],
            ["name" => "Crear establecimientos", "slug" => "crear.establecimiento", "description" => "Permite crear nuevo establecimientos en el sistema"],
            ["name" => "Editar establecimiento", "slug" => "editar.establecimiento", "description" => "Permite editar establecimientos"],
            ["name" => "Editar reglas establecimiento", "slug" => "editar.reglas.esta", "description" => "Permite editar las reglas de un convenio de un establecimiento"],
            ["name" => "Agregar sucursal", "slug" => "agregar.sucursal", "description" => "Permite agregar nuevas sucursales a un establecimiento"],
            ["name" => "Editar sucursal", "slug" => "editar.sucursal", "description" => "Permite editar la informaci\u00f3n de una sucursal"],
            ["name" => "Editar terminal", "slug" => "editar.terminal", "description" => "Permite editar informaci\u00f3n de una terminal"],
            ["name" => "Cambiar estado terminal", "slug" => "estado.terminal", "description" => "Permite cambiar el estado de una terminal"],
            ["name" => "Trasladar terminales", "slug" => "trasladar.terminal", "description" => "Permite trasladar una terminal"],
            ["name" => "Agregar terminal", "slug" => "agregar.terminal", "description" => "Permite agregar una nueva terminal a un establecimiento"],
            ["name" => "Parametrizar productos", "slug" => "parametrizar.producto", "description" => "Permite al usuario parametrizar lo referente a los servicios de tarjeta regalo bono y cupo rotativo"],
            ["name" => "Duplicar tarjetas", "slug" => "duplicar.tarjeta", "description" => "Permite al usuario duplicar una tarjeta"],
            ["name"=>"Editar monto regalo","slug"=>"editar.monto.regalo","description"=>"Permite editar el monto de una tarjeta regalo"]
        ]);
    }
}
