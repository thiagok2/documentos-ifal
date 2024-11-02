<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;

use App\Models\Unidade;
use Illuminate\Support\Facades\DB;


class UnidadeDefaultTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('unidades')->delete();
        $unidadeAdmin = Unidade::create([
            'nome' => 'Reitoria IFAL', 
            'tipo' => 'Reitoria', 
            'esfera' => 'Estadual',
            'admin' => true,
            'email' => 'ifal@email.com.br',
            'url' => 'https://www2.ifal.edu.br/',
            'sigla' => 'IBR',
            'contato' => 'João IFAL',
            'telefone' => '(82)9999-9999',
            'responsavel_id' => '1',
            'user_id' => '1',
            'confirmado' => true, //debug
            'friendly_url' => 'nbr',
        ]);

        DB::table('users')
            ->where('tipo', 'admin')
            ->update(array('unidade_id' => $unidadeAdmin->id));  
       
    }
}
