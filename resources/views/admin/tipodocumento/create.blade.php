@extends('adminlte::page')

@section('title', 'Documentos IFAL')

@section('content_header')
    
@stop

@section('content')        
    <div class="container-fluid">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="{{route('home')}}">Painel</a></li>
            <li class="breadcrumb-item active"><a href="{{route('tiposdocumento')}}">Tipos de Documento</a></li>
            <li class="breadcrumb-item active"><a href="#">Novo</a></li>
        </ol>

        <div class="row">
            @include('admin.includes.alerts')
            <div class="col-lg-8">
                <div class="card card-default">
                    <div class="card-header">Novo Tipo de documento: Informe nome e descrição</div>
                    <div class="card-body">
                    <form  name="form" id="form" method="post" action="{{route('tiposdocumento-store')}}"> 
                        {!! csrf_field() !!}
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="nome">Nome*</label>
                                <input type="text" class="form-control" value="{{ old('nome') }}" name="nome" id="nome"
                                    required maxlength="100" minlength="10">
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="descricao">Descrição*</label>
                                <textarea class="form-control" rows="10" name="descricao" id="descricao">{{ old('descricao') }}</textarea>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg" value="Salvar">Salvar</button>
                        
                    </form>
                    </div><!-- end panel-body-->
                </div><!-- end panel-->
            </div><!-- end col-8 main-->
            <div class="col-lg-4">
                <div class="card card-default">
                    <div class="card-header">Tipos de Documentos cadastrados: Clique para editar</div>
                    <div class="card-body">
                        <div class="list-group">
                            @foreach ($tipodocumentos as $t)
                                
                                <a href="{{route('tiposdocumento-edit',$t->id)}}" class="list-group-item">
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
    </div><!-- end container-->

    
@stop