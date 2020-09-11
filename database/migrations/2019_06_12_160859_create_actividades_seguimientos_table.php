<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActividadesSeguimientosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actividades_seguimientos', function (Blueprint $table) {
            $table->increments('id');
            $table->char('mes', 3);
            $table->boolean('aplica');
            $table->date('fechaReporte');
            $table->text('observacion');
            $table->unsignedInteger('fk_id_actividades');
            $table->foreign('fk_id_actividades')->references('id')->on('actividades');
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
        Schema::dropIfExists('actividades_seguimientos');
    }
}
