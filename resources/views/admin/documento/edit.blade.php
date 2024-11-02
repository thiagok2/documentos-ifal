@extends('adminlte::page')

@section('title', 'Documentos IFAL')

@section('content_header')

@stop

@push('css')

<style>
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type=number] {
        -moz-appearance: textfield;
    }
</style>

@endpush

@section('content')        
    <div class="container-fluid">
        <ol class="breadcrumb">
            <li><a href="{{route('home')}}">Painel</a></li>
            <li> <a href="{{route('documentos')}}" ><a href="#">Documentos</a></li>
            <li> <a href="#" class="active"><a href="#">Editar</a></li>
        </ol>        

        @include('admin.includes.alerts')
        
        <form name="form" id="form" action="{{route('documento-update', $documento->id)}}" method="post" enctype="multipart/form-data">
            {!! csrf_field() !!}
            <input type="hidden" name="documento_id" id="documento_id" value="{{$documento->id}}"/>
            <input type="hidden" name="tags" id="tags" value="{{$tags}}"/>            
            <input type="hidden" class="form-control" value="{{ $unidade->nome }} - {{ $unidade->sigla }}" id="unidade">
            
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="form-group no-margin">
                        <h4>Edição de informações do arquivo</h4>
                    </div>

                    @if (!$documento->completed)
                        <div class="alert alert-warning fade in">
                            <a class="close" data-dismiss="alert" href="#">&times;</a>
                            O documento ainda não foi indexado para a busca. Complete as informações.
                        </div>
                    @endif

                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#general">Geral</a></li>
                        <li><a data-toggle="tab" href="#extra">Outros detalhes</a></li>
                    </ul>                    
                </div><!-- end box-header -->
                
                <div class="box-body">                
                    <div class="tab-content">
                        <div id="general" class="tab-pane fade in active">                                
                            <div class="row">
                            <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="unidade">Unidade</label>
                                        <input type="text" value="{{ $documento->unidade->nome }}" readonly disabled class="form-control">                                        
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="ano">Ano de publicação</label>
                                        <input type="number" value="{{ $documento->ano }}" required class="form-control" id="ano" name="ano" placeholder="Ex.: 2019" maxlength="4">                                        
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="numero">Número</label>
                                        <input type="text" value="{{ $documento->numero }}" required class="form-control" id="numero" name="numero" placeholder="Ex.: CEE/BR Nº 12.123" maxlength="20">                                        
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="data_publicacao">Data de Publicação</label>
                                        <div class='input-group date'>
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar">
                                                </span>
                                            </span>
                                            <input type='date' value="{{ $documento->data_publicacao }}" required class="form-control" id="data_publicacao" name="data_publicacao"/>                                            
                                        </div>
                                    </div>
                                </div>    
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="url">URL</label>
                                        <small class=".text-muted">(Endereço do documento online - opcional)</small>
                                        <div class='input-group'>
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-globe">
                                                </span>
                                            </span>
                                            <input type='url' value="{{$documento->url}}" class="form-control" id="url" name="url" placeholder="HTTP://..." maxlength="200"/>                                            
                                        </div>
                                    </div>
                                </div>               
                            </div><!-- end row -->                

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="titulo">Título</label>
                                        <input type="text" value="{{ $documento->titulo }}" required class="form-control" id="titulo" name="titulo" placeholder="Ex.: Deliberação CEEBR Nº 12321...">                                        
                                    </div>
                                </div>  
                            </div><!-- end row -->
                                                                                                                    
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ementa">Ementa</label>
                                        <small class=".text-muted">(Máximo de 255 caracteres)</small>                                        
                                        <textarea id="ementa" required class="form-control" rows="5" name="ementa">{{$documento->ementa}}</textarea>                                
                                    </div>
                                </div>
                                <div class="col-md-6">                                                        
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="assunto">Assunto</label>
                                                <select class="form-control" required id="assunto_id" name="assunto_id">
                                                    <option value="">Selecione</option>
                                                    @foreach ($assuntos as $assunto)
                                                        <option value="{{$assunto->id}}" {{ $documento->assunto_id == $assunto->id ? "selected":""}}>{{$assunto->nome}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="tipo_documento">Tipo de Documento</label>
                                                <select class="form-control" required id="tipo_documento_id" name="tipo_documento_id">
                                                    <option value="">Selecione</option>
                                                    @foreach ($tiposDocumento as $tipo)
                                                        <option value="{{$tipo->id}}" {{ $documento->tipo_documento_id == $tipo->id ? "selected":""}}>{{$tipo->nome}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="palavras_chave">Palavras chave</label>
                                                <small class=".text-muted">(Insira os termos mais relevantes abordados neste documento, pressione ENTER para confirmar a inserção)</small>
                                                <input type="text" value="{{$documento->palavras_chave}}" class="tags-required" data-role="tagsinput" id="palavras_chave" name="palavras_chave"/>                                                                                                                                                                                                                                                                                     </div>
                                        </div>
                                    </div>
                                </div>
                            </div><!--end row -->

                            <div class="row">
                                @if ($documento->arquivo)
                                    <div class="col-md-3">
                                        <a target="_blank" class="btn btn-primary btn-block mb-0" href="{{route('pdfNormativa',$documento->arquivo)}}">
                                            <i class="fa fa-download"></i> Baixar documento
                                        </a>
                                        <span style="word-break: break-all">{{$documento->nome_original ? $documento->nome_original : $documento->arquivo}}</span>
                                    </div>      
                                    <div class="col-md-9">
                                @else
                                    <div class="col-md-12">
                                @endif                    
                                        <div class="form-group">
                                            <label for="arquivo">Alterar arquivo(PDF)</label>
                                            <small class=".text-muted">(Tamanho máximo: 5 MB)</small>
                                            <input id="arquivo_novo" name="arquivo_novo" class="form-control" type="file" accept="application/pdf">
                                            <small class=".text-muted">Arquivos digitalizados não são indexados para busca. Dê preferência ao PDF original.</small>
                                        </div>
                                    </div>                             
                            </div><!--end row -->                                
                        </div><!--end tab-pane general -->
        
                        <div id="extra" class="tab-pane fade">
                            <div class="extra_fields">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="criado">Criação</label>
                                            <input readonly value="{{$documento->created_at ? $documento->created_at->format('d-m-Y'):''}}" class="form-control">
                                        </div>
                                    </div>                                    
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="update">Última modificação</label>
                                            <input readonly value="{{$documento->updated_at ? $documento->updated_at->format('d-m-Y'):''}}" class="form-control">
                                        </div>
                                    </div>                                    
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="nome_original">Arquivo Original</label>
                                            <input readonly value="{{$documento->nome_original}}" class="form-control">
                                        </div>
                                    </div>
                                </div><!-- end row-->
                        
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="tipo_entrada">Tipo Entrada</label>
                                            <input readonly value="{{$documento->tipo_entrada}}" class="form-control">
                                        </div>
                                    </div>
                    
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="formato">Formato</label>
                                            <input readonly value="{{$documento->formato}}" class="form-control">
                                        </div>
                                    </div>
                    
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="url_extrator">URL Extrator</label>
                                            <input readonly value="{{$documento->url_extrator}}" class="form-control">
                                        </div>
                                    </div>
                                </div><!-- end row-->
                        
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="numero_processo">Num. Processo</label>
                                            <input readonly value="{{$documento->numero_processo}}" class="form-control">
                                        </div>
                                    </div>
                    
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="status_extrator">Status Extrator</label>
                                            <input readonly value="{{$documento->status_extrator}}" class="form-control">
                                        </div>
                                    </div>
                    
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="usuario">Usuário</label>
                                            <input readonly value="{{$documento->user->name}}" class="form-control">
                                        </div>
                                    </div>
                                </div><!-- end row-->
                            </div><!-- end div extra fields-->
                        </div><!--end tab-pane extra -->
                    </div><!--end tab-content -->                                                                           
                </div><!-- end box-body -->      
            </div><!-- end box-->    
            
            <div class="col-md-12">
                <div class="pull-right">
                    <button type="submit" class="btn btn-primary btn-lg" value="Enviar">Atualizar</button>
                    <a href="{{route('documentos')}}" class="btn btn-warning btn-lg">Fechar</a>                
                    <button type="button" class="btn btn-danger btn-lg" data-toggle="modal" data-target="#modalConfirm">Excluir</button>
                    @if($documento->completed)
                    <button type="button" class="btn btn-warning btn-lg" data-toggle="modal" data-target="#modalConfirmOcultar">Ocultar</button>
                    @else
                    <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#modalConfirmIndexar">Publicar</button>
                    @endif
                </div>                                              
            </div>        
        </form>         

        <div class="modal fade" id="modalConfirm" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Confirmação de exclusão</h4>
                    </div>
                    <div class="modal-body">
                        <p>Tem certeza que deseja excluir este documento?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <a href="{{route('delete-edit',['id' => $documento->id])}}" class="btn btn-danger">Excluir</a>                                                        
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

        <div class="modal fade" id="modalConfirmOcultar" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Confirmação de exclusão dos resultados</h4>
                    </div>
                    <div class="modal-body">
                        <p>Tem certeza que deseja não exibir este documento dos possíveis resultados?</p>
                        <p>
                        <em class="alert alert-warning" role="alert">
                            Posteriormente ele poderá ser 'reindenxado', e disponível para busca.
                        </em>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <a href="{{route('documento-ocultar',['id' => $documento->id])}}" class="btn btn-info">Ocultar</a>                                                        
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal --> 

        <div class="modal fade" id="modalConfirmIndexar" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Confirmação de publicação</h4>
                    </div>
                    <div class="modal-body">
                        <p>Tem certeza que deseja inserir este documento nos possíveis resultados?</p>
                        <em class="alert alert-warning" role="alert">
                            O documento estará novamente acessível a partir das novas buscas.
                        </em>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <a href="{{route('documento-indexar',['id' => $documento->id])}}" class="btn btn-info">Indexar</a>                                                        
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal --> 

    </div><!-- end div container-->
@endsection

@push('scripts')
    <script src="{{ asset('js/app-edit.js') }}"></script>
@endpush