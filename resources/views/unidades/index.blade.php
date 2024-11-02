@extends('layouts.master')

<script>
    function share(id, sigla, nome) {           
        url = $('#a-' + id).attr('href');

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
<!-- header -->
<section id="header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8 offset-lg-2 text-right p-0 ">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-pill btn-login m-1 mt-2">Home <i class="fa fa-user badge-info"></i></a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-pill btn-login m-1 mt-2">Entrar <i class="fa fa-user badge-info"></i></a>
                        <!--
                        <a href="{{ route('register') }}">Registrar</a>
                        -->
                    @endauth
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 text-center">                        
                <h1>
                    <a href="{{route('index')}}">   
                    </a>
                </h1>                
            </div>
        </div>
    </div>
</section>
<!-- end header -->

<!-- search form -->
<section id="search">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <form action="{{route('unidades-search')}}" method="GET" class="">
                    <h3 class="text-center"><small class="text-muted">Pesquisar Campus Municipais</small></h3>
                    <br/>
                    <div class="input-group">
                        <input type="text" name="q" id="q" class="form-control" value="{{$q}}"
                            placeholder="Busque pelos Município de Alagoas: Maceió, Rio largo, Coruripe..." value="" />
                    </div>
                    <div class="row">
                        <div class="col text-center mt-2 mb-2">
                            <button type="submit" class="btn btn-primary mr-1"><i class="fa fa-search"></i>
                                Pesquisar conselhos
                            </button>
                            <a class="btn btn-info ml-1" href="/" target="_blank">
                                <i class="fa fa-cogs"></i> Atos Normativos
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<hr class="split-sm">

<section id="results">
    <div class="container-fluid">
            @if (isset($federal))
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                    <div class="card mb-3">
                        <div class="card-header">
                            <a id="a-{{$federal->id}}" href="{{route('unidades-page',$federal->friendly_url)}}">
                                <i class="fa fa-external-link"></i>  {{ $federal->nome }}
                            </a>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-5">
                                    <p>
                                    <strong>Esfera:</strong>
                                    <span class="form-value">{{$federal->esfera}}</span>
                                    <br />
                                    <strong>Sigla:</strong>
                                    <span class="form-value">{{$federal->sigla}}</span>
                                    <br />
                                    <strong>Estado/Município:</strong>
                                    <span class="form-value">
                                        {{$federal->estado['nome'].'('.$federal->estado['sigla'].')'}}
                                    </span>
                                    <br />
                                    <strong>Gestão:</strong><br />
                                    <span class="form-value">{{$federal->contato}}</span><br />
                                    <span class="form-value">{{$federal->contato2}}</span>
                                    </p>

                                </div>

                                <div class="col-lg-5">
                                    <p>
                                        <strong>Email:</strong>
                                        <span class="form-value">{{$federal->email}}</span>
                                        <br />
                                        <strong>URL:</strong>
                                        <a class="form-value" href="{{$federal->url}}" target="_blank">{{$federal->url}}</a>
                                        <br />
                                        <strong>Telefone:</strong>
                                        <span class="form-value">
                                                <a href="tel:{{$federal->telefone}}">{{$federal->telefone}}</a>
                                        </span>
                                        <br />
                                        <strong>Endereço:</strong>
                                        <span class="form-value">
                                            <address>{{$federal->endereco}}</address>
                                        </span>
                                    </p>
                                </div>
                                 <div class="col-lg-2">
                                    <div class="tooltip-custom">
                                        <span class="tooltiptext" id="tooltip-{{ $federal->id}}">Link copiado!</span>
                                        <input aria-hidden="true" id="url-{{ $federal->id}}"/>
                                        <button class="btn btn-secondary pull-right" type="button" onclick="share('{{ $federal->id}}','{{$federal->sigla}}','{{$federal->nome}}')">
                                            <i class="fa fa-share-alt"></i>
                                        </button>                                        
                                    </div>
                                </div>
                            </div>
                        </div><!-- end card-body -->

                        @if (isset($federal->confirmado_em))
                        <div class="card-footer">

                            @if (isset($federal->confirmado_em))
                            <div class="col-lg-8">
                                <label class="form-label"><strong>Ingressou na plataforma em:</strong></label>
                                <span class="form-value">{{date('d/m/Y', strtotime($federal->confirmado_em))}}</span>
                            </div>
                            @endif                            
                        </div>
                        @endif


                    </div><!-- end card -->
                </div><!-- end col-10 -->
            </div><!-- end row-->
            @endif
        @forelse ($unidades as $u)
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                    <div class="card mb-3">
                        <div class="card-header">
                            <a id="a-{{$u->id}}" href="{{route('unidades-page',$u->friendly_url)}}">
                                <i class="fa fa-external-link"></i>  {{ $u->nome }} ({{ $u->documentosCount }})
                            </a>

                          
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-5">
                                <p>
                                    <strong>Esfera:</strong>
                                    <span class="form-value">{{$u->esfera}}</span>
                                    <br />
                                    <strong>Sigla:</strong>
                                    <span class="form-value">{{$u->sigla}}</span>
                                    <br />
                                    <strong>Estado/Município:</strong>
                                    <span class="form-value">
                                        {{$u->estado['nome'].'('.$u->estado['sigla'].')'}}
                                    </span>
                                    <br />
                                    <strong>Gestão:</strong><br />
                                    <span class="form-value">{{$u->contato}}</span><br />
                                    <span class="form-value">{{$u->contato2}}</span>
                                </p>
                                </div>

                                <div class="col-lg-5">
                                    <strong>Email:</strong>
                                    <span class="form-value">{{$u->email}}</span>
                                    <br />
                                    <strong>URL:</strong>
                                    <a class="form-value" href="{{$u->url}}" target="_blank">{{$u->url}}</a>
                                    <br />
                                    <strong>Telefone:</strong>
                                    <span class="form-value">
                                            <a href="tel:{{$u->telefone}}">{{$u->telefone}}</a>
                                    </span>
                                    <br />
                                    <strong>Endereço:</strong>

                                    <span class="form-value">
                                        <address>{{$u->endereco}}</address>
                                    </span>
                                </div>
                                <div class="col-lg-2">
                                    <div class="tooltip-custom">
                                        <span class="tooltiptext" id="tooltip-{{ $u->id}}">Link copiado!</span>
                                        <input aria-hidden="true" id="url-{{ $u->id}}"/>
                                        <button class="btn btn-secondary pull-right" type="button" onclick="share('{{ $u->id}}','{{$u->sigla}}','{{$u->nome}}')">
                                            <i class="fa fa-share-alt"></i>
                                        </button>                                        
                                    </div>
                                </div>
                            </div>
                        </div><!-- end card-body -->

                        @if (isset($federal->confirmado_em))
                        <div class="card-footer">

                            @if (isset($u->confirmado_em))
                            <div class="col-lg-8">
                                <label class="form-label"><strong>Ingressou na plataforma em:</strong></label>
                                <span class="form-value">{{date('d/m/Y', strtotime($u->confirmado_em))}}</span>
                            </div>
                            @endif                            
                        </div>
                        @endif

                    </div><!-- end card -->
                </div><!-- end col-10 -->
            </div><!-- end row-->
        @empty
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                    <div class="alert alert-secondary" role="alert">
                        Nenhum resultado encontrado.
                    </div>
                </div>
            </div>

        @endforelse

            <!-- pagination-->            
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                    <nav>
                        <ul class="pagination justify-content-center">                        
                            @if ($page > 1)
                            <li class="page-item">                            
                            <a href="?{{isset($q) ? 'q=' . urlencode($q) . '&' : ''}}page={{ 1 }}"
                                    class="page-link" tabindex="-1">Primeira</a>
                            </li>
                            <li class="page-item">
                                <a href="?{{isset($q) ? 'q=' . urlencode($q) . '&' : ''}}page={{ ($page - 1) }}"
                                    class="page-link" tabindex="-1">Anterior</a>
                            </li>
                            @endif

                            @if($total_pages > 0 && $page <= $total_pages)

                                @php($auxb = $page+5>$total_pages ? 9-($total_pages-$page)  : 5 )                                
                                @php($begin = $page-$auxb>0 ? $page-$auxb : 1)                                                            
                                @php($auxe = $page-6>0 ? 5 : 10-$page)
                                @php($end = $page+$auxe>$total_pages ? $total_pages : $page+$auxe) 
                                @for ($i = $begin; $i <= $end; $i++)
                                     <li class="page-item"><a class="page-link {{$i == $page ? 'active' :'' }}" href="?{{isset($q) ? 'q=' . urlencode($q) . '&' : ''}}page={{ ($i) }}">{{$i}}</a></li>
                                @endfor

                            @endif

                            @if ($page < $total_pages)
                                <li class="page-item">
                                <a href="?{{isset($q) ? 'q=' . urlencode($q) . '&' : ''}}page={{ ($page + 1) }}"
                                    class="page-link">Próxima</a>
                                </li>
                                <li class="page-item">
                                <a href="?{{isset($q) ? 'q=' . urlencode($q) . '&' : ''}}page={{ ($total_pages) }}"
                                    class="page-link">Última</a>
                                </li>
                            @endif
                        </ul>
                    </nav>
                </div>
            </div>
            <!-- end pagination-->        

    </div>
</section>

<hr class="split">
@endsection
