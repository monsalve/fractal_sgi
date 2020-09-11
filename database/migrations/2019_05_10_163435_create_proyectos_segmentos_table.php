<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProyectosSegmentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proyectos_segmentos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('fk_id_empresas');
            $table->foreign('fk_id_empresas')->references('id')->on('empresas');
            $table->unsignedInteger('fk_id_proyectos');
            $table->foreign('fk_id_proyectos')->references('id')->on('proyectos');
            $table->unsignedInteger('fk_id_segmentos');
            $table->foreign('fk_id_segmentos')->references('id')->on('segmentos');
            $table->unsignedInteger('fk_usuCrea_users');
            $table->foreign('fk_usuCrea_users')->references('id')->on('users');
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
        Schema::dropIfExists('proyectos_segmentos');
    }
}
