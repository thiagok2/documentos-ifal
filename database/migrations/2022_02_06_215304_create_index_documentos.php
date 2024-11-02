<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndexDocumentos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //dd.id, dd.titulo,dd.numero, dd.arquivo, dd.ementa, dd.ano, dd.data_publicacao
        Schema::table('documentos', function ($table) {
            $table->index(['unidade_id']);
            $table->index(['assunto_id']);
            $table->index(['tipo_documento_id']);
            //$table->index(['numero']);
            //$table->index(['arquivo']);
            $table->index(['titulo', 'numero', 'arquivo','ano','data_publicacao']);
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
