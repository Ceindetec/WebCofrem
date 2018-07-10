<?php

use Illuminate\Database\Seeder;

class TipoDocumentos extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('tipo_documentos')->insert([
            ['tip_codi'=>'1','descripcion'=>'NUMERO DE IDENTIFICACIÓN TRIBUTARIA','equivalente'=>'NIT'],
            ['tip_codi'=>'2','descripcion'=>'CÉDULA DE CIUDADANÍA','equivalente'=>'CC'],
            ['tip_codi'=>'3','descripcion'=>'TARJETA DE IDENTIDAD','equivalente'=>'TI'],
            ['tip_codi'=>'4','descripcion'=>'CÉDULA DE EXTRANJERÍA','equivalente'=>'CE'],
            ['tip_codi'=>'5','descripcion'=>'REGISTRO CIVIL DE NACIMIENTO','equivalente'=>'RC'],
            ['tip_codi'=>'6','descripcion'=>'PASAPORTE','equivalente'=>'PS'],
            ['tip_codi'=>'9','descripcion'=>'PERMISO ESPECIAL DE PERMANENCIA','equivalente'=>'PE']
        ]);
    }
}
