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
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('convenio_id')->unsigned()->nullable();
            $table->bigInteger('contrato_emprs_id')->nullable()->unsigned();
            $table->string('numero_tarjeta');
            $table->date('fecha_cracion');
            $table->date('fecha_activacion')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->double('monto_inicial',15,2);
            $table->string('factura')->nullable();
            $table->enum('estado',['A','I','N'])->default('I');
            $table->timestamps();


            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('convenio_id')->references('id')->on('convenios_emps')->onDelete('cascade');
            $table->foreign('contrato_emprs_id')->references('id')->on('contratos_emprs')->onDelete('cascade');

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
