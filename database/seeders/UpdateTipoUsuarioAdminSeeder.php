<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;

class UpdateTipoUsuarioAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::where('tipo','administrador(a)')->update(['tipo' =>'admin']);
    }
}
