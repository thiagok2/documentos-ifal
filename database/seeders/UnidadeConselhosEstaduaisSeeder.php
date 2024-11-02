<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Unidade;

class UnidadeConselhosEstaduaisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Unidade::create([
            'nome' => 'IFAL - RIO LARGO', 
            'tipo' => 'Campus', 
            'esfera' => 'Municipal',
            'email' => 'dg.riolargo@ifal.edu.br',
            'url' => 'https://www2.ifal.edu.br/campus/riolargo',
            'sigla' => 'IFAL',
            'contato' => '',
            'contato2' => '',
            'endereco' => 'Rio Largo - AL, CEP: 57100-000', 
            'telefone' => '(82)2126-6290',
            'user_id' => '1',
            'estado_id' => 1,
            'friendly_url' => '',
            'confirmado' => true
        ]);

        Unidade::create([
            'nome' => 'IFAL - MACEIÓ', 
            'tipo' => 'Campus', 
            'esfera' => 'Municipal',
            'email' => 'dg.maceio@ifal.edu.br',
            'url' => 'https://www2.ifal.edu.br/campus/maceio',
            'sigla' => 'IFAL',
            'contato' => '',
            'contato2' => '',
            'endereco' => 'R. Mizael Domingues, 530 - Centro, Maceió - AL, 57020-600', 
            'telefone' => null,
            'user_id' => '1',
            'estado_id' => 1,
            'friendly_url' => '',
            'confirmado' => true
        ]);

        Unidade::create([
            'nome' => 'IFAL - ARAPIRACA', 
            'tipo' => 'Campus', 
            'esfera' => 'Municipal',
            'email' => 'dg.arapiraca@ifal.edu.br',
            'url' => 'https://www2.ifal.edu.br/campus/arapiraca',
            'sigla' => 'IFAL',
            'contato' => '',
            'contato2' => '',
            'endereco' => 'Rodovia estadual AL-110, 359, bairro Deputado Nezinho, Arapiraca. Cep 57.317', 
            'telefone' => null,
            'user_id' => '1',
            'estado_id' => 1,
            'friendly_url' => '',
            'confirmado' => true
        ]);

        Unidade::create([
            'nome' => 'IFAL - BATALHA', 
            'tipo' => 'Campus', 
            'esfera' => 'Municipal',
            'email' => 'dg.batalha@ifal.edu.br',
            'url' => 'https://www2.ifal.edu.br/campus/batalha',
            'sigla' => 'IFAL',
            'contato' => '',
            'contato2' => '',
            'endereco' => 'Av. Afrânio Lages, 391-453, Batalha - AL, 57420-000', 
            'telefone' => null,
            'user_id' => '1',
            'estado_id' => 1,
            'friendly_url' => '',
            'confirmado' => true
        ]);

        Unidade::create([
            'nome' => 'IFAL - BENEDITO BENTES', 
            'tipo' => 'Campus', 
            'esfera' => 'Municipal',
            'email' => 'dg.beneditobentes@ifal.edu.br',
            'url' => 'https://www2.ifal.edu.br/campus/benedito',
            'sigla' => 'IFAL',
            'contato' => '',
            'contato2' => '',
            'endereco' => 'Avenida Benedito Bentes - Benedito Bentes II, Maceió - AL, 57084-651', 
            'telefone' => null,
            'user_id' => '1',
            'estado_id' => 1,
            'friendly_url' => '',
            'confirmado' => true
        ]);

        Unidade::create([
            'nome' => 'IFAL - CORURIPE', 
            'tipo' => 'Campus', 
            'esfera' => 'Municipal',
            'email' => 'dg.coruripe@ifal.edu.br',
            'url' => 'https://www2.ifal.edu.br/campus/maragogi',
            'sigla' => 'IFAL',
            'contato' => '',
            'contato2' => '',
            'endereco' => 'Rodovia Engenheiro Guttemberg Brêda Neto - Alto do km 82 - AL 101 Sul - Alto do Cruzeiro, Coruripe - AL, 57230-000', 
            'telefone' => null,
            'user_id' => '1',
            'estado_id' => 1,
            'friendly_url' => '',
            'confirmado' => true
        ]);


        Unidade::create([
            'nome' => 'IFAL - MARAGOGI', 
            'tipo' => 'Campus', 
            'esfera' => 'Municipal',
            'email' => 'dg.maragogi@ifal.edu.br',
            'url' => 'https://www2.ifal.edu.br/campus/coruripe',
            'sigla' => 'IFAL',
            'contato' => '',
            'contato2' => '',
            'endereco' => 'Rodovia AL-101 Norte, Km 139. Bairro: Peroba, Maragogi - AL, 57955-000', 
            'telefone' => null,
            'user_id' => '1',
            'estado_id' => 1,
            'friendly_url' => '',
            'confirmado' => true
        ]);


        Unidade::create([
            'nome' => 'IFAL - MARACHEL DEODORO', 
            'tipo' => 'Campus', 
            'esfera' => 'Municipal',
            'email' => 'dg.marechal@ifal.edu.br',
            'url' => 'https://www2.ifal.edu.br/campus/marechal',
            'sigla' => 'IFAL',
            'contato' => '',
            'contato2' => '',
            'endereco' => 'R. da Matança (Rua Lourival Alfredo), 176 - Poeira, Mal. Deodoro - AL, 57160-000', 
            'telefone' => null,
            'user_id' => '1',
            'estado_id' => 1,
            'friendly_url' => '',
            'confirmado' => true
        ]);

        Unidade::create([
            'nome' => 'IFAL - PALMEIRA DOS ÍNDIOS ', 
            'tipo' => 'Campus', 
            'esfera' => 'Municipal',
            'email' => 'dg.palmeira@ifal.edu.br',
            'url' => 'https://www2.ifal.edu.br/campus/palmeira',
            'sigla' => 'IFAL',
            'contato' => '',
            'contato2' => '',
            'endereco' => 'Av. Alagoas, S/N - Palmeira de Fora, Palmeira dos Índios - AL, 57608-180', 
            'telefone' => null,
            'user_id' => '1',
            'estado_id' => 1,
            'friendly_url' => '',
            'confirmado' => true
        ]);


        Unidade::create([
            'nome' => 'IFAL - MURICI', 
            'tipo' => 'Campus', 
            'esfera' => 'Municipal',
            'email' => 'dg.murici@ifal.edu.br',
            'url' => 'https://www2.ifal.edu.br/campus/murici',
            'sigla' => 'IFAL',
            'contato' => '',
            'contato2' => '',
            'endereco' => 'BR-104, 111, Murici - AL, 57820-000', 
            'telefone' => null,
            'user_id' => '1',
            'estado_id' => 1,
            'friendly_url' => '',
            'confirmado' => true
        ]);


        Unidade::create([
            'nome' => 'IFAL - PENEDO', 
            'tipo' => 'Campus', 
            'esfera' => 'Municipal',
            'email' => 'dg.penedo@ifal.edu.br',
            'url' => 'https://www2.ifal.edu.br/campus/penedo',
            'sigla' => 'IFAL',
            'contato' => '',
            'contato2' => '',
            'endereco' => 'Rod. Eng. Joaquim Gonçalves - Dom Constantino, Penedo - AL, 57200-000', 
            'telefone' => null,
            'user_id' => '1',
            'estado_id' => 1,
            'friendly_url' => '',
            'confirmado' => true
        ]);

        Unidade::create([
            'nome' => 'IFAL - PIRANHAS', 
            'tipo' => 'Campus', 
            'esfera' => 'Municipal',
            'email' => 'dg.piranhas@ifal.edu.br',
            'url' => 'https://www2.ifal.edu.br/campus/piranhas',
            'sigla' => 'IFAL',
            'contato' => '',
            'contato2' => '',
            'endereco' => 'Av. Sergipe, 1477 - Piranhas, AL, 57460-000', 
            'telefone' => null,
            'user_id' => '1',
            'estado_id' => 1,
            'friendly_url' => '',
            'confirmado' => true
        ]);

        Unidade::create([
            'nome' => 'IFAL - SATANA DE IPANEMA', 
            'tipo' => 'Campus', 
            'esfera' => 'Municipal',
            'email' => 'dg.santana@ifal.edu.br',
            'url' => 'https://www2.ifal.edu.br/campus/santana',
            'sigla' => 'IFAL',
            'contato' => '',
            'contato2' => '',
            'endereco' => 'Rodovia AL 130, Km 4, Nº 1609, R. Domingos Acácio, Santana do Ipanema - AL, 57500-000', 
            'telefone' => null,
            'user_id' => '1',
            'estado_id' => 1,
            'friendly_url' => '',
            'confirmado' => true
        ]);

        Unidade::create([
            'nome' => 'IFAL - SÃO MIGUEL DOS CAMPOS', 
            'tipo' => 'Campus', 
            'esfera' => 'Municipal',
            'email' => 'dg.saomiguel@ifal.edu.br',
            'url' => 'https://www2.ifal.edu.br/campus/saomiguel',
            'sigla' => 'IFAL',
            'contato' => '',
            'contato2' => '',
            'endereco' => 'Loteamento Hélio Jatobá III, Quadra B6 Hélio Jatobá III, São Miguel dos Campos - AL, 57246-615', 
            'telefone' => null,
            'user_id' => '1',
            'estado_id' => 1,
            'friendly_url' => '',
            'confirmado' => true
        ]);

        Unidade::create([
            'nome' => 'IFAL - SATUBA', 
            'tipo' => 'Campus', 
            'esfera' => 'Municipal',
            'email' => ' dg.satuba@ifal.edu.br',
            'url' => 'https://www2.ifal.edu.br/campus/satuba',
            'sigla' => 'IFAL',
            'contato' => '',
            'contato2' => '',
            'endereco' => 'R. Dezessete de Agosto, s/n - Zona Rural, Satuba - AL, 57120-000', 
            'telefone' => null,
            'user_id' => '1',
            'estado_id' => 1,
            'friendly_url' => '',
            'confirmado' => true
        ]);


        Unidade::create([
            'nome' => 'IFAL - VIÇOSA', 
            'tipo' => 'Campus', 
            'esfera' => 'Municipal',
            'email' => 'dg.vicosa@ifal.edu.br',
            'url' => 'https://www2.ifal.edu.br/campus/vicosa',
            'sigla' => 'IFAL',
            'contato' => '',
            'contato2' => '',
            'endereco' => 'R. Dezessete de Agosto, s/n - Zona Rural, Satuba - AL, 57120-000', 
            'telefone' => null,
            'user_id' => '1',
            'estado_id' => 1,
            'friendly_url' => '',
            'confirmado' => true
        ]);
    }
}
