<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        {
            Schema::table('unidades', function ($table) {
                $table->integer('pai_id')->nullable()->unsigned();
                $table->foreign('pai_id')->references('id')->on('unidades');

            });
    
            // $unidadeAdmin = Unidade::find(1);
            // if($unidadeAdmin){
            //     $unidadeAdmin->admin = true;
            //     $unidadeAdmin->save();
            // }
            
        }    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
