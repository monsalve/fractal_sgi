<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRaicsEvidenciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('raics_evidencias', function (Blueprint $table) {
            $table->increments('id');
            $table->string('evidencia', 50);
            $table->string('ruta');
            $table->unsignedInteger('fk_id_raics_contingencias');
            $table->foreign('fk_id_raics_contingencias')->references('id')->on('raics_contingencias');
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
        Schema::dropIfExists('raics_evidencias');
    }
}
