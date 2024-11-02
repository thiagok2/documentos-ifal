
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
                        <!--
                        <a href="{{ route('register') }}">Registrar</a>
                        -->
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
                <h3><small class="text-muted">Documentos mais baixados nos últimos meses</small></h3>
            </div>
        </div>
    </div>
</section>
<!-- end header -->

<section id="results">
    <div class="container-fluid">
        <div class="row mb-4">
            @foreach($downloads as $d)
            <div class="col-4">
                <div class="card mt-3">
                    <div class="card-header">
                       
                        <a href="/normativa/view/{{ $d->arquivo }}">
                            {{$d->titulo}}
                        </a>
                        <span class="badge bg-dark pull-right text-white">{{$d->downloads}}</span>
                        
                    </div>

                    <div class="card-body small">
                    {{$d->ementa}}
                    </div>

                    <div class="card-footer small text-muted">
                        <a href="{{route('unidades-page',$d->friendly_url)}}">
                            <i class="fa fa-external-link"></i> {{$d->unidade}} ({{$d->sigla}})
                        </a>
                        
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="row">
            <div class="col-lg-12 no-padding">
                {{ $downloads->appends(request()->query())->links() }}
            </div>                                
        </div>
    </div>
    </section>

@endsection