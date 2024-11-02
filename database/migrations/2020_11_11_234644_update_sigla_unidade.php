<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use Illuminate\Support\Str;

use App\Models\Unidade;

class UpdateSiglaUnidade extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $unidadesMunicipais = Unidade::where('tipo', '=', 'Conselho')
                ->where('esfera', '=', 'Municipal')->with('estado')->with('municipio')
                ->get();

        foreach($unidadesMunicipais as $unidadeMunicipal){

            $nome_slug = Str::slug($unidadeMunicipal->municipio->nome, '-');

            $unidadeMunicipal->sigla = 'CME-'.strtoupper($nome_slug)."-".strtoupper($unidadeMunicipal->estado->sigla);
            $unidadeMunicipal->update();
        }
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
