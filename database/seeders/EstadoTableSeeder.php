<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Estado;
class EstadoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('estados')->delete();
        Estado::create(['id' => 1, 'nome' => 'Alagoas', 'sigla' => 'AL']);
    }

}
