<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClearUnidadeUncme extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {//Duvida_caio-> como isso se traduz pro novo sistema de Departamento, Campus e Coordenação 
        DB::statement("DELETE FROM unidades WHERE email like 'alterar_email%'");

        DB::statement("UPDATE municipios SET criado = false");

        DB::statement("UPDATE municipios SET criado = true WHERE (SELECT count(*) FROM unidades WHERE unidades.municipio_id = municipios.id and esfera = 'Municipal') >= 1;");
    }
}
