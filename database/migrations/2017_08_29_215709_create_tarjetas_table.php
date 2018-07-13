<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTarjetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tarjetas', function (Blueprint $table) {
            $table->bigIncrements('id')->nocache();
            $table->primary('id');
            $table->bigInteger('persona_id')->nullable()->unsigned();
            $table->string('numero_tarjeta')->unique()->index();
            $table->boolean('cambioclave')->default(false);
            $table->string('password');
            $table->enum('estado',['A','I','C','N'])->default('C');
            $table->timestamps();

            $table->foreign('persona_id')->references('id')->on('personas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tarjetas');
    }
}
