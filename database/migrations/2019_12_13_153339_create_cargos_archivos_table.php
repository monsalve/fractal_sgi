<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCargosArchivosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cargos_archivos', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('crear');
            $table->boolean('ver');
            $table->unsignedInteger('fk_id_cargos');
            $table->foreign('fk_id_cargos')->references('id')->on('cargos');
            $table->unsignedInteger('fk_id_segmentos_archivos');
            $table->foreign('fk_id_segmentos_archivos')->references('id')->on('segmentos_archivos');

            $table->unsignedInteger('fk_usuCrea_users');
            $table->foreign('fk_usuCrea_users')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cargos_archivos');
    }
}
