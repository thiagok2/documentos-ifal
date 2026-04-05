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
        Schema::create('pnld_avaliacaos', function (Blueprint $table) {
            $table->id();
            // Adicionando os campos do nosso formulário:
            $table->string('nome_avaliador');
            $table->string('codigo_obra');
            $table->text('parecer');
            
            $table->timestamps(); // Cria as colunas created_at e updated_at
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pnld_avaliacaos');
    }
};
