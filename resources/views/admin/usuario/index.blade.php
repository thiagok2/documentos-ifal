@extends('adminlte::page')

@section('title', 'Documentos IFAL')

@section('content_header')
    
@stop

@section('content')
@if (auth()->user()->id != $user->id  && (auth()->user()->isResponsavel() || auth()->user()->isAdmin() || auth()->user()->isGestor()))                                                                                                            
    <div class="modal fade" id="modalConfirm" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Confirmação de exclusão</h4>
                </div>
                <div class="modal-body">
                    <p>Tem certeza que deseja excluir este usuário?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <a href="{{route('usuario-delete',$user->id)}}" class="btn btn-danger">Excluir</a>                                                        
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal --> 
@endif

<div class="container-fluid">
    <ol class="breadcrumb">
        <li><a href="{{route('home')}}">Painel</a></li>
        <li> <a href="{{route('usuarios')}}" class="active">Usuário</a></li>
    </ol>
    @include('admin.includes.alerts')
    <div class="row">
        @if ($user->trashed())
            <div class="col-lg-12 alert alert-danger" style="font-size: 120%">
                Usuário desabilitado! Volte a habilitá-lo para edição.
                @if ($user->isResponsavel())
                    <br/>
                    Usuário é o responsável pela unidade desabilitada. Remova a unidade permanentemente caso deseje a remoção definitiva.
                @endif
            </div>                    
        @endif 

        @if (auth()->user()->isAdmin() || auth()->user()->isGestor())
            
            <div class="col-lg-2">
            <a href="{{route('usuario-convidar')}}" class="btn btn-primary btn-block btn-lg">Novo colaborador</a>
                <p>
            </div>
        
        @endif
        @if (auth()->user()->isAdmin())
            
            <div class="col-lg-2">
                <a href="{{route('usuario-search')}}" class="btn btn-primary btn-block btn-lg">Pesquisar</a>
                    <p>
            </div>
            
        @endif
    </div>

    
    <div class="row">
        <div class="col-lg-8">
                <div @if ($user->unidade->trashed()) class="panel panel-danger" @else class="panel panel-primary" @endif>
                    <div class="panel-heading">
                        <h4>{{ $user->unidade->nome }}</h4>                                                                                
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-8">
                                <div class="form-group">
                                    <label for="nome">Nome</label>
                                    <input type="text" class="form-control" value="{{ $user->unidade->nome }}" name="nome" readonly>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="nome">Sigla</label>
                                    <input type="text" class="form-control" value="{{ $user->unidade->sigla }}" name="sigla" readonly>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="nome">Responsável</label>
                                    <input type="text" class="form-control" value="{{ $user->unidade->responsavel->name }} ({{ $user->unidade->responsavel->email }})" name="responsavel" readonly>
                                </div>
                            </div>
            
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="friendly_url">URL Amigável</label>
                                                                        
                                    <div class="input-group">
                                        <span class="input-group-addon" id="basic-addon1">
                                            <span class="glyphicon glyphicon-globe"></span>
                                        </span>
                                        <input type="text" class="form-control" value="{{route('unidades-page', $user->unidade->friendly_url)}}" name="friendly_url" id="friendly_url" required readonly>
                                        <span class="input-group-addon" id="btn-copy" style="cursor:pointer" title="Copiar URL" data-toggle="popover" title="Popover title" data-content="URL copiada!" onclick="$('#btn-copy').popover('show'); document.getElementById('friendly_url').select();  document.execCommand('copy'); setTimeout(function(){ $('#btn-copy').popover('hide') }, 1500)"> 
                                            <span class="glyphicon glyphicon-copy"></span>                                        
                                        </span>
                                    </div>
                                    
                                    <small id="friendly_url_help" class="form-text text-muted">URL Interna para a plataforma normativas</small>                                    
                                </div>
                            </div>
                        </div> <!-- row nome/url -->                                               

                        <div class="row">
                            <div class="col-sm-5">
                                <div class="form-group">
                                    <label for="telefone">Telefone</label>                                    
                                    <input type="text" class="form-control" value="{{ $user->unidade->telefone }}" name="telefone"
                                        readonly>
                                </div>
                            </div>
            
                            <div class="col-sm-7">
                                <div class="form-group">
                                    <label for="email">Email</label>                                    
                                    <input type="text" class="form-control" value="{{ $user->unidade->email }}" name="email"
                                        readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="btn-group btn-group-justified" role="group" aria-label="...">
                                    <a href="{{route("unidade-edit",$user->unidade->id)}}" class="btn btn-primary btn-lg" value="Editar">Editar</a>
                        
                                  
                                    <a href="{{route('usuario-reconvidar',$user->unidade->responsavel->id)}}" class="btn btn-primary btn-lg" target='_self'>Enviar Convite({{$user->unidade->responsavel->email}})</a>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>    

            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4>{{ $user->name }}</h4>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-8">
                            <div class="form-group">
                                <label for="nome">Nome</label>
                                <input type="text" class="form-control" value="{{ $user->name }}" name="name" readonly>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="tipo">Função</label>
                                <select class="form-control" disabled id="tipo" name="tipo">
                                    <option {{($user->tipo == 'admin' ? 'selected="selected"':'')}}>Administrador</option>
                                    <option {{($user->tipo == 'gestor' ? 'selected="selected"':'')}}>Gestor</option>
                                    <option {{($user->tipo == 'colaborador' ? 'selected="selected"':'')}}>Colaborador</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-8">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <small class=".text-muted"></small>
                                <div class='input-group'>
                                    <span class="input-group-addon">
                                        <span class="fa fa-envelope-o">
                                        </span>
                                    </span>
                                    <input type='email' readonly class="form-control" id="email" name="email" value="{{ $user->email }}" maxlength="255"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="cpf">CPF</label>
                                <input type="text" readonly class="form-control" value="{{ $user->cpf}}" name="cpf">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="btn-group btn-group-justified" role="group" aria-label="...">
                                @if (auth()->user()->id == $user->id || auth()->user()->isGestor() || auth()->user()->isAdmin())
                                    <a href="{{route('usuario-edit',$user->id)}}" class="btn btn-primary btn-lg ">Editar</a>    
                                @endif
                                @if (!$user->trashed() && auth()->user()->id != $user->id && (auth()->user()->isGestor() || auth()->user()->isAdmin()))
                                    <a href="{{route('usuario-reconvidar',$user->id)}}" class="btn btn-primary btn-lg ">Enviar novo convite</a>    
                                @endif

                                @if (!$user->trashed() && auth()->user()->id != $user->id  &&
                                        (auth()->user()->isGestor() || auth()->user()->isAdmin()))                                    
                                    <a href="#modalConfirm" class="btn btn-primary btn-lg" data-toggle="modal">Excluir</button></a>                                                                        
                                @endif

                                @if (!$user->isResponsavel() && !$user->trashed())
                                    <a href="{{route('unidade-novo-responsavel',['unidadeId'=>$user->unidade->id, 'usuarioId'=>$user->id])}}" class="btn btn-primary">Tornar responsável</a>
                                @endif
                            
                            </div>
                        </div>
                    </div>
                </div><!-- end panel-body-->
            </div><!-- end panel-->
        </div><!-- end col-form-->

        <div class="col-lg-4">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4>Usuários</h4>
                </div>
                <div class="panel-body">
                    <div class="list-group" role="tablist">
                        @foreach ($usuarios as $u)
                            <div @if ($u->isResponsavel()) class="list-group-item list-group-item-action list-group-item-success" @elseif($user->id === $u->id) class="list-group-item list-group-item-action active" @else class="list-group-item list-group-item-action" @endif  role="tab">
                                <a href="{{route('usuarios',['id' => $u->id])}}" >
                                    <h4 class="list-group-item-heading">{{$u->name}}</h4>
                                    
                                    <p class="list-group-item-text">
                                        {{$u->email}}

                                        <span class="badge pull-right">{{$u->tipo}}</span>

                                        @if (!$u->confirmado)  
                                            <span class="badge pull-right">Não confirmado</span>
                                            @if ($u->convidado_em)
                                                <span class="badge pull-right">Convidado em {{$u->convidado_em}}</span> 
                                            @endif     
                                        @endif

                                       

                                        @if ($u->isResponsavel())
                                            <span class="badge pull-right">Responsável</span>
                                        @endif

                                        @if ($u->trashed())
                                            <span class="badge pull-right">REMOVIDO</span>
                                        @endif
                                    </p>

                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div><!-- end row -->

    <div class="row">
        <div class="container-fluid">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4>Documentos enviados por {{$user->name}} ({{$documentos->count()}})</h4>
                </div>
                <div class="panel-body">
                    <table class="table table-hover">
                        <thead>
                            <tr >
                                <th style="width: 1%">#</th>
                                <th style="width: 2%">Ano</th>
                                <th style="width: 4%">Número</th>
                                <th style="width: 4%">Tipo</th>
                                <th style="width: 20%">Título</th>
                                <th style="width: 5%">Tags</th>
                                <th style="width: 5%">Publicação</th>
                                <th style="width: 5%">Envio</th>
                                <th style="width: 5%">Por</th>
                                <th style="width: 4%"></th>
                            </tr>
                        <thead>  
                        <tbody>
                            
                                @forelse ($documentos as $doc)
                                <tr  @if ($doc->completed) class='bg-success' @else class='bg-warning' @endif>
                                    <td>{{$loop->index + 1}}</td>
                                    <td>{{$doc->ano}}</td>
                                    <td>{{$doc->numero}}</td>
                                    <td>{{$doc->tipoDocumento->nome}}</td>
                                    <td>{{$doc->titulo}}</td>
                                    <td>
                                    @foreach ($doc->palavrasChaves as $p)
                                        <span class="badge bg-secondary">{{$p->tag}}</span>
                                    @endforeach
                                    </td>
                                    <td>{{date('d-m-Y', strtotime($doc->data_publicacao))}}</td>
                                    <td>{{date('d-m-Y', strtotime($doc->data_envio))}}</td>
                                    <td>{{$doc->user->firstName()}}</td>
                                    <td>
                                        <a href="{{ route("pdfNormativa",$doc->arquivo) }}" target="_blank">
                                            <i class="fa fa-arrow-circle-down"></i>
                                        </a>
                                        <a href="{{ route("documento",$doc->id) }}">
                                            <i class="fa fa-arrow-circle-right"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="10">
                                            <span class="no-results">Sem documentos enviados</span>
                                        </td>
                                    </tr>
                                @endforelse

                        </tbody>
                    </table>
                    <div class="box-footer">
                        {{ $documentos->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</div><!-- end container -->


@stop