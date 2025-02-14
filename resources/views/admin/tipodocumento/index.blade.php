@extends('adminlte::page')

@section('title', 'Documentos IFAL')

@section('content_header')
    
@stop

@section('content')    
    <div class="container-fluid">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="{{route('home')}}">Painel</a></li>
            <li class="breadcrumb-item active"> <a href="#" class="active">Tipos Documento</a></li>
        </ol>
        <div class="page-header">
            <h2> 
                <small>Tipos de documentos a serem enviados</small>
            </h2>
            @if(auth()->user()->isAdmin())
            <a href="{{route('tiposdocumento-create')}}" class="btn btn-primary btn-lg my-2">Novo Tipo de Documento</a>
            @endif
        </div>

        @include('admin.includes.alerts')
        <div class="row">
            @forelse ($tipodocumentos as $doc)
                <div class="col-lg-12">
                    <div class="card card-default">
                        <div class="card-header">
                            <span class="lead">
                                {{$doc->nome}}
                                ({{$doc->documentos_count}})
                            </span>
                            @if(auth()->user()->isAdmin())
                            <div class="float-right">
                                <a href="{{route('tiposdocumento-edit', $doc->id)}}">
                                    <i class="fa fa-edit"></i>
                                </a>
                            </div>
                            @endif
                        </div>
                        <div class="card-body">
                            {{$doc->descricao}}
                        </div>
                    </div>
                </div>
            @empty
                <li>Nenhum assunto</li>
            @endforelse
        </div>
        @if(auth()->user()->isAdmin())
        <div class="row">
            <div class="col-lg-12">
                <a href="{{route('tiposdocumento-removidos')}}" class="btn btn-danger btn-lg">Tipos de Documentos Removidos</a>
            </div>    
        </div>
        @endif
    </div>
@stop