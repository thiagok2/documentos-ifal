<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConstraintsNotNullDocumentos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('documentos', function ($table) {
            $table->integer('unidade_id')->nullable()->change();
            $table->integer('user_id')->nullable()->change();
            $table->integer('tipo_documento_id')->nullable()->change();
            $table->integer('assunto_id')->nullable()->change();
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
