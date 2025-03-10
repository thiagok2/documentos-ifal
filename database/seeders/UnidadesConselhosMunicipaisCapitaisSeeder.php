<?php
namespace Database\Seeders;

use App\Models\Municipio;
use App\Models\Unidade;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UnidadesConselhosMunicipaisCapitaisSeeder extends Seeder//Duvida_caio-> como isso se traduz pro novo sistema de Departamento, Campus e Coordenação 
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $capitais = Municipio::where('capital',true)->with('estado')->get();

        foreach($capitais as $capital){

            $nome_slug = Str::slug($capital->nome, '-');

            $unidadeCapital = Unidade::where('municipio_id', $capital->id)->first();

            if($unidadeCapital === null){
                $unidadeCapital = Unidade::create([
                    'nome' => 'CONSELHO MUNICIPAL DE EDUCAÇÃO DE '.strtoupper($capital->nome), 
                    'tipo' => 'Conselho', 
                    'esfera' => 'Municipal',
                    'email' => $nome_slug.'@'.strtoupper('cme-'.$nome_slug).'.com.br',
                    'url' => null,
                    'sigla' => 'CME-'.strtoupper($nome_slug)."-".strtoupper($capital->estado->sigla),
                    'user_id' => '1',
                    'estado_id' => $capital->estado->id,
                    'municipio_id' => $capital->id,
                    'friendly_url' => strtolower('CME-'.Str::slug($capital->nome, '-')),
                    'confirmado' => false
                ]);
            }
            
            $gestorCapital = User::where('tipo', 'gestor')->where('unidade_id', $unidadeCapital->id)->first();

            if($gestorCapital === null){
                $gestorCapital = User::create([
                    'name' => 'Gestor '.$unidadeCapital->nome,
                    'email' => $unidadeCapital->email,
                    'password' => Hash::make('987654321'),
                    'unidade_id' => $unidadeCapital->id,
                    'tipo' => 'gestor'
                ]);
            }
            
            $roboCapital = User::where('tipo', User::TIPO_EXTRATOR)->where('unidade_id', $unidadeCapital->id)->first();

            if($roboCapital === null){
                $roboCapital = User::create([
                    'name' => 'Robô Extrator '.$unidadeCapital->sigla,
                    'email' => strtolower($unidadeCapital->sigla).'@extrator.com.br',
                    'password' =>  Hash::make('extrator'),
                    'unidade_id' => $unidadeCapital->id,
                    'tipo' => User::TIPO_EXTRATOR
                ]);
            }
            

            $unidadeCapital->responsavel()->associate($gestorCapital);
            $unidadeCapital->save();
            
        }
    }
}