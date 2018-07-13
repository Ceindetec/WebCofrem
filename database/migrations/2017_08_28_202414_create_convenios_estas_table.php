<?php

use Carbon\Carbon;
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
            $table->bigInteger('establecimiento_id')->unsigned();
            $table->string('numero_convenio');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->default(Carbon::now());
            $table->boolean('prorrogable')->default('1');
            $table->enum('estado',['A','I','P'])->default('A');
            $table->timestamps();

            $table->foreign('establecimiento_id')->references('id')->on('establecimientos')->onDelete('cascade');
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
