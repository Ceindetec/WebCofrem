<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagaPlasticosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paga_plasticos', function (Blueprint $table) {
            $table->increments('id')->nocache();
            $table->boolean('pagaplastico');
            $table->enum('estado',['A','I'])->default('A');
            $table->string('servicio_codigo')->index();
            $table->foreign('servicio_codigo')->references('codigo')->on('servicios')->onDelete('cascade');
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
        Schema::dropIfExists('paga_plasticos');
    }
}
