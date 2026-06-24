@extends('layouts.master')

@section('content')

@include('admin.includes.alerts')

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
                            <div class="card mb-3 card-primary" style="border-left: 5px solid #19882c;">
                                <div class="card-header bg-white">
                                    <h6>
                                        <i class="fa fa-file-pdf" style="color: #19882c;"></i> {{ $doc['titulo'] ?? 'Artefato Sem Título' }}
                                        <span class="float-right text-muted" style="font-size: 0.8rem">
                                            {{ date('d/m/Y', strtotime($doc['created_at'])) }}
                                        </span>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    @if(!empty($doc['especificacao']))
                                        <p><strong>Especificação:</strong> {{ $doc['especificacao'] }}</p>
                                    @endif
                                    
                                    <p class="text-justify">{{ $doc['resumo'] }}</p>

                                    @if (!empty($doc['entidades']))
                                        <div class="mb-3">
                                            @foreach ($doc['entidades'] as $entidade)
                                                <span class="badge badge-info">{{ $entidade['texto'] }}</span>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="buttons-card d-flex justify-content-end">
                                        <a href="{{ route('artefato-download', $doc['id']) }}" class="btn btn-outline-primary btn-sm mr-2" target="_blank" title="Baixar PDF">
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
