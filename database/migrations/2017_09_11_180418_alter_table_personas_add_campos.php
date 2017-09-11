<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTablePersonasAddCampos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('personas', function (Blueprint $table) {
            $table->string('direccion')->nullable();
            $table->string('latitud')->nullable();
            $table->string('longitud')->nullable();
            $table->enum('sexo',['M','F'])->default('M');
            $table->date('fecha_nacimiento')->nullable();
            $table->enum('tipo_perosna',['A','T'])->default('T');
            $table->bigInteger('padre_id')->nullable();
            $table->string('municipio_codigo')->nullable()->index();
            $table->foreign('municipio_codigo')->references('codigo')->on('municipios')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('personas', function (Blueprint $table) {
            $table->dropColumn('direccion');
            $table->dropColumn('latitud');
            $table->dropColumn('longitud');
            $table->dropColumn('sexo');
            $table->dropColumn('fecha_nacimiento');
            $table->dropColumn('padre_id');
            $table->dropColumn('municipio_codigo');
            $table->dropColumn('tipo_perosna');
        });
    }
}
