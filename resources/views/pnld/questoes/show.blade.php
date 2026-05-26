@extends('layouts.master')

@section('content')
@include('admin.includes.alerts')

<div class="container">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('pnld.questoes.index', $query ? ['q' => $query] : []) }}">Banco de Questões PNLD</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                Questão {{ $questao['nu_questao'] ?? $questao['co_questao'] }}
            </li>
        </ol>
    </nav>

    <div class="card mb-4">
        <div class="card-header">
            <h1 class="h4 mb-0">
                Questão {{ $questao['nu_questao'] ?? '' }}
            </h1>
        </div>
        <div class="card-body">
            <div class="mb-3">
                @if(!empty($questao['ds_etapa_ensino']))
                    <span class="badge badge-primary">{{ $questao['ds_etapa_ensino'] }}</span>
                @endif
                @if(!empty($questao['ds_objeto']))
                    <span class="badge badge-secondary">{{ $questao['ds_objeto'] }}</span>
                @endif
                @if(!empty($questao['ds_area_conhecimento']))
                    <span class="badge badge-info">{{ $questao['ds_area_conhecimento'] }}</span>
                @endif
                @if(!empty($questao['ds_componente']))
                    <span class="badge badge-light border">{{ $questao['ds_componente'] }}</span>
                @endif
            </div>

            <p class="lead">{{ $questao['ds_questao'] ?? '' }}</p>

            @if(!empty($questao['ds_orientacoes_questao']))
                <div class="alert alert-light border">
                    <strong>Orientações da questão:</strong>
                    <p class="mb-0 mt-2">{{ $questao['ds_orientacoes_questao'] }}</p>
                </div>
            @endif

            @if(!empty($questao['ds_orientacoes_bloco']))
                <div class="alert alert-light border">
                    <strong>Orientações do bloco:</strong>
                    <p class="mb-0 mt-2">{{ $questao['ds_orientacoes_bloco'] }}</p>
                </div>
            @endif
        </div>
        <div class="card-footer text-muted small">
            <dl class="row mb-0">
                <dt class="col-sm-3">Edital</dt>
                <dd class="col-sm-9">{{ $questao['ds_titulo_edital'] ?? 'Não informado' }} (nº {{ $questao['nu_edital'] ?? '—' }})</dd>

                <dt class="col-sm-3">Bloco</dt>
                <dd class="col-sm-9">{{ $questao['nu_bloco'] ?? '' }} — {{ $questao['ds_bloco'] ?? 'Não informado' }}</dd>

                <dt class="col-sm-3">Subbloco</dt>
                <dd class="col-sm-9">{{ $questao['nu_subbloco'] ?? '' }} — {{ $questao['ds_subbloco'] ?? 'Não informado' }}</dd>

                <dt class="col-sm-3">Grupo</dt>
                <dd class="col-sm-9">{{ $questao['nu_grupo'] ?? '' }} — {{ $questao['ds_grupo'] ?? 'Não informado' }}</dd>

                <dt class="col-sm-3">Modelo de resposta</dt>
                <dd class="col-sm-9">{{ $questao['ds_modelo_resposta'] ?? 'Não informado' }}</dd>

                <dt class="col-sm-3">Código</dt>
                <dd class="col-sm-9">{{ $questao['co_questao'] ?? '—' }}</dd>
            </dl>
        </div>
    </div>

    @if(count($relacionadas) > 0)
        <h2 class="h5 mb-3">Outras questões do mesmo grupo</h2>
        @foreach($relacionadas as $hit)
            @php $rel = $hit['_source']; @endphp
            <a href="{{ route('pnld.questoes.show', ['id' => $rel['co_questao'] ?? $hit['_id'], 'q' => $query]) }}"
               class="card mb-2 text-decoration-none text-dark">
                <div class="card-body py-3">
                    <strong>{{ $rel['nu_questao'] ?? '' }}</strong>
                    <span class="d-block text-muted small mt-1">{{ $rel['ds_questao'] ?? '' }}</span>
                </div>
            </a>
        @endforeach
    @endif

    <div class="text-center mt-4 mb-4">
        <a href="{{ route('pnld.questoes.index', $query ? ['q' => $query] : []) }}" class="btn btn-outline-primary">
            Voltar aos resultados
        </a>
    </div>
</div>
@endsection
