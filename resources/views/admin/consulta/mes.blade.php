@extends('adminlte::page')

@section('title', 'Documentos IFAL')

@section('content_header')

@stop

@section('content')
<div class="container">
    <ol class="breadcrumb">
        <li><a href="{{route('home')}}">Painel</a></li>
        <li><a href="#" class="active"><a href="#">Consultas nos Meses Top 20</a></li>
    </ol>

    <div class="row">
        @include('admin.includes.alerts')
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <table id="tbl-consultas" class="table table-striped table-hover table-condensed">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Termos</th>
                                <th>Período</th>
                                <th>Quantidade</th>                                 
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($consultasMes as $c)
                                <tr>
                                    <td>{{$c->ranking}}</td>
                                    <td>
                                        <a href="{{ route('consultas', ['q' => $c->termo ] ) }}">
                                        {{$c->termo}}
                                        </a>
                                    </td>
                                    <td>{{$c->data_label}}</td>
                                    <td>{{$c->quantidade}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-6 no-padding">
                                {{ $consultasMes->appends(request()->query())->links() }}
                            </div>                                
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="alert alert-success col-lg-8 col-lg-offset-1">
                <p>Até novembro de 2020, os avanços nas páginas de resultados contabilizavam como novas consultas. 
                Posteriomente, o entendimento é que seja contabilizada apenas uma consulta.</p>
            </div>
        </div>
    </div>

</div>
@stop