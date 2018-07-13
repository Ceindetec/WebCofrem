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
        $this->call(Departamentos::class);
        $this->call(Municipios::class);
        $this->call(TipoTarjetasSeed::class);
        $this->call(PermisosSeeder::class);
        $this->call(MotivoSeed::class);
        $this->call(TipoDocumentos::class);
        $this->call(UsuarioAdmin::class);
    }
}
