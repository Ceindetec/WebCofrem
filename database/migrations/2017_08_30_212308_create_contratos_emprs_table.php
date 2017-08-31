<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContratosEmprsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contratos_emprs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('n_contrato')->unique();
            $table->double('valor_contrato',5,2);
            $table->integer('valor_impuesto');
            $table->date('fecha');
            $table->bigInteger('empresa_id')->unsigned();
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
            $table->integer('n_tarjetas');
            $table->enum('forma_pago',['E','C']);//efectivo, consumo
            $table->string('pdf')->nullable();//archivo contrato fisico
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
        Schema::dropIfExists('contratos_emprs');
    }
}
