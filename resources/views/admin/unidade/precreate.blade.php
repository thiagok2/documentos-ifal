@extends('adminlte::page')

@section('title', 'Documentos IFAL')

@section('content_header')
    
@stop

@section('content')
    <div class="container-fluid">
        <ol class="breadcrumb">
            <li><a href="{{route('home')}}">Painel</a></li>
            <li> <a href="{{route('unidades')}}" >Conselho</a></li>
            <li> <a href="#" ><a href="#">Novo Conselho</a></li>
        </ol>

        @include('admin.includes.alerts')

        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Novo Usuário/Conselho</div>
                    <div class="panel-body">
                    <form name="form" id="form" method="post" action="{{route('unidade-convidar')}}">
                            {!! csrf_field() !!}
                            <input type="hidden" name="tipo" id="tipo" value="Conselho"/>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="tipo">Esfera*</label>
                                        <select class="form-control input-lg" required id="esfera" name="esfera">
                                            <option value="Municipal" selected>Municipal</option>
                                        </select>
                                    </div>
                                </div>
                            
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="estado_id">Estado*</label>
        
                                        <select class="form-control input-lg" required id="estado_id" name="estado_id">
                                            <option>Selecione</option>
                                            @foreach ($estados as $estado)
                                                <option value="{{$estado->sigla}}">{{$estado->nome}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div><!-- end col estado-->
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="municipio">Município*</label>
        
                                        <select class="form-control input-lg" required id="municipio_id" name="municipio_id" required>
                                            <option>Selecione</option>                                            
                                        </select>

                                        <span class="help-text small text-muted">* Apenas municípios sem conselho criado.</span>
                                    </div>
                                </div>
                            </div><!-- end estados/municipios-->
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="email">Nome*</label>
                                        <small class="text-muted">Primeiro nome</small>
                                        <input type="text" class="form-control input-lg" name="nome"
                                            required maxlength="255" placeholder="Ex.: Maria Júlia">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="email">Email*</label>
                                        <small class="text-muted">(Separar emails com <b>;(ponto e virgula))</b></small>
                                        <input type="text" class="form-control input-lg"  name="email"
                                            required maxlength="255" placeholder="Ex.: conselho@educacaomaceio.com.br">
                                    </div>
                                </div>
                            </div>
                        
                            <button type="submit" class="btn btn-success btn-lg" value="Enviar convite">Enviar Convite</button>
                            <a href="{{route('home')}}" class="btn btn-danger btn-lg" value="Fechar">FechaADO</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@push('scripts')
<script src="{{ asset('js/app-unidades-simple.js') }}"></script>
@endpush