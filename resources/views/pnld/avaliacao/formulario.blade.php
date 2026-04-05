@extends('layouts.app') @section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Formulário de Avaliação PNLD</h3>
        </div>
        <div class="card-body">
            
            {{-- Mensagem de Sucesso (Será exibida quando o Controller retornar a variável 'success') --}}
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            {{-- O Form aponta para a rota POST que criamos --}}
            <form action="{{ route('pnld.avaliacao.store') }}" method="POST">
                @csrf {{-- Obrigatório no Laravel para segurança --}}

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nome_avaliador" class="form-label">Nome do Avaliador</label>
                        <input type="text" class="form-control" id="nome_avaliador" name="nome_avaliador" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="codigo_obra" class="form-label">Código da Obra</label>
                        <input type="text" class="form-control" id="codigo_obra" name="codigo_obra" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="parecer" class="form-label">Parecer Técnico</label>
                    <textarea class="form-control" id="parecer" name="parecer" rows="6" placeholder="Escreva sua avaliação detalhada aqui..." required></textarea>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-success px-4">Salvar Avaliação</button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection