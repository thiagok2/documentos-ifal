<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>PNLD Questões</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 40px; background-color: #f0f2f5; color: #333; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        
        .search-box { display: flex; gap: 10px; margin-bottom: 25px; }
        input[type="text"] { flex: 1; padding: 12px 15px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px; transition: border 0.3s; }
        input[type="text"]:focus { border-color: #3182ce; outline: none; }
        button { padding: 0 25px; background: #3182ce; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        button:hover { background: #2c5282; }

        .results-info { font-size: 0.9em; color: #718096; margin-bottom: 20px; border-bottom: 1px solid #edf2f7; padding-bottom: 10px; }
        .item { padding: 20px 0; border-bottom: 1px solid #edf2f7; }
        .item:last-child { border-bottom: none; }
        
        .badges { margin-bottom: 8px; }
        .badge { display: inline-block; padding: 4px 8px; font-size: 0.75em; border-radius: 4px; font-weight: 600; letter-spacing: 0.5px; text-transform: uppercase; margin-right: 5px; }
        .badge-blue { background: #ebf8ff; color: #2b6cb0; }
        .badge-gray { background: #edf2f7; color: #4a5568; }

        h3 { margin: 0 0 10px; font-size: 1.1em; color: #2d3748; line-height: 1.4; }
        .text-body { font-size: 0.95em; color: #4a5568; line-height: 1.6; }
        .meta { margin-top: 10px; font-size: 0.8em; color: #a0aec0; }
    </style>
</head>
<body>

<div class="container">
    <h1 style="margin-top:0; color:#2d3748;">Banco de Questões PNLD</h1>
    
    <form action="" method="GET" class="search-box">
        <input type="text" name="q" value="{{ $query }}" placeholder="Pesquise por termos (ex: professor, digital, recursos)...">
        <button type="submit">Buscar</button>
    </form>

    <div class="results-info">
        Encontrados: <strong>{{ $total }}</strong> registros.
    </div>

    @if(isset($results) && count($results) > 0)
        @foreach($results as $hit)
            @php 
                $source = $hit['_source']; 
            @endphp
            <div class="item">
                <div class="badges">
                    @if(!empty($source['ds_etapa_ensino']))
                        <span class="badge badge-blue">{{ $source['ds_etapa_ensino'] }}</span>
                    @endif
                    @if(!empty($source['ds_objeto']))
                        <span class="badge badge-gray">{{ $source['ds_objeto'] }}</span>
                    @endif
                </div>

                <h3>{{ $source['ds_grupo'] ?? $source['ds_bloco'] ?? 'Item sem título' }}</h3>
                
                <div class="text-body">
                    {{ $source['ds_questao'] ?? '' }}
                </div>

                <div class="meta">
                    ID: {{ $source['co_questao'] ?? $hit['_id'] }} | 
                    Edital: {{ $source['ds_titulo_edital'] ?? 'N/A' }} |
                    Score: {{ number_format($hit['_score'], 2) }}
                </div>
            </div>
        @endforeach
    @else
        @if($query)
            <p style="text-align: center; color: #718096; margin-top: 40px;">
                Nenhum resultado encontrado para "<strong>{{ $query }}</strong>".
            </p>
        @else
            <p style="text-align: center; color: #718096; margin-top: 40px;">
                Digite algo acima para pesquisar no banco PNLD.
            </p>
        @endif
    @endif
</div>

</body>
</html>