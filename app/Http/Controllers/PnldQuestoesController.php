<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Searches\Commands\SearchCommandPnldQuestoes;

class PnldQuestoesController extends Controller
{
    public function index(Request $request)
    {
        app()->setLocale('pt-br');

        $query = $request->input('q', '');

        if ($query === null || trim($query) === '') {
            return view('pnld.questoes.questoes', [
                'results' => [],
                'total' => 0,
                'query' => '',
            ]);
        }

        $page = max(1, (int) $request->input('page', 1));
        $size = 15;
        $from = ($page - 1) * $size;

        $index = env('ELASTICSEARCH_INDEX_QUESTOES', 'pnld_questao_v1');
        $searchCommand = new SearchCommandPnldQuestoes($index, '');

        $result = $searchCommand->search($query, [], $from, $size);

        $documents = $result->documentsResult ?? [];
        $total = $result->totalResults ?? 0;

        $paginator = new LengthAwarePaginator(
            $documents,
            $total,
            $size,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('pnld.questoes.questoes', [
            'results' => $paginator,
            'total' => $total,
            'query' => $query,
        ]);
    }

    public function show(Request $request, $id)
    {
        app()->setLocale('pt-br');

        $index = env('ELASTICSEARCH_INDEX_QUESTOES', 'pnld_questao_v1');
        $searchCommand = new SearchCommandPnldQuestoes($index, '');

        $questao = $searchCommand->findById($id);

        if ($questao === null) {
            abort(404, 'Questão não encontrada.');
        }

        $relacionadas = $searchCommand->findRelated($questao);

        return view('pnld.questoes.show', [
            'questao' => $questao,
            'relacionadas' => $relacionadas,
            'query' => $request->input('q', ''),
        ]);
    }
}
