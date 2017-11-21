<?php

use Illuminate\Database\Seeder;

class MotivoSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('motivos')->insert([
                ["id" => "1", "codigo" => 'P', "motivo" => 'POR PERDIDA', 'tipo' => 'D', 'estado' => 'A'],
                ["id" => "2", "codigo" => 'D', "motivo" => 'POR DETERIORO', 'tipo' => 'D', 'estado' => 'A'],
                ["id" => "3", "codigo" => 'R', "motivo" => 'POR ROBO', 'tipo' => 'D', 'estado' => 'A']
            ]
        );
    }
}
