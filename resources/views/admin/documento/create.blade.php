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
<link rel="stylesheet" href="{{ asset('vendor/tagsinput/bootstrap-tagsinput.css') }}">

@endpush

@section('content')    
    <div class="container-fluid">

    <ol class="breadcrumb">
        <li class="breadcrumb-item active"><a href="{{route('home')}}">Painel</a></li>
        <li class="breadcrumb-item active"> <a href="#" ><a href="#">Documentos</a></li>
        <li class="breadcrumb-item active"> <a href="#" class="active"><a href="#">Publicar</a></li>
    </ol>

    @include('admin.includes.alerts')

        <form name="form" id="form" action="{{route('enviar')}}" method="post" enctype="multipart/form-data">
            {!! csrf_field() !!}
        
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="form-group no-margin">
                        <label for="unidade"><h4>Envio de arquivo <small>Orgão: {{ $unidade->nome }} - {{ $unidade->sigla }}</small></h4></label>
                        <input type="hidden" class="form-control" readonly
                        value="{{ $unidade->nome }} - {{ $unidade->sigla }}" id="unidade">
                    </div>
                </div><!-- end box-header -->
                
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="ano">Ano de publicação</label>
                                <input type="number" value="{{ old('ano') }}" required class="form-control" id="ano" name="ano" placeholder="Ex.: 2019" maxlength="4">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="numero">Número</label>
                                <input type="text" value="{{ old('numero') }}" required class="form-control" id="numero" name="numero"
                                placeholder="Ex.: CEE/BR Nº 12.123" maxlength="20">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="data_publicacao">Data de Publicação</label>
                                <div class='input-group'>
                                    <!--<span class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </span>-->
                                    <input type='date' value="{{ old('data_publicacao') }}" required class="form-control" id="data_publicacao" name="data_publicacao"/>
                                </div>
                            </div>
                        </div>    
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="url">URL</label>
                                <small class=".text-muted">(Endereço do documento online - opcional)</small>
                                <div class='input-group'>
                                    <span class="input-group-prepend">
                                        <span class="input-group-text"> <i class="fa fa-globe"></i>
                                        </span>
                                    </span>
                                    <input type='url' value="{{old('url')}}" class="form-control" id="url" name="url" placeholder="HTTP://..." maxlength="200"/>
                                </div>
                            </div>
                        </div>               
                    </div><!-- end row -->

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="titulo">Título</label>
                                <input type="text" value="{{old('titulo')}}" required class="form-control" id="titulo" name="titulo" placeholder="Ex.: Deliberação CEEBR Nº 12321...">
                            </div>
                        </div>  
                    </div><!-- end row -->

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ementa">Ementa</label>
                                <small class=".text-muted">(Máximo de 255 caracteres)</small>
                                <textarea id="ementa" required class="form-control" rows="5" name="ementa">{{old('ementa')}}</textarea>                                
                            </div>
                        </div>
                        <div class="col-md-6">                                                        
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="assunto">Assunto</label>
                                        <select class="form-control" required id="assunto_id" name="assunto_id">
                                            <option value=""  {{ old('tipo_documento_id') ? "" : "selected" }}>Selecione</option>
                                            @foreach ($assuntos as $assunto)
                                                <option value="{{$assunto->id}}" {{ old('tipo_documento_id') && old('assunto_id') == $assunto->id ? "selected":""}}>{{$assunto->nome}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tipo_documento">Tipo de Documento</label>
                                        <select class="form-control" required id="tipo_documento_id" name="tipo_documento_id">
                                            <option selected value="" {{ old('tipo_documento_id') ? "" : "selected" }}>Selecione</option>
                                            @foreach ($tiposDocumento as $tipo)
                                                <option value="{{$tipo->id}}" {{ old('tipo_documento_id') && old('tipo_documento_id') == $tipo->id ? "selected":""}}>{{$tipo->nome}}</option>
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
                                        <input type="text" value="{{old('palavras_chave')}}" class="tags-required" data-role="tagsinput" id="palavras_chave" name="palavras_chave"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!--end row -->

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="arquivo">Arquivo(PDF)</label>
                                <small class=".text-muted">(Tamanho máximo: 5 MB)</small>
                                <input id="arquivo" value="{{old('arquivo')}}" name="arquivo" class="form-control" type="file" accept="application/pdf" required>
                                <small class=".text-muted">Arquivos digitalizados não são indexados para busca. Dê preferência ao PDF original.</small>
                            </div>
                        </div>                           
                    </div><!--end row -->  
                    <div class="switch-wrapper mt-3">
                        <input type="hidden" name="publico" value="0">
                        <input type="checkbox" id="publico" name="publico" class="switch-input" value="1" checked>
                        <label for="publico" class="switch-label">
                          <span class="switch-slider"></span>
                          Documento Público
                        </label>
                    </div>
                </div><!-- end box-body -->        
            </div><!-- end box-->
            <button type="submit" class="btn btn-primary btn-lg" value="Enviar">Enviar</button>
        </form>
    </div> <!--end container ALL 4332-->
@stop

@push('js')
    <script src="{{ asset('vendor/tagsinput/bootstrap-tagsinput.min.js') }}"></script>
    <script src="{{ asset('js/app-create.js') }}"></script>
@endpush
