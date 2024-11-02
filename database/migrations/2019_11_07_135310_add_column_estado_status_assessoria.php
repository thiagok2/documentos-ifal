<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnEstadoStatusAssessoria extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('estados', function ($table) {
            $table->boolean('possui_assessoria')->default(false);
        });

        DB::statement("UPDATE estados SET possui_assessoria = true WHERE (SELECT count(*) FROM unidades WHERE unidades.estado_id = estados.id and tipo = 'Assessoria' and esfera = 'Estadual' ) >= 1");
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
