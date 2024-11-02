@extends('layouts.master')

@section('title', $unidade->nome )

@section('description', "Página do ".$unidade->nome.". Essa plataforma disponibiliza os atos normativos deste órgão." )

@section('keywords', $unidade->nome.','.$unidade->sigla )

<script>
    function share(id, sigla, nome) {           
        url = window.location.href;

        if (navigator.share) {            
            navigator.share({                
                text: 'Acesse a página do conselho: ' + nome + ' no Normativas',
                url: url,
            })            
            .catch((error) => {});
        } else {                            
            $('#tooltip-' + id).css("visibility", "visible");
            $('#tooltip-' + id).css("opacity", "1");  
                
            $('#url-' + id).val(url);
            $('#url-' + id).select();
            document.execCommand('copy');    
    
            setTimeout(function(){ 
                $('#tooltip-' + id).css("opacity", "0");
            }, 1500);
            setTimeout(function(){             
                $('#tooltip-' + id).css("visibility", "hidden");        
            }, 1800);  
        }                        
    }
    
    function shareDoc(id, titulo, ementa) {                 
        url = $('#a-doc-' + id).attr('href');

        if (navigator.share) {            
            navigator.share({
                text: 'Acesse: ' + titulo + ' no Documentos IFAL',                
                url: url,
            })            
            .catch((error) => {});
        } else {                            
            $('#tooltip-doc-' + id).css("visibility", "visible");
            $('#tooltip-doc-' + id).css("opacity", "1");  
                
            $('#url-doc-' + id).val(url);
            $('#url-doc-' + id).select();
            document.execCommand('copy');    
    
            setTimeout(function(){ 
                $('#tooltip-doc-' + id).css("opacity", "0");
            }, 1500);
            setTimeout(function(){             
                $('#tooltip-doc-' + id).css("visibility", "hidden");        
            }, 1800);  
        }                                            
    }
    
</script>

<style>
    .tooltip-custom {
        position: relative;        
    }

    .tooltip-custom .tooltiptext {
        visibility: hidden;
        width: 120px;
        background-color: #555;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 5px 0;
        position: absolute;
        z-index: 1;
        bottom: 100%;
        left: 100%;
        margin-left: -120px;
        margin-bottom: 10px;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .tooltip-custom .tooltiptext::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 80%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: #555 transparent transparent transparent;
    }

    [aria-hidden="true"] {
        opacity: 0;
        position: absolute;
        z-index: -9999;
        pointer-events: none;
    }
</style>

@section('content')

<!-- mini-header -->
<section id="mini-header">
    <div class="container-fluid">
        <div class="row mt-4">
            <div class="col-lg-2 offset-lg-1">
                <h1>
                <a href="{{route('index')}}">
                    <img class="img-fluid" src="/img/normativos-logo.png" srcset="/img/normativos-logo@2x.png 2x" alt="Normativas" /></h1>
                </a>
            </div>
            <div class="col-lg-4"></div>
            <div class="col-lg-4 text-right">
                <a class="btn btn-mobile btn-info btn-pill m-1 mt-2 btn-sm" href="{{route('unidades-search')}}"><i class="fa fa-search"></i> Pesquisar Conselhos</a>
                <a class="btn btn-outline-secondary btn-pill btn-login m-1 mt-2" href="{{route('consultas-public')}}">Termos frequentes<i class="fa fa-search-plus"></i></a>
                <a class="btn btn-outline-secondary btn-pill btn-login m-1 mt-2" href="{{route('downloads-public')}}">Mais baixados<i class="fa fa-download"></i></a>
                @if (Route::has('login'))
                    @auth
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-pill btn-login m-1 mt-2">Home <i class="fa fa-user badge-info"></i></a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-pill btn-login m-1 mt-2">Entrar <i class="fa fa-user badge-info"></i></a>
                    @endauth
                @endif
            </div>
        </div>
    </div>
</section>
<!-- end mini-header -->

