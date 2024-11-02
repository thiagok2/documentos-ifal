<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'tipo' => 'admin',
            'password' => bcrypt('123456')
        ]);

        DB::table('users')->insert([
            'name' => 'Reitoria',
            'email' => 'reitoria@ifal.com.br',
            'tipo' => 'admin',
            'confirmado' => true,
            'password' => bcrypt('123456')
        ]);
    }
}
