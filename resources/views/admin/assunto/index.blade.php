@extends('adminlte::page')

@section('title', 'Documentos IFAL')

@section('content_header')
    
@stop

@section('content')    
    <div class="container-fluid">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="{{route('home')}}">Painel</a></li>
            <li class="breadcrumb-item active"><a href="{{route('Assuntos')}}" class="active">Assuntos</a></li>
        </ol>
        <div class="page-header">
            <small style="margin-bottom: 10px">Assuntos gerais abordados nos documentos</small>
            <a href="{{route('assuntos-create')}}" class="btn btn-primary btn-lg">Novo Assunto</a>
        </div>
        

        @include('admin.includes.alerts')
        <div class="row" style="margin-bottom: 10px">
            @forelse ($assuntos as $assunto)
                <div class="col-lg-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <span class="lead">
                                {{$assunto->nome}}
                                ({{$assunto->documentos_count}})
                            </span>

                            <div class="float-left" style="margin-right: 5px;">
                                <a href="{{route('assunto-edit',$assunto->id)}}">
                                    <i class="fa fa-edit"></i>
                                </a>
                            </div>
                        </div>
                        <div class="panel-body">
                            {{$assunto->descricao}}
                        </div>
                    </div>
                </div>
            @empty
                <li>Nenhum assunto</li>
            @endforelse
        </div>
        <div class="row">
            <div class="col-lg-12">
                <a href="{{route('assunto-removidos')}}" class="btn btn-danger btn-lg">Assuntos Removidos</a>
            </div>            
        </div>
    </div>

    
@stop