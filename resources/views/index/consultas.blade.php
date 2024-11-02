@extends('layouts.master')

@section('content')

<section id="header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8 offset-lg-2 text-right p-0 ">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-pill btn-login m-1 mt-2">Home <i class="fa fa-user badge-info"></i></a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-pill btn-login m-1 mt-2">Entrar <i class="fa fa-user badge-info"></i></a>
                        
                    @endauth
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 text-center">                        
                <h1>
                    <a href="{{route('index')}}">
                        <!-- <img src="/img/normativos-logo.png" srcset="/img/normativos-logo@2x.png 3x" alt="Normativas" /> -->
                    </a>
                </h1>
                <h3><small class="text-muted">Termos mais pesquisados nos últimos meses</small></h3>
            </div>
        </div>
    </div>
</section>

<section>
    <div class="container">
        @include('admin.includes.alerts')        
    </div>
</section>


<section id="results">
    <div class="container">
        <div class="row">
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
                            <a href="{{ route('index', ['query' => $c->termo ] ) }}">
                            {{$c->termo}}
                            </a>
                        </td>
                        <td>{{$c->data_label}}</td>
                        <td>{{number_format($c->quantidade,0, ",",".")}}</td>
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
</section>
        

@endsection