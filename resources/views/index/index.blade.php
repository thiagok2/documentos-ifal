@extends('layouts.master')

@if (!empty($query) && (!empty($documentos)))
@section('keywords', $query)
@endif

@section('content')
<!-- mini-header -->

@include('admin.includes.alerts')


@if (!empty($query) && (!empty($documentos)))

    <script>
        function share(id, titulo, ementa) {
            if (id.indexOf(".pdf") != -1) {
                id = [id.slice(0, -4), "\\", id.slice(-4)].join('');
            }
            url = $('#a-' + id).attr('href');

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

    <style>
        /* Migrado para o app-search.css */
    </style>

    <header id="header-buscado">
        <div class="container-fluid">
            <div class="row">
                <div class="col-6 col-lg-8 offset-lg-1">
                    <a href="{{ route('index') }}">
                        {{-- IMPORTANTE: Coloque o caminho correto para sua logo --}}
                        <img src="/img/logo_if.svg" alt="Documentos IFAL" class="logo-img" />
                    </a>
                </div>

                <div class="col-6 col-lg-2 text-right">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-pill btn-login">Home <i
                                    class="fa fa-user"></i></a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-pill btn-login">Entrar <i
                                    class="fa fa-user"></i></a>
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </header>
    <section id="mini-search">
        <div class="row">
            <div class="col-lg-10 offset-lg-1 mt-3">
                <form action="/" method="GET" class="">
                    <div class="row">
                        <div class="col-lg-8 offset-lg-2">
                            <div class="input-group">
                                <input type="text" name="query" class="form-control "
                                    placeholder="Digite os termos da consulta" value="{{ $query }}" />
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-mobile btn-primary"><i class="fa fa-search"></i>
                                        Pesquisar</button>
                                    @if (auth()->check() && auth()->user()->unidade)
                                        <button type="button" class="btn btn-mobile btn-info btn-sm" data-toggle="collapse"
                                            data-target="#filters-menu" aria-expanded="false" aria-controls="collapseExample"><i
                                                class="fa fa-cogs"></i> Configurações da busca</button>
                                        <input type="hidden" name="orgao" value="{{ auth()->user()->unidade->nome }}">
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="filters-menu" class="collapse <?php    if ($filters) {
            echo 'show';
        } else {
            echo 'hidden';
        }?>">
                        <div class="row">
                            <div class="col text-center mb-3">
                                <label class="custom-switch">
                                    <input type="checkbox" name="publico" value="1" {{$changePrivateFlag ? '' : 'checked'}}>
                                    <span class="slider round"></span>
                                    <span class="switch-label ml-2">Buscar Apenas Documentos Públicos</span>
                                </label>
                            </div>
                            {{-- <div class="col col-12 col-lg-4 offset-lg-2 mb-1">
                                <select class="form-control form-control-sm" name="esfera">
                                    <option value="all" <?php if($esfera=="all" ){ echo ' selected' ; }?>>Todas as Esferas
                                    </option>
                                    <option value="Federal" <?php if($esfera=="Federal" ){ echo ' selected' ; }?>>Federal
                                    </option>
                                    <option value="Estadual" <?php if($esfera=="Estadual" ){ echo ' selected' ; }?>>Estadual
                                    </option>
                                    <option value="Municipal" <?php if($esfera=="Municipal" ){ echo ' selected' ; }?>
                                        >Municipal</option>
                                </select>
                            </div>
                            <div class="col col-12 col-lg-4 mb-1">
                                <select class="form-control form-control-sm" name="periodo">
                                    <option value="all">Desde o princípio</option>
                                    <option value="<?php echo date(" Y"); ?>"
                                        <?php if($periodo == date("Y")){ echo ' selected'; }?>>Deste Ano
                                    </option>
                                    <option value="<?php echo (date(" Y")-2); ?>"
                                        <?php if($periodo == date("Y")-2){ echo ' selected'; }?>>Últimos 2 anos
                                    </option>
                                    <option value="<?php echo (date(" Y")-5); ?>"
                                        <?php if($periodo == date("Y")-5){ echo ' selected'; }?>>Últimos 5 anos
                                    </option>
                                </select>
                            </div> --}}
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@else
    <!-- fim mini-header -->

    <!-- header -->
    <header id="header" style="background-image: radial-gradient(ellipse at center, #19882c 1%, #025310 100%);">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-8 offset-lg-2 text-right p-0 ">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ route('home') }}" style="color: aliceblue !important"
                                class="btn btn-outline-secondary btn-pill btn-login m-1 mt-2">Home <i
                                    class="fa fa-user badge-info"></i></a>
                        @else
                            <a href="{{ route('login') }}" style="color: aliceblue !important"
                                class="btn btn-outline-secondary btn-pill btn-login m-1 mt-2">Entrar <i
                                    class="fa fa-user badge-info"></i></a>
                        @endauth
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 text-center">
                    <hr class="split">
                    <a href="{{route('index')}}">
                        <!-- <img src="/img/" srcset="/img/normativos-logo@2x.png 2x" alt="Documentos IFAL" /> -->
                        <h1 style="color: aliceblue">Documentos <strong style="color: limegreen"> IFAL </strong></h1>
                    </a>
                    <hr class="split">
                </div>
            </div>
        </div>
    </header>

    <!-- end header -->

    <!-- search form -->
    <section id="search">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    @if (isset($erro))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>{{$erro['titulo']}}</strong>
                            <br />
                            Notifique a administração do sistema através do email:
                            <a href="reitoria@ifal.com.br" target="_top">reitoria@ifal.com.br</a>

                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>

                        </div>
                        @if(getenv('APP_DEBUG'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <b>DEBUG</b>

                                <small>
                                    <p>{{$erro['local']}}</p>

                                    <p>{{$erro['trace']}}</p>
                                </small>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                    @endif

                    <form action="/" method="GET" class="">

                        <div class="input-group">
                            <input type="text" name="query" class="form-control" placeholder="Digite os termos da consulta"
                                value="{{ $query }}" />
                        </div>

                        <div class="row">
                            <div class="col text-center mt-3 mb-3">
                                <button type="submit" class="btn btn-mobile btn-primary"><i
                                        class="fa fa-search mr-1"></i>Pesquisar</button>
                                @if (auth()->check() && auth()->user()->unidade)
                                    <button type="button" class="btn btn-mobile btn-info ml-1" data-toggle="collapse"
                                        data-target="#filters-menu" aria-expanded="false" aria-controls="collapseExample"><i
                                            class="fa fa-cogs"></i> Configurações da busca</button>
                                    <input type="hidden" name="orgao" value="{{ auth()->user()->unidade->nome }}">
                                @endif
                            </div>


                        </div>
                        <div id="filters-menu" class="collapse <?php    if ($filters) {
            echo 'show';
        } else {
            echo 'hidden';
        }?>">
                            <div class="row">
                                <div class="col text-center mb-3">
                                    <label class="custom-switch">
                                        <input type="checkbox" id="switchPublico" value="1" name="publico" checked>
                                        <span class="slider round"></span>
                                        <span class="switch-label ml-2">Buscar Apenas Documentos Públicos</span>
                                    </label>
                                </div>
                            </div>
                            <!--<div class="row">
                                                                    <div class="col col-12 col-lg-4 offset-lg-2 mb-1">
                                                                        <select class="form-control" name="esfera" >
                                                                            <option value="all" <?php    if ($esfera == "all") {
            echo ' selected';
        }?>>Todas as Esferas</option>
                                                                            <option value="Federal" <?php    if ($esfera == "Federal") {
            echo ' selected';
        }?>>Federal</option>
                                                                            <option value="Estadual" <?php    if ($esfera == "Estadual") {
            echo ' selected';
        }?>>Estadual</option>
                                                                            <option value="Municipal" <?php    if ($esfera == "Municipal") {
            echo ' selected';
        }?>>Municipal</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col col-12 col-lg-4 mb-1">
                                                                        <select class="form-control" name="periodo">
                                                                            <option value="all">Desde o princípio</option>
                                                                            <option value="<?php    echo date("Y"); ?>" <?php    if ($periodo == date("Y")) {
            echo ' selected';
        }?>>Deste Ano</option>
                                                                            <option value="<?php    echo (date("Y") - 2); ?>" <?php    if ($periodo == date("Y") - 2) {
            echo ' selected';
        }?>>Últimos 2 anos</option>
                                                                            <option value="<?php    echo (date("Y") - 5); ?>" <?php    if ($periodo == date("Y") - 5) {
            echo ' selected';
        }?>>Últimos 5 anos</option>
                                                                        </select>
                                                                    </div>
                                                                </div>-->
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!-- end search form -->
@endif

<!-- results -->
<section id="results">
    <div class="container-fluid">
        @if (isset($fonte) && isset($conselho))
            <div class="row">
                <div class="col-lg-10 offset-lg-1 mt-3">
                    <div>
                        <h4 class="rounded">
                            {{$conselho->nome}}
                            <a class="pull-right" href="{{route('unidades-page', $conselho->friendly_url)}}"
                                style="color:white;">
                                <i class="fa fa-external-link"></i>
                            </a>
                        </h4>
                    </div>
                </div>
            </div>
        @endif

        @if (!empty($documentos))
        <!-- aggregates -->
        @if (!empty($query) && (!empty($documentos)))
            <nav class="row">
                <div class="col-lg-10 offset-lg-1">
                    @if ($changePrivateFlag)
                        <h2>Documentos Privados</h1>
                    @endif
                        <p class="mb-3 mt-3">
                            <i class="fa fa-filter"></i> <span>Filtrar resultados</span> <em>({{ $total }}</em>
                            resultados encontrados. Exibindo de
                            @if ($total > ($page) * $size_page)
                                <em>{{(($page - 1) * $size_page) + 1}} até {{($page) * $size_page}})</em>
                            @else
                                <em>{{(($page - 1) * $size_page) + 1}} até {{$total}})</em>
                            @endif

                            <br />
                            <!--Score máximo ({{ $max_score }}).-->
                        </p>
                        <div class="mt-2">
                            @if ((($esfera && $esfera != "all") || $ano || $fonte))
                                <a href="?query={{ $query }}" class="btn btn-outline-secondary btn-pill btn-sm mb-2">
                                    Limpar Filtros
                                    <span class="badge badge-pill badge-info"></span>
                                </a>
                            @endif

                            @if (isset($aggregations))

                                @foreach ($aggregations['ano']['labels'] as $bucket)
                                        <a href="?query={{ $query }}&ano={{ urlencode($bucket['nome']) }}&esfera={{ $esfera  }}&fonte={{ $fonte  }}"
                                            class="btn btn-outline-secondary btn-pill btn-sm mb-2 <?php                if (isset($ano))
                                    echo "bg-secondary text-light" ?>">
                                            {{ ucfirst($bucket['nome']) }}
                                            <span class="badge badge-pill badge-info">{{ $bucket['quantidade'] }}</span>
                                        </a>
                                @endforeach

                                @foreach ($aggregations['esfera']['labels'] as $bucket)
                                        <a href="?query={{ $query }}&esfera={{ urlencode($bucket['nome']) }}&ano={{ $ano }}&fonte={{ $fonte  }}"
                                            class="btn btn-outline-secondary btn-pill btn-sm mb-2 <?php                if (isset($esfera))
                                    echo "bg-secondary text-light" ?>">
                                            {{ ucfirst($bucket['nome']) }}
                                            <span class="badge badge-pill badge-info">{{ $bucket['quantidade'] }}</span>
                                        </a>
                                @endforeach

                                @foreach ($aggregations['fonte']['labels'] as $bucket)
                                        <a href="?query={{ $query }}&fonte={{ urlencode($bucket['nome']) }}&ano={{ $ano  }}&esfera={{ $esfera }}"
                                            class="btn btn-outline-secondary btn-pill btn-sm mb-2 <?php                if (isset($fonte))
                                    echo "bg-secondary text-light" ?>">
                                            {{ ucfirst($bucket['nome']) }}
                                            <span class="badge badge-pill badge-info">{{ $bucket['quantidade'] }}</span>
                                        </a>
                                @endforeach
                                @foreach ($aggregations['tipo_doc']['labels'] as $bucket)
                                        <a href="?query={{ $query }}&tipo_doc={{ urlencode($bucket['nome']) }}&ano={{ $ano  }}&esfera={{ $esfera }}&fonte={{ $fonte  }}"
                                            class="btn btn-outline-secondary btn-pill btn-sm mb-2 <?php                if (isset($tipo_doc))
                                    echo "bg-secondary text-light" ?>">
                                            {{ ucfirst($bucket['nome']) }}
                                            <span class="badge badge-pill badge-info">{{ $bucket['quantidade'] }}</span>
                                        </a>
                                @endforeach
                            @endif

                        </div>
                </div>
            </nav>
            <hr class="split-sm">
        @endif
        <!-- fim aggregates -->

        @foreach ($documentos as $doc)
            <article class="row">
                <div class="col-lg-10 offset-lg-1">
                    <div @class([
                        'card',
                        'mb-3',
                        'card-secondary' => $changePrivateFlag,
                        'card-primary' => !$changePrivateFlag,
                    ])>
                        <div style="background-color: white !important;" class="card-header">
                            <h6>
                                <a style="color: #14791b !important;font-size: 20px; font-weight: bold;"
                                    onmouseover="this.style.setProperty('text-decoration','underline','important');"
                                    onmouseout="this.style.setProperty('text-decoration','none','important');"
                                    id="a-{{$doc['id']}}" href="/normativa/view/{{ $doc['id'] }}?query={{$query}}">
                                    {{-- <i class="fa fa-external-link"></i>--}}
                                    {{ $doc['titulo'] }}
                                </a>


                                <div id="max_score" class="float-lg-right float-xs-left">
                                    <input value="{{ ($doc['score'])  }}" type="text" class="kv-fa rating-loading"
                                        data-min=0 data-max={{$max_score}} data-step=0.01 data-size="xs" required title="">
                                </div>

                            </h6>
                            @if (isset($doc['fonte']['sigla']))
                                <a class="card-down link-subtitulo"
                                    href="?query={{$query}}&fonte={{ $doc['fonte']['sigla'] }}">
                                    {{ $doc['fonte']['orgao'] }}
                                </a>
                            @else
                                {{ $doc['fonte']['orgao'] }}
                            @endif
                        </div>
                        <div class="card-down card-body">
                            {{-- Funcionalidade imterrompida por causa do CRAWLER
                            @if ( isset($doc['ementa']) &&
                            (substr( $doc['ementa'] , -10) != substr($doc['titulo'], -10))
                            )
                            <strong>Ementa:&nbsp;&nbsp;</strong>{{ $doc['ementa'] }}
                            <hr />
                            @endif --}}{{--
                            <span style="font-size: 20px">{{ $doc['ementa'] }}</span>
                            <hr />--}}
                            <!-- <div class="row">
                                                                    <div class=" col-md-10"> -->
                            @if (!empty($doc['numero']) && $doc['numero'] != '00/00')
                                <span class="descricao"> {{ $doc['numero']}} </span>
                            @endif
                            @if (!empty($doc['tipo_doc']) && $doc['tipo_doc'] != 'Indefinido' && $doc['tipo_doc'] != 'Indeterminado')
                                <span class="descricao"> {{ $doc['tipo_doc']}} </span>
                            @endif
                     
                            @php
                                $anoDocumento = date('Y', strtotime($doc['data_publicacao']));
                            @endphp

                            @if($anoDocumento != '1800')
                                <span class="descricao">
                                    {{ date('d/m/Y', strtotime($doc['data_publicacao'])) }}
                            @else

                                @endif
                            </span>
                            @if (!empty($doc['tags']) && is_string($doc['tags']))
                                <span class="descricao">
                                    @foreach (explode(',', $doc['tags']) as $tag)
                                        <a href="?query={{ trim($tag) }}" class="badge badge-info">
                                            {{ trim($tag) }}
                                        </a>
                                    @endforeach
                                </span>
                            @endif

                           
                            <!-- <div class="col-md-2"> -->
                            <!--
                                                                                <div class="tooltip-custom">
                                                                                    <span class="tooltiptext" id="tooltip-{{ $doc['id']}}">Link copiado!</span>
                                                                                    <input aria-hidden="true" id="url-{{ $doc['id']}}"/>
                                                                                    <button class="btn btn-secondary pull-right" type="button" onclick="share('{{ $doc['id']}}','{{ $doc['titulo']}}','{{ $doc['ementa']}}')">
                                                                                        <i class="fa fa-share-alt"></i>
                                                                                    </button>                                        
                                                                                </div>-->
                            <!-- </div> -->
                         
                            <!--<hr class="split-sm">-->


                            @auth
                                @if (
                                        (auth()->user()->isAdmin() || auth()->user()->unidade->sigla === $doc['fonte']['sigla'])
                                        && isset($doc['id_persisted']) && isset($doc['persisted'])
                                    )
                                    <a style="color: white !important" href="{{ route("documento-edit", $doc['id_persisted']) }}"
                                        title="Editar" class="btn btn-primary pull-right m-1">
                                        <i class="fa fa-edit"></i>
                                    </a>

                                    @if (auth()->user()->isAdmin() && !$doc['persisted'])
                                        <a style="color: white !important" href="{{route('delete-elastic', $doc['id'])}}"
                                            class="btn btn-danger pull-right m-1">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    @endif
                                @endif


                                @if(!$doc['persisted'] && auth()->user()->isAdmin())
                                    <div class="alert alert-danger">
                                        <strong>Atenção:</strong> este documento não está sendo gerenciado pela área de
                                        administração.
                                    </div>
                                @endif

                            @endauth
                            <!-- </div> -->
                            <div class="container-ementa">
                                  <div class="texto-ementa">
                                      @php
                                          $ementa   = $doc['ementa'] ?? '';
                                          $limit    = 500;
                                          $hasMore  = Str::length($ementa) > $limit;
                                          $short    = $hasMore ? Str::substr($ementa, 0, $limit) : $ementa;
                                          $rest     = $hasMore ? Str::substr($ementa, $limit) : '';
                                          $uid      = 'ementa-'.$doc['id'] ?? 'ementa-'.$loop->index;
                                      @endphp

                                      <small class="ementa">
                                        {!! nl2br(e($short)) !!}

                                        @if($hasMore)
                                            <a class="ementa-more"
                                              data-bs-toggle="collapse"
                                              href="#{{ $uid }}"
                                              role="button"
                                              aria-expanded="false"
                                              aria-controls="{{ $uid }}">…(ver mais)</a>

                                            <span id="{{ $uid }}" class="collapse">
                                                {!! nl2br(e($rest)) !!}
                                            </span>
                                        @endif
                                    </small>
                                  </div>
                                  <div class="buttons-card">
                                    <button id='popoverBtn' {{-- data-bs-toggle="popover" data-bs-placement="top"
                                        data-bs-title="Trechos" data-bs-content="Aqui tem os trechos encontrados." --}}
                                        @class(['btn-new', 'btn', 'btn-secondary' => $changePrivateFlag, 'btn-primary' => !$changePrivateFlag]) type="button" data-toggle="collapse"
                                        data-target="#trechos-{{$loop->index}}" aria-expanded="false"
                                        aria-controls="highlight-collapse-{{$doc['id']}}" {{empty($doc['trechos_destaque']) ? 'disabled' : ''}}>
                                        <i class="fa fa-quote-right"></i>

                                    </button>

                                    <a href="/normativa/pdf/{{ $doc['id'] }}" @class(['btn-new', 'btn', 'btn-secondary' => $changePrivateFlag, 'btn-primary' => !$changePrivateFlag]) target="_blank">
                                        <i class="fa fa-download"></i>
                                    </a>
                                    <div class="tooltip-custom">
                                        <span class="tooltiptext" id="tooltip-{{ $doc['id']}}">Link copiado!</span>
                                        <input aria-hidden="true" id="url-{{ $doc['id']}}" />
                                        <button class="btn-new btn btn-secondary pull-right" type="button"
                                            onclick="share('{{ $doc['id']}}','{{ $doc['titulo']}}','{{ $doc['ementa']}}')">
                                            <i class="fa fa-share-alt"></i>
                                        </button>
                                    </div>
                                  </div>
                            </div>

                            <div id="trechos-{{$loop->index}}" class="collapse">
                                @if (!empty($doc['trechos_destaque']))
                                    <h6>Trechos encontrados</h6>
                                    <small>
                                        <ul class="list-group">
                                            @foreach ($doc['trechos_destaque'] as $highlight)
                                                <li class="list-group-item">
                                                    <?php                echo html_entity_decode($highlight); ?>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </small>
                                @endif
                            </div>
                        </div>
                        <hr class="discreta">
                        <!-- </div> -->
                    </div>
                </div>
            </article>
        @endforeach
        <!-- end results-->


        <!-- pagination-->
        <!-- param page_size(default=10)-->
        <div class="row">
            <div class="col-lg-10 offset-lg-1">
                <nav>
                    <ul class="pagination justify-content-center">
                        @if ($page > 1)
                            <li class="page-item">
                                <a href="?query={{ urlencode($query) }}&page={{ 1 }}&esfera={{ $esfera }}&fonte={{ $fonte }}&ano={{$ano}}&periodo={{$periodo}}&publico={{$changePrivateFlag ? 0 : 1}}"
                                    class="page-link" tabindex="-1">Primeira</a>
                            </li>
                            <li class="page-item">
                                <a href="?query={{ urlencode($query) }}&page={{ ($page - 1) }}&esfera={{ $esfera }}&fonte={{ $fonte }}&ano={{$ano}}&periodo={{$periodo}}&publico={{$changePrivateFlag ? 0 : 1}}"
                                    class="page-link" tabindex="-1">Anterior</a>
                            </li>
                        @endif

                        @if($total_pages > 0 && $page <= $total_pages)

                        @php($auxb = $page + 5 > $total_pages ? 9 - ($total_pages - $page) : 5 )
                        @php($begin = $page - $auxb > 0 ? $page - $auxb : 1)
                        @php($auxe = $page - 6 > 0 ? 5 : 10 - $page)
                        @php($end = $page + $auxe > $total_pages ? $total_pages : $page + $auxe)
                                            @for ($i = $begin; $i <= $end; $i++)
                                                <li class="page-item"><a class="page-link {{$i == $page ? 'active' : '' }}"
                                                        href="?query={{ urlencode($query) }}&page={{ ($i) }}&esfera={{ $esfera }}&fonte={{ $fonte }}&ano={{$ano}}&periodo={{$periodo}}&publico={{$changePrivateFlag ? 0 : 1}}">{{$i}}</a>
                                                </li>
                                            @endfor

                                            @endif

                                            @if ($page < $total_pages)
                                                <li class="page-item">
                                                    <a href="?query={{ urlencode($query) }}&page={{ ($page + 1) }}&esfera={{ $esfera }}&fonte={{ $fonte }}&ano={{$ano}}&periodo={{$periodo}}&publico={{$changePrivateFlag ? 0 : 1}}"
                                                        class="page-link">Próxima</a>
                                                </li>
                                                <li class="page-item">
                                                    <a href="?query={{ urlencode($query) }}&page={{ ($total_pages) }}&esfera={{ $esfera }}&fonte={{ $fonte }}&ano={{$ano}}&periodo={{$periodo}}&publico={{$changePrivateFlag ? 0 : 1}}"
                                                        class="page-link">Última</a>
                                                </li>
                                            @endif
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                            <!-- end pagination-->

                        @elseif($query)
        <div class="row mt-3" id="no-results">
            <div class="col-lg-6 offset-md-3">
                <div class="alert alert-secondary" role="alert">
                    Nenhum resultado encontrado para
                    <b>
                        "{{ $query }}"
                        @if($tipo_doc)
                            . Para o tipo {{ $tipo_doc }}.
                        @endif
                    </b>
                </div>
            </div>
        </div>
        @endif
    </div>
</section>
<!-- results -->
<hr class="split">

@endsection
@push('scripts-caio')
    <script src="/js/bootstrap5.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
            const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));

            // Aqui ativamos o evento blur para esconder o popover
            const popoverBtn = document.getElementById('popoverBtn');

            popoverBtn.addEventListener('blur', function () {
                const popover = bootstrap.Popover.getInstance(popoverBtn);
                if (popover) {
                    popover.hide();  // Esconde o popover ao perder o foco
                }
            });
        });
    </script>
@endpush