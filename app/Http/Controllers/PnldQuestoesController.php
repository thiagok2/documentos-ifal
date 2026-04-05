<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Searches\Commands\SearchCommandPnldQuestoes;

class PnldQuestoesController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q', ''); 
        
        $page = $request->input('page', 1);
        $size = 20;
        $from = ($page - 1) * $size;

        $index = env('ELASTICSEARCH_INDEX_QUESTOES', 'pnld_questao_v1');

        // MUDANÇA 1: Tente passar string vazia '' ao invés de 'ato'. 
        // Se o JSON do banco for plano (sem prefixo), isso fará a busca funcionar.
        $searchCommand = new SearchCommandPnldQuestoes($index, ''); 

        $result = $searchCommand->search($query, [], $from, $size);

        // MUDANÇA 2: Usando os nomes que vimos no dd($result)
        // Se documentsResult for null, enviamos um array vazio [] para não quebrar a view
        return view('pnld.questoes.questoes', [
            'results' => $result->documentsResult ?? [], 
            'total' => $result->totalResults ?? 0,
            'query' => $query
        ]);
    }
}