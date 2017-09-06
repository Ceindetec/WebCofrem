<?php

use Illuminate\Database\Seeder;

class TipoTarjetasSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tipo_tarjetas')->insert(
            ["codigo"=>'A', "descripcion"=>'AFILIADO'],
            ["codigo"=>'R', "descripcion"=>'REGALO'],
            ["codigo"=>'B', "descripcion"=>'BONO EMPRESARIAL']
        );
    }
}
