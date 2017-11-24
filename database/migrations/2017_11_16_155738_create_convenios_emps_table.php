<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConveniosEmpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('convenios_emps', function (Blueprint $table) {
            $table->bigIncrements('id')->nocache();
            $table->string('numero_convenio');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->bigInteger('empresa_id')->unsigned();
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
            $table->enum("estado", ["A","I","P"]); // A -> Activo , Inactivo, pendiente
            $table->string('pdf');
            $table->enum("tipo", ["L","C","A"]); // L -> libre inversion , C ->cupo rotativo, A -> ambos (L y C)
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
        Schema::dropIfExists('convenios_emps');
    }
}
