<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRaicsContingenciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('raics_contingencias', function (Blueprint $table) {
            $table->increments('id');
            $table->string('consecutivo', 30);
            $table->string('accion', 100);
            $table->date('fechaLimite');
            $table->text('observacionAlCerrar')->nullable();
            $table->text('observacionAlAbrir')->nullable();
            $table->enum('condicion', ['Abierto', 'Cerrado'])->default('Abierto');
            $table->boolean('estado')->default(1);
            $table->unsignedInteger('fk_id_empresas');
            $table->foreign('fk_id_empresas')->references('id')->on('empresas');
            $table->unsignedInteger('fk_id_raics');
            $table->foreign('fk_id_raics')->references('id')->on('raics');
            $table->unsignedInteger('fk_respons_users');
            $table->foreign('fk_respons_users')->references('id')->on('users');
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
        Schema::dropIfExists('raics_contingencias');
    }
}
