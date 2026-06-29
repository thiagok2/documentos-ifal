@extends('layouts.master')

@section('title', $normativa['ato']['titulo'] )

@section('keywords', $keywords )

@section('content')

<script>
    function share(id, titulo, ementa) {
        if (id.indexOf(".pdf") != -1) {
            id = [id.slice(0, -4), "\\", id.slice(-4)].join('');
        }
        url = window.location.href;

        if (navigator.share) {
            navigator.share({
                text: 'Acesse: ' + titulo + ' no Normativas',
                url: url,
            })
                .catch((error) => { });
        } else {
            $('#tooltip-' + id).css("visibility", "visible");
            $('#tooltip-' + id).css("opacity", "1");

            $('#url-' + id).val(url);
            $('#url-' + id).select();
            document.execCommand('copy');

            setTimeout(function () {
                $('#tooltip-' + id).css("opacity", "0");
            }, 1500);
            setTimeout(function () {
                $('#tooltip-' + id).css("visibility", "hidden");
            }, 1800);
        }
    }
</script>

<header id="header-buscado">
    <div class="container-fluid">
        <div class="row">
            <div class="col-6 col-lg-8 offset-lg-1">
                <a href="{{ route('index') }}">
                    <img src="/img/logo.png" alt="CentralDoc" class="logo-img" />
                </a>
            </div>
            <div class="col-6 col-lg-2 text-right">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-pill btn-login">Home <i class="fa fa-user"></i></a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-pill btn-login">Entrar <i class="fa fa-user"></i></a>
                    @endauth
                @endif
            </div>
        </div>
    </div>
</header>

<section id="results">
    <div class="container-fluid">
        <article class="row">
            <div class="col-lg-10 offset-lg-1">
                <div class="card mb-3 card-primary">
                    <div style="background-color: white !important;" class="card-header">
                        <div class="row align-items-start">
                            <div class="col-lg-10">
                                <h6 class="mb-1">
                                    <span class="documento-titulo-link">{{ $normativa['ato']['titulo'] }}</span>
                                </h6>
                                @if (!empty($normativa['ato']['fonte']['orgao']))
                                    <a class="card-down link-subtitulo"
                                       href="{{ route('index') }}?query={{ urlencode($normativa['ato']['fonte']['orgao']) }}&publico=1">
                                        {{ $normativa['ato']['fonte']['orgao'] }}
                                    </a>
                                @endif
                            </div>
                            <div class="col-lg-2 text-right">
                                <div class="tooltip-custom">
                                    <span class="tooltiptext" id="tooltip-{{ $id }}">Link copiado!</span>
                                    <input aria-hidden="true" id="url-{{ $id }}" />
                                    <button class="btn-new btn btn-secondary pull-right" type="button"
                                        onclick="share('{{ $id }}','{{ $normativa['ato']['titulo'] }}','{{ $normativa['ato']['ementa'] }}')">
                                        <i class="fa fa-share-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-down card-body">
                        @if (!empty($normativa['ato']['numero']) && $normativa['ato']['numero'] != '00/00')
                            <span class="descricao">{{ $normativa['ato']['numero'] }}</span>
                        @endif
                        @if (!empty($normativa['ato']['tipo_doc']) && $normativa['ato']['tipo_doc'] != 'Indefinido' && $normativa['ato']['tipo_doc'] != 'Indeterminado')
                            <span class="descricao">{{ $normativa['ato']['tipo_doc'] }}</span>
                        @endif
                        @if (date('Y', strtotime($normativa['ato']['data_publicacao'])) >= 1900)
                            <span class="descricao">
                                {{ date('d/m/Y', strtotime($normativa['ato']['data_publicacao'])) }}
                            </span>
                        @endif
                        @if (!empty($normativa['ato']['fonte']['esfera']))
                            <span class="descricao">{{ $normativa['ato']['fonte']['esfera'] }}</span>
                        @endif

                        <div class="text-right mt-2">
                            <a class="normativa-detalhes-toggle" data-toggle="collapse" href="#dadosDoPdfCollapse"
                               role="button" aria-expanded="false" aria-controls="dadosDoPdfCollapse">
                                <i class="fa fa-info-circle"></i> Mostrar / ocultar detalhes do documento
                            </a>
                        </div>

                        <div class="collapse mt-2" id="dadosDoPdfCollapse">
                            <div class="normativa-detalhes-panel">
                                @if (!empty($normativa['ato']['ementa']))
                                    <p class="mb-2"><strong>Ementa:</strong> {{ $normativa['ato']['ementa'] }}</p>
                                @endif

                                @if (!empty($normativa['ato']['tags']))
                                    <div class="descricao">
                                        <strong>Palavras-chave:</strong>
                                        @foreach ($normativa['ato']['tags'] as $tag)
                                            <a href="/?query={{ urlencode($tag) }}&publico=1" class="badge badge-info">{{ $tag }}</a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @auth
                    @if (auth()->user()->isAdmin() && !$persisted)
                        <div class="alert alert-danger">
                            <strong>Atenção:</strong> este documento não está sendo gerenciado pela área de administração.
                        </div>
                    @endif
                @endauth

                <div class="mb-3">
                    @if ($normativa['ato']['tipo_doc'] != 'doc' && $normativa['ato']['tipo_doc'] != 'docx' && substr($id, -3) != "doc" && substr($id, -4) != "docx")
                        <iframe src="/normativa/pdf/{{ $id }}#zoom=133" width="100%" height="960px" title="Visualização do documento"></iframe>
                    @else
                        <iframe src="https://docs.google.com/gview?url={{ str_replace('view', 'pdf', Request::url()) }}&embedded=true" width="100%" height="600px" title="Visualização do documento"></iframe>
                    @endif
                </div>

                <div class="buttons-card mb-4">
                    @auth
                        @if ((auth()->user()->isAdmin() || auth()->user()->unidade->sigla === $normativa['ato']['fonte']['sigla'])
                            && isset($normativa['ato']['id_persisted']))
                            <a href="{{ route('documento-edit', $normativa['ato']['id_persisted']) }}" title="Editar" class="btn btn-primary">
                                <i class="fa fa-edit"></i> Editar
                            </a>
                        @endif
                    @endauth

                    @auth
                        @if (auth()->user()->isAdmin() && !$persisted)
                            <a href="{{ route('delete-elastic', $arquivoId) }}" class="btn btn-danger">Excluir</a>
                        @endif
                    @endauth
                </div>
            </div>
        </article>
    </div>
</section>

<hr class="split-sm">
<hr class="split">

@include('includes.chat', ['document_id' => $id, 'is_normativa' => true])

@endsection
