<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PnldAvaliacaoController extends Controller
{
    // Método para EXIBIR o formulário
    public function index()
    {
        // Se precisar buscar dados no banco para preencher selects (ex: lista de escolas), faça aqui
        // $escolas = Escola::all();
        
        return view('pnld.avaliacao.formulario'); 
        // return view('pnld.avaliacao.formulario', compact('escolas')); // se for passar variáveis
    }

    // Método para SALVAR os dados do formulário
    public function store(Request $request)
    {
        // Aqui você vai receber os dados que vieram do form
        $dados = $request->all();

        // Lógica de validação e salvamento no banco de dados entrará aqui no futuro
        // ...

        // Redireciona de volta com uma mensagem de sucesso temporária
        return redirect()->back()->with('success', 'Avaliação enviada com sucesso! (Ainda não está salvando no banco)');
    }
}