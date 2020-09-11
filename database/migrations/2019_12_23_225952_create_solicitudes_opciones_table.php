<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSolicitudesOpcionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('solicitudes_opciones', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('fk_id_solicitudes');
            $table->foreign('fk_id_solicitudes')->references('id')->on('solicitudes');
            $table->unsignedInteger('fk_id_categorias_opciones');
            $table->foreign('fk_id_categorias_opciones')->references('id')->on('categorias_opciones');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('solicitudes_opciones');
    }
}
