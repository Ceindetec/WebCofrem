<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFerecuenciaConvEsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ferecuencia_conv_es', function (Blueprint $table) {
            $table->bigIncrements('id')->nocache();
            $table->enum('frecuencia_corte',['S','Q','M'])->default('S');
            $table->bigInteger('convenios_esta_id')->unsigned();
            $table->foreign('convenios_esta_id')->references('id')->on('convenios_estas')->onDelete('cascade');
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
        Schema::dropIfExists('ferecuencia_conv_es');
    }
}
