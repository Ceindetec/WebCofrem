<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSucursalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sucursales', function (Blueprint $table) {
            $table->bigIncrements('id')->nocache();
            $table->bigInteger('establecimieto_id')->unsigned();
            $table->string('nombre');
            $table->string('direccion');
            $table->string('latitud');
            $table->string('longitud');
            $table->string('password');
            $table->enum('estado' ,['A','I']);
            $table->string('municipio_codigo')->index();
            $table->foreign('municipio_codigo')->references('codigo')->on('municipios')->onDelete('cascade');
            $table->foreign('establecimieto_id')->references('id')->on('establecimientos')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sucursales');
    }
}
