<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAsignacionesEvidenciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asignaciones_evidencias', function (Blueprint $table) {
            $table->increments('id');
            $table->string('evidencia');
            $table->string('ruta' ,100);
            $table->boolean('estado')->default(1);
            $table->unsignedInteger('fk_id_asignaciones');
            $table->foreign('fk_id_asignaciones')->references('id')->on('asignaciones');
            $table->unsignedInteger('fk_usuCrea_users');
            $table->foreign('fk_usuCrea_users')->references('id')->on('users');
            $table->unsignedInteger('fk_usuActualiza_users');
            $table->foreign('fk_usuActualiza_users')->references('id')->on('users');
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
        Schema::dropIfExists('asignaciones_evidencias');
    }
}
