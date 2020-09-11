<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCausasNovedadesItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('causas_novedades_items', function (Blueprint $table) {
            $table->increments('id');
            $table->char('codigo', 4)->unique();
            $table->string('causaNovedadItem', 100);
            $table->unsignedInteger('fk_id_causas_novedades');
            $table->foreign('fk_id_causas_novedades')->references('id')->on('causas_novedades');
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
        Schema::dropIfExists('causas_novedades_items');
    }
}
