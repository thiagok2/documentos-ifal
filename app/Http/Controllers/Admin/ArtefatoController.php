<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;

class ArtefatoController extends Controller
{
    /**
     * @var Client
     */
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

    public function create()
    {
        return view('admin.artefato.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'resumo' => 'required|string',
            'lista_entidades' => 'nullable|string',
            'especificacao' => 'nullable|string',
            'arquivo' => 'required|file|mimes:pdf|max:20480',
        ]);

        try {
            // Gerar UUID único para o artefato
            $artefatoId = Str::uuid()->toString();
            $filename = 'artefato_' . time() . '_' . $artefatoId . '.pdf';

            // Armazenar o PDF no disco como backup físico
            if ($request->hasFile('arquivo') && $request->file('arquivo')->isValid()) {
                $file = $request->file('arquivo');
                $file->storeAs('uploads/artefatos', $filename);
                $arquivoConteudo = file_get_contents($file->getRealPath());
            } else {
                throw new Exception("Arquivo inválido ou não enviado.");
            }

            // Tratar lista de entidades que chegam como uma string (tags separadas por vírgula)
            $entidadesString = explode(',', $request->input('lista_entidades', ''));
            $entidadesFormatadas = [];
            foreach ($entidadesString as $e) {
                $e = trim($e);
                if (!empty($e)) {
                    $entidadesFormatadas[] = [
                        'texto' => $e,
                        'categoria' => 'Geral', // Categoria padrão por enquanto
                        'confianca' => 1.0
                    ];
                }
            }

            // Montar objeto raiz para o ElasticSearch
            $bodyDocumentElastic = [
                'artefato' => [
                    'artefato_id' => $artefatoId,
                    'resumo' => $request->input('resumo'),
                    'especificacao' => $request->input('especificacao'),
                    'entidades' => $entidadesFormatadas,
                    'uploaded_by' => (string) auth()->user()->id,
                    'created_at' => date('c'), // Formato aceito pelo ES date type
                    'titulo' => $filename
                ],
                'data' => base64_encode($arquivoConteudo)
            ];

            // Usa o índice configurado dinamicamente no .env
            $index = env('ELASTICSEARCH_INDEX_ARTEFATOS', 'artefatos_v2');

            $params = [
                'index' => $index,
                'type'  => '_doc',
                'id'    => $filename, // Usando o nome do arquivo único como ID para seguir o DocumentoController
                'pipeline' => 'attachment', 
                'body'  => $bodyDocumentElastic
            ];

            // Inserir no Elasticsearch diretamente
            $resultElastic = $this->client->index($params);

            if (!in_array($resultElastic['result'], ['created', 'updated'])) {
                throw new Exception("Erro Elastic: " . json_encode($resultElastic));
            }

            return redirect()->route('artefato-create')->with('success', 'Artefato salvo no Elasticsearch com sucesso!');

        } catch (Exception $e) {
            Log::error('Erro ao salvar artefato no Elasticsearch: ' . $e->getMessage());
            
            $messageErro = env('APP_DEBUG') ? $e->getMessage() : "Problemas na indexação do documento. Tente novamente ou contate o administrador.";
            return redirect()->back()->withInput()->with('error', $messageErro);
        }
    }

    public function destroy($id)
    {
        try {
            $index = env('ELASTICSEARCH_INDEX_ARTEFATOS', 'artefatos_v2');

            // Deleta do Elasticsearch
            $params = [
                'index' => $index,
                'type'  => '_doc',
                'id'    => $id,
            ];
            
            $this->client->delete($params);

            // Deleta do disco
            $filePath = storage_path('app/uploads/artefatos/' . $id);
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            return redirect()->route('artefatos-search')->with('success', 'Artefato deletado com sucesso!');

        } catch (\Exception $e) {
            Log::error('Erro ao deletar artefato: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ocorreu um erro ao excluir o artefato.');
        }
    }
}
