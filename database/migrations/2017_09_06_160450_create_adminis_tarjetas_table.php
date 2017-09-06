<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminisTarjetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adminis_tarjetas', function (Blueprint $table) {
            $table->increments('id')->nocache();
            $table->integer('porcentaje');
            $table->enum('estado',['A','I'])->default('A');
            $table->string('tarjeta_codigo')->index();
            $table->foreign('tarjeta_codigo')->references('codigo')->on('tipo_tarjetas')->onDelete('cascade');
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
        Schema::dropIfExists('adminis_tarjetas');
    }
}
