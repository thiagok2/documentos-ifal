import sys
import os
sys.path.append(os.path.dirname(os.path.abspath(__file__)))
from crawler_connections import es, INDEX_NAME, cursor, conn

def limpar_lixo():
    # Buscar documentos recentes no ES (que têm manual_score = 1000)
    query = {
        "query": {
            "term": {
                "ato.manual_score": 1000
            }
        },
        "size": 10000
    }
    
    try:
        result = es.search(index=INDEX_NAME, body=query)
        hits = result['hits']['hits']
        print(f"Encontrados {len(hits)} documentos com manual_score=1000 no ES.")
        
        removidos = 0
        for hit in hits:
            es_id = hit['_id']
            # Verifica se o ID existe no Postgres
            cursor.execute("SELECT id FROM documentos WHERE arquivo = %s", (es_id,))
            row = cursor.fetchone()
            
            if not row:
                print(f"Lixo encontrado no ES (órfão, sem DB): {es_id} | Título: {hit['_source']['ato'].get('titulo')}")
                es.delete(index=INDEX_NAME, id=es_id)
                removidos += 1
                
        print(f"Limpeza concluída. {removidos} duplicatas removidas do Elasticsearch.")
    except Exception as e:
        print(f"Erro: {e}")

if __name__ == "__main__":
    limpar_lixo()
