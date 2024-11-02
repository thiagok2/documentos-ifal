<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixAssessoria extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // DB::statement("UPDATE unidades SET tipo = 'Assessoria' WHERE tipo = 'Acessoria'");

        // Schema::table('estados', function (Blueprint $table) {
        //     $table->renameColumn('possui_acessoria', 'possui_assessoria');
        // });
        
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
