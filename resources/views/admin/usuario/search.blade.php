@extends('adminlte::page')

@section('title', 'Documentos IFAL')

@section('content_header')
    
@stop

@section('content')
    <div class="container-fluid">
        <ol class="breadcrumb">
            <li><a href="{{route('home')}}">Painel</a></li>
            <li> <a href="#" class="active"><a href="#">Usuários</a></li>
        </ol>

        @include('admin.includes.alerts')

        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Filtrar</div>
                    <div class="panel-body">
                        <form class="form-inline" method="GET" action="{{route('usuario-search')}}">
                            
                            <input type="text" id="q" name="q" class="form-control" 
                                value='{{$q}}'
                                placeholder="Nome ou email" aria-describedby="basic-addon1"/>
                            
                            <input type="text" id="unidadeQ" name="unidadeQ" class="form-control" 
                                value='{{$unidadeQ}}'
                                placeholder="Unidade ou Sigla" aria-describedby="basic-addon1"/>

                            <select class="form-control" name="ordenarPor" id="ordenarPor">
                                <option value="confirmado_em" @if($ordenarPor == 'confirmado_em') selected @endif>Ordenar por confirmação</option>
                                <option value="convidado_em" @if($ordenarPor == 'convidado_em') selected @endif>Ordenar por convite</option>
                                <option value="ultimo_acesso_em" @if($ordenarPor == 'ultimo_acesso_em') selected @endif>Ordenar por último acesso</option>
                                <option value="updated_at" @if($ordenarPor == 'updated_at') selected @endif>Ordenar por atualização</option>
                            </select>

                            <select class="form-control" name="ordemPor" id="ordemPor">
                                <option value="desc" @if($ordemPor == 'desc') selected @endif>Ordenar descrescente</option>
                                <option value="asc" @if($ordemPor == 'asc') selected @endif>Ordenar crescente</option>
                            </select>

                            <button type="submit" class="btn btn-primary">Pesquisar</button>

                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="incluirDesabilitados" id="incluirDesabilitados" value="true" @if($incluirDesabilitados) checked @endif/>
                                <label class="form-check-label" for="emailCadastrado">Incluir desabilitados</label>
                            </div>

                            {!! csrf_field() !!}
                        </form>
                    </div>
                    @if($ordenarPor == 'ultimo_acesso_em' || $ordenarPor == 'convidado_em')
                    <div class="panel-footer">
                        <div class="alert alert-warning">
                            <strong>Aviso!</strong> Ordenando por '{{$ordenarPor}}', usuários que não realizeram {{$ordenarPor}} não aparecem na lista
                        </div>
                    </div>
                    @endif
                
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                            Resultados ({{$usuarios->total()}})
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped table-hover table-condensed">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Unidade</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($usuarios as $u)
                                <tr @if ($u->trashed()) class='danger' @endif>
                                    <td>
                                        <a href="{{route('usuarios',['id' => $u->id])}}">
                                            {{ $u->id }}
                                        </a>
                                    </td>
                                    <td>
                                        {{$u->name}}
                                    </td>
                                    <td>{{$u->email}}</td>
                                    <td>{{$u->unidade->nome}}</td>
                                    <td>
                                        <span class="label label-info" style="display:block;color:white;">
                                            Criado em:
                                            {{$u->created_at}}
                                        </span>
                                        @if($u->created_at != $u->updated_at)
                                        <span class="label label-info" style="display:block;color:white;">
                                            Atualizado em:
                                           {{$u->updated_at}}
                                        </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($u->convidado_em)
                                            <span class="label label-info" style="display:block;color:white;">
                                                Convidado em: {{$u->convidado_em}}
                                            </span>
                                        @endif

                                        @if($u->confirmado_em)
                                            <span class="label label-info" style="display:block;color:white;">
                                                Confirmado em: {{$u->confirmado_em}}
                                            </span>
                                        @else
                                            <span class="label label-warning"style="display:block;color:white;">
                                                Não confirmado!
                                            </span>
                                        @endif
                                        
                                        @if($u->ultimo_acesso_em)
                                            <span class="label label-info"style="display:block;color:white;">
                                                Último acesso:
                                                {{$u->ultimo_acesso_em}}
                                            </span> 
                                        @else
                                            <span class="label label-warning"style="display:block;color:white;">
                                                Sem acessos
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if (auth()->user()->id != $u->id && (auth()->user()->isGestor() || auth()->user()->isAdmin()))
                                        <a href="{{route('usuario-reconvidar',$u->id)}}" class="btn 
                                            {{$u->confirmado_em !== null ? 'btn-primary':'btn-danger'}} ">Enviar novo convite
                                        </a>    
                                        @endif
                                    </td>
                                </tr>
                                @empty

                                @endforelse
                            </tbody>
                        </table>
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-lg-6 no-padding">
                                    {{ $usuarios->appends(request()->query())->links() }}
                                </div>                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
@stop