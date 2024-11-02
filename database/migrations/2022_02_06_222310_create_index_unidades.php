<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndexUnidades extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //u.sigla, u.friendly_url, u.nome unidade
        Schema::table('unidades', function ($table) {
            $table->index(['sigla', 'friendly_url', 'nome']);
            $table->index(['estado_id']);
            $table->index(['municipio_id']);
            $table->index(['estado_id','municipio_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
