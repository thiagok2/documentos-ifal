<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Unidade;
use App\Models\Estado;

class BrasilEstadoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Estado::create(['id' => 2, 'nome' => 'Brasil', 'sigla' => 'BR']);

        Unidade::whereNull('estado_id')->update([ 'estado_id' => 1]);
    }
}
