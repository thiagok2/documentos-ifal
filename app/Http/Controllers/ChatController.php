<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $message = $request->input('message');
        $documentId = $request->input('document_id');
        $isNormativa = $request->input('is_normativa', false);

        Log::info("Chat message recebida para DocID {$documentId}: {$message}");

        try {
            // Busca o texto do documento no ElasticSearch
            $hosts = [getenv('ELASTIC_URL')];
            $username = getenv('ELASTICSEARCH_USERNAME');
            $password = getenv('ELASTICSEARCH_PASSWORD');
            $client = \Elastic\Elasticsearch\ClientBuilder::create()
                ->setHosts($hosts)
                ->setBasicAuthentication($username, $password)
                ->build();

            $documentContext = "";

            if ($isNormativa) {
                try {
                    $esResponse = $client->get([
                        'index' => 'documentos_ifal',
                        'type' => '_doc',
                        'id' => $documentId
                    ]);
                    $source = $esResponse['_source'] ?? [];
                    $titulo = $source['ato']['ementa'] ?? '';
                    $texto = $source['attachment']['content'] ?? '';
                    $paginas = $source['attachment']['page_count'] ?? 'Desconhecido';
                    $documentContext = "Metadados:\n- Título/Ementa: {$titulo}\n- Total de Páginas: {$paginas}\n\nTexto do Documento:\n" . mb_substr($texto, 0, 400000); 
                } catch (\Exception $e) {
                    Log::error("Erro ES Normativa no Chat: " . $e->getMessage());
                }
            } else {
                try {
                    $index = env('ELASTICSEARCH_INDEX_ARTEFATOS', 'artefatos_v2');
                    $esResponse = $client->get([
                        'index' => $index,
                        'id' => $documentId
                    ]);
                    $source = $esResponse['_source'] ?? [];
                    $titulo = $source['artefato']['titulo'] ?? '';
                    $resumo = $source['artefato']['resumo'] ?? '';
                    $texto = $source['attachment']['content'] ?? '';
                    $paginas = $source['attachment']['page_count'] ?? 'Desconhecido';
                    $documentContext = "Metadados:\n- Título: {$titulo}\n- Resumo: {$resumo}\n- Total de Páginas: {$paginas}\n\nTexto do Documento:\n" . mb_substr($texto, 0, 400000);
                } catch (\Exception $e) {
                    Log::error("Erro ES Artefato no Chat: " . $e->getMessage());
                }
            }

            // Chama a API do Rasa
            $rasaUrl = rtrim(env('RASA_URL', 'http://127.0.0.1:5050'), '/');
            $response = \Illuminate\Support\Facades\Http::timeout(60)->post($rasaUrl . '/webhooks/rest/webhook', [
                'sender' => session()->getId(), // ID único do usuário na sessão
                'message' => $message,
                'metadata' => [
                    'document_id' => $documentId,
                    'is_normativa' => $isNormativa,
                    'document_context' => $documentContext
                ]
            ]);

            $rasaData = $response->json();
            
            $reply = "";
            if (!empty($rasaData)) {
                foreach ($rasaData as $msg) {
                    if (isset($msg['text'])) {
                        $reply .= $msg['text'] . "\n\n";
                    }
                }
                $reply = trim($reply);
            }
            
            if (empty($reply)) {
                $reply = "Desculpe, a IA (Iúna) não retornou uma resposta válida.";
            }

        } catch (\Exception $e) {
            Log::error("Erro ao conectar com o Rasa: " . $e->getMessage());
            $reply = "Desculpe, o cérebro da Iúna (Rasa) parece estar offline no momento.";
        }

        return response()->json([
            'reply' => $reply
        ]);
    }
}
