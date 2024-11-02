@extends('layouts.master')

@section('title', $normativa['ato']['titulo'] )

@section('keywords', $keywords )

@section('content')

<script>
    function share(id, titulo, ementa) {      
        if(id.indexOf(".pdf")!=-1){
            id = [id.slice(0, -4), "\\", id.slice(-4)].join('');
        }              
        url = window.location.href;

        if (navigator.share) {            
            navigator.share({
                text: 'Acesse: ' + titulo + ' no Normativas',                
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

<!-- header -->
<section id="header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 text-center">
                <hr class="split">
                <h1>
                    <a href="{{route('index')}}">
                        <img src="/img/normativos-logo.png" srcset="/img/normativos-logo@2x.png 2x" alt="Documentos IFAL" />
                    </a>
                </h1>
                <hr class="split">
            </div>
        </div>
    </div>
</section>
<!-- end header -->

<main class="container-fluid">
    <div class="row">
        <article class="col-lg-10 offset-lg-1">
            <div class="card">

                <div class="card-header">
                    <div class="row">
                    <div class="col-lg-10">
                        <h3>
                            {{ $normativa['ato']['titulo'] }}
                        </h3>
                    </div>
                    <div class="col-lg-2">
                        <div class="tooltip-custom">
                            <span class="tooltiptext" id="tooltip-{{ $id }}">Link copiado!</span>
                            <input aria-hidden="true" id="url-{{ $id }}"/>
                            <button class="btn btn-secondary pull-right" type="button" onclick="share('{{ $id }}','{{ $normativa['ato']['titulo'] }}','{{ $normativa['ato']['ementa']}}')">
                                <i class="fa fa-share-alt"></i>
                            </button>                                        
                        </div>
                    </div>
                    </div>
                </div>

                <div class="card-body">
                    <form>                            
                        <table class="table table-sm">
                            <tr>
                                <th>
                                    <strong>Ementa</strong>                                                
                                </th>
                                @if($normativa['ato']['ementa'])
                                <td>
                                    {{ $normativa['ato']['ementa'] }}
                                </td>                          
                                @else
                                <td>
                                    <i class="text-muted">(Ementa indisponível)</i>
                                </td>                                                          
                                @endif
                            </tr>
                            <tr>
                                <th>
                                    <strong>Tipo: </strong>
                                </th>
                                <td>
                                    {{ $normativa['ato']['tipo_doc'] }}
                                </td>                                        
                            </tr>
                            <tr>
                                <th>
                                    <strong>Publicação: </strong>
                                </th>
                                <td>
                                    {{ date('d/m/Y', strtotime($normativa['ato']['data_publicacao'])) }}
                                </td>                                        
                            </tr>
                            <tr>
                                <th>
                                    <strong>Conselho: </strong>
                                </th>
                                <td>
                                    {{ $normativa['ato']['fonte']['orgao'] }}
                                </td>                                        
                            </tr>     
                            <tr>
                                <th>
                                    <strong>Esfera: </strong>
                                </th>
                                <td>
                                    {{ $normativa['ato']['fonte']['esfera'] }}
                                </td>                                        
                            </tr>    
                            @if($normativa['ato']['tags'])
                                <tr>
                                    <th>
                                        <strong>Palavras-Chave: </strong>
                                    </th>
                                    <td>
                                        @foreach ($normativa['ato']['tags'] as $tag)
                                            <span class="badge badge-primary tags">{{ $tag }}</span>
                                        @endforeach
                                    </td>                                        
                                </tr>        
                            @endif                  
                        </table>                                      
                </div>

            <div class="col-sm-12">
                    @if ($normativa['ato']['tipo_doc'] != 'doc' && $normativa['ato']['tipo_doc'] != 'docx' && substr($id, -3) != "doc" && substr($id, -4) != "docx")
                        <iframe src="/normativa/pdf/{{ $id }}" width="100%" height="600px">
                        </iframe> 
                    @else
                        <iframe src="https://docs.google.com/gview?url={{str_replace("view","pdf",Request::url())}}&embedded=true" width="100%" height="600px">
                        </iframe>
                    @endif  
                    
                    <nav>                         
                        <a href="javascript:history.back();" class="btn btn-secondary"><i class="fa fa-reply"></i> Voltar</a>
                        <a href="/normativa/pdf/{{ $id }}" class="btn btn-success" target="_blank"><i class="fa fa-download"></i> Baixar</a>                                              
                        <a href="/" class="btn btn-primary"><i class="fa fa-search"></i> Nova Busca</a>
                    </nav>                   
                   
                    @auth
                        @if ((auth()->user()->isAdmin() || auth()->user()->unidade->sigla === $doc['fonte']['sigla']) &&
                            isset($normativa['ato']['id_persisted']))
                            <a href="{{ route("documento-edit", $normativa['ato']['id_persisted']) }}" title="Editar" class="btn btn-primary">
                                <i class="fa fa-edit" ></i>
                            </a>
                        @endif
                    @endauth
                                                                   
                    @auth
                        @if (auth()->user()->isAdmin() && !$persisted)                                 
                            <a href="{{route('delete-elastic',$arquivoId)}}" class="btn btn-danger btn-lg pull-right" >Excluir</a>                                                            
                        @endif
                    @endauth                                                                          
                        
                </form>
            </div>
            <div class="col-sm-12">
                @auth
                    @if (auth()->user()->isAdmin() && !$persisted)                                 
                        <div class="alert alert-danger">
                            <strong>Atenção:</strong> este documento não está sendo gerenciado pela área de administração.
                        </div>                                                            
                    @endif
                @endauth
            </div>
            </div>
            <hr class="split-sm">
        </article>        
    </div>        
</main>

<hr class="split-sm">

<hr class="split">
@endsection
