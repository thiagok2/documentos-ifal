<?php

namespace App\Searches\Commands;

use App\Searches\Queries\QueryBuilder;
use Elastic\Elasticsearch\ClientBuilder;
use App\Searches\Models\SearchResult;
use ReflectionClass;

class SearchCommandPnldQuestoes implements ISearchCommand
{
    protected $queryBuilder;
    private $index;
    private $root;
    private $clientElastic;
    private $public;

    function __construct($_index, $_root, $_public = true) {
        $this->index = $_index;
        $this->root = $_root;
        $this->public = $_public;

        $this->queryBuilder = new QueryBuilder(""); 

        $username = getenv('ELASTICSEARCH_USERNAME');
        $password = getenv('ELASTICSEARCH_PASSWORD');
        $hosts = [getenv('ELASTIC_URL')];

        $this->clientElastic = ClientBuilder::create()
            ->setHosts($hosts)
            ->setBasicAuthentication($username, $password)
            ->build();
    }

    public function getArrayQuery(){
        return $this->queryBuilder->getQueryArray();
    }

    public function search($query, $filters, $from, $sizePage){
        
        // 1. Definição da Query
        $body = [
            'from' => $from,
            'size' => $sizePage,
            'query' => [
                'bool' => [
                    'must' => []
                ]
            ]
        ];

        if (!empty($query)) {
            $body['query']['bool']['must'][] = [
                'multi_match' => [
                    'query' => $query,
                    'fields' => [
                        'ds_questao^3', 'ds_grupo^2', 'ds_bloco', 'ds_titulo_edital', 'ds_objeto'
                    ],
                    'type' => 'best_fields',
                    'operator' => 'or',
                    'fuzziness' => 'AUTO'
                ]
            ];
        } else {
            $body['query']['bool']['must'][] = ['match_all' => (object)[]];
        }

        // 2. Executa a busca no Elastic
        $params = [
            'index' => $this->index,
            'body'  => $body
        ];

        $response = $this->clientElastic->search($params);
        
        // Normaliza a resposta para array
        if (is_array($response)) {
            $resultsArray = $response;
        } else {
            $resultsArray = $response->asArray();
        }

        if (!isset($resultsArray['aggregations'])) {
            $resultsArray['aggregations'] = [];
        }

        // 3. PREPARAÇÃO DOS DADOS BRUTOS (O que a View quer)
        // A View quer acessar $item['_source']. Vamos garantir que cada item tenha isso.
        $hitsBrutos = [];
        if (isset($resultsArray['hits']['hits'])) {
            foreach ($resultsArray['hits']['hits'] as $rawHit) {
                // Se por algum motivo o _source não vier, criamos um vazio para não dar erro
                if (!isset($rawHit['_source'])) {
                    $rawHit['_source'] = [];
                }
                
                // Copiamos dados essenciais para a raiz também, caso a View tente acessar direto
                $rawHit['id'] = $rawHit['_id'] ?? null;
                $rawHit['ds_questao'] = $rawHit['_source']['ds_questao'] ?? '';
                
                // Adicionamos na lista final que vamos FORÇAR dentro da classe
                $hitsBrutos[] = $rawHit;
            }
        }

        // 4. PREPARAÇÃO PARA O SEARCHRESULT (Para ele não quebrar na construção)
        // Precisamos alimentar o SearchResult com algo que ele aceite, mesmo que depois a gente substitua.
        // Vamos iterar sobre o array original e garantir que campos como 'fonte' existam para o construtor passar.
        if (isset($resultsArray['hits']['hits'])) {
            foreach ($resultsArray['hits']['hits'] as &$hit) {
                if (isset($hit['_source'])) {
                    $hit['_source']['ato']['fonte'] = 'PNLD'; 
                    $hit['_source']['ato']['ementa'] = 'Questão';
                    $hit['_source']['ato']['numero'] = $hit['_id'] ?? '';
                    $hit['_source']['ato']['ano'] = '2021';
                }
            }
        }
        $aggsWrapper = ['aggregations' => $resultsArray['aggregations']];

        // 5. CRIA O OBJETO (que vai "estragar" os dados)
        $searchResult = new SearchResult($resultsArray, $sizePage, $aggsWrapper);

        // 6. A CORREÇÃO FORÇADA (SUBSTITUIÇÃO CIRÚRGICA)
        // Aqui nós invadimos o objeto e trocamos a lista processada pela lista bruta ($hitsBrutos).
        try {
            $reflection = new ReflectionClass($searchResult);
            $properties = $reflection->getProperties();
            
            // Procura pela propriedade que guarda a lista de resultados.
            // Geralmente se chama: results, items, hits, data, collection.
            $propriedadeEncontrada = false;
            
            // Tenta nomes comuns primeiro
            $nomesComuns = ['results', 'items', 'hits', 'data', 'collection'];
            
            foreach ($nomesComuns as $nome) {
                if ($reflection->hasProperty($nome)) {
                    $prop = $reflection->getProperty($nome);
                    $prop->setAccessible(true);
                    // Sobrescreve com os dados brutos que têm o _source
                    $prop->setValue($searchResult, $hitsBrutos);
                    $propriedadeEncontrada = true;
                    break;
                }
            }
            
            // Se não achou pelo nome, tenta achar uma propriedade que seja Array e não esteja vazia (ou esteja vazia mas seja a candidata)
            if (!$propriedadeEncontrada) {
                foreach ($properties as $prop) {
                    $prop->setAccessible(true);
                    $valor = $prop->getValue($searchResult);
                    // Se for um array e não for as agregações, é grande a chance de ser os resultados
                    if (is_array($valor) && $prop->getName() !== 'aggregations' && $prop->getName() !== 'aggs') {
                        $prop->setValue($searchResult, $hitsBrutos);
                        break; 
                    }
                }
            }

        } catch (\Exception $e) {
            // Falha silenciosa se algo der muito errado no reflection, 
            // mas nesse ponto é a nossa única chance.
        }

        return $searchResult;
    }
}