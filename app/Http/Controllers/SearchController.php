<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        // 1. Pegamos o termo que o usuário digitou (ex: ?q=editais)
        $query = $request->input('q');

        if (!$query) {
            return response()->json(['error' => 'Por favor, envie um termo de busca. Ex: /search?q=termo'], 400);
        }

        // 2. Conectamos no ElasticSearch (Internamente no Docker)
        // OBS: Se der erro de conexão, pode ser que o nome do host não seja 'elasticsearch', mas sim o nome do container.
        $elasticHost = 'http://elasticsearch:9200'; 
        $indexName = 'documentos_ifal';

        try {
            // 3. Faz a pergunta para o ElasticSearch
            // ADICIONAMOS 'size' => 1 para não travar o Postman
            $response = Http::get("$elasticHost/$indexName/_search", [
                'q' => $query, 
                'size' => 20   // <--- O SEGREDO ESTÁ AQUI (Traz só 1 documento)
            ]);

           $data = $response->json();

            // 4. A Mágica da Limpeza (Data Mapping) 🧹
            // Vamos transformar aquele JSON complexo em algo simples
            $resultadosLimpos = array_map(function ($hit) {
                
                // Atalhos para facilitar a leitura
                $source = $hit['_source'];
                $ato = $source['ato'] ?? [];
                
                // Pega o conteúdo pesado, mas corta ele (Resumo)
                $conteudoCompleto = $source['data']['attachment']['content'] ?? '';
                $resumo = mb_substr($conteudoCompleto, 0, 300) . '...'; // Pega só os primeiros 300 letras

                return [
                    'id_elastic' => $hit['_id'],
                    'score'      => $hit['_score'], // Quão relevante é esse documento
                    'titulo'     => $ato['titulo'] ?? 'Sem título',
                    'tipo'       => $ato['tipo_doc'] ?? 'Documento',
                    'numero'     => $ato['numero'] ?? 'S/N',
                    'ano'        => $ato['ano'] ?? '',
                    'link_original' => $ato['fonte']['url'] ?? '#',
                    'data_publicacao' => $ato['data_publicacao'] ?? null,
                    'resumo_conteudo' => $resumo // Aqui vai o texto cortado!
                ];

            }, $data['hits']['hits']);

            // 5. Retorna o JSON limpo e informativo
            return response()->json([
                'termo_buscado' => $query,
                'total_encontrado' => $data['hits']['total']['value'],
                'quantidade_exibida' => count($resultadosLimpos),
                'resultados' => $resultadosLimpos
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao conectar ou processar dados',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}