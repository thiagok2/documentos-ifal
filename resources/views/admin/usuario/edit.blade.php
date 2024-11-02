@extends('adminlte::page')

@section('title', 'Documentos IFAL')

@section('content_header')
    
@stop

@section('content')
<div class="container-fluid">
    <ol class="breadcrumb">
        <li><a href="{{route('home')}}">Painel</a></li>
        <li> <a href="{{route('unidades')}}">Unidade</a></li>
        <li> <a href="#">Usuário</a></li>
    </ol>
    
    @include('admin.includes.alerts')

    @if ($user->trashed())
        <div class="alert alert-danger" style="font-size: 120%">
            Usuário desabilitado! Volte a habilitá-lo para edição.
            @if ($user->isResponsavel())
                <br/>
                Usuário é o responsável pela unidade desabilitada. Remova a unidade permanentemente caso deseje a remoção definitiva.
            @endif
        </div>                    
    @endif 

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">Atualizar cadastro de usuário</div>
                <div class="panel-body">
                    <form name="form" id="form" method="post" action="{{route('usuario-store')}}">
                        {!! csrf_field() !!}
                        <input type="hidden" value="{{ $user->id }}" name="id">
                    
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="nome">Nome*</label>
                                    <input type="text" class="form-control" value="{{ $user->name }}" name="name"
                                        required maxlength="255" minlength="10">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-8">
                                <div class="form-group">
                                    <label for="unidade">Unidade</label>
                                    <input type="text" readonly class="form-control" value="{{ $user->unidade->nome }} - {{ $user->unidade->sigla }}" name="unidade">
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="tipo">Tipo</label>
                                    <select class="form-control" id="tipo" name="tipo" disabled>
                                        <option {{($user->tipo == 'admin' ? 'selected="selected"':'')}}>Administrador</option>
                                        <option {{($user->tipo == 'gestor' ? 'selected="selected"':'')}}>Gestor</option>
                                        <option {{($user->tipo == 'assessor' ? 'selected="selected"':'')}}>Assessor</option>
                                        <option {{($user->tipo == 'colaborador' ? 'selected="selected"':'')}}>Colaborador</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-8">
                                <div class="form-group">
                                    <label for="email">Email*</label>
                                    <small class=".text-muted"></small>
                                    <div class='input-group'>
                                        <span class="input-group-addon">
                                            <span class="fa fa-envelope-o">
                                            </span>
                                        </span>
                                        <input type='email' class="form-control" id="email" name="email" value="{{ $user->email }}" maxlength="255"
                                            required @if($user->trashed()) disabled @endif/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="cpf">CPF*</label>
                                    <input type="text" class="form-control cpf" value="{{ $user->cpf}}" name="cpf"
                                        required maxlength="15" min="12" @if($user->trashed()) disabled @endif/>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-8">
                                <div class="form-group">
                                    <label for="email_confirmation">Confirmar email*</label>
                                    <small class=".text-muted"></small>
                                    <div class='input-group'>
                                        <span class="input-group-addon">
                                            <span class="fa fa-envelope-o">
                                            </span>
                                        </span>
                                        <input type='email' class="form-control" id="email_confirmation" name="email_confirmation" maxlength="255"
                                            required @if($user->trashed()) disabled @endif/>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="password">Nova senha*</label>
                                    <small class=".text-muted"></small>
                                    <input type="password" class="form-control" name="password" id="password" required minlength="6" maxlength="12" 
                                        @if($user->trashed()) disabled @endif>
                                </div>
                            </div>
                        </div>    
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="password_confirmation">Confirmar nova senha*</label>
                                    <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" required minlength="6" maxlength="12"
                                    @if($user->trashed()) disabled @endif>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-primary btn-lg" value="Atualizar" @if($user->trashed()) disabled @endif>Atualizar</button>
                                
                                @if ($user->trashed() && !$user->isResponsavel())
                                    <a href="{{route('usuario-force-delete',$user->id)}}" class="btn btn-danger btn-lg">Remoção Permanente</a>
                                @elseif(!$user->trashed() && !$user->isResponsavel())
                                    <a href="#modalConfirmDeleteUsuario" class="btn btn-danger btn-lg" data-toggle="modal">Desabilitar</button></a>
                                @endif
                                
                                @if($user->trashed())
                                    <a href="{{route('usuario-restore',$user->id)}}" class="btn btn-primary btn-lg" data-toggle="modal">Reabilitar</button></a>
                                @endif

                                @if ($user->confirmado)
                                    <a href="{{route('home')}}" class="btn btn-danger btn-lg" value="Fechar">Fechar</a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div><!-- end panel-body-->
            </div><!-- end panel-->
        </div><!-- end col-form-->
    
    </div><!-- end row container-->
</div><!-- end container-->
<div class="modal fade" id="modalConfirmDeleteUsuario" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Confirmar desabilitar usuário?</h4>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja desabilitar o usuário {{$user->name}}?</p>
                Ele não terá mais acesso ao sistema!;
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <a href="{{route('usuario-delete',$user->id)}}" class="btn btn-danger">Desabilitar</a>                                                        
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal --> 

@stop

@push('scripts')
<script src="{{ asset('js/app-usuarios.js') }}"></script>
@endpush