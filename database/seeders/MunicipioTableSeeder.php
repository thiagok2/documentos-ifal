<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Municipio;

class MunicipioTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('municipios')->delete();
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2700102', 'nome' => "Água Branca"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2700201', 'nome' => "Anadia"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2700300', 'nome' => "Arapiraca"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2700409', 'nome' => "Atalaia"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2700508', 'nome' => "Barra de Santo Antônio"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2700607', 'nome' => "Barra de São Miguel"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2700706', 'nome' => "Batalha"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2700805', 'nome' => "Belém"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2700904', 'nome' => "Belo Monte"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2701001', 'nome' => "Boca da Mata"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2701100', 'nome' => "Branquinha"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2701209', 'nome' => "Cacimbinhas"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2701308', 'nome' => "Cajueiro"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2701357', 'nome' => "Campestre"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2701407', 'nome' => "Campo Alegre"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2701506', 'nome' => "Campo Grande"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2701605', 'nome' => "Canapi"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2701704', 'nome' => "Capela"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2701803', 'nome' => "Carneiros"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2701902', 'nome' => "Chã Preta"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2702009', 'nome' => "Coité do Nóia"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2702108', 'nome' => "Colônia Leopoldina"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2702207', 'nome' => "Coqueiro Seco"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2702306', 'nome' => "Coruripe"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2702355', 'nome' => "Craíbas"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2702405', 'nome' => "Delmiro Gouveia"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2702504', 'nome' => "Dois Riachos"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2702553', 'nome' => "Estrela de Alagoas"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2702603', 'nome' => "Feira Grande"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2702702', 'nome' => "Feliz Deserto"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2702801', 'nome' => "Flexeiras"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2702900', 'nome' => "Girau do Ponciano"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2703007', 'nome' => "Ibateguara"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2703106', 'nome' => "Igaci"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2703205', 'nome' => "Igreja Nova"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2703304', 'nome' => "Inhapi"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2703403', 'nome' => "Jacaré dos Homens"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2703502', 'nome' => "Jacuípe"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2703601', 'nome' => "Japaratinga"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2703700', 'nome' => "Jaramataia"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2703759', 'nome' => "Jequiá da Praia"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2703809', 'nome' => "Joaquim Gomes"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2703908', 'nome' => "Jundiá"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2704005', 'nome' => "Junqueiro"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2704104', 'nome' => "Lagoa da Canoa"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2704203', 'nome' => "Limoeiro de Anadia"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2704302', 'nome' => "Maceió"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2704401', 'nome' => "Major Isidoro"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2704906', 'nome' => "Mar Vermelho"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2704500', 'nome' => "Maragogi"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2704609', 'nome' => "Maravilha"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2704708', 'nome' => "Marechal Deodoro"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2704807', 'nome' => "Maribondo"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2705002', 'nome' => "Mata Grande"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2705101', 'nome' => "Matriz de Camaragibe"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2705200', 'nome' => "Messias"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2705309', 'nome' => "Minador do Negrão"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2705408', 'nome' => "Monteirópolis"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2705507', 'nome' => "Murici"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2705606', 'nome' => "Novo Lino"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2705705', 'nome' => "Olho d'Água das Flores"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2705804', 'nome' => "Olho d'Água do Casado"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2705903', 'nome' => "Olho d'Água Grande"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2706000', 'nome' => "Olivença"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2706109', 'nome' => "Ouro Branco"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2706208', 'nome' => "Palestina"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2706307', 'nome' => "Palmeira dos Índios"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2706406', 'nome' => "Pão de Açúcar"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2706422', 'nome' => "Pariconha"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2706448', 'nome' => "Paripueira"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2706505', 'nome' => "Passo de Camaragibe"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2706604', 'nome' => "Paulo Jacinto"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2706703', 'nome' => "Penedo"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2706802', 'nome' => "Piaçabuçu"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2706901', 'nome' => "Pilar"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2707008', 'nome' => "Pindoba"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2707107', 'nome' => "Piranhas"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2707206', 'nome' => "Poço das Trincheiras"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2707305', 'nome' => "Porto Calvo"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2707404', 'nome' => "Porto de Pedras"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2707503', 'nome' => "Porto Real do Colégio"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2707602', 'nome' => "Quebrangulo"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2707701', 'nome' => "Rio Largo"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2707800', 'nome' => "Roteiro"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2707909', 'nome' => "Santa Luzia do Norte"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2708006', 'nome' => "Santana do Ipanema"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2708105', 'nome' => "Santana do Mundaú"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2708204', 'nome' => "São Brás"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2708303', 'nome' => "São José da Laje"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2708402', 'nome' => "São José da Tapera"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2708501', 'nome' => "São Luís do Quitunde"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2708600', 'nome' => "São Miguel dos Campos"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2708709', 'nome' => "São Miguel dos Milagres"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2708808', 'nome' => "São Sebastião"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2708907', 'nome' => "Satuba"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2708956', 'nome' => "Senador Rui Palmeira"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2709004', 'nome' => "Tanque d'Arca"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2709103', 'nome' => "Taquarana"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2709152', 'nome' => "Teotônio Vilela"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2709202', 'nome' => "Traipu"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2709301', 'nome' => "União dos Palmares"]);
        Municipio::create(['estado_id' => 2, 'codigo_ibge' => '2709400', 'nome' => "Viçosa"]);
    }
}
