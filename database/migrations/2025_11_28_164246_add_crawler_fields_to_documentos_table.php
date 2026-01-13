<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documentos', function (Blueprint $table) {
            if (!Schema::hasColumn('documentos', 'titulo')) {
                $table->string('titulo')->nullable();
            }
            if (!Schema::hasColumn('documentos', 'url')) {
                $table->text('url')->nullable();
            }
            if (!Schema::hasColumn('documentos', 'conteudo')) {
                $table->longText('conteudo')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('documentos', function (Blueprint $table) {
            $table->dropColumn(['titulo', 'url', 'conteudo']);
        });
    }
};
