<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConveniosEstasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('convenios_estas', function (Blueprint $table) {
            $table->bigIncrements('id')->nocache();
            $table->string('numero_convenio');
            $table->date('fecha');
            $table->bigInteger('establecimiento_id')->unsigned();
            $table->foreign('establecimiento_id')->references('id')->on('establecimientos')->onDelete('cascade');
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
        Schema::dropIfExists('convenios_estas');
    }
}
