<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTerminalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('terminales', function (Blueprint $table) {
            $table->bigIncrements('id')->nocache();
            $table->string('codigo');
            $table->string('uid')->nullable();
            $table->string('mac')->nullable();
            $table->string('imei')->nullable();
            $table->string('password');
            $table->bigInteger('sucursal_id')->unsigned();
            $table->foreign('sucursal_id')->references('id')->on('sucursales')->onDelete('cascade');
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
        Schema::dropIfExists('terminales');
    }
}
