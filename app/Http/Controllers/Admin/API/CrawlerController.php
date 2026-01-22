<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Documento; 
use Illuminate\Support\Facades\Log;

class CrawlerController extends Controller
{
    
    public function store(Request $request)
{
    
    $request->validate([
        'titulo' => 'required|string|max:255',
        'url' => 'required|url',
        'conteudo' => 'required|string',
    ]);

    
    $documento = Documento::create([
        
        
        'titulo' => $request->titulo,
        'url' => $request->url,
        'conteudo' => $request->conteudo,
        
        
        'arquivo' => 'documento_crawler_temp.pdf', 
        
        
        'ementa' => 'Documento extraído pelo sistema crawler.', 
        
        
        'numero' => 'CRAWLER-' . time(), 
        
        
        'ano' => date('Y'),
        'data_publicacao' => now(), 
        
        'tipo_documento_id' => 1,
        'assunto_id' => 1,
        'unidade_id' => 1, 
        
        'tipo_entrada' => Documento::ENTRADA_EXTRATOR, 
        'publico' => true,
    ]);

    return response()->json($documento, 201);
}
}
