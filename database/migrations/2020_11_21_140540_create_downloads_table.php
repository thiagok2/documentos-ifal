<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDownloadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('downloads', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('documento_id')->unsigned()->nullable();
            $table->foreign('documento_id')->references('id')->on('documentos');
            $table->timestamp('data_download')->useCurrent();


            $table->string('ip',20)->default('NA');
            $table->string('pais',100)->default('NA');
            $table->string('regiao',100)->default('NA');
            $table->string('cidade',100)->default('NA');
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('downloads');
    }
}
