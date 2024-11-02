<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Assunto;
use Illuminate\Support\Facades\DB;

class AssuntoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('assuntos')->delete();        
        Assunto::create(['id' => 0, 'nome' => 'Assunto Desconhecido']);
        Assunto::create(['nome' => 'Ensino']);
        Assunto::create(['nome' => 'Pesquisa']);
        Assunto::create(['nome' => 'Extensão']);
        Assunto::create(['nome' => 'Recursos Humanos']);        
        Assunto::create(['nome' => 'Biblioteca']);
        Assunto::create(['nome' => 'Monitoria e Ações Integradas']);
    }
}
