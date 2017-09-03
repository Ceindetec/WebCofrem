<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTarjetasBonosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tarjetas_bonos', function (Blueprint $table) {
            $table->bigIncrements('id')->nocache();
            $table->primary('id');
            $table->string('numero_tarjeta')-> index();
            $table->foreign('numero_tarjeta')->references('numero_tarjeta')->on('tarjetas')->onDelete('cascade');
            $table->bigInteger('tercero_id')-> unsigned() -> index();
            $table->foreign('tercero_id')->references('id')->on('terceros')->onDelete('cascade');
            $table->double('monto_inicial',15,1)->nullable();
            $table->double('monto_restante',15,1)->nullable();
            $table->date('fecha_creacion');
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_vence')->nullable();
            //pendiente adicionar nuevo campo que es la llave foranea de contratos_empresa: numero_contrato y modificar los montos a 2 decimales
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
        Schema::dropIfExists('tarjetas_bonos');
    }
}
