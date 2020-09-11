<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAsignacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asignaciones', function (Blueprint $table) {
            $table->increments('id');
            $table->date('fechaLimite');
            $table->string('asignacion', 50);
            $table->string('observacion', 100);
            $table->text('alCerrar')->nullable();
            $table->text('alAbrir')->nullable();
            $table->enum('condicion', ['Abierto', 'Cerrado'])->default('Abierto');
            $table->boolean('estado')->default(1);
            $table->unsignedInteger('fk_id_empresas');
            $table->foreign('fk_id_empresas')->references('id')->on('empresas');
            $table->unsignedInteger('fk_id_pendientes');
            $table->foreign('fk_id_pendientes')->references('id')->on('pendientes');
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
        Schema::dropIfExists('asignaciones');
    }
}
