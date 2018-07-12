<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterEmpresas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('empresas', function (Blueprint $table) {
            $table->integer('tipo_documento')->unsigned()->nullable();
            $table->foreign('tipo_documento')->references('tip_codi')->on('tipo_documentos')->onDelete('cascade');
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn('tipo_documento');
        });
    }
}
