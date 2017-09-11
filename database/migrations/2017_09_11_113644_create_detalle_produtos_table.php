<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetalleProdutosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_produtos', function (Blueprint $table) {
            $table->bigIncrements('id')->nocache();
            $table->string('numero_tarjeta');
            $table->date('fecha_cracion');
            $table->date('fecha_activacion')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->double('monto_inicial',15,2);
            $table->bigInteger('contrato_emprs_id')->nullable()->unsigned();
            $table->foreign('contrato_emprs_id')->references('id')->on('contratos_emprs')->onDelete('cascade');
            $table->string('factura')->nullable();
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->enum('estado',['A','I'])->default('A');
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
        Schema::dropIfExists('detalle_produtos');
    }
}
