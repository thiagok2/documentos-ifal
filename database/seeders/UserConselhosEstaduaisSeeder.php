<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;

use App\Models\Unidade;
use App\Models\User;

use Illuminate\Support\Facades\Hash;

class UserConselhosEstaduaisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $unidades = Unidade::where('tipo', '=', 'Campus')
                ->where('esfera', '=', 'Municipal')
                ->get();

        foreach($unidades as $unidade){
            $email = trim(explode(";",$unidade->email)[0]);
            $nome = $unidade->nome;
            $senha = Hash::make('123456') ;

            $user = User::create([
                'name' => $nome,
                'email' => $email,
                'password' => $senha,
                'unidade_id' => $unidade->id,
                'confirmado' => true,
                'tipo' => 'gestor'
            ]);

            $unidade->responsavel()->associate($user);

            $unidade->save();

        }

                
    }
}
