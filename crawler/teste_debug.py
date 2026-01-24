from crawler_connections import es, INDEX_NAME
import sys

print(f"--- Testando conexão com índice: {INDEX_NAME} ---")

# 1. Tenta verificar se o índice existe e se temos acesso
try:
    info = es.info()
    print(f"[OK] Conectado ao cluster: {info['name']}")
except Exception as e:
    print(f"[ERRO] Falha na conexão inicial: {e}")
    sys.exit(1)

# 2. Tenta indexar um documento simples (sem PDF/Pipeline) para testar permissão de escrita
doc_teste = {
    "titulo": "Teste de Debug Manual",
    "descricao": "Verificando se o Python consegue escrever no Elastic",
    "tags": ["debug", "teste"]
}

try:
    resp = es.index(index=INDEX_NAME, document=doc_teste)
    print(f"[SUCESSO] Documento indexado! ID: {resp['_id']}")
    print(f"Resultado: {resp['result']}")
except Exception as e:
    print(f"[ERRO] Falha ao indexar documento: {e}")

    # Dica comum: Se o erro for 'Forbidden' ou '403', o usuário pode ser apenas leitura
    # Se o erro for 'MapperParsingException', os campos enviados estão errados