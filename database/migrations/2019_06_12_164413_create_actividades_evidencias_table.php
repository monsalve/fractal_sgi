<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActividadesEvidenciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actividades_evidencias', function (Blueprint $table) {
            $table->increments('id');
            $table->string('evidencia', 50);
            $table->string('ruta');
            $table->unsignedInteger('fk_id_actividades_seguimientos');
            $table->foreign('fk_id_actividades_seguimientos')->references('id')->on('actividades_seguimientos');
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
        Schema::dropIfExists('actividades_evidencias');
    }
}
