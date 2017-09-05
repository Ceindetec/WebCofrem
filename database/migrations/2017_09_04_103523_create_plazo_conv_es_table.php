<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlazoConvEsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plazo_conv_es', function (Blueprint $table) {
            $table->bigIncrements('id')->nocache();
            $table->integer('dias');
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
        Schema::dropIfExists('plazo_conv_es');
    }
}
