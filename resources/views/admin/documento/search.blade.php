@extends('adminlte::page')

@section('title', 'Documentos IFAL')

@section('content_header')

@stop

@push('css')

<style>
    /*EXTRA*/
    ul.legenda > li  {        
        padding: 5px 5px;       
    }
</style>
<link rel="stylesheet" href="{{ asset('vendor/tagsinput/bootstrap-tagsinput.css') }}">


@endpush

@section('content')
<div class="container-fluid">
    <ol class="breadcrumb">
        <li class="breadcrumb-item active"><a href="{{route('home')}}">Painel</a></li>
        <li class="breadcrumb-item active"> <a href="#" class="active"><a href="#">Pesquisar Pendências</a></li>
    </ol>

    <div class="row">
        <div class="col-lg-8">
            <div class="box box-warning">
                <div class="box-header">
                    <h3 class="box-title">Filtrar</h3>
                </div>
                <div class="box-body">
                    <form class="form" method="GET" action="{{route('documentos-pesquisar-status')}}">                        
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="unidadeNome">Unidade:</label>
                                        <input type="text" id="unidadeNome" name="unidadeNome" class="form-control" value="{{$queryParams['unidadeQuery']}}"
                                            placeholder="Ex.: Alagoas, Maceió..." aria-describedby="basic-addon1"/>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label for="tipo_entrada">Formato:</label>
                                        <select class="form-control select2" id="formato" name="formato">
                                            <option value="">Todos</option>
                                            @foreach ($listFormatos as $f)
                                                <option value="{{$f->formato}}" @if ($queryParams['formato'] == $f->formato) selected @endif>{{$f->formato}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label for="tipo_entrada">Médoto de Inserção:</label>
                                        <select class="form-control select2" id="tipo_entrada" name="tipo_entrada">
                                            <option value="">Todos</option>
                                            @foreach ($listTipoEntrada as $tipo)
                                                <option value="{{$tipo->tipo_entrada}}" @if ($queryParams['status'] == $tipo->tipo_entrada) selected @endif>{{$tipo->tipo_entrada}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label for="status">Status:</label>
                                        <select class="form-control select2" id="status" name="status">
                                            <option value="PENDENTE" @if ($queryParams['status'] == "PENDENTE") selected @endif>PENDENTES</option>
                                            @foreach ($listStatus as $status)
                                                <option value="{{$status->status_extrator}}" @if ($queryParams['status'] == $status->status_extrator) selected @endif>{{$status->status_extrator}}</option>
                                            @endforeach 
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg">Pesquisar</button>                                               
                        {!! csrf_field() !!}
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">            
            <div class="box box-warning">
                <div class="box-header">
                    <h3 class="box-title">Ajuda</h3>
                </div>
                <div class="box-body">
                    <div class="col-md-12">
                        <ul class="list-group legenda">
                            <li class="list-group-item active">
                                Status <small>(para documentos do extrator)</small>
                            </li>
                            <li class="list-group-item">
                                Cadastrado:
                                <span class="small text-muted"> Metadados extraídos mas documento ausente</span>
                            </li>
                            <li class="list-group-item">
                                Baixado: 
                                <span class="small text-muted">Documento capturado porém não publicado</span>
                            </li>
                            <li class="list-group-item">
                                Falha Download: <span class="small text-muted">Problemas ao baixar o arquivo. Documento não enviado</span>
                            </li>
                            <li class="list-group-item">
                                Falha Elastic (Indexação): <span class="small text-muted"> Não foi possível disponibilizar o arquivo para consulta</span>
                            </li>
                            <li class="list-group-item">
                                Indexado:<span class="small text-muted"> Documento publicado e disponível para consulta</span>
                            </li>
                        </ul>   
                    </div><!-- end col-->
                    <div class="col-md-12">
                        <ul class="list-group legenda">
                            <li class="list-group-item active">
                                Método de Inserção
                            </li>
                            <li class="list-group-item">
                                Individual:
                                <span class="small text-muted"> Inserção manual (indivudual)</span>
                            </li>
                            <li class="list-group-item">
                                Lote: 
                                <span class="small text-muted">Inserção manual (multiplos arquivos)</span>
                            </li>
                            <li class="list-group-item">
                                Extrator: 
                                <span class="small text-muted">Extração do site do unidade via Robô (documentos antigos)</span>
                            </li>
                        </ul>   
                    </div><!-- end col-->

                </div><!-- end box-body-->
            </div><!-- end box -->                
        </div>
    </div>

    @include('admin.includes.alerts')
    <div class="row">
        <div class="col-lg-12">
            <div class="alert bg-yellow alert-dismissible fade in">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <p>
                    <h4>Complete aqueles documentos que não se encontram indexados, ou seja, possuem status: 
                    <strong>CADASTRADO, BAIXADO OU EM FALHA.</strong> <br/>
                    Esses documentos se encontraram em destaque(vermelho).
                    </h4>
                </p>
            </div>
        </div>    
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="box box-warning">
                <div class="box-header">
                    <h3 class="box-title">Documentos pesquisados</h3>
                    <br/>
                    <small class="form-text text-muted">Total de {{$documentos->total()}} registros</small>
                </div>
                    <!-- /.box-header -->
                <div class="box-body no-padding">
                    <table class="table table-condensed table-hover">
                        <thead>
                            <tr>
                                <th style="width: 1%">#</th>
                                <th>Unidade</th>
                                <th>Ano de publicação</th>
                                <th>Documento</th>
                                <th>Tags</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>

                                @forelse ($documentos as $key=>$doc)
                                <tr @if ($doc->isIndexado()) class='table table-info' @else class='table table-danger' @endif>
                                    <td>
                                        {{ ($documentos->currentpage()-1) * $documentos->perpage() + $key + 1 }}
                                    </td>
                                    <td>{{$doc->unidade->sigla}}</td>
                                    <td>{{$doc->ano}}</td>
                                    <td>{{$doc->titulo}}({{$doc->numero}})</td>
                                    <td>
                                        <span class="badge bg-secondary">{{$doc->formato}}</span>
                                        <span class="badge bg-secondary">{{$doc->tipo_entrada}}</span>
                                        <span class="badge bg-secondary">{{$doc->status()}}</span>
                                    </td>
                                    <td>
                                        @if ($doc->isIndexado())
                                            <a href="/normativa/view/{{ $doc['arquivo'] }}" target="_blank" title="Abrir no Documentos IFAL">
                                                <i class="fa fa-external-link"></i>
                                            </a>
                                       
                                            <a  target="_blank"  href="{{route('pdfNormativa',$doc->arquivo)}}" title="Download">
                                                <i class="fa fa-cloud-download"></i>
                                            </a>
                                        @elseif ($doc->isBaixado())
                                            <a href='{{ Storage::url("uploads/$doc->arquivo")}}' target="_blank" title="Download(local)">
                                                <i class="fa fa-download"></i>
                                            </a>
                                        @endif

                                        <a href="{{ route("documento",$doc->id) }}" title="Visualizar">
                                            <i class="fa fa-eye" ></i>
                                        </a>
                                        <a href="{{ route("documento-edit",$doc->id) }}" title="Editar">
                                            <i class="fa fa-edit" ></i>
                                        </a>
                                        
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="9">
                                            <span class="no-results">Sem documentos enviados</span>
                                        </td>
                                    </tr>
                                @endforelse

                        </tbody>
                    </table>
                </div><!-- /.box-body -->
                <div class="box-footer">
                    {{ $documentos->appends($queryParams)->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@stop
