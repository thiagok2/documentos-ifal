@extends('adminlte::page')

@section('title', 'Documentos IFAL')

@section('content_header')
    
@stop

@section('content')
<div class="container-fluid">
    <ol class="breadcrumb">
        <li class="breadcrumb-item active"><a href="{{route('home')}}">Painel</a></li>
        <li class="breadcrumb-item active"> <a href="{{route('usuarios')}}">Usuário</a></li>
        <li class="breadcrumb-item active"> <a href="#">Criar usuário</a></li>
    </ol>
    @include('admin.includes.alerts')
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-default">
                <div class="card-header">Criar novo usuário</div>
                <div class="card-body">
                    <form name="form" id="form" method="post" action="{{route('usuario-create')}}">
                        {!! csrf_field() !!}

                        <input type="hidden" name="unidadeId" id="unidadeId" value="{{$unidade->id}}"/>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="unidade">Unidade</label>
                                    <input type="text" class="form-control" name="unidade"
                                            readonly value="{{$unidade->nome}}">
                                </div>
                            </div>
                        </div>
                       
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="nome">Nome*</label>
                                    <input type="text" class="form-control" name="name" value="{{$usuario->name}}" required maxlength="255" minlength="10">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                           <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="tipo">Tipo</label>
                                    <select class="form-control" id="tipo" name="tipo">
                                        @if (auth()->user()->isAdmin() && $unidade->responsavel)
                                            <option value="admin">Administrador</option>
                                        @endif
                                        <option value="gestor">Gestor</option>
                                        @if ($unidade->responsavel)
                                            <option value="colaborador">Colaborador</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="email">Email*</label>
                                    <small class=".text-muted"></small>
                                    <div class='input-group'>
                                        <span class="input-group-addon">
                                            <span class="fa fa-envelope-o">
                                            </span>
                                        </span>
                                        <input type='email' class="form-control" id="email" name="email"  maxlength="255"
                                                required value="{{$usuario->email}}"/>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg" value="Convidar">Enviar convite</button>
                        
                    </form>
                </div><!-- end card-body-->
            </div><!-- end card-->
        </div><!-- end col-form-->
    
    </div><!-- end row container-->
</div><!-- end container-->

@stop
@push('js')
    <script src="{{ asset('js/app-usuario.js') }}"></script>
@endpush