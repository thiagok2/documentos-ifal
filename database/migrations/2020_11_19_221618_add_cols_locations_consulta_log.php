<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColsLocationsConsultaLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('consultas', function ($table) {
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
        //
    }
}
