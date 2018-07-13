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
            $table->bigInteger('sucursal_id')->unsigned();
            $table->string('codigo',15)->unique();
            $table->string('uid')->nullable();
            $table->string('mac')->nullable();
            $table->string('imei')->nullable();
            $table->string('celular',10);
            $table->string('numero_activo')->unique();
            $table->string('password');
            $table->enum('estado',['A','I'])->default('A');
            $table->timestamps();

            $table->foreign('sucursal_id')->references('id')->on('sucursales')->onDelete('cascade');
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
