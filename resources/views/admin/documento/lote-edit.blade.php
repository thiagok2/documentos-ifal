@extends('adminlte::page')

@section('title', 'Documentos IFAL')

@section('content_header')

@stop

@push('css')

<link href="{{ asset('css/select2.min.css')}}" rel="stylesheet"/>
<link rel="stylesheet" href="{{ asset('vendor/tagsinput/bootstrap-tagsinput.css') }}">

@endpush

@section('content')
    <div class="container-fluid">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="{{route('home')}}">Painel</a></li>
            <li class="breadcrumb-item active"> <a href="#" ><a href="#">Documentos</a></li>
            <li class="breadcrumb-item active"> <a href="#" class="active"><a href="#">Publicar em Lote</a></li>
        </ol>
        
        @include('admin.includes.alerts')

        <div class="row">
            <div class="col-lg-12">
                <div class="box box-warning">
                    <div class="box-header">
                        <h3 class="box-title">Últimos documentos enviados</h3>
                    </div> <!-- /.box-header -->
    
                    <div class="box-body no-padding">
                        <table class="table table-striped table-hover table-responsive">
                            <thead>
                                <tr>
                                    <th style="width: 7%">Ano</th>
                                    <th style="width: 10%">Publicação/Número</th>
                                    <th style="width: 1 %">Tipo/Assunto</th>
                                    <th style="width: 25%">Título</th>
                                    <th style="width: 20%">Ementa</th>
                                    <th style="width: 5%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($documentos as $key=>$doc)
                                <tr id="tr_doc_{{$doc->id}}">
    
                                    <td>
                                        <input style="width: 60px" type="text" class="form-control"  name="ano_{{$doc->id}}" id="ano_{{$doc->id}}" value="{{$doc->ano}}" required/>
                                    </td>
                                    <td>
    
                                        <input type='date' class="form-control" id="data_publicacao_{{$doc->id}}" name="data_publicacao_{{$doc->id}}" required/>
                                        <br/>
                                        <input type="text" class="form-control" maxlength="20" name="numero_{{$doc->id}}" id="numero_{{$doc->id}}" value="{{$doc->numero}}" placeholder="Número: Ex.: 123/2019" required/>
    
                                    </td>
    
                                    <td>
    
                                        <select style="width: 120px" class="form-control" required id="assunto_id_{{$doc->id}}" name="assunto_id_{{$doc->id}}">
                                            @foreach ($assuntos as $ass)
                                                <option value="{{$ass->id}}" {{($doc->assunto->id == $ass->id ? 'selected="selected"':'')}}>{{$ass->nome}}</option>
                                            @endforeach
                                        </select>
                                        <br/>
                                        <select style="width: 120px" class="form-control" required id="tipo_documento_id_{{$doc->id}}" name="tipo_documento_id_{{$doc->id}}">
                                            @foreach ($tiposDocumento as $tipo)
                                                <option value="{{$tipo->id}}" {{($doc->tipoDocumento->id == $tipo->id ? 'selected="selected"':'')}}>{{$tipo->nome}}</option>
                                            @endforeach
                                        </select>
    
                                    </td>
    
                                    <td>
                                        <input type="text" class="form-control"  name="titulo_{{$doc->id}}" id="titulo_{{$doc->id}}" value="{{$doc->titulo}}" placeholder="Título do ato normativo" required/>
                                        <br/>
                                        <a href='{{ Storage::url("uploads/$doc->arquivo")}}' target="_blank" class="d-flex justify-content-start">
                                            {{$doc->nomeOriginal()}}
                                            <i class="fa fa-download fa-2x"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <textarea rows="4" cols="50" name="ementa_{{$doc->id}}" id="ementa_{{$doc->id}}" required></textarea>
                                    </td>
    
                                    <td>
                                        <button type="button" class="btn btn-primary btn_salvar" id="btn_{{$doc->id}}" data-id="{{$doc->id}}" >
                                            <span class="fa fa-save fa-lg" aria-hidden="true"></span>
                                        </button>
                                        <br/>
                                        <br/>
                                        <button type='button' onclick="deleteUpload({{$doc->id}})" value='Remover' class='btn btn-danger btn'>
                                            <span class="fa fa-trash fa-lg" aria-hidden="true"></span>
                                        </button>
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
    
                    </div><!--end body -->
                    <div class="box-footer">
                        {{ $documentos->links() }}
                    </div>
                </div><!--end box -->
                <a href="{{route('home')}}" class="btn btn-primary btn-lg"><span class="fa fa-times mr-2"></span> Fechar</a>
            </div>
            
        </div><!-- end row -->

    </div><!-- end container -->


    <div class="alert alert-success alert-dismissible autoclose-alert-success" >
        Operação realizada com sucesso!
    </div>

    <div class="alert alert-danger alert-dismissible autoclose-alert-danger" >
        Algo deu errado com esta operação.
    </div>

@endsection
@push('js')
    <script src="{{ asset('js/select2.full.min.js')}}"></script>
    <script src="{{ asset('vendor/tagsinput/bootstrap-tagsinput.min.js') }}"></script>

    <script src="{{ asset('js/app-lote.js') }}"></script>
    <script src="{{ asset('js/jquery.ui.widget.js') }}"></script>

    <script src="{{ asset('js/jquery.iframe-transport.js') }}"></script>
    <script src="{{ asset('js/jquery.fileupload.js') }}"></script>
@endpush
