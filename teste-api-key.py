import requests
import json
import datetime

TOKEN_SECRETO ="Crawlerskey12345"

url_api = "http://localhost:88/api/documentos/store"

agora = datetime.datetime.now()
titulo_teste = "Teste API Compativel - " + str(agora)
numero_teste = "API-" + str(agora.second)

dados_para_enviar = {
    "titulo": titulo_teste,
    "data_publicacao": "2025-11-05",
    "ementa": "Este é o conteúdo de teste da ementa.",
    "arquivo": "http://exemplo.com/arquivo.pdf",
    "numero": numero_teste,
    "tipo_documento_id": 1, 
    "assunto_id": 1         
}

headers = {
    'Authorization': 'Bearer ' + TOKEN_SECRETO,
    'Content-Type': 'application/json',
    'Accept': 'application/json'
}

print("Enviando requisição...")
try:
    response = requests.post(url_api, data=json.dumps(dados_para_enviar), headers=headers)

    print("\n========= RESPOSTA DO LARAVEL =========\n")
    print("Código de Status: " + str(response.status_code))
    print("Corpo da Resposta:")
    print(response.text)

except requests.exceptions.ConnectionError as e:
    print("\nERRO DE CONEXÃO! Verifique se o Sail está rodando.")
    print(str(e))
 
