@extends('adminlte::page')

@section('title', 'Documentos IFAL')

@section('content_header')
    
@stop

@section('content')    
    <div class="container-fluid">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="{{route('home')}}">Painel</a></li>
            <li class="breadcrumb-item active"><a href="{{route('Assuntos')}}">Assuntos</a></li>
            <li class="breadcrumb-item active"> <a href="#" class="active">Removidos</a></li>
        </ol>
        <div class="page-header">
            <a href="{{route('assuntos-create')}}" class="btn btn-primary btn-lg">Novo Assunto</a>
        </div>

        @include('admin.includes.alerts')
        <div class="row">
            @forelse ($assuntos as $assunto)
                <div class="col-lg-12">
                    <div class="card card-danger">
                        <div class="card-header">
                            <span class="lead">
                                {{$assunto->nome}}
                                ({{$assunto->documentos_count}})
                            
                            </span>

                            <div class="float-right">
                                <a href="{{route('assunto-edit',$assunto->id)}}">
                                    <i class="fa fa-edit"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            {{$assunto->descricao}}
                        </div>
                        <div class="card-footer">
                            Assunto desabilitado em {{date('d/m/Y H:i:s', strtotime($assunto->deleted_at))}}        
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-lg-12">
                    <div class="alert alert-warning">
                        Nenhum assunto utilizado anteriormente foi desabilitado.
                    </div>
                </div>                
            @endforelse
        </div>
    </div>
@stop