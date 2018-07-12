<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRangoConvEsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rango_conv_es', function (Blueprint $table) {
            $table->bigIncrements('id')->nocache();
            $table->double('valor_min',15,2);
            $table->double('valor_max',15,2);
            $table->integer('dias');
            $table->integer('porcentaje');
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
        Schema::dropIfExists('rango_conv_es');
    }
}
