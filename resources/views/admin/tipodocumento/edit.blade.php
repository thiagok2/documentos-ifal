@extends('adminlte::page')

@section('title', 'Documentos IFAL')

@section('content_header')

    
@stop

@section('content')
    <div class="container-fluid">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="{{route('home')}}">Painel</a></li>
            <li class="breadcrumb-item active"><a href="{{route('tiposdocumento')}}">Tipos de Documento</a></li>
            <li class="breadcrumb-item active"><a href="#">Editar</a></li>
        </ol>

        <div class="page-header">
            @if(auth()->user()->isAdmin())
            <a href="{{route('tiposdocumento-create')}}" class="btn btn-primary btn-lg">Novo Tipo de Documento</a>
            @endif
        </div>

        <div class="row">
            @include('admin.includes.alerts')
            <div class="col-lg-8">
                <div class="card card-default">
                    <div class="card-header"><b>Editar Tipo de documento:</b> Informe nome e descrição</div>
                    <div class="card-body">
                    @if (isset($tipodocumento->deleted_at))
                        <div class="alert alert-warning">
                            Tipo de documento desabilitado em {{date('d/m/Y H:i:s', strtotime($tipodocumento->deleted_at))}}
                        </div>
                    @endif
                    <form  name="form" id="form" method="post" action="{{route('tiposdocumento-store')}}"> 
                        {!! csrf_field() !!}

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="id">Id*</label>
                                <input type="text" class="form-control" value="{{$tipodocumento->id}}" name="id" id="id"
                                    readonly maxlength="100" minlength="10">
                            </div>
                        </div>

                        <div class="col-sm-9">
                            <div class="form-group">
                                <label for="nome">Nome*</label>
                                <input type="text" class="form-control" value="{{$tipodocumento->nome}}" name="nome" id="nome"
                                    required maxlength="100" minlength="10" @if (isset($tipodocumento->deleted_at)) readonly @endif >
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="sigla">Sigla</label>
                                <input type="text" class="form-control" value="{{$tipodocumento->sigla}}" name="sigla" id="sigla"
                                    maxlength="10" minlength="3" @if (isset($tipodocumento->deleted_at)) readonly @endif >
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="descricao">Descrição*</label>
                                <textarea class="form-control" rows="10"  name="descricao" id="descricao" @if (isset($tipodocumento->deleted_at)) readonly @endif>{{ $tipodocumento->descricao }}</textarea>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg float-right ml-2" value="Salvar" @if(!auth()->user()->isAdmin()) disabled @endif>Salvar</button>
                        
                        @if(auth()->user()->isAdmin())
                        
                            @if (isset($tipodocumento->deleted_at))
                                <a href="{{route('tiposdocumento-restore', $tipodocumento->id)}}" class="btn btn-warning btn-lg float-right" value="Habilitar">Habilitar</a>
                            @else
                                <a href="#modalConfirm" class="btn btn-danger btn-lg float-right" data-toggle="modal">Excluir</button></a>                                                                                                    

                                <div class="modal fade" id="modalConfirm" tabindex="-1" role="dialog">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title">Confirmação de exclusão</h4>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Tem certeza que deseja excluir este tipo de documento?</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                                <a href="{{route('tiposdocumento-delete', $tipodocumento->id)}}" class="btn btn-danger">Excluir</a>                                                        
                                            </div>
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div><!-- /.modal --> 
                            @endif
                        @endif <!-- endif check admin -->
                        
                    </form>
                    </div><!-- end panel-body-->
                </div><!-- end panel-->
            </div><!-- end col-8 main-->
            <div class="col-lg-4">
                <div class="card card-default">
                    <div class="card-header">Tipos de documento cadastrados</div>
                    <div class="card-body">
                        <div class="list-group">
                            @foreach ($tipodocumentos as $t)
                                
                                <a href="{{route('tiposdocumento-edit',$t->id)}}" class="list-group-item @if ($t->id == $tipodocumento->id) active @endif">
                                    <span class="list-group-item-text">
                                    {{$t->nome}}
                                    ({{$t->documentos_count}})
                                    </span>

                                    <span class="float-right">
                                        <i class="fa fa-edit"></i>
                                    </span>
                                </a>
                                
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- end row main-->

        <div class="row">
            <div class="col-lg-12">
                <div class="box box-info">
                <div class="box-header">Documentos associados a {{$tipodocumento->nome}} ({{$documentos->total()}})</div>
                    <div class="box-body">
                        <table class="table table-condensed table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 1%">#</th>
                                    <th style="width: 2%">Ano</th>
                                    <th style="width: 3%">Número</th>
                                    <th style="width: 8%">Tipo</th>
                                    <th style="width: 30%">Título</th>
                                    <th style="width: 6%">Publicação</th>
                                    <th style="width: 6%">Envio</th>
                                    <th style="width: 4%">Por</th>
                                    <th style="width: 2%"></th>
                                </tr>
                            </thead> 
                            <tbody>
                                @forelse ($documentos as $key=>$doc)
                                <tr>
                                    <td>
                                        {{ ($documentos->currentpage()-1) * $documentos->perpage() + $key + 1 }}
                                    </td>
                                    <td>{{$doc->ano}}</td>
                                    <td>{{$doc->numero}}</td>
                                    <td>{{$doc->tipoDocumento->nome}}</td>
                                    <td>{{$doc->titulo}}</td>
                                    <td>{{date('d-m-Y', strtotime($doc->data_publicacao))}}</td>
                                    <td>{{date('d-m-Y', strtotime($doc->data_envio))}}</td>
                                    <td>{{$doc->unidade->sigla}} - {{$doc->user->firstName()}}</td>
                                    <td>
                                        <a href="{{ route("pdfNormativa",$doc->arquivo) }}" target="_blank">
                                            <i class="fa fa-arrow-circle-down"></i>
                                        </a>
                                        <a href="{{ route("documento",$doc->id) }}">
                                            <i class="fa fa-arrow-circle-right"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="9">
                                            <span class="no-results">Sem documentos associados</span>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="box-footer">
                        <div class="float-right">
                            {{ $documentos->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- end container-->
@stop