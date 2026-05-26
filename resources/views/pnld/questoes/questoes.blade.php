@extends('layouts.master')
@if (!empty($query) && (!empty($documentos)))
@section('keywords', $query)
@endif
@section('content')
<!-- mini-header -->
@include('admin.includes.alerts')

<div class="container">
    <h1 class="mt-0 mb-4 text-center">Banco de Questões PNLD</h1>
    <form action="" method="GET" class="search-box form-inline justify-content-center mb-3">
        <input type="text" name="q" value="{{ $query }}" placeholder="Pesquise por termos (ex: professor, digital, recursos)..." class="form-control mr-2">
        <button type="submit" class="btn btn-primary">Buscar</button>
    </form>
    <div class="results-info text-center mb-3">
        Encontrados: <strong>{{ $total }}</strong> registros.
    </div>
    @if(isset($results) && count($results) > 0)
        @foreach($results as $hit)
            @php 
                $source = $hit['_source']; 
            @endphp
            <div class="card mb-3">
                <div class="card-body">
                    <div class="badges">
                        @if(!empty($source['ds_etapa_ensino']))
                            <span class="badge badge-primary">{{ $source['ds_etapa_ensino'] }}</span>
                        @endif
                        @if(!empty($source['ds_objeto']))
                            <span class="badge badge-secondary">{{ $source['ds_objeto'] }}</span>
                        @endif
                    </div>
                    <h3 class="card-title">{{ $source['ds_grupo'] ?? $source['ds_bloco'] ?? 'Item sem título' }}</h3>
                    <div class="text-body">
                        {{ $source['ds_questao'] ?? '' }}
                    </div>
                    <div class="meta text-muted">
                        ID: {{ $source['co_questao'] ?? $hit['_id'] }} | 
                        Edital: {{ $source['ds_titulo_edital'] ?? 'N/A' }} |
                        Score: {{ number_format($hit['_score'], 2) }}
                    </div>
                </div>
            </div>
        @endforeach
    @else
        @if($query)
            <p class="text-center text-muted mt-4">
                Nenhum resultado encontrado para "<strong>{{ $query }}</strong>".
            </p>
        @else
            <p class="text-center text-muted mt-4">
                Digite algo acima para pesquisar no banco PNLD.
            </p>
        @endif
    @endif
</div>

@endsection

<!-- Inclua o JS do Bootstrap -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>