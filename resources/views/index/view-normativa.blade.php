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
    body{
        background-color: #f0f0f0;;
        border:0;
    }
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

    .card{
        background-color:#f0f0f0;
    }

    .card-header{
        background-color: #f0f0f0;
    }

    .bootstrap-tagsinput-mt-2{
        font-size:13px;
        margin-top:3px;
        gap:10px;

        display:flex;
        flex-direction:row;
        justify-content:flex-start;
        align-items:center;

    }
    .tag-label-label-info{
        border:solid 1px #f3f3f3ff;
        background-color: #f3f3f3ff;
        color:#000;
        border-radius:30px;
        width:6%;
        padding:4px;
        
        display:flex;
        flex-direction:row;
        justify-content:center;
        font-size:13px;
        
    }

    .main_container-fluid{
        background:radial-gradient(ellipse at center, #17872aff 1%, #025310 100%);
        height:100px;
        margin-top:0;
    }

    .titulo_pdf{
        font-size:25px;
        color:#025310;
    }

    .main-collapse{
        display:flex;
        flex-direction:row;
        justify-content:flex-end;
        align-items:center;

        padding-left:40px;
    }

    .botao-collapse{
        border:solid 1px #e2e8e8ff;
        background-color: #efeeeeff;
        border-radius:10px;
        font-size:14px;
        font-weight:normal;
        max-width:100%;
        white-space:nowrap;
        padding:2px;
        padding-left:6px;
        padding-right:12px;
        margin-bottom:10px;

        display:flex;
        flex-direction:row;
        justify-content:center;
        align-items:center;

        gap:10px;
        
    }

    .botao-collapse:hover{
        background-color: #ebebf1ff;
    }

    .collapse{
        display:flex;
        flex-direction:row;
        justify-content:flex-end;
        align-items:center;
    }

.collapsing {
    display: block !important;
    overflow: hidden;
    transition: height 0.35s ease;
}


    .logo_if{
        width: 175px;
        height:53px;
    }

    .main_collapse{
        border:solid 1px #e8e5e5ff;
        border-radius:13px;
        background-color:#f0f0f0;
        width:100%;
        padding:9px;
        display:flex;
        justify-content:flex-start;
        align-items:center;

        gap:10px;
        margin-top:10px;
        margin-bottom:10px;
    }

    .collapse-info_main{
        display:flex;
        flex-direction:column;
        justify-content:flex-start;
        align-items:flex-start;

        gap:10px;
    }

    .main_infos{
        display:flex;
        flex-direction:row;
        justify-content:flex-start;
        align-items:center;

        margin-bottom:6px;
    }

    .px-3{
        color:#222;
        font-size:15px;
        display:flex;
        justify-content:flex-start;
        align-items:center;
        gap:1px;
    }

    .main_tags{
        display:flex;
        flex-direction:row;

        /* gap:5px;  */
        margin-bottom:5px;
        margin-left:15px;
    }

    .main_pdf{
        padding-left:20px;
    }

    .tag-link:hover{
        text-decoration:underline;
    }

    .main_article{
        padding-left:10px;
        width:98%;
    }

    .tags{
        font-size:15px;
        display:flex;
        flex-direction:row;
        align-items:center;

        margin-left:5px;
        gap:0;

    }
    .tags:hover{
        text-decoration:underline;
    }

    .main_infos{
        display:flex;
        flex-direction:row;
        align-items:center;

        font-size:15px;
        margin-left:20px;
        gap:15px;
    }

    .main-link_tags:hover{
        text-decoration:underline;
    }

</style>

<!-- header -->

<!-- end header -->

<main class="main_container-fluid ">
    <div class="main_row">
        <div class="main_article">

        <section id="header d-flex justify-content-start">
            <div class="main_container-fluid d-flex align-items-center">
                    <div class="col-12 text-start">                   
                        <a href="{{route('index')}}">

                            <svg width="175" height="53" viewBox="0 0 550 160" xmlns="http://www.w3.org/2000/svg">
                              <style>
                                .text {
                                  font-family: Arial, sans-serif;
                                  font-size: 50px;
                                  font-weight: bold;
                                  dominant-baseline: middle;
                                  text-anchor: start;
                                  fill: #FFFFFF;
                                }
                              </style>

                              <g transform="translate(20, 80)">
                                <text class="text" stroke="#FFFFFF" stroke-width="2px">Documentos</text>
                              </g>

                              <g class="logo_if" transform="translate(335, 10)">
                                <circle cx="20" cy="20" r="18" fill="#FFFFFF"/>
                                <rect x="40" y="0" width="35" height="35" fill="#FFFFFF"/>
                                <rect x="80" y="0" width="35" height="35" fill="#FFFFFF"/>
                                
                                <rect x="0" y="40" width="35" height="35" fill="#FFFFFF"/>
                                <rect x="40" y="40" width="35" height="35" fill="#FFFFFF"/>
                                
                                
                                <rect x="0" y="80" width="35" height="35" fill="#FFFFFF"/>
                                <rect x="40" y="80" width="35" height="35" fill="#FFFFFF"/>
                                <rect x="80" y="80" width="35" height="35" fill="#FFFFFF"/>
                              

                                <rect x="0" y="120" width="35" height="35" fill="#FFFFFF"/>
                                <rect x="40" y="120" width="35" height="35" fill="#FFFFFF"/>
                              </g>
                            </svg>
                        </a>
                    </div>
            </div>
        </section>
            <div class="card" style="border:0;">
                <div class="card-header" style="border:0; margin-top:15px;">
                    <div class="row">
                        <div class="col-lg-10">
                            <h3 class="titulo_pdf">
                                    {{ $normativa['ato']['titulo'] }}
                            </h3>
                                <h6 style="color:#555;">Instituto Federal de Alagoas</h6>
                        </div>
                        <div class="col-lg-2">
                            <div class="tooltip-custom">
                                <span class="tooltiptext" id="tooltip-{{ $id }}">Link copiado!</span>
                                <input aria-hidden="true" id="url-{{ $id }}"/>
                                <button class="btn-new btn btn-secondary pull-right" style="background-color:#828282ff; border:0;"type="button" onclick="share('{{ $id }}','{{ $normativa['ato']['titulo'] }}','{{ $normativa['ato']['ementa']}}')">
                                    <i class="fa fa-share-alt"></i>
                                </button>                                        
                        </div>
                    </div>
            </div>

        <div class="main-collapse">
            <a class="botao-collapse" data-toggle="collapse" href="#dadosDoPdfCollapse" role="button" aria-expanded="false" aria-controls="dadosDoPdfCollapse">
                <i class="fa fa-info-circle"></i> Mostrar / Ocultar Detalhes do Documento
           </a>
        </div>

        <div class="collapse" id="dadosDoPdfCollapse">
            <div class="main_collapse">
                 <div class="collapse-info_main">

                        <div class="main_infos mt-1">

                                <div class="main-link_tags">
                               <a href="{{ route('index') }}" class="info_tags">{{ $normativa['ato']['tipo_doc'] }}</a>
                                </div>

                                <div class="main-link_tags">
                                @if(date('Y', strtotime($normativa['ato']['data_publicacao'])) >= 1900)
                                    <div class="px-3"> Publicado em: {{ date('d/m/Y', strtotime($normativa['ato']['data_publicacao'])) }}</div>
                                @endif
                                </div>

                                <div class="main-link_tags">
                                <a href="{{ route('index') }}">{{ $normativa['ato']['fonte']['orgao'] }}</a>
                                </div>

                                <div class="main-link_tags">
                                <a href="{{ route('index') }}">{{ $normativa['ato']['fonte']['esfera'] }}</a>
                                </div>
                        </div>

                        <div class="palavras-chave w-100 border-top border-dark pt-3">

                                @if($normativa['ato']['tags'])
                                    <div class="main_tags">
                                        <strong>Palavras-chaves: </strong>
                                        @foreach($normativa['ato']['tags'] as $tag)
                                        <div class="tags">
                                            <a href="/?query={{ urlencode($tag) }}&publico=1" class="link_tags">{{ $tag }}</a>@if(!$loop->last), @endif
                                        </div>
                                        @endforeach
                                    </div>
                                @endif
                        </div>
                    </div>
                </div>
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

               

            <div class="main_pdf">
    @if ($normativa['ato']['tipo_doc'] != 'doc' && $normativa['ato']['tipo_doc'] != 'docx' && substr($id, -3) != "doc" && substr($id, -4) != "docx")
        <iframe src="/normativa/pdf/{{ $id }}#zoom=133" width="99%" height="960px">
        </iframe> 
    @else
        <iframe src="https://docs.google.com/gview?url={{str_replace("view","pdf",Request::url())}}&embedded=true" width="100%" height="600px">
        </iframe>
    @endif 
    
    <!-- <nav class="mt-3">       
        <a href="javascript:history.back();" class="btn btn-secondary"><i class="fa fa-reply"></i> Voltar</a>
        <a href="/normativa/pdf/{{ $id }}" class="btn btn-success" target="_blank"><i class="fa fa-download"></i> Baixar</a>              
        <a href="/" class="btn btn-primary"><i class="fa fa-search"></i> Nova Busca</a>
    </nav>  -->
   
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

@push('js')
    
@endpush
