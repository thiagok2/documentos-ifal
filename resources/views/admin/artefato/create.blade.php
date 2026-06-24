@extends('adminlte::page')

@section('title', 'CentralDoc - Upload de Artefato')

@section('content_header')

@stop

@push('css')
<link rel="stylesheet" href="{{ asset('vendor/tagsinput/bootstrap-tagsinput.css') }}">
@endpush

@section('content')    
    <div class="container-fluid">

    <ol class="breadcrumb">
        <li class="breadcrumb-item active"><a href="{{route('home')}}">Painel</a></li>
        <li class="breadcrumb-item active"> <a href="#">Artefatos</a></li>
        <li class="breadcrumb-item active"> <a href="#" class="active">Upload</a></li>
    </ol>

    @include('admin.includes.alerts')

        <form name="form" id="form" action="{{route('artefato-store')}}" method="post" enctype="multipart/form-data">
            {!! csrf_field() !!}
        
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="form-group no-margin">
                        <label><h4>Upload de Artefato</h4></label>
                    </div>
                </div><!-- end box-header -->
                
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="resumo">Resumo</label>
                                <small class=".text-muted">(Descrição breve do artefato)</small>
                                <textarea id="resumo" required class="form-control" rows="4" name="resumo">{{old('resumo')}}</textarea>                                
                            </div>
                        </div>
                    </div><!-- end row -->

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="especificacao">Especificação</label>
                                <textarea id="especificacao" class="form-control" rows="3" name="especificacao">{{old('especificacao')}}</textarea>                                
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lista_entidades">Lista de Entidades</label>
                                <small class=".text-muted">(Pressione ENTER para confirmar a inserção)</small>
                                <input type="text" value="{{old('lista_entidades')}}" class="tags-required form-control" data-role="tagsinput" id="lista_entidades" name="lista_entidades"/>
                            </div>
                        </div>
                    </div><!--end row -->

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="arquivo">Arquivo (PDF)</label>
                                <small class=".text-muted">(Tamanho máximo: 20 MB)</small>
                                <input id="arquivo" value="{{old('arquivo')}}" name="arquivo" class="form-control" type="file" accept="application/pdf" required>
                            </div>
                        </div>                           
                    </div><!--end row -->  
                </div><!-- end box-body -->        
            </div><!-- end box-->
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary btn-lg" value="Enviar">Fazer Upload</button>
            </div>
        </form>
    </div>
@stop

@push('js')
    <script src="{{ asset('vendor/tagsinput/bootstrap-tagsinput.min.js') }}"></script>
@endpush
