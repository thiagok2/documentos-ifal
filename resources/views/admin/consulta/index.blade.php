@extends('adminlte::page')

@section('title', 'Documentos ifal')

@section('content_header')

@stop

@section('content')
<div class="container-fluid">
  <ol class="breadcrumb">
    <li class="breadcrumb-item active"><a href="{{route('home')}}">Painel</a></li>
    <li class="breadcrumb-item active"> <a href="#" class="active"><a href="#">Consultas</a></li>
  </ol>
  <div class="row">
    @include('admin.includes.alerts')
  </div>

  <div class="row">
    <div class="col-lg-2">
      <a href="{{route('consultasMes')}}" class="btn btn-primary"><i class="fa fa-plus"></i>Consultas por mês</a>
    </div>
  </div>
  
  <div class="row">
    <div class="col-lg-12 mt-3">
        <div class="card card-default">
            <div class="card-header">Filtrar</div>
            <div class="card-body">
              <form class="form-inline" method="GET" action="{{route('consultas')}}">
                <input type="text" id="q" name="q" class="form-control" value="{{$q}}"
                    placeholder="Termo de pesquisa" aria-describedby="basic-addon1">

                  <button type="submit" class="btn btn-primary">Pesquisar</button>
                  <a href="{{route('consultas')}}" class="btn btn-primary">Limpar</a>
                    {!! csrf_field() !!}
              </form>
            </div>
        </div>
    </div>

      <div class="col-lg-12">
        <div class="box box-info">
          <div class="box-header">
            Resultados ({{$consultas->total()}})
          </div>

          <div class="box-body no-padding">
            <table id="tbl-consultas" class="table table-striped table-hover table-condensed">
              <thead>
                <tr>
                    <th>#</th>
                    <th>Consulta</th>
                    <th>Data</th>
                    <th></th>
                    <th>Localização</th>                                    
                </tr>
              </thead>
              <tbody>
              @foreach($consultas as $c)
                <tr>
                  <td>{{$c->id}}</td>
                  <td>
                    <a href="{{ route('consultas', ['q' => $c->termos ] ) }}">
                        {{$c->termos}}
                    </a>
                  </td>
                  <td>{{$c->data_consulta}}</td>
                  <td>{{$c->ip}}</td>
                  <td>{{$c->regiao}} ({{$c->cidade}})</td>       
                </tr>
              @endforeach
              </tbody>
            </table>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-6 no-padding mt-2">
                        {{ $consultas->appends(request()->query())->links() }}
                    </div>                                
                </div>
            </div>
          </div>

        </div>
      </div>
  </div>
</div>

@stop