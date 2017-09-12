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
        DB::table('servicios')->insert([
            ["id" => "1", "codigo" => 'A', "descripcion" => 'AFILIADO'],
            ["id" => "2", "codigo" => 'R', "descripcion" => 'REGALO'],
            ["id" => "3", "codigo" => 'B', "descripcion" => 'BONO EMPRESARIAL']
            ]
        );
    }
}
