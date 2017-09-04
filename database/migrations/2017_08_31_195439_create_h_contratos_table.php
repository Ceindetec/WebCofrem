<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHContratosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('h_contratos', function (Blueprint $table) {
            $table->bigIncrements('id')->nocache();
            $table->bigInteger('contrato_id')->unsigned();
            $table->foreign('contrato_id')->references('id')->on('contratos_emprs')->onDelete('cascade');
            $table->date('fecha');
            $table->string('motivo')->nullable();
            $table->enum('estado',['A','I']);//activo o inactivo
            $table->time('hora');
            $table->integer('usuario_id')->unsigned();
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('h_contratos');
    }
}
