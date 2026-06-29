<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $message = $request->input('message');
        $documentId = $request->input('document_id');
        $isNormativa = $request->input('is_normativa', false);

        // TODO no futuro: 
        // 1. Pegar $message e mandar para http://localhost:5005/webhooks/rest/webhook
        // 2. O Rasa, via action_session_start, poderá receber o $documentId para manter contexto.

        Log::info("Chat message recebida para DocID {$documentId}: {$message}");

        // Simulando delay de processamento do RASA
        usleep(1000000); // 1 segundo

        return response()->json([
            'reply' => 'Olá! Eu sou a Iúna. Esta é uma interface preparada para o meu cérebro (RASA). No futuro serei conectada ao modelo LLM para responder dúvidas com base no documento que você está lendo (ID: ' . $documentId . ').'
        ]);
    }
}
