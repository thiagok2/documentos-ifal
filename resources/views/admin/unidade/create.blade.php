@extends('adminlte::page')

@section('title', 'Documentos IFAL')

@section('content_header')
    
@stop

@section('content')
    <div class="container-fluid">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="{{route('home')}}">Painel</a></li>
            <li class="breadcrumb-item active"> <a href="{{route('unidades')}}" >Unidade</a></li>
            <li class="breadcrumb-item active"> <a href="#" ><a href="#">Nova Unidade</a></li>
        </ol>

        @include('admin.includes.alerts')

        <div class="row">
            <div class="col-lg-10">
                <div class="card card-default">
                    <div class="card-header">Nova Unidade</div>
                    <div class="card-body">
                    <form name="form" id="form" method="post" action="{{route('unidade-save')}}">
                            {!! csrf_field() !!}
                            <input type="hidden" name="tipo" id="tipo" value="Conselho"/>
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="tipo">Esfera*</label>
                                        <select class="form-control" required id="esfera" name="esfera" required>
                                            <option value="Municipal" selected>Municipal</option>
                                            <option value="Estadual">Estadual</option>
                                            <option value="Federal">Federal</option>
                                        </select>
                                    </div>
                                </div>
                            
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="estado_id">Estado*</label>
        
                                        <select class="form-control" required id="estado_id" name="estado_id" required>
                                            <option>Selecione</option>
                                            @foreach ($estados as $estado)
                                                <option value="{{$estado->sigla}}" {{ old('estado_id') == $estado->id ? "selected":""}}>{{$estado->nome}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div><!-- end col estado-->
                                <div class="col-sm-5">
                                    <div class="form-group" style="margin-bottom: 0">
                                        <label for="municipio">Município*</label>
        
                                        <select class="form-control" required id="municipio_id" name="municipio_id" required>
                                            <option>Selecione</option>                                            
                                        </select>

                                        <span class="help-text small text-muted">* Apenas municípios sem unidade criado.</span>
                                    </div>
                                </div>
                            </div><!-- end estados/municipios-->
                            <div class="row">
                                <div class="col-sm-9">
                                    <div class="form-group">
                                        <label for="nome">Nome*</label>
                                        <input type="text" class="form-control" value="{{ $unidade->nome }}" name="nome" id="nome"
                                            required maxlength="255" minlength="10" placeholder="Ex.: Conselho Municipal de Educação de Maceió">
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="sigla">Sigla*</label>
                                        <input type="text" class="form-control" value="{{ $unidade->sigla }}" name="sigla" id="sigla"
                                            required minlength="3" maxlength="30" placeholder="Ex.: maceio">
                                    </div>
                                </div>                                                                        
                            </div><!-- end row-->

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="friendly_url">URL Amigável</label>
                                        <div class="input-group">
                                            <span class="input-group-prepend">
                                                <span class="input-group-text"> <i class="fa fa-globe"></i>
                                                </span>
                                            </span>
                                            <input type="text" class="form-control" name="friendly_url" id="friendly_url" readonly required>                                            
                                            <span class="input-group-append" id="btn-edit" style="cursor:pointer" title="Editar URL" onclick="$('#friendly_url').attr('readonly', !$('#friendly_url').attr('readonly'))"> 
                                                <span class="input-group-text"> <i class="fa fa-edit"></i></span>                                        
                                            </span>
                                        </div>
                                        
                                        <small id="friendly_url_help" class="form-text text-muted">URL Interna para a plataforma normativas</small>
                                        
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-5">
                                    <div class="form-group">
                                        <label for="telefone">Telefone</label>
                                        <small class=".text-muted">* (DDD) 0000-0000</b></small>
                                        <input type="text" class="form-control phone" value="{{ $unidade->telefone }}" name="telefone"
                                            required maxlength="100" minlength="12" placeholder="(00) 00000-0000">
                                    </div>
                                </div>
                
                                <div class="col-sm-7">
                                    <div class="form-group">
                                        <label for="email">Email*</label>
                                        <small class="text-muted">(Separar emails com <b>;(ponto e virgula))</b></small>
                                        <input type="text" class="form-control" value="{{ $unidade->email }}" name="email"
                                            required maxlength="255" placeholder="Ex.: comedmaceio@maceio.com.br">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="url">Endereço na Web</label>
                                        <small class="text-muted">(Site da unidade - opcional)</small>
                                        <div class='input-group'>
                                            <span class="input-group-prepend">
                                                <span class="input-group-text"> <i class="fa fa-globe"></i>
                                                </span>
                                            </span>
                                            <input type='url' class="form-control" id="url" name="url" value="{{ $unidade->url }}" 
                                                maxlength="255" placeholder="Ex.: http://comedmaceio-comed.blogspot.com/"/>
                                        </div>
                                    </div>
                                </div>
                            </div><!--end row -->
                
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="endereco">Endereço</label>
                                        <small class="text-muted">(opcional)</small>
                                        <input type='text' class="form-control" name="endereco" value="{{ $unidade->endereco }}" maxlength="255"/>
                                    </div>
                                </div>
                            </div><!--end row -->
                
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="endereco">Gestor</label>
                                        <small class="text-muted">(opcional)</small>
                                        <input type='text' class="form-control" id="contato" name="contato" 
                                            value="{{ $unidade->contato }}" maxlength="255"/>
                                    </div>
                                </div>
                            </div><!--end row -->

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="endereco">Outros cargos/responsáveis</label>
                                        <small class="text-muted">(opcional)</small>
                                        <textarea class="form-control" id="contato2" name="contato2" maxlength="255">{{ $unidade->contato2 }}</textarea>
                                    </div>
                                </div>
                            </div><!--end row -->

                            <button type="submit" class="btn btn-success" value="Criar unidade">Criar Unidade</button>
                            <a href="{{route('home')}}" class="btn btn-danger" value="Fechar">Fechar</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@push('js')
<script src="{{ asset('js/app-unidades.js') }}"></script>
@endpush