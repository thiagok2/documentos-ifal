@extends('layouts.master')

@section('content')

@include('admin.includes.alerts')

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
                <div class="card card-primary" style="border-top: 5px solid #19882c;">
                    <div class="card-body">
                        <div class="row">
                            <!-- Metadados -->
                            <div class="col-md-4">
                                <h4 style="color: #19882c; word-break: break-all;">
                                    <i class="fa fa-file-pdf"></i> {{ $doc['titulo'] ?? 'Artefato Sem Título' }}
                                </h4>
                                <hr>
                                
                                <h6><strong>Data de Upload:</strong></h6>
                                <p class="text-muted">{{ date('d/m/Y \à\s H:i', strtotime($doc['created_at'])) }}</p>

                                @if(!empty($doc['especificacao']))
                                    <h6><strong>Especificação:</strong></h6>
                                    <p>{{ $doc['especificacao'] }}</p>
                                @endif

                                <h6><strong>Resumo:</strong></h6>
                                <p class="text-justify">{{ $doc['resumo'] }}</p>

                                @if (!empty($doc['entidades']))
                                    <h6><strong>Entidades Mapeadas:</strong></h6>
                                    <div class="mb-4">
                                        @foreach ($doc['entidades'] as $entidade)
                                            <span class="badge badge-info mb-1">{{ $entidade['texto'] }}</span>
                                        @endforeach
                                    </div>
                                @endif

                                <a href="{{ route('artefato-download', $doc['id']) }}" class="btn btn-primary btn-block mb-3" target="_blank">
                                    <i class="fa fa-download"></i> Baixar Arquivo Original
                                </a>
                            </div>

                            <!-- PDF Viewer -->
                            <div class="col-md-8">
                                <div class="embed-responsive" style="height: 600px; border: 1px solid #ddd; border-radius: 5px;">
                                    <iframe class="embed-responsive-item" src="{{ route('artefato-view-pdf', $doc['id']) }}#toolbar=1" allowfullscreen style="width: 100%; height: 100%;"></iframe>
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
