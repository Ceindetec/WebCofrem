<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $this->call(UsuarioAdmin::class);
         $this->call(Departamentos::class);
         $this->call(Municipios::class);
         $this->call(TipoTarjetasSeed::class);
    }
}
