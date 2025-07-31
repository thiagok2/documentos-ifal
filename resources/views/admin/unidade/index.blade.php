@extends('adminlte::page')

@section('title', 'Documentos IFAL')

@section('content_header')

@stop

@section('content')    
    <div class="container-fluid">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="{{route('home')}}">Painel</a></li>
            <li class="breadcrumb-item active"> <a href="#" class="active"><a href="#">Unidades</a></li>
        </ol>
        <div class="row">
            @if (auth()->user()->isAdmin() || auth()->user()->isAssessor())
                
                <div class="col-lg-2">
                    <a href="{{route('unidade-create')}}" class="btn btn-primary "><i class="fa fa-plus"></i> Adicionar Unidade</a>
        
                </div>
               
            @endif

        </div>

        <div class="row">
            @include('admin.includes.alerts')
        </div>

        <div class="row">
            <div class="col-lg-12 mt-3  ">
                <div class="card card-default">
                    <div class="card-header">Filtrar</div>
                    <div class="card-body">
                        <form class="form-inline" method="GET" action="{{route('unidades')}}">

                            <input type="text" id="nome" name="nome" class="form-control" value="{{$nome}}"
                                placeholder="Nome da unidade" aria-describedby="basic-addon1">
                            
                            <input type="text" name="pai_nome" class="form-control" value="{{ request()->query('pai_nome') }}" placeholder="Nome do pai" aria-describedby="basic-addon1">
                            
                            <select class="form-control" name="esfera" id="esfera">
                                <option value="0">Todas as Esferas</option>
                                <option value="Departamento"   @if($esfera=="Departamento") selected @endif>Departamento</option>                                
                                <option value="Campus"    @if($esfera=="Campus") selected @endif @if(auth()->user()->isAssessor()) disabled @endif>Campus</option>                                
                                <option value="Coordenação"     @if($esfera=="Coordenação") selected @endif @if(auth()->user()->isAssessor()) disabled @endif>Coordenação</option>
                            </select>

                            <!--<select class="form-control" name="estado" id="estado">
                                <option value=0>Todos os Estados</option>
                                @foreach ($estados as $e)
                                    <option value="{{$e->id}}" @if($estado==$e->id) selected @endif>{{$e->nome}}</option>
                                @endforeach
                            </select> -->


                            <select class="form-control" name="statusConvite" id="statusConvite">
                                <option value="0" @if($statusConvite ===0) selected @endif>Convite ?</option>
                                <option value="convidado_em" @if($statusConvite === 'convidado_em') selected @endif>Convite enviado</option>
                                <option value="confirmado_em"  @if($statusConvite === 'confirmado_em') selected @endif>Convite confirmado</option>
                            </select>

                            <button type="submit" class="btn btn-primary">Pesquisar</button>
                        
                            <select class="form-control" name="ordenarPor" id="ordenarPor">
                                <option value="confirmado_em" @if($ordenarPor == 'confirmado_em') selected @endif>Ordenar por Confirmação</option>
                                <option value="updated_at" @if($ordenarPor == 'updated_at') selected @endif>Ordenar por Atualização</option>
                                <option value="documentos_count" @if($ordenarPor == 'documentos_count') selected @endif disabled>Ordenar por Documentos </option>
                            </select>

                            <br/>
                            
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="emailCadastrado" id="emailCadastrado" value="true" @if($emailCadastrado) checked @endif/>
                                <label class="form-check-label" for="emailCadastrado">Somente email atualizados</label>
                            </div>

                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="incluirDesabilitados" id="incluirDesabilitados" value="true" @if($incluirDesabilitados) checked @endif/>
                                <label class="form-check-label" for="emailCadastrado">Incluir desabilitados</label>
                            </div>

                            {!! csrf_field() !!}
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="box box-info">
                    <div class="box-header">
                            Resultados ({{$unidades->total()}})
                    </div>
                    <div class="box-body no-padding">
                        <table id="tbl-conselhos" class="table table-striped table-hover table-condensed">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Esfera</th>
                                    <!-- <th>Estado</th>-->
                                    <!--<th>Município</th>-->
                                    <th>Nome da Unidade</th>
                                    <th>Unidade Pai</th>
                                    <th>Criado/Atualizado</th>
                                    <th class="col-md-1 text-center">Documentos</th>                                                                        
                                    <th class="col-md-1 text-center">Status</th>
                                    <th class="col-md-1 text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 110%">
                                @forelse ($unidades as $key=>$unidade)                                                                                                                
                                    <tr @if ($unidade->documentos_count > 0 && !$unidade->trashed()) class='table table-info' @endif
                                        @if ($unidade->trashed()) class='danger' @endif>
                                        <td class="text-bold">{{ ($unidades->currentpage()-1) * $unidades->perpage() + $key + 1 }}</td>
                                        <td>{{ $unidade->esfera }}</td>
                                        <!-- <td>Alagoas</td> -->
                                        <!--<td>{{ $unidade->municipio ? $unidade->municipio['nome'] :  'NA' }}</td>-->
                                        <td>
                                            <a style="cursor: pointer;" href="{{route("unidade-show",$unidade->id)}}" data-conselho-id="{{ $unidade->id }}">{{ $unidade->nome}}</a>
                                        </td>
                                        <td> @if ($unidade->pai) {{$unidade->pai->nome}}@else N/A @endif
                                        <td>
                                            <span>
                                                CRIADO:
                                                <small>({{$unidade->created_at}})</small>
                                            </span>
                                            @if($unidade->updated_at != $unidade->created_at)
                                            <br/>
                                            <span>
                                                ATUALIZADO:
                                                <small>({{$unidade->updated_at}})</small>
                                            </span>
                                            @endif
                                            @if(!str_contains($unidade->email,'alterar_email'))
                                                <br/>
                                                <span>
                                                    <small>({{substr($unidade->email,0,40)}})</small>
                                                </span>
                                            @else
                                                <br/>
                                                <span>
                                                    <small>SEM EMAIL</small>
                                                </span>
                                            @endif
                                        </td>

                                        <td class="text-center">                                            
                                            @if($unidade->documentos_count > 0)
                                                <h4><span>{{$unidade->documentos_count}} <i class="fa fa-file"></i></span></h4>
                                            @else
                                                <h4><span>{{$unidade->documentos_count}} <i class="fa fa-file"></i></span></h4>
                                            @endif                                                                                        
                                        </td>                                        
                                        <td class="text-center">   
                                            @if ($unidade->trashed())                                            
                                                <h4>
                                                    <span>
                                                    <small>
                                                    DESABILITADA
                                                    ({{$unidade->deleted_at}})
                                                    </small>
                                                </h4>
                                            @endif

                                            @if (!$unidade->trashed() && ($unidade->responsavel && $unidade->responsavel->confirmado))                                            
                                                <h4>
                                                    <span>
                                                    <small>
                                                    CONFIRMADO {{$unidade->confirmado_em}}
                                                    </small>
                                                </h4>
                                            @else
                                                <h4>
                                                    <span>
                                                        <small>
                                                        NÃO CONFIRMADO
                                                        </small>
                                                    </span>
                                                </h4>
                                                @if ($unidade->convidado_em)
                                                    <h4>
                                                        <span>
                                                        <small>
                                                            CONVIDADO
                                                            ({{$unidade->convidado_em}})
                                                        </small>
                                                        </span>
                                                    </h4>
                                                @endif
                                            @endif                                                                                                                                    
                                        </td>                                        
                                        <td class="text-center">   
                                            <h4>                                        
                                                <a href="{{route("unidade-edit",$unidade->id)}}" title="Editar">
                                                    <span><i class="fa fa-edit"></i></span>
                                                </a>                                                    
                                                <a href="#modalAtualizarConvidar" data-toggle="modal"
                                                    title="Enviar convite" class="modal-unidade" data-conselho-id="{{ $unidade->id }}">
                                                    <span><i class="fa fa-share-square"></i></span>
                                                </a>
                                            </h4>                                                                 
                                        </td>
                                    </tr>                                    
                                @empty
                                    <tr>
                                        <td colspan="6">Sem resultados</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="container-fluid">
                            <div class="row float-right">
                                <div class="mt-3">
                                    {{ $unidades->appends(request()->query())->links() }}
                                </div>                                
                            </div>
                        </div>
                    </div>



                </div>
            </div>
        </div>
    </div>    

    <div class="modal fade" id="modalAtualizarConvidar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <form action="{{route("unidade-novo-acesso")}}" method="POST" id="form-novo-acesso">
                {!! csrf_field() !!}
                <input type="hidden" name="unidade_id" id="unidade_id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="conselho_titulo"></h4>
                        <span class="help-text text-muted">Edite as informações do conselho e envie um convite para liberar o acesso.</span>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-sm-9">
                                    <div class="form-group">
                                        <label for="conselho_nome">Conselho *</label>
                                        <input type="text" class="form-control" name="conselho_nome" id="conselho_nome"
                                        required maxlength="255" minlength="10">
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="conselho_sigla">Sigla*</label>
                                        <input type="text" class="form-control" name="conselho_sigla" id="conselho_sigla" required minlength="3" maxlength="10">
                                    </div>
                                </div>                                                       
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="gestor_nome">Responsável</label>
                                    
                                        <input type="text" class="form-control" name="gestor_nome" id="gestor_nome" required maxlength="255" minlength="10">
                                    
                                        <small id="gestor_nome_help" class="help-text form-text text-muted">Nome do gestor na plataforma Normativas.</small>                                
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="gestor_email">Email</label>
                                        <div class="input-group">
                                            <span class="input-group-prepend">
                                                <span class="input-group-text"> <i class="fa fa-envelope"></i>
                                                </span>
                                            </span>
                                            <input type="email" class="form-control" name="gestor_email" id="gestor_email" required maxlength="255" minlength="10">
                                        </div>
                                        
                                        <small id="gestor_email_help" class="help-text form-text text-muted">Este email receberá uma nova senha de acesso caso seja enviado um novo convite.</small>                                
                                    </div>
                                </div> 
                            </div>
                        </div> <!-- end container-fluid-->
                    </div> <!-- end modal-body -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-lg" data-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-primary btn-lg">Atualizar e Enviar Convite</button>
                    </div>
                
                </div> <!-- end modal-content -->
            </form>
        </div>
    </div><!-- end modal-->
@stop
@push('js')
    <script src="{{ asset('js/app-unidades.js') }}"></script>
    <script src="{{ asset('vendor/mask/jquery.mask.min.js') }}"></script>
@endpush