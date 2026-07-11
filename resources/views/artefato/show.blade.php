@extends('layouts.master')

@section('content')

@include('admin.includes.alerts')

<style>
    .btn-custom-green {
        background-color: #19882c !important;
        border-color: #19882c !important;
        color: #ffffff !important;
    }
    .btn-custom-green:hover, .btn-custom-green:focus, .btn-custom-green:active {
        background-color: #025310 !important;
        border-color: #025310 !important;
        color: #ffffff !important;
        box-shadow: 0 0 0 0.2rem rgba(25, 136, 44, 0.25) !important;
    }
    .btn-outline-custom-green {
        color: #19882c !important;
        border-color: #19882c !important;
        background-color: transparent !important;
    }
    .btn-outline-custom-green:hover, .btn-outline-custom-green:focus, .btn-outline-custom-green:active {
        color: #ffffff !important;
        background-color: #19882c !important;
        border-color: #19882c !important;
        box-shadow: 0 0 0 0.2rem rgba(25, 136, 44, 0.25) !important;
    }
    .btn-custom-green:focus, .btn-outline-custom-green:focus {
        outline: none !important;
    }
</style>

<header id="header-buscado" style="background-image: radial-gradient(ellipse at center, #19882c 1%, #025310 100%); padding: 20px 0;">
    <div class="container-fluid">
        <div class="row">
            <div class="col-6 col-lg-8 offset-lg-1">
                <a href="{{ route('index') }}">
                    <img src="/img/logo.png" alt="CentralDoc" class="logo-img" style="height: 50px;" />
                </a>
                <span class="text-white ml-3" style="font-size: 1.5rem; vertical-align: middle;">| Detalhes do Artefato</span>
            </div>
            <div class="col-6 col-lg-2 text-right">
                <a href="{{ route('artefatos-search') }}" style="color: aliceblue !important" class="btn btn-outline-secondary btn-pill btn-login mt-2">Voltar à Busca <i class="fa fa-arrow-left"></i></a>
            </div>
        </div>
    </div>
</header>

<section id="detalhes-conteudo" class="mt-4 mb-5">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-10 offset-lg-1">
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 0.5rem; overflow: hidden;">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-2" style="border-top: 5px solid #19882c !important;">
                        <h4 class="m-0 font-weight-bold" style="color: #19882c; word-break: break-all;">
                            <i class="fa fa-file-pdf text-danger mr-2"></i> {{ $doc['titulo'] ?? 'Artefato Sem Título' }}
                        </h4>
                    </div>
                    <div class="card-body pt-2">
                        <div class="row">
                            <!-- Metadados -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <span class="text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; font-weight: 600;">Data de Upload</span><br>
                                    <span class="text-secondary" style="font-size: 0.95rem;">
                                        <i class="fa fa-calendar-alt mr-1"></i>{{ date('d/m/Y \à\s H:i', strtotime($doc['created_at'])) }}
                                    </span>
                                </div>

                                @if(!empty($doc['especificacao']))
                                    <div class="mb-3">
                                        <span class="text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; font-weight: 600;">Especificação</span><br>
                                        <span class="text-dark" style="font-size: 0.95rem;">{{ $doc['especificacao'] }}</span>
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <span class="text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; font-weight: 600;">Resumo</span><br>
                                    <p class="text-justify text-secondary mb-0" style="font-size: 0.95rem; line-height: 1.5;">{{ $doc['resumo'] }}</p>
                                </div>

                                @if (!empty($doc['entidades']))
                                    <div class="mb-4">
                                        <span class="text-muted text-uppercase d-block mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px; font-weight: 600;">Entidades Mapeadas</span>
                                        @foreach ($doc['entidades'] as $entidade)
                                            <span class="badge badge-pill badge-light border text-muted mr-1 px-2 py-1 mb-1" style="font-weight: 500;">
                                                <i class="fa fa-tag mr-1" style="color: #19882c;"></i>{{ $entidade['texto'] }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                <a href="{{ route('artefato-download', $doc['id']) }}" class="btn btn-outline-custom-green btn-block mb-3 mt-4" target="_blank">
                                    <i class="fa fa-download"></i> Baixar Arquivo Original
                                </a>
                            </div>

                            <!-- PDF Viewer -->
                            <div class="col-md-8">
                                <div class="embed-responsive shadow-sm" style="height: 700px; border: 1px solid #e0e0e0; border-radius: 0.5rem; overflow: hidden;">
                                    <iframe class="embed-responsive-item" src="{{ route('artefato-view-pdf', $doc['id']) }}#toolbar=1" allowfullscreen style="width: 100%; height: 100%; border: none;"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@include('includes.chat', ['document_id' => $doc['id'], 'is_normativa' => false])

@endsection
