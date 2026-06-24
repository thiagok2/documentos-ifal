<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class PublicArtefatoController extends Controller
{
    private $client;

    public function __construct()
    {
        $hosts = [
            getenv('ELASTIC_URL')
        ];
        $username = getenv('ELASTICSEARCH_USERNAME');
        $password = getenv('ELASTICSEARCH_PASSWORD');
        
        $this->client = ClientBuilder::create()
            ->setHosts($hosts)
            ->setBasicAuthentication($username, $password)
            ->build();
    }

    public function index(Request $request)
    {
        $query = $request->query('query');
        $page = $request->query('page', 1);
        $size_page = 10;
        $from = ($page - 1) * $size_page;
        $index = env('ELASTICSEARCH_INDEX_ARTEFATOS', 'artefatos_v2');

        $documentos = [];
        $total = 0;
        $total_pages = 0;

        if ($request->has('query')) {
            try {
                $params = [
                    'index' => $index,
                    'body' => [
                        'from' => $from,
                        'size' => $size_page,
                        '_source' => [
                            'includes' => ['artefato.*']
                        ]
                    ]
                ];

                if (empty($query)) {
                    $params['body']['query'] = [
                        'match_all' => new \stdClass()
                    ];
                    $params['body']['sort'] = [
                        'artefato.created_at' => ['order' => 'desc']
                    ];
                } else {
                    $params['body']['query'] = [
                        'multi_match' => [
                            'query' => $query,
                            'fields' => [
                                'artefato.resumo',
                                'artefato.especificacao',
                                'artefato.titulo',
                                'artefato.entidades.texto',
                                'attachment.content'
                            ]
                        ]
                    ];
                }

                $response = $this->client->search($params);

                if (isset($response['hits']['hits'])) {
                    $total = $response['hits']['total']['value'];
                    $total_pages = ceil($total / $size_page);

                    foreach ($response['hits']['hits'] as $hit) {
                        $doc = $hit['_source']['artefato'];
                        $doc['id'] = $hit['_id']; 
                        $documentos[] = $doc;
                    }
                }

            } catch (\Exception $e) {
                Log::error('Erro na busca de artefatos: ' . $e->getMessage());
                $erro = [
                    'titulo' => 'Erro na busca de artefatos.',
                    'local' => $e->getFile() . " #" . $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ];
                return view('artefato.index', compact('documentos', 'query', 'page', 'size_page', 'total', 'total_pages', 'erro'));
            }
        }

        return view('artefato.index', compact('documentos', 'query', 'page', 'size_page', 'total', 'total_pages'));
    }

    public function download($id)
    {
        $filePath = storage_path('app/uploads/artefatos/' . $id);
        if (file_exists($filePath)) {
            return response()->download($filePath);
        }
        return redirect()->back()->with('error', 'Arquivo PDF original não foi encontrado no servidor local.');
    }
}