<section id="unidade" class="mt-2">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-10 offset-lg-1">
                <div class="box-head">
                    <div class="row">
                        <div class="col-lg-8">
                            <h1 style="font-size: 180%">{{$unidade->nome}}</h1>
                            @if($unidade->esfera == "Municipal")
                                <h2>{{$unidade->estado['nome']}}</h2>
                            @else
                                <h2>{{$unidade->sigla}}</h2>
                            @endif
                            @if (isset($unidade->confirmado_em))
                                <small><b>Data de ingresso:</b> {{date('d/m/Y', strtotime($unidade->confirmado_em))}}</small>
                            @endif
                        </div>
                        <div class="col-lg-4 text-right">
                            @if ($unidade->documentos_count > 0)
                                <p class="n-atos">
                                    <span>{{$unidade->documentos_count}}</span>
                                    <em>atos normativos cadastrados</em>
                                </p>
                                <br />
                            @endif
                            @if (isset($unidade->url))
                                <a class="btn btn-mobile btn-info btn-pill btn-sm" href="{{$unidade->url}}" target="_blank"><i class="fa fa-external-link"></i> Acesse o site</a>
                                <br />
                            @endif    
                            <div class="tooltip-custom">
                                <span class="tooltiptext" id="tooltip-{{ $unidade->id}}">Link copiado!</span>
                                <input aria-hidden="true" id="url-{{ $unidade->id}}"/>
                                <button class="btn btn-mobile btn-light btn-pill btn-sm" type="button" onclick="share('{{ $unidade->id}}','{{$unidade->sigla}}','{{$unidade->nome}}')">
                                    <i class="fa fa-share-alt"></i> Compartilhar conselho
                                </button>                                        
                            </div>                            
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row mt-2 mb-4">
                            <div class="col-lg-12">
                                <form action="{{route('index')}}" method="GET">
                                    <div class="input-group">
                                        <input type="text" name="query" id="query" class="form-control"
                                            placeholder="Busque atos normativos do {{$unidade->sigla}}" value=""/>
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-mobile btn-primary"><i class="fa fa-search"></i> Pesquisar</button>
                                            </div>
                                    </div>
                                    <input type="hidden" name="fonte" id="fonte" value="{{$unidade->sigla}}" />
                                </form>
                            </div>
                        </div>

                        <ul class="nav nav-tabs">
                            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#info">Info</a></li>
                            @forelse ($tiposTotal as $i => $tipo)
                                <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#{{$tipo->tipo}}">{{$tipo->tipo}} <span class="badge badge-pill badge-dark">{{$tipo->total}}</span></a></li>
                            @empty
                            @endforelse
                        </ul>

                        <div class="row">
                            <div class="col-lg-12">
                                    <div class="tab-content">
                                        <div id="info" class="tab-pane fade in active show">
                                            <!-- sobre o conselho -->
                                            <div class="row mt-4">
                                                <div class="col-lg-6">
                                                    <div class="card no-border">
                                                        <div class="card-header">
                                                            Sobre o Conselho
                                                        </div>
                                                        <div class="card-body pl-0 pr-0">
                                                            <div class="row pl-3 pr-3">
                                                                <div class="col-lg-4">
                                                                    <i class="fa fa-users"></i> <b><i>Gestão:</i></b>
                                                                </div>
                                                                <div class="col-lg-8">
                                                                    <span class="form-value">{{$unidade->contato}}</span><br />
                                                                    <span class="form-value">{{$unidade->contato2}}</span>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            <div class="row pl-3 pr-3">
                                                                <div class="col-lg-4">
                                                                    <i class="fa fa-adjust"></i> <b><i>Esfera:</i></b>
                                                                </div>
                                                                <div class="col-lg-8">
                                                                    {{$unidade->esfera}}
                                                                </div>
                                                            </div>
                                                             <hr>
                                                            <div class="row pl-3 pr-3">
                                                                <div class="col-lg-4">
                                                                    <i class="fa fa-globe"></i> <b><i>Localização:</i></b>
                                                                </div>
                                                                <div class="col-lg-8">
                                                                    {{$unidade->estado['nome'].' ('.$unidade->estado['sigla'].')'}}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="card no-border">
                                                        <div class="card-header">
                                                            Contato
                                                        </div>
                                                        <div class="card-body pl-0 pr-0">
                                                            <div class="row pl-3 pr-3">
                                                                <div class="col-lg-4">
                                                                     <i class="fa fa-envelope"></i> <b><i>Email:</i></b>
                                                                </div>
                                                                <div class="col-lg-8">
                                                                    {{$unidade->email}}
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            <div class="row pl-3 pr-3">
                                                                <div class="col-lg-4">
                                                                    <i class="fa fa-phone"></i> <b><i>Telefone:</i></b>
                                                                </div>
                                                                <div class="col-lg-8">
                                                                    <a href="tel:{{$unidade->telefone}}">{{$unidade->telefone}}</a>
                                                                </div>
                                                            </div>
                                                             <hr>
                                                            <div class="row pl-3 pr-3">
                                                                <div class="col-lg-4">
                                                                    <i class="fa fa-map-marker"></i> <b><i>Endereço:</i></b>
                                                                </div>
                                                                <div class="col-lg-8">
                                                                    {{$unidade->endereco}}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- fim sobre o conselho -->
                                        </div>
                                        @forelse ($tiposTotal as $i => $tipo)
                                            <div id="{{$tipo->tipo}}" class="tab-pane fade in">
                                                <div class="card mt-4">
                                                    <div class="card-body">
                                                        <div class="alert" style="background-color: #357a90; color: #ffffff;">
                                                            @if ($tipo->total > 25)
                                                                Listando apenas 25 atos normativos. <br/>
                                                                Para ver mais <a class="text-light" href="{{route('index', ['query' => $unidade->local(),'fonte' => $unidade->sigla])}}">clique aqui</a>
                                                            @elseif ($tipo->total > 1)
                                                                Listando todos os <strong>{{$tipo->total}}</strong> atos normativos. <br/>
                                                            @else
                                                                Listando <strong>1</strong> ato normativo. <br/>
                                                            @endif                                                            
                                                        </div>
                                                        
                                                        @foreach ($documentos as $k => $docs)
                                                            @if ($k === $tipo->id)
                                                                @foreach ($docs as $doc)                                                        
                                                                    <article class="row">
                                                                        <div class="col-lg-12">
                                                                            <div class="card mb-3">
                                                                                <div class="card-header">
                                                                                    <h6>
                                                                                        <a id="a-doc-{{$doc['id']}}" href="/normativa/view/{{ $doc['arquivo'] }}">
                                                                                            <i class="fa fa-external-link"></i>                                      
                                                                                            {{ $doc['titulo'] }}                   
                                                                                        </a>
                                                                                    </h6>
                                                                                </div>

                                                                                <div class="card-body">
                                                                                    @if ( isset($doc['ementa']) && 
                                                                                        (substr( $doc['ementa'] , -10) !=  substr($doc['titulo'], -10))
                                                                                        )
                                                                                        <strong>Ementa:&nbsp;&nbsp;</strong>{{ $doc['ementa'] }}
                                                                                        <hr/>
                                                                                    @endif
                                                                                    

                                                                                    <div class="row">
                                                                                        <div class="col-md-10">
                                                                                            @if (!empty($doc['numero']))
                                                                                            <strong>Número:</strong> {{ $doc['numero']}}
                                                                                            @endif

                                                                                            <br/>
                                                                                            <strong>Publicação:</strong> {{ date('d/m/Y', strtotime($doc['data_publicacao'] )) }}
                                                                                            @if($doc['tags'])                                            
                                                                                                <br />
                                                                                                <strong>Palavras-Chave:</strong>
                                                                                                @foreach ($doc['tags'] as $tag)
                                                                                                    <a href="?query={{$tag}}" class="badge badge-info">
                                                                                                        {{ $tag }}
                                                                                                    </a>
                                                                                                @endforeach
                                                                                            @endif
                                                                                        </div>                                    
                                                                                        <div class="col-md-2">
                                                                                            <div class="tooltip-custom">
                                                                                                <span class="tooltiptext" id="tooltip-doc-{{ $doc['id']}}">Link copiado!</span>
                                                                                                <input aria-hidden="true" id="url-doc-{{ $doc['id']}}"/>
                                                                                                <button class="btn btn-secondary pull-right" type="button" onclick="shareDoc('{{ $doc['id']}}','{{ $doc['titulo']}}','{{ $doc['ementa']}}')">
                                                                                                    <i class="fa fa-share-alt"></i>
                                                                                                </button>                                        
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <hr class="split-sm">                                                                                    

                                                                                    <a href="/normativa/pdf/{{ $doc['id'] }}" class="btn btn-primary" target="_blank">
                                                                                        <i class="fa fa-download"></i> Baixar
                                                                                    </a>

                                                                                    @auth
                                                                                        @if ((auth()->user()->isAdmin() || auth()->user()->unidade->sigla === $doc['fonte']['sigla'])
                                                                                        && isset($doc['id_persisted']) && isset($doc['persisted']))
                                                                                            <a href="{{ route("documento-edit", $doc['id_persisted']) }}" title="Editar" class="btn btn-primary pull-right m-1">
                                                                                                <i class="fa fa-edit" ></i>
                                                                                            </a>

                                                                                            @if (auth()->user()->isAdmin() && !$doc['persisted'])
                                                                                                <a href="{{route('delete-elastic',$doc['id'])}}" class="btn btn-danger pull-right m-1" >
                                                                                                    <i class="fa fa-trash" ></i>
                                                                                                </a>
                                                                                            @endif
                                                                                        @endif


                                                                                        @if(!$doc['persisted'] && auth()->user()->isAdmin())
                                                                                            <div class="alert alert-danger">
                                                                                                <strong>Atenção:</strong> este documento não está sendo gerenciado pela área de administração.
                                                                                            </div>
                                                                                        @endif

                                                                                    @endauth                                

                                                                                    <br/>

                                                                                    <div id="trechos-{{$loop->index}}" class="collapse">
                                                                                    @if (!empty($doc['trechos_destaque']))
                                                                                        <small>
                                                                                        <ul class="list-group">
                                                                                            @foreach ($doc['trechos_destaque'] as $highlight)
                                                                                            <li class="list-group-item">
                                                                                                <?php echo html_entity_decode ($highlight); ?>
                                                                                            </li>
                                                                                            @endforeach
                                                                                        </ul>
                                                                                        </small>
                                                                                    @endif
                                                                                    </div>

                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </article>
                                                                @endforeach
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @empty

                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="mx-3 pull-left">
                            <a href="javascript:history.back();" class="btn btn-primary"><i class="fa fa-long-arrow-left" aria-hidden="true"></i> Voltar</a>
                        </div>

                    </div><!-- end card-body -->
                </div><!-- end card -->
            </div><!-- end col-10 -->
        </div>
</section>

<hr class="split">
@endsection
