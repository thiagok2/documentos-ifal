@extends('layouts.master')

@section('content')

@include('admin.includes.alerts')

<style>
    .btn-custom-green {
        background-color: #19882c !important;
        border-color: #19882c !important;
        color: #ffffff !important;
    }
    .btn-custom-green:hover, .btn-custom-green:focus, .btn-custom-green:active {
        background-color: #025310 !important;
        border-color: #025310 !important;
        color: #ffffff !important;
        box-shadow: 0 0 0 0.2rem rgba(25, 136, 44, 0.25) !important;
    }
    .btn-outline-custom-green {
        color: #19882c !important;
        border-color: #19882c !important;
        background-color: transparent !important;
    }
    .btn-outline-custom-green:hover, .btn-outline-custom-green:focus, .btn-outline-custom-green:active {
        color: #ffffff !important;
        background-color: #19882c !important;
        border-color: #19882c !important;
        box-shadow: 0 0 0 0.2rem rgba(25, 136, 44, 0.25) !important;
    }
    .btn-custom-green:focus, .btn-outline-custom-green:focus {
        outline: none !important;
    }
</style>

<header id="header-buscado" style="background-image: radial-gradient(ellipse at center, #19882c 1%, #025310 100%); padding: 20px 0;">
    <div class="container-fluid">
        <div class="row">
            <div class="col-6 col-lg-8 offset-lg-1">
                <a href="{{ route('index') }}">
                    <img src="/img/logo.png" alt="CentralDoc" class="logo-img" style="height: 50px;" />
                </a>
                <span class="text-white ml-3" style="font-size: 1.5rem; vertical-align: middle;">| Busca de Artefatos</span>
            </div>
            <div class="col-6 col-lg-2 text-right">
                @auth
                    <a href="{{ route('home') }}" style="color: aliceblue !important" class="btn btn-outline-secondary btn-pill btn-login m-1 mt-2">Home <i class="fa fa-user badge-info"></i></a>
                @else
                    <a href="{{ route('login') }}" style="color: aliceblue !important" class="btn btn-outline-secondary btn-pill btn-login m-1 mt-2">Entrar <i class="fa fa-user badge-info"></i></a>
                @endauth
            </div>
        </div>
    </div>
</header>

<section id="mini-search">
    <div class="row">
        <div class="col-lg-10 offset-lg-1 mt-4 mb-4">
            <form action="{{ route('artefatos-search') }}" method="GET">
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <div class="input-group">
                            <input type="text" name="query" class="form-control" placeholder="Buscar por resumo, título ou termo em artefatos..." value="{{ $query }}" />
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-search"></i> Pesquisar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<div id="index-conteudo">
    <section id="results">
        <div class="container-fluid">
            @if(isset($erro))
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="alert alert-danger">
                        <strong>{{ $erro['titulo'] }}</strong><br>
                        @if(env('APP_DEBUG'))
                        <small>{{ $erro['local'] }}<br>{{ $erro['trace'] }}</small>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            @if (!empty($documentos))
                <div class="row">
                    <div class="col-lg-10 offset-lg-1">
                        <p class="mb-3 mt-3">
                            <em>{{ $total }}</em> resultados encontrados.
                        </p>
                    </div>
                </div>

                @foreach ($documentos as $doc)
                    <article class="row">
                        <div class="col-lg-10 offset-lg-1">
                            <div class="card mb-4 border-0 shadow-sm" style="border-left: 5px solid #19882c !important; border-radius: 0.5rem;">
                                <div class="card-header bg-white border-bottom-0 position-relative pt-3 pb-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="m-0 font-weight-bold" style="font-size: 1.25rem;">
                                            <a href="{{ route('artefato-view', $doc['id']) }}" class="stretched-link" style="color: #19882c !important; text-decoration: none;">
                                                <i class="fa fa-file-pdf mr-2" style="color: #19882c;"></i>{{ $doc['titulo'] ?? 'Artefato Sem Título' }}
                                            </a>
                                        </h5>
                                        <span class="text-muted" style="font-size: 0.85rem; position: relative; z-index: 2;">
                                            <i class="fa fa-calendar-alt mr-1"></i>{{ date('d/m/Y', strtotime($doc['created_at'])) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body pt-2">
                                    @if(!empty($doc['especificacao']))
                                        <div class="mb-2">
                                            <span class="text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; font-weight: 600;">Especificação</span><br>
                                            <span class="text-dark" style="font-size: 0.95rem;">{{ $doc['especificacao'] }}</span>
                                        </div>
                                    @endif
                                    
                                    <div class="mb-3">
                                        <span class="text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; font-weight: 600;">Resumo</span><br>
                                        <p class="text-justify text-secondary mb-0" style="font-size: 0.95rem; line-height: 1.5;">{{ $doc['resumo'] }}</p>
                                    </div>

                                    @if (!empty($doc['entidades']))
                                        <div class="mb-2">
                                            @foreach ($doc['entidades'] as $entidade)
                                                <span class="badge badge-pill badge-light border text-muted mr-1 px-2 py-1" style="font-weight: 500;">
                                                    <i class="fa fa-tag mr-1" style="color: #19882c;"></i>{{ $entidade['texto'] }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="buttons-card d-flex justify-content-end mt-3" style="position: relative; z-index: 2;">
                                        <a href="{{ route('artefato-view', $doc['id']) }}" class="btn btn-custom-green btn-sm mr-2" title="Visualizar Artefato">
                                            <i class="fa fa-eye"></i> Visualizar
                                        </a>
                                        <a href="{{ route('artefato-download', $doc['id']) }}" class="btn btn-outline-custom-green btn-sm mr-2" target="_blank" title="Baixar PDF">
                                            <i class="fa fa-download"></i> Baixar
                                        </a>

                                        @auth
                                            @if(auth()->user()->isAdmin())
                                                <form action="{{ route('artefato-delete', $doc['id']) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este artefato? Esta ação é irreversível.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Excluir">
                                                        <i class="fa fa-trash"></i> Excluir
                                                    </button>
                                                </form>
                                            @endif
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach

                <!-- Paginação -->
                <div class="row">
                    <div class="col-lg-10 offset-lg-1">
                        <nav aria-label="Navegação de páginas">
                            <ul class="pagination justify-content-center">
                                @if($page > 1)
                                    <li class="page-item"><a class="page-link" href="?query={{ urlencode($query) }}&page={{ $page - 1 }}">Anterior</a></li>
                                @endif
                                
                                <li class="page-item disabled"><span class="page-link">Página {{ $page }} de {{ $total_pages == 0 ? 1 : $total_pages }}</span></li>

                                @if($page < $total_pages)
                                    <li class="page-item"><a class="page-link" href="?query={{ urlencode($query) }}&page={{ $page + 1 }}">Próxima</a></li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                </div>

            @elseif(isset($query))
                <div class="row mt-3">
                    <div class="col-lg-6 offset-md-3">
                        <div class="alert alert-secondary text-center" role="alert">
                            Nenhum artefato encontrado para <b>"{{ $query }}"</b>.
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
</div>

@endsection
