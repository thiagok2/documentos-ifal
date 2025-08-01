@extends('adminlte::page')

@section('title', 'Documentos IFAL')

@section('content_header')

@stop

@section('content')

<div class="container-fluid">
    <ol class="breadcrumb">
        <li class="breadcrumb-item active"><a href="{{route('home')}}">Painel</a></li>
        <li class="breadcrumb-item active"> <a href="#" class="active"><a href="#">Documentos</a></li>
    </ol>
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-secondary">
                <div class="card-header">
                    <i class="fas fa-lock me-1 mr-1"></i> Filtrar
                </div>
                <div class="card-body">
                    <form class="form" method="GET" action="{{route('documentos-pesquisar')}}">


                            <div class="row">
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label for="formato">Formato:</label>
                                        <select class="form-control select2" id="formato" name="formato">
                                            <option value="">Todos</option>
                                            @foreach ($listFormatos as $f)
                                                <option value="{{$f->formato}}" @if ($queryParams['formato'] == $f->formato) selected @endif>{{$f->formato}}</option>
                                            @endforeach
                                        </select>
                                        <br/>
                                        <label for="usuarioNome">Usuário:</label>
                                        <input type="text" id="usuarioNome" name="usuarioNome" class="form-control"  value="{{$queryParams['usuarioNome']}}"
                                            placeholder="Ex.: Maria, João..." aria-describedby="basic-addon1"/>
                                        <small class="form-text text-muted">Nome/sigla da unidade e/ou nome do usuário</small>
                                    </div>
                                </div>
                           
                                <div class="col-lg-2">

                                    <div class="form-group">
                                        <label for="dataInicioEnvio">Data de Envio (Início):</label>
                                        <input class="form-control" type="date" id="dataInicioEnvio" name="dataInicioEnvio" value="{{$queryParams['dataInicioEnvio']}}">
                                        <br/>
                                        <label for="dataFimEnvio">Data de Envio (Fim):</label>
                                        <input class="form-control" type="date" id="dataFimEnvio" name="dataFimEnvio" value="{{$queryParams['dataFimEnvio']}}">
                                        <small class="form-text text-muted">Data início e fim do envio no sistema</small>
                                    </div>
                                </div>

                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label for="dataInicioPublicacao">Publicação (Início):</label>
                                        <input class="form-control" type="date" id="dataInicioPublicacao" name="dataInicioPublicacao" value="{{$queryParams['dataInicioPublicacao']}}">
                                        <br/>
                                        <label for="dataFimPublicacao">Publicação (Fim):</label>
                                        <input class="form-control" type="date" id="dataFimPublicacao" name="dataFimPublicacao" value="{{$queryParams['dataFimPublicacao']}}">
                                        <small class="form-text text-muted">Data início e fim do publicação</small>
                                    </div>
                                </div>

                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label for="numero">Número:</label>
                                        <input class="form-control" id="numero" name="numero" value="{{$queryParams['numero']}}" placeholder="CEE-AL 2/2019">               
                                        <br/>
                                        <label for="arquivo">Nome:</label>
                                        <input class="form-control" id="arquivo" name="arquivo" value="{{$queryParams['arquivo']}}" placeholder="Nome do arquivo">
                                    </div>
                                </div>

                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label for="tipo_entrada">Entrada:</label>
                                        <select class="form-control select2" id="tipo_entrada" name="tipo_entrada">
                                            <option value="">Todos</option>
                                            <option value="manual" @if ($queryParams['tipo_entrada'] == 'manual') selected @endif>Manual</option>
                                            <option value="extrator" @if ($queryParams['tipo_entrada'] == 'extrator') selected @endif>Extrator</option>
                                        </select>
                                        <br/>
                                        <label for="status">Completo:</label>
                                        <select class="form-control select2" id="status" name="status">
                                            <option value="">Todos</option>
                                            <option value="indexado" @if ($queryParams['status'] == 'indexado') selected @endif>Indexado</option>
                                            <option value="pendente" @if ($queryParams['status'] == 'pendente') selected @endif>Pendente</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">

                                    </div>
                                </div>


                        </div>

                        <button type="submit" class="btn btn-secondary">Pesquisar</button>
                        {!! csrf_field() !!}
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('admin.includes.alerts')

    <div class="row">
        <div class="col-sm-12">
            <div class="box box-secondary">
                <div class="box-header">
                    <h3 class="box-title">Documentos Privados de <strong> {{Auth::user()->unidade->nome}}</strong> </h3>
                    <br/>
                    <small class="form-text text-muted">Total de {{$documentos->total()}} registros</small>
                </div>
                    <!-- /.box-header -->
                <div class="box-body no-padding">
                    <table class="table table-condensed table-hover table-responsive">
                        <thead>
                            <tr>
                                <th style="width: 1%">#</th>
                                <th style="width: 2%">Ano</th>
                                <th style="width: 8%">Número</th>
                                <th style="width: 6%">Tipo</th>
                                <th style="width: 20%">Documento</th>
                                <th style="width: 10%">Tags</th>
                                <th style="width: 10%">Publicação</th>
                                <th style="width: 8%">Envio</th>
                                <th style="width: 8%">Por</th>
                                <th style="width: 10%"></th>
                            </tr>
                        </thead>
                        <tbody>

                                @forelse ($documentos as $key=>$doc)
                                <tr @if ($doc->completed) class='table table-info' @else class='table table-warning' @endif>
                                    <td>
                                        {{ ($documentos->currentpage()-1) * $documentos->perpage() + $key + 1 }}
                                    </td>
                                    <td>{{$doc->ano ?? 'Nenhum informado'}}</td>
                                    <td>{{$doc->numero ?? 'Nenhum informado'}}</td>
                                    <td>{{$doc->tipoDocumento->nome ?? 'Nenhum informado'}}</td>
                                    <td>{{$doc->titulo ?? 'Nenhum informado'}}</td>
                                    <td>
                                        {{-- 
                                        @foreach ($doc->palavrasChaves as $p)
                                            <span class="badge bg-secondary">{{$p->tag}}</span>
                                        @endforeach
                                        --}}
                                        <span class="badge bg-secondary">{{$doc->formato ?? 'Nenhum informado'}}</span>
                                        <span class="badge bg-secondary">{{$doc->tipo_entrada ?? 'Nenhum informado'}}</span>
                                        <span class="badge bg-secondary">{{$doc->status() ?? 'Nenhum informado'}}</span>
                                    </td>
                                    <td>{{ $doc->data_publicacao ? date('d-m-Y', strtotime($doc->data_publicacao)) : 'Nenhuma informada' }}</td>
                                    
                                    <td>{{date('d-m-Y', strtotime($doc->data_envio)) ?? 'Nenhum informado'}}</td>
                                    
                                    <td>{{$doc->unidade->sigla}} - {{$doc->user->firstName() ?? 'Nenhum informado'}}</td>
                                    <td>
                                        <div class="d-flex flex-row">
                                            @if ($doc->arquivo)
                                                <a href="/normativa/view/{{ $doc['arquivo'] }}" target="_blank" title="Abrir no Documentos IFAL">
                                                    <i class="fa fa-external-link"></i>
                                                </a>
                                           
                                                <a  target="_blank"  href="{{route('pdfNormativa',$doc->arquivo)}}" title="Download">
                                                    <i class="fa fa-cloud-download"></i>
                                                </a>
                                            
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
                                        </div>
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
