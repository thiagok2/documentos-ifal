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
            'esfera' => 'Campus',
            'email' => 'dg.riolargo@ifal.edu.br',
            'url' => 'https://www2.ifal.edu.br/campus/riolargo',
            'sigla' => 'IFAL',
            'contato' => 'João RIO LARGO',
            'contato2' => '',
            'endereco' => 'Rio Largo - AL, CEP: 57100-000', 
            'telefone' => '(82)2126-6290',
            'responsavel_id' => '1',
            'user_id' => '1',
            'estado_id' => 1,
            'friendly_url' => '',
            'confirmado' => true
        ]);

        Unidade::create([
            'nome' => 'IFAL - MACEIÓ', 
            'tipo' => 'Campus', 
            'esfera' => 'Campus',
            'email' => 'dg.maceio@ifal.edu.br',
            'url' => 'https://www2.ifal.edu.br/campus/maceio',
            'sigla' => 'IFAL',
            'contato' => 'João MACEIÓ',
            'contato2' => '',
            'endereco' => 'R. Mizael Domingues, 530 - Centro, Maceió - AL, 57020-600', 
            'telefone' => null,
            'responsavel_id' => '1',
            'user_id' => '1',
            'estado_id' => 1,
            'friendly_url' => '',
            'confirmado' => true
        ]);

        Unidade::create([
            'nome' => 'IFAL - ARAPIRACA', 
            'tipo' => 'Campus', 
            'esfera' => 'Campus',
            'email' => 'dg.arapiraca@ifal.edu.br',
            'url' => 'https://www2.ifal.edu.br/campus/arapiraca',
            'sigla' => 'IFAL',
            'contato' => 'João ARAPIRACA',
            'contato2' => '',
            'endereco' => 'Rodovia estadual AL-110, 359, bairro Deputado Nezinho, Arapiraca. Cep 57.317', 
            'telefone' => null,
            'responsavel_id' => '1',
            'user_id' => '1',
            'estado_id' => 1,
            'friendly_url' => '',
            'confirmado' => true
        ]);

        Unidade::create([
            'nome' => 'IFAL - BATALHA', 
            'tipo' => 'Campus', 
            'esfera' => 'Campus',
            'email' => 'dg.batalha@ifal.edu.br',
            'url' => 'https://www2.ifal.edu.br/campus/batalha',
            'sigla' => 'IFAL',
            'contato' => 'João BATALHA',
            'contato2' => '',
            'endereco' => 'Av. Afrânio Lages, 391-453, Batalha - AL, 57420-000', 
            'telefone' => null,
            'responsavel_id' => '1',
            'user_id' => '1',
            'estado_id' => 1,
            'friendly_url' => '',
            'confirmado' => true
        ]);

        Unidade::create([
            'nome' => 'IFAL - BENEDITO BENTES', 
            'tipo' => 'Campus', 
            'esfera' => 'Campus',
            'email' => 'dg.beneditobentes@ifal.edu.br',
            'url' => 'https://www2.ifal.edu.br/campus/benedito',
            'sigla' => 'IFAL',
            'contato' => 'João BENEDITO BENTES',
            'contato2' => '',
            'endereco' => 'Avenida Benedito Bentes - Benedito Bentes II, Maceió - AL, 57084-651', 
            'telefone' => null,
            'responsavel_id' => '1',
            'user_id' => '1',
            'estado_id' => 1,
            'friendly_url' => '',
            'confirmado' => true
        ]);

        Unidade::create([
            'nome' => 'IFAL - CORURIPE', 
            'tipo' => 'Campus', 
            'esfera' => 'Campus',
            'email' => 'dg.coruripe@ifal.edu.br',
            'url' => 'https://www2.ifal.edu.br/campus/maragogi',
            'sigla' => 'IFAL',
            'contato' => 'João CORURIPE',
            'contato2' => '',
            'endereco' => 'Rodovia Engenheiro Guttemberg Brêda Neto - Alto do km 82 - AL 101 Sul - Alto do Cruzeiro, Coruripe - AL, 57230-000', 
            'telefone' => null,
            'responsavel_id' => '1',
            'user_id' => '1',
            'estado_id' => 1,
            'friendly_url' => '',
            'confirmado' => true
        ]);


        Unidade::create([
            'nome' => 'IFAL - MARAGOGI', 
            'tipo' => 'Campus', 
            'esfera' => 'Campus',
            'email' => 'dg.maragogi@ifal.edu.br',
            'url' => 'https://www2.ifal.edu.br/campus/coruripe',
            'sigla' => 'IFAL',
            'contato' => 'João MARAGOGI',
            'contato2' => '',
            'endereco' => 'Rodovia AL-101 Norte, Km 139. Bairro: Peroba, Maragogi - AL, 57955-000', 
            'telefone' => null,
            'responsavel_id' => '1',
            'user_id' => '1',
            'estado_id' => 1,
            'friendly_url' => '',
            'confirmado' => true
        ]);


        Unidade::create([
            'nome' => 'IFAL - MARACHEL DEODORO', 
            'tipo' => 'Campus', 
            'esfera' => 'Campus',
            'email' => 'dg.marechal@ifal.edu.br',
            'url' => 'https://www2.ifal.edu.br/campus/marechal',
            'sigla' => 'IFAL',
            'contato' => 'João MARACHEL DEODORO',
            'contato2' => '',
            'endereco' => 'R. da Matança (Rua Lourival Alfredo), 176 - Poeira, Mal. Deodoro - AL, 57160-000', 
            'telefone' => null,
            'responsavel_id' => '1',
            'user_id' => '1',
            'estado_id' => 1,
            'friendly_url' => '',
            'confirmado' => true
        ]);

        Unidade::create([
            'nome' => 'IFAL - PALMEIRA DOS ÍNDIOS', 
            'tipo' => 'Campus', 
            'esfera' => 'Campus',
            'email' => 'dg.palmeira@ifal.edu.br',
            'url' => 'https://www2.ifal.edu.br/campus/palmeira',
            'sigla' => 'IFAL',
            'contato' => 'João PALMEIRA DOS ÍNDIOS',
            'contato2' => '',
            'endereco' => 'Av. Alagoas, S/N - Palmeira de Fora, Palmeira dos Índios - AL, 57608-180', 
            'telefone' => null,
            'responsavel_id' => '1',
            'user_id' => '1',
            'estado_id' => 1,
            'friendly_url' => '',
            'confirmado' => true
        ]);


        Unidade::create([
            'nome' => 'IFAL - MURICI', 
            'tipo' => 'Campus', 
            'esfera' => 'Campus',
            'email' => 'dg.murici@ifal.edu.br',
            'url' => 'https://www2.ifal.edu.br/campus/murici',
            'sigla' => 'IFAL',
            'contato' => 'João MURICI',
            'contato2' => '',
            'endereco' => 'BR-104, 111, Murici - AL, 57820-000', 
            'telefone' => null,
            'responsavel_id' => '1',
            'user_id' => '1',
            'estado_id' => 1,
            'friendly_url' => '',
            'confirmado' => true
        ]);


        Unidade::create([
            'nome' => 'IFAL - PENEDO', 
            'tipo' => 'Campus', 
            'esfera' => 'Campus',
            'email' => 'dg.penedo@ifal.edu.br',
            'url' => 'https://www2.ifal.edu.br/campus/penedo',
            'sigla' => 'IFAL',
            'contato' => 'João PENEDO',
            'contato2' => '',
            'endereco' => 'Rod. Eng. Joaquim Gonçalves - Dom Constantino, Penedo - AL, 57200-000', 
            'telefone' => null,
            'responsavel_id' => '1',
            'user_id' => '1',
            'estado_id' => 1,
            'friendly_url' => '',
            'confirmado' => true
        ]);

        Unidade::create([
            'nome' => 'IFAL - PIRANHAS', 
            'tipo' => 'Campus', 
            'esfera' => 'Campus',
            'email' => 'dg.piranhas@ifal.edu.br',
            'url' => 'https://www2.ifal.edu.br/campus/piranhas',
            'sigla' => 'IFAL',
            'contato' => 'João PIRANHAS',
            'contato2' => '',
            'endereco' => 'Av. Sergipe, 1477 - Piranhas, AL, 57460-000', 
            'telefone' => null,
            'responsavel_id' => '1',
            'user_id' => '1',
            'estado_id' => 1,
            'friendly_url' => '',
            'confirmado' => true
        ]);

        Unidade::create([
            'nome' => 'IFAL - SANTANA DE IPANEMA', 
            'tipo' => 'Campus', 
            'esfera' => 'Campus',
            'email' => 'dg.santana@ifal.edu.br',
            'url' => 'https://www2.ifal.edu.br/campus/santana',
            'sigla' => 'IFAL',
            'contato' => 'João SANTANA DE IPANEMA',
            'contato2' => '',
            'endereco' => 'Rodovia AL 130, Km 4, Nº 1609, R. Domingos Acácio, Santana do Ipanema - AL, 57500-000', 
            'telefone' => null,
            'responsavel_id' => '1',
            'user_id' => '1',
            'estado_id' => 1,
            'friendly_url' => '',
            'confirmado' => true
        ]);

        Unidade::create([
            'nome' => 'IFAL - SÃO MIGUEL DOS CAMPOS', 
            'tipo' => 'Campus', 
            'esfera' => 'Campus',
            'email' => 'dg.saomiguel@ifal.edu.br',
            'url' => 'https://www2.ifal.edu.br/campus/saomiguel',
            'sigla' => 'IFAL',
            'contato' => 'João SÃO MIGUEL DOS CAMPOS',
            'contato2' => '',
            'endereco' => 'Loteamento Hélio Jatobá III, Quadra B6 Hélio Jatobá III, São Miguel dos Campos - AL, 57246-615', 
            'telefone' => null,
            'responsavel_id' => '1',
            'user_id' => '1',
            'estado_id' => 1,
            'friendly_url' => '',
            'confirmado' => true
        ]);

        Unidade::create([
            'nome' => 'IFAL - SATUBA', 
            'tipo' => 'Campus', 
            'esfera' => 'Campus',
            'email' => ' dg.satuba@ifal.edu.br',
            'url' => 'https://www2.ifal.edu.br/campus/satuba',
            'sigla' => 'IFAL',
            'contato' => 'João SATUBA',
            'contato2' => '',
            'endereco' => 'R. Dezessete de Agosto, s/n - Zona Rural, Satuba - AL, 57120-000', 
            'telefone' => null,
            'responsavel_id' => '1',
            'user_id' => '1',
            'estado_id' => 1,
            'friendly_url' => '',
            'confirmado' => true
        ]);


        Unidade::create([
            'nome' => 'IFAL - VIÇOSA', 
            'tipo' => 'Campus', 
            'esfera' => 'Campus',
            'email' => 'dg.vicosa@ifal.edu.br',
            'url' => 'https://www2.ifal.edu.br/campus/vicosa',
            'sigla' => 'IFAL',
            'contato' => 'João VIÇOSA',
            'contato2' => '',
            'endereco' => 'R. Dezessete de Agosto, s/n - Zona Rural, Satuba - AL, 57120-000', 
            'telefone' => null,
            'responsavel_id' => '1',
            'user_id' => '1',
            'estado_id' => 1,
            'friendly_url' => '',
            'confirmado' => true
        ]);

        Unidade::create([
            'nome' => 'IFAL - TESTE', 
            'tipo' => 'Campus', 
            'esfera' => 'Departamento',
            'email' => 'dg.teste@ifal.edu.br',
            'url' => 'https://www2.ifal.edu.br/campus/teste',
            'sigla' => 'IFAL',
            'contato' => 'João TESTE',
            'contato2' => '',
            'endereco' => 'R. Dezessete de Agosto, s/n - Zona Rural, Satuba - AL, 57120-000', 
            'telefone' => null,
            'responsavel_id' => '1',
            'user_id' => '1',
            'estado_id' => 1,
            'friendly_url' => '',
            'confirmado' => true,
            'pai_id' => '1'
        ]);
    }
}
