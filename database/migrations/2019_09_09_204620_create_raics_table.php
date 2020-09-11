<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRaicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('raics', function (Blueprint $table) {
            $table->increments('id');
            $table->string('rpDocumento', 15)->nullable();
            $table->string('rpNombresCompletos', 80)->nullable();
            $table->string('rpCargo', 80)->nullable();
            $table->string('rpAreaTrabajo', 80);
            $table->string('jiDocumento', 15)->nullable();
            $table->string('jiNombresCompletos', 80)->nullable();
            $table->string('jiCargo', 80)->nullable();
            $table->string('supDocumento', 15)->nullable();
            $table->string('supNombresCompletos', 80)->nullable();
            $table->date('fechaReporte');
            $table->enum('tipoEvento', ['1', '2', '3', '4']);
            $table->string('areaTrabajo', 80);
            $table->date('fechaEvento');
            $table->string('sitioEspecifico', 100);
            $table->char('prioridad', 2);
            $table->varchar('descripcionCorta', 50);
            $table->string('descripcionEvento', 1000);
            $table->enum('haySeguimiento', ['No', 'Si'])->default('Si');
            $table->enum('condicion', ['1', '2'])->default('1');
            $table->unsignedTinyInteger('estado')->default(1);
            
            // NO VA A HABER TIPO SOLICITUD, EN CAMBIO SE CONSIDERA INCLUIR INFORMACION DESCRIPTIVA EN LA MATRIZ RAIC DE SER NECESARIA.
            $table->unsignedInteger('fk_id_empresas');
            $table->foreign('fk_id_empresas')->references('id')->on('empresas');
            $table->unsignedInteger('fk_id_proyectos');
            $table->foreign('fk_id_proyectos')->references('id')->on('proyectos');
            $table->unsignedInteger('fk_id_raics_fuentes');
            $table->foreign('fk_id_raics_fuentes')->references('id')->on('raics_fuentes');
            $table->unsignedInteger('fk_id_raics_tipos_desviaciones');
            $table->foreign('fk_id_raics_tipos_desviaciones')->references('id')->on('raics_tipos_desviaciones');
            $table->unsignedInteger('fk_piJefInmed_users')->nullable();
            $table->foreign('fk_piJefInmed_users')->references('id')->on('users');
            $table->unsignedInteger('fk_piRepPor_users')->nullable();
            $table->foreign('fk_piRepPor_users')->references('id')->on('users');
            $table->unsignedInteger('fk_piSuperv_users')->nullable();
            $table->foreign('fk_piSuperv_users')->references('id')->on('users');

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
        Schema::dropIfExists('raics');
    }
}
