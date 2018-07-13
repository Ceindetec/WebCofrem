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
            $table->bigIncrements('id')->nocache();
            $table->integer('adminis_tarjeta_id')->unsigned();
            $table->string('n_contrato')->unique();
            $table->double('valor_contrato', 10, 2)->default(0);
            $table->integer('valor_impuesto');
            $table->date('fecha');
            $table->bigInteger('empresa_id')->unsigned();
            $table->integer('n_tarjetas');
            $table->enum('forma_pago',['E','C']);//efectivo, consumo
            $table->string('pdf')->nullable();//archivo contrato fisico
            $table->boolean('cons_mensual')->defaul(0);
            $table->string('dias_consumo',3)->nullable(true);
            $table->timestamps();

            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
            $table->foreign('adminis_tarjeta_id')->references('id')->on('adminis_tarjetas')->onDelete('cascade');
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
