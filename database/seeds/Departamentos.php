<?php

use Illuminate\Database\Seeder;

class Departamentos extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('departamentos')->insert([
            ['codigo'=>'05','descripcion'=>'ANTIOQUIA'],
            ['codigo'=>'08','descripcion'=>'ATLÁNTICO'],
            ['codigo'=>'11','descripcion'=>'BOGOTÁ, D.C.'],
            ['codigo'=>'13','descripcion'=>'BOLÍVAR'],
            ['codigo'=>'15','descripcion'=>'BOYACÁ'],
            ['codigo'=>'17','descripcion'=>'CALDAS'],
            ['codigo'=>'18','descripcion'=>'CAQUETÁ'],
            ['codigo'=>'19','descripcion'=>'CAUCA'],
            ['codigo'=>'20','descripcion'=>'CESAR'],
            ['codigo'=>'23','descripcion'=>'CÓRDOBA'],
            ['codigo'=>'25','descripcion'=>'CUNDINAMARCA'],
            ['codigo'=>'27','descripcion'=>'CHOCÓ'],
            ['codigo'=>'41','descripcion'=>'HUILA'],
            ['codigo'=>'44','descripcion'=>'LA GUAJIRA'],
            ['codigo'=>'47','descripcion'=>'MAGDALENA'],
            ['codigo'=>'50','descripcion'=>'META'],
            ['codigo'=>'52','descripcion'=>'NARIÑO'],
            ['codigo'=>'54','descripcion'=>'NORTE DE SANTANDER'],
            ['codigo'=>'63','descripcion'=>'QUINDIO'],
            ['codigo'=>'66','descripcion'=>'RISARALDA'],
            ['codigo'=>'68','descripcion'=>'SANTANDER'],
            ['codigo'=>'70','descripcion'=>'SUCRE'],
            ['codigo'=>'73','descripcion'=>'TOLIMA'],
            ['codigo'=>'76','descripcion'=>'VALLE DEL CAUCA'],
            ['codigo'=>'81','descripcion'=>'ARAUCA'],
            ['codigo'=>'85','descripcion'=>'CASANARE'],
            ['codigo'=>'86','descripcion'=>'PUTUMAYO'],
            ['codigo'=>'88','descripcion'=>'ARCHIPIÉLAGO DE SAN ANDRÉS, PROVIDENCIA Y SANTA CATALINA'],
            ['codigo'=>'91','descripcion'=>'AMAZONAS'],
            ['codigo'=>'94','descripcion'=>'INÍRIDA'],
            ['codigo'=>'95','descripcion'=>'GUAVIARE'],
            ['codigo'=>'97','descripcion'=>'VAUPÉS'],
            ['codigo'=>'99','descripcion'=>'VICHADA']
        ]);
    }
}
