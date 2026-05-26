@extends('layouts.master')
@if (!empty($query))
@section('keywords', $query)
@endif
@section('content')
@include('admin.includes.alerts')

<div class="container pt-4">
    <h1 class="mt-3 mb-4 text-center">Banco de Questões PNLD</h1>
    <form id="form-pnld-busca" action="{{ route('pnld.questoes.index') }}" method="GET" class="search-box mb-3">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="input-group">
                    <input type="text" name="q" value="{{ $query }}" placeholder="Pesquise por termos (ex.: professor, digital, recursos)..." class="form-control" aria-label="Termo de busca">
                    <div class="input-group-append">
                        <button id="btn-pnld-buscar" type="submit" class="btn btn-primary">
                            <span id="texto-botao-pnld">
                                <i class="fa fa-search"></i> Buscar
                            </span>
                            <span id="loading-botao-pnld" class="d-none">
                                <i class="fa fa-spinner fa-spin"></i> Pesquisando...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div id="pnld-loading" class="pnld-loading index-loading text-center py-5 d-none" role="status" aria-live="polite">
        <i class="fa fa-spinner fa-spin fa-2x"></i>
        <p class="mt-3 text-muted mb-0">Buscando questões, aguarde...</p>
    </div>

    <div id="pnld-conteudo">
    @if($query)
        <div class="results-info text-center mb-3">
            Encontrados: <strong>{{ $total }}</strong> {{ $total === 1 ? 'registro' : 'registros' }}.
        </div>
    @endif

    @if(isset($results) && count($results) > 0)
        @foreach($results as $hit)
            @php
                $source = $hit['_source'];
                $questaoId = $source['co_questao'] ?? $hit['_id'];
            @endphp
            <a href="{{ route('pnld.questoes.show', ['id' => $questaoId, 'q' => $query]) }}"
               class="card mb-3 text-decoration-none text-dark pnld-questao-card">
                <div class="card-body">
                    <div class="badges mb-2">
                        @if(!empty($source['ds_etapa_ensino']))
                            <span class="badge badge-primary">{{ $source['ds_etapa_ensino'] }}</span>
                        @endif
                        @if(!empty($source['ds_objeto']))
                            <span class="badge badge-secondary">{{ $source['ds_objeto'] }}</span>
                        @endif
                    </div>
                    <h2 class="h5 card-title mb-2">
                        {{ $source['nu_questao'] ?? '' }}
                        — {{ $source['ds_grupo'] ?? $source['ds_bloco'] ?? 'Item sem título' }}
                    </h2>
                    <div class="text-body">
                        {{ \Illuminate\Support\Str::limit($source['ds_questao'] ?? '', 280) }}
                    </div>
                    <div class="meta text-muted small mt-2">
                        Código: {{ $questaoId }}
                        | Edital: {{ $source['ds_titulo_edital'] ?? 'Não informado' }}
                    </div>
                    <span class="text-primary small">Ver detalhes da questão &rarr;</span>
                </div>
            </a>
        @endforeach

        @if(method_exists($results, 'links'))
            <div class="d-flex justify-content-center mt-3">
                {{ $results->appends(['q' => $query])->links('pnld.questoes.pagination') }}
            </div>
        @endif
    @else
        @if($query)
            <p class="text-center text-muted mt-4">
                Nenhum resultado encontrado para "<strong>{{ $query }}</strong>".
            </p>
        @else
            <p class="text-center text-muted mt-4">
                Digite um termo acima para pesquisar no banco PNLD.
            </p>
        @endif
    @endif
    </div>
</div>

<style>
    .pnld-questao-card:hover {
        box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.1);
        border-color: #3182ce;
    }
</style>
@endsection

@push('scripts-caio')
<script>
    (function () {
        var form = document.getElementById('form-pnld-busca');
        var loading = document.getElementById('pnld-loading');
        var conteudo = document.getElementById('pnld-conteudo');
        var btn = document.getElementById('btn-pnld-buscar');
        var textoBotao = document.getElementById('texto-botao-pnld');
        var loadingBotao = document.getElementById('loading-botao-pnld');

        if (!form) {
            return;
        }

        function ativarLoading() {
            if (loading) {
                loading.classList.remove('d-none');
            }
            if (conteudo) {
                conteudo.classList.add('d-none');
            }
            if (btn) {
                btn.disabled = true;
                btn.style.pointerEvents = 'none';
                btn.style.opacity = '0.85';
            }
            if (textoBotao && loadingBotao) {
                textoBotao.classList.add('d-none');
                loadingBotao.classList.remove('d-none');
            }
        }

        form.addEventListener('submit', ativarLoading);

        document.addEventListener('click', function (event) {
            var link = event.target.closest('.pnld-pagination a.page-link');
            if (link) {
                ativarLoading();
            }
        });
    })();
</script>
@endpush
