<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTarjetasRegalosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tarjetas_regalos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->primary('id');
            $table->string('numero_tarjeta')-> index();
            $table->foreign('numero_tarjeta')->references('numero_tarjeta')->on('tarjetas')->onDelete('cascade');
            $table->double('monto_inicial',15,1)->nullable();
            $table->double('monto_restante',15,1)->nullable();
            $table->date('fecha_creacion');
            $table->date('fecha_activacion')->nullable();
            $table->date('fecha_vence')->nullable();
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
        Schema::dropIfExists('tarjetas_regalos');
    }
}
