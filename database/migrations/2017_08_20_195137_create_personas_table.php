<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersonasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personas', function (Blueprint $table) {
            $table->bigIncrements('id')->nocache();
            $table->primary('id');
            $table->string('identificacion')-> unique();
            $table->string('nombres');
            $table->string('apellidos');
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            $table->string('celular')->nullable();
            $table->string('direccion')->nullable();
            $table->string('latitud')->nullable();
            $table->string('longitud')->nullable();
            $table->enum('sexo',['M','F'])->default('M');
            $table->date('fecha_nacimiento')->nullable();
            $table->enum('tipo_persona',['A','T'])->default('T');
            $table->string('municipio_codigo')->nullable()->index();
            $table->bigInteger('padre_id')->nullable();
            $table->timestamps();

            $table->foreign('municipio_codigo')->references('codigo')->on('municipios')->onDelete('cascade');
            $table->foreign('padre_id')->references('id')->on('personas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('personas');
    }
}
