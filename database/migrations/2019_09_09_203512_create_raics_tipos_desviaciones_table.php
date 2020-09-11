<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRaicsTiposDesviacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('raics_tipos_desviaciones', function (Blueprint $table) {
            $table->increments('id');
            $table->string('raicTipoDesviacion', 100);
            $table->boolean('estado')->default(1);
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
        Schema::dropIfExists('raics_tipos_desviaciones');
    }
}
