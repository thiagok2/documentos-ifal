@extends('adminlte::page')

@section('title', 'Normativas')

@section('content_header')
    
@stop

@section('content')
    <div class="container-fluid">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="{{route('home')}}">Painel</a></li>
            <li class="breadcrumb-item active"> <a href="{{route('unidades')}}" ><a href="#">Unidades</a></li>            
            <li class="breadcrumb-item active"> <a href="{{route('assessorias')}}" >Assessorias</a></li>
            <li class="breadcrumb-item active"> <a href="#" class="active"><a href="#">Nova Assessoria</a></li>            
        </ol>

        @include('admin.includes.alerts')

        <div class="row">
            <div class="col-lg-10">
                <div class="card card-default">
                    <div class="card-header">
                        Nova Unidade de Assessoria
                    </div>
                    <div class="card-body">
                        <form name="form" id="form" method="post" action="{{route('assessoria-store')}}">
                            {!! csrf_field() !!}
                            <input type="hidden" name="tipo" id="tipo" value="Assessoria"/>
                            <div class="row">
                            
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="estado_id">Estado</label>
        
                                        <select class="form-control" required id="estado_id" name="estado_id" required>
                                            <option>Selecione</option>
                                            @foreach ($estados as $estado)
                                                <option value="{{$estado->sigla}}" {{ old('estado_id') == $estado->id ? "selected":""}}>{{$estado->nome}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div><!-- end col estado-->

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="municipio">Município</label>
        
                                        <select class="form-control" required id="municipio_id" name="municipio_id" required>
                                            <option>Selecione</option>
                                            
                                        </select>

                                    </div>
                                </div>
                    
                            </div><!-- end estados/municipios-->
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="nome">Nome*</label>
                                        <input type="text" class="form-control" value="{{ $unidade->nome }}" name="nome" id="nome"
                                            required maxlength="255" minlength="10" placeholder="Ex.: Departamento de Ensino Campus - Rio Largo">
                                    </div>
                                </div>      
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="telefone">Telefone</label>
                                        <small class=".text-muted">* (DDD) 0000-0000</b></small>
                                        <input type="text" class="form-control phone" value="{{ $unidade->telefone }}" name="telefone"
                                            required maxlength="100" minlength="12" placeholder="(00) 00000-0000">
                                    </div>
                                </div>                                                              
                            </div><!-- end row-->
                
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="endereco">Gestor</label>
                                        <small class="text-muted"></small>
                                        <input type='text' class="form-control" id="contato" name="contato" 
                                            value="{{ $unidade->contato }}" maxlength="255"/>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="email">Email*</label>
                                        <small class="text-muted"></small>
                                        <input type="text" class="form-control" value="{{ $unidade->email }}" name="email"
                                            required maxlength="255" placeholder="Ex.: assessor@educacaoalagoas.com.br">
                                    </div>
                                </div>
                            </div><!--end row -->

                            <button type="submit" class="btn btn-success" value="Criar unidade">Criar unidade</button>
                            <a href="{{route('home')}}" class="btn btn-danger" value="Fechar">Fechar</a>
                        </form>
                    </div>
                    <div class="card-footer">

                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@push('js')
<script src="{{ asset('js/app-assessoria.js') }}"></script>
@endpush