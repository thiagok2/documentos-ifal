@extends('adminlte::page')

@section('title', 'Documentos IFAL')

@section('content_header')
    
@stop

@section('content')
    <div class="container-fluid">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="{{route('home')}}">Painel</a></li>
            <li class="breadcrumb-item active"> <a href="{{route('unidades')}}" >Unidades</a></li>
            <li class="breadcrumb-item active"> <a href="#" ><a href="#">Atualizar dados da Unidade</a></li>
        </ol>
        
        <div class="row">
            @include('admin.includes.alerts')

            @if ($unidade->trashed())
                <div class="alert alert-danger" style="font-size: 120%">
                    Unidade desabilitada! Clique em habilitar caso queira voltar a utiliza-lá.
                </div>                    
            @endif    

            <div class="col-lg-8">
                <div @if ($unidade->trashed()) class="card card-danger " @else class="card card-default" @endif>
                    <div  class="card-header">
                        Atualizar dados da Unidade 
                        <!--@if (isset($alerta))
                            <span class="align-middle pull-right label label-warning" style="font-size: 95%">
                                {{$alerta}}
                            </span>                    
                        @endif--> 
                    </div>
                    <div class="card-body">                
                        <form name="form" id="form" method="post" action="{{route('unidade-store')}}" disabled>
                            {!! csrf_field() !!}
                            <input type="hidden" value="{{ $unidade->id }}" name="id">
                            <input type="hidden" value="{{ $unidade->estado_id }}" name="estado_id">
                            <input type="hidden" value="{{ $unidade->municipio_id }}" name="municipio_id">

                            <div class="row">                                
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="esfera">Esfera*</label>
                                        <select class="form-control" required id="esfera" name="esfera">
                                            <option {{($unidade->esfera == 'Municipal' ? 'selected="selected"':'')}}>Municipal</option>
                                            <option {{($unidade->esfera == 'Estadual' ? 'selected="selected"':'')}}>Estadual</option>
                                            <option {{($unidade->esfera == 'Federal' ? 'selected="selected"':'')}}>Federal</option>
                                        </select>
                                    </div>
                                </div>
                                <input type="hidden" value="Unidade" name="tipo">
                                <!--<div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="tipo">Tipo*</label>
                                        <select class="form-control" required id="tipo" name="tipo">
                                            <option value="Unidade" {{($unidade->tipo == 'Unidade' ? 'selected="selected"':'')}}>Unidade</option>
                                            <option value="Assessoria" {{($unidade->tipo == 'Assessoria' ? 'selected="selected"':'')}}>Assessoria</option>
                                            <option value="Outros" {{($unidade->tipo == 'Outros' ? 'selected="selected"':'')}}>Outros</option>
                                        </select>
                                    </div>
                                </div>-->
                                <div class="col-sm-6">
                                    <div class="form-group">
                                         <label for="pai_id">Unidade Pai</label>
                                         <small class="text-muted">(opcional)</small>

        
                                        <select class="form-control" id="pai_id" name="pai_id">
                                            <option>N/A</option>
                                            @foreach ($unidades as $u)
                                                @if ($u->esfera == 'Campus')
                                                <option value="{{$u->id}}">{{$u->nome}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-9">
                                    <div class="form-group">
                                        <label for="nome">Nome*</label>
                                        <input type="text" class="form-control" value="{{ $unidade->nome }}" name="nome" id="nome"
                                            required maxlength="255" minlength="10">
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="sigla">Sigla*</label>
                                        <input type="text" class="form-control" value="{{ $unidade->sigla }}" name="sigla"
                                            required minlength="3" maxlength="10">
                                    </div>
                                </div>                                                       
                            </div>    

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="friendly_url">URL Amigável*</label>
                                        <div class="input-group">
                                            <span class="input-group-prepend">
                                                <span class="input-group-text"> <i class="fa fa-globe"></i>
                                                </span>
                                            </span>
                                            <input type="text" class="form-control" value="{{ $unidade->friendly_url }}" name="friendly_url" id="friendly_url" required>
                                        </div>
                                    
                                        <small id="friendly_url_help" class="form-text text-muted">URL Interna para a plataforma normativas</small>                                
                                    </div>
                                </div>                         
                                    
                                <div class="col-sm-5">
                                    <div class="form-group">
                                        <label for="telefone">Telefone</label>
                                        <small class=".text-muted">* (DDD) 0000-0000</b></small>
                                        <input type="text" class="form-control" value="{{ $unidade->telefone }}" name="telefone"
                                            required maxlength="100" minlength="12">
                                    </div>
                                </div>
                
                                <div class="col-sm-7">
                                    <div class="form-group">
                                        <label for="email">Email*</label>
                                        <small class="text-muted">(Separar emails com <b>;(ponto e virgula))</b></small>
                                        <input type="text" class="form-control" value="{{ $unidade->email }}" name="email"
                                            required maxlength="255">
                                    </div>
                                </div>
                            </div>
                
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="url">Endereço na web</label>
                                        <small class="text-muted">(Site da unidade - opcional)</small>
                                        <div class='input-group'>
                                            <span class="input-group-prepend">
                                                <span class="input-group-text"> <i class="fa fa-globe"></i>
                                                </span>
                                            </span>
                                            <input type='url' class="form-control" id="url" name="url" value="{{ $unidade->url }}" 
                                                maxlength="255"/>
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
                
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="endereco">Outros cargos/responsáveis</label>
                                        <small class="text-muted">(opcional)</small>
                                        <textarea class="form-control" id="contato2" name="contato2" maxlength="255">{{ $unidade->contato2 }}</textarea>
                                    </div>
                                </div>
                            </div><!--end row -->
                
                            <button type="submit" class="btn btn-success btn-lg" value="Confirmar alterações">Confirmar alterações</button>
                            
                            @if (auth()->user()->isAdmin())
                                @if (!$unidade->trashed())
                                <a href="#modalConfirmUnidade{{$unidade->id}}" class="btn btn-danger btn-lg" data-toggle="modal">Desabilitar</button></a>
                                @else
                                <a href="{{route('unidade-restore',$unidade->id)}}" class="btn btn-primary btn-lg" data-toggle="modal">Reabilitar</button></a>
                                <a href="{{route('unidade-force-delete',$unidade->id)}}" class="btn btn-danger btn-lg" data-toggle="modal">Remoção Permanente</button></a>
                                @endif
                            @endif
                            <a href="{{route('home')}}" class="btn btn-danger btn-lg" value="Fechar">Fechar</a>
                        </form>

                        <div class="modal fade" id="modalConfirmUnidade{{$unidade->id}}" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Confirmação exclusão de Unidade</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Tem certeza que deseja desabilitar a unidade {{$unidade->nome}}?</p>
                                        Seus usuários também serão desabilitados;
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                        @if ( auth()->user()->isAdmin())


                                            <a href="{{route('unidade-delete',$unidade->id)}}" class="btn btn-primary">Desabilitar</a>                                                        
                                        @endif
                                    </div>
                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- /.modal --> 
                    </div><!--end panel-body-->
                </div><!--end panel-->
            </div> <!-- end col 8-->

        <div class="col-lg-4">
        <div class="card card-default">
            <div class="card-header">Colaboradores</div>
            <div class="card-body">
                @if(!$unidade->trashed())

                    @if ( auth()->user()->isAdmin())
                        <p>
                            <a href="{{route('usuario-convidar',['unidade_id'=>$unidade->id])}}" class="btn btn-primary" value="Fechar"><i class="fa fa-plus"></i> Adicionar Colaborador</a>
                        </p>
                    @endif

                    @if (auth()->user()->isGestor() && $unidade->confirmado)
                        <p>
                            <a href="{{route('usuario-convidar')}}"  class="btn btn-primary" value="Fechar">Adicionar Colaborador</a>
                        </p>
                    @endif
                @endif
                
                @forelse ($users as $user)
                    
                    <div @if($user->isResponsavel()) class="card card-primary" @else class="card card-default" @endif>
                        <div class="card-header">{{ $user->name }}</div>
                        <div class="card-body">
                            @if ($user->trashed())
                                <div class="badge badge-danger">DESABILITADO</div>
                                <br/>
                            @endif

                            Email: {{ $user->email }}

                            <br/>
                            @if ($user->isResponsavel())
                                <span class="badge pl-0">RESPONSÁVEL</span>
                                <br/>
                            @endif
                            {{ $user->tipo }}
                            <br/>
                            @if($user->created_at)
                                Criação: {{ $user->created_at->format('d/m/Y') }}
                                <br/>
                            @endif                            
                            @if ( $user->confirmado)
                                Confirmação: {{ $user->confirmado_em }}
                            @else
                                <span class="badge">Não confirmado</span>
                            @endif
                        </div>
                        <div class="card-footer">
                            @if(!$unidade->trashed())
                                @if (!$user->trashed() && auth()->user()->id != $user->id  &&
                                    (auth()->user()->isGestor() || auth()->user()->isAdmin()))                            
                                    <a href="#modalConfirm{{$user->id}}" class="btn btn-danger" data-toggle="modal">Excluir</button></a>                                                                                                    

                                    <div class="modal fade" id="modalConfirm{{$user->id}}" tabindex="-1" role="dialog">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title">Confirmação de exclusão</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
                                @if (auth()->user()->isGestor() || auth()->user()->isAdmin())
                                    <a href="{{route('usuario-edit',$user->id)}}" class="btn btn-primary">Editar</a>
                                    @if(!$user->trashed())    
                                        <a href="{{route('usuario-reconvidar',$user->id)}}" class="btn btn-success">Enviar novo convite</a>

                                        @if (!$user->isResponsavel())
                                            <a href="{{route('unidade-novo-responsavel',['unidadeId'=>$unidade->id, 'usuarioId'=>$user->id])}}" class="btn btn-primary">Tornar responsável</a>
                                        @endif
                                    @endif
                                @endif

                                @if ($user->trashed() && (auth()->user()->isResponsavel() || auth()->user()->isAdmin()))
                                    <a href="{{route('usuario-force-delete',$user->id)}}" class="btn btn-danger">Remoção Permanente</a>
                                @endif 
                            @endif                           
                        </div>
                    </div>   
                    
                @empty
                    <h2>Sem usuários</h2>
                @endforelse                    
                    
            </div>
        </div>
        </div> <!-- end div col-4-->

        </div><!-- end row-->
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-default">
                    <div class="card-header">Atos normativos cadastrados ({{$documentos->total()}})</div>
                    <div class="card-body">
                        <div class="row">
                            @forelse ($documentos as $doc)
                            <div class="col-lg-6">
                                <div class="card card-default">
                                    <div class="card-header">{{ $doc->titulo }}</div>
                                    <div class="card-body">
                                        {{ $doc->ementa }}
                                        <hr/>
                                        
                                        <br/>
                                        Ano: {{ $doc->ano }}
                                        <br/>
                                        Publicação: {{date('d-m-Y', strtotime($doc->data_publicacao))}}
                                        <br/>
                                        Número: {{ $doc->numero }}
                                    </div>
                                    <div class="card-footer">
                                            <a href="{{ route("pdfNormativa",$doc->arquivo) }}" target="_blank" class="btn btn-primary btn-lg">
                                                Baixar
                                            </a>
                                            <a href="{{ route("documento",$doc->id) }}" class="btn btn-primary btn-lg">
                                                Visualizar
                                            </a>
                                    </div>
                                </div>   
                            </div><!-- end card-doc-->
                            
                        @empty
                            <div class="col-lg-8">
                                <div class="alert alert-warning">
                                    <strong>Aviso!</strong> Nenhum documento foi enviado por essa unidade.
                                </div>
                            </div>
                        @endforelse  
                        </div>
                    </div><!-- end card body -->
                </div> <!-- card body-->
                <div class="box-footer">
                    {{ $documentos->links() }}
                </div>
            </div>
        </div>
    </div><!--end container-->
@stop
@push('js')
    <script src="{{ asset('vendor/mask/jquery.mask.min.js') }}"></script>
    <script src="{{ asset('js/app-unidades.js') }}"></script>
@endpush