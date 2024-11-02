<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class AddCapitalMunicipio extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('municipios')
            ->whereIn('codigo_ibge',['2704302',])
            ->update(['capital' => true]);
    }
}
