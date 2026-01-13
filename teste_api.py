import requests
import json
import random

# --- CONFIGURAÇÕES DE ENVIO ---
# O servidor Laravel deve estar rodando em http://127.0.0.1:8000
API_URL = "http://127.0.0.1:88/api/documentos" 

HEADERS = {
    'Content-Type': 'application/json', # Informa que o corpo da requisição é JSON
    'Accept': 'application/json'
}
# ------------------------------

def enviar_documento_teste():
    
    # Dados de Exemplo com os campos que o Laravel agora espera
    payload = {
        "titulo": f"Teste - {random.randint(1000, 9999)}",
        "url": f"http://127.0.0.1:88/doc/{random.randint(100, 999)}",
        "conteudo": "O conteúdo que será indexado pelo Elastic.",
        
        # OBRIGATÓRIO pelo log:
        "arquivo": "documento_crawler_temp.pdf",
    }
    
    
    print(f"Tentando salvar: {payload['titulo']}")
    print("-" * 40)

    try:
        # Envia a requisição POST para a API
        response = requests.post(API_URL, json=payload, headers=HEADERS)
        
        print(f"Status Code da Resposta: {response.status_code}")
        
        if response.status_code == 201:
            # 201 significa "Created" (Criado com Sucesso)
            print("\n✅ SUCESSO! O documento foi salvo no PostgreSQL.")
            print("   (O Laravel deve ter acionado a indexação automática para o site.)")
            print(f"   Resposta da API: {response.json()}")
            
        elif response.status_code == 422:
            # 422 significa "Unprocessable Entity" (Erro de validação)
            print("\n❌ ERRO DE VALIDAÇÃO (422): Os dados enviados estão incompletos ou incorretos.")
            print(f"   Detalhes: {response.json()}")
            
        else:
            print(f"\n❌ ERRO NA API: Falha na inserção. Status: {response.status_code}")
            print(f"   Resposta Completa: {response.text}")

    except requests.exceptions.ConnectionError:
        print("\n❌ ERRO DE CONEXÃO: O servidor Laravel não está acessível.")
        print("   Verifique se 'php artisan serve' ou 'sail up' está rodando.")
    except Exception as e:
        print(f"\n❌ ERRO DESCONHECIDO: {e}")

if __name__ == "__main__":
    enviar_documento_teste()