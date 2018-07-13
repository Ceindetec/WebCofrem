<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDuplicadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('duplicados', function (Blueprint $table) {
            $table->bigIncrements('id')->nochae();
            $table->string('oldtarjeta');
            $table->string('newtarjeta');
            $table->date('fecha');
            $table->timestamps();

            $table->foreign('oldtarjeta')->references('numero_tarjeta')->on('tarjetas')->onDelete('cascade');
            $table->foreign('newtarjeta')->references('numero_tarjeta')->on('tarjetas')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('duplicados');
    }
}
