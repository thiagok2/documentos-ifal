<?php

namespace App\Http\Controllers;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Input;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Models\Documento;
use Illuminate\Support\Facades\Log;

use App\Models\TipoDocumento;
use App\Models\Unidade;
use App\Services\SearchComponent;
use App\Searches\Commands\SearchCommandA1;
use App\Searches\Commands\SearchCommandA0;

use App\Services\DocumentoQuery;

class IndexController extends Controller
{
    const RESULTS_PER_PAGE = 10;

    /**
     * @var \Elastic\Elasticsearch\Client
     */
    private $client;


    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $hosts = [
            getenv('ELASTIC_URL')
        ];
        $this->client = ClientBuilder::create()->setHosts($hosts)->build();
    }

    function tirarAcentos($string)
    {
        return preg_replace(array("/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/"), explode(" ", "a A e E i I o O u U n N"), $string);
    }

    public function index(Request $request)
    { 
        $changePrivateFlag = False;

        $size_page = $request->has('page_size') ? $request->query("page_size") : self::RESULTS_PER_PAGE;

        $publico = filter_var($request->query("publico", false), FILTER_VALIDATE_BOOLEAN);
        $tipo_doc = $request->query("tipo_doc");
        $esfera = $request->query("esfera");
        $periodo = $request->query("periodo");
        $aggregations = [];

        $ano = $request->query("ano");
        $fonte = $request->query("fonte");
        $page = $request->query('page', 1);
        $total = 0;
        $total_pages = 0;
        $max_score = 0;

        $conselho = null;
        if (isset($fonte)) {
            $conselho = Unidade::where('sigla', $this->tirarAcentos($fonte))->first();
        }

        $tiposDocumento = TipoDocumento::has('documentos')->get();

        $query = $request->query('query');
        $queryFilters = $request->query();
        
        $filters = $this->hasFilters($queryFilters);

        $documentos = [];

        try {

            //Busca Privada
            $usuario = auth()->user();
            $unidadeUsuario = $usuario?->unidade?->nome ?? null;

            if (!$publico && $unidadeUsuario) {
                $queryFilters['orgao'] = $unidadeUsuario;
                $changePrivateFlag = True;
            }
            
            

            if (isset($query)) {

                $query = trim($query);

                $arrayTerms = explode(' ', $query);
                $beginTerm = $arrayTerms[0];
                $endTerm = end($arrayTerms);

                $hasFilterTipoDoc = TipoDocumento::where('nome', 'ilike', $beginTerm)->count();

                if ($hasFilterTipoDoc) {
                    $query = str_replace($beginTerm, "", $query);
                    $queryFilters['tipo_doc'] = $beginTerm;
                    $tipo_doc = $beginTerm;
                }



                if ($page == 1) //cadastrar consulta apenas no primeiro acesso
                    SearchComponent::logging($query, $request);
                
                $from = (($page - 1) * $size_page);
            
                if(str_ends_with($query, '"') && str_starts_with($query, '"')){
                    $from = (($page - 1) * $size_page);
                    $searchCommand = new SearchCommandA0('documentos_ifal', 'ato', $publico);
                    $result = $searchCommand->search($query, $queryFilters, $from, $size_page);
                    $total = $result->totalResults;
                    
                    $max_score = $result->maxScore;
                    
                    $total_pages = $result->totalPages;
                    
                    $documentos = $result->documentsResult;
                    $aggregations = $result->aggResults;                
                }
                else{
                    // Executa A0 e A1

                    $searchCommandA0 = new SearchCommandA0('documentos_ifal', 'ato', $publico);
                    $resultA0 = $searchCommandA0->search($query, $queryFilters, 0, 1000); // recupera até 1000 resultados para evitar paginação fragmentada
                    
                    $searchCommandA1 = new SearchCommandA1('documentos_ifal', 'ato', $publico);
                    $resultA1 = $searchCommandA1->search($query, $queryFilters, 0, 1000);
                    
                    // Primeira fonte: A0
                    $docIdsA0 = [];
                    $finalResults = [];
                    
                    // Verifica se existe documentos em resultA0
                    $docsA0 = (array) $resultA0->documentsResult;
                    $docsA1 = (array) $resultA1->documentsResult;
                    
                    $merged = array_merge($docsA0, $docsA1);
                    
                    // Usa array_reduce para eliminar duplicados com base no 'id'
                    $finalResultsAssoc = array_reduce($merged, function ($carry, $doc) {
                        $id = $doc['id'] ?? null;
                        if ($id !== null && !isset($carry[$id])) {
                            $carry[$id] = $doc;
                        }
                        return $carry;
                    }, []);
                    
                    // Reindexa para array simples
                    $finalResults = array_values($finalResultsAssoc);                    
                    
                    // Pagina manualmente os resultados combinados
                    $total = count($finalResults);
                    $total_pages = ceil($total / $size_page);
                    $from = (($page - 1) * $size_page);     
                    $documentos = array_slice($finalResults, $from, $size_page);
                    
                    // Calcula o max_score dos dois resultados
                    $max_score = max($resultA0->maxScore ?? 0, $resultA1->maxScore ?? 0);
                    
                    // Agregações: escolha uma ou combine (aqui pegamos do A1 como base)
                    $aggregations = $resultA1->aggResults;
                }
                
                }
                return view(
                    'index.index',
                    compact(
                        'query',
                        'conselho',
                        'tiposDocumento',
                        'tipo_doc',
                        'esfera',
                        'periodo',
                        'ano',
                        'fonte',
                        'filters',
                        'page',
                        'total',
                        'size_page',
                        'total_pages',
                        'max_score',
                        'documentos',
                        'aggregations',
                        'changePrivateFlag'
                    )
                );
            
        } catch (\Exception $e) {
            $erro['titulo'] = getenv('APP_DEBUG') ? "DEBUG:: " . $e->getMessage() : 'Plataforma de busca indisponível';
            $erro['local'] = $e->getFile() . " #" . $e->getLine();
            $erro['trace'] = $e->getTraceAsString();
        
            Log::error($e->getFile() . ' - Linha ' . $e->getLine() . ' - search::' . $e->getMessage());
        
            throw $e;
        }
    
    }

    public function viewNormativa($normativaId)
    {
        $result = $this->client->get([
            'index' => 'documentos_ifal',
            'type' => '_doc',
            'id' => $normativaId
        ]);

        $documento = Documento::where('arquivo', $normativaId)->first();

        $keywords = $documento ? $documento->keywords() : null;

        $persisted = isset($documento);

        return view('index.view-normativa', ['normativa' => $result['_source'], 'id' => $result['_id'], 'arquivoId' => $result['_id'], 'persisted' => $persisted, 'keywords' => $keywords]);
    }

    private function hasFilters($queryParams)
    {
        foreach ($queryParams as $q => $v) {
            if (($q == "esfera" || $q == "periodo") && isset($v) && $v != "all") {
                return true;
            }

        }
    }

    protected function likeDocuments($docResult)
    {

        $params = [
            "index" => "documentos_ifal",
            "type" => "_doc",
            "body" => [
                "_source" => [
                    "include" => ["ato.*"]
                ],
                "size" => 6,
                "query" => [
                    "more_like_this" => [
                        "fields" => ["ato.ementa", "ato.tags"],
                        "like" => [

                            $docResult['_source']['ato']['ementa']
                        ],
                        "min_term_freq" => 1,
                        "max_query_terms" => 15
                    ]
                ]
            ]
        ];

        return $this->client->search($params);

    }


    public function delete(Request $request)
    {

        $admin = auth()->user() ? auth()->user()->isAdmin() : false;

        if ($admin) {
            $arquivoId = $request->arquivoId;

            $doc = Documento::where('arquivo', $arquivoId)->first();
            if ($doc)
                $doc->delete();

            $params = [
                'index' => 'documentos_ifal',
                'type' => '_doc',
                'id' => $arquivoId,
            ];

            $response = $this->client->delete($params);

            return redirect("/");
        } else {
            return redirect()->route('index')
                ->with('error', 'Apenas os administradores podem realizar esta ação.');
        }
    }

    public function downloads(Request $request)
    {
        $documentoQuery = new DocumentoQuery();

        $resultQuery = $documentoQuery->documentosDownloads();

        $downloads = $this->arrayPaginator($resultQuery, $request);

        return view('index.downloads', compact('downloads'));
    }

    public function arrayPaginator($array, $request)
    {
        $page = Input::get('page', 1);
        $perPage = 20;
        $offset = ($page * $perPage) - $perPage;

        return new LengthAwarePaginator(
            array_slice($array, $offset, $perPage, true),
            count($array),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
    }


}