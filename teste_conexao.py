from elasticsearch import Elasticsearch
import urllib3

# Desabilita avisos de SSL
urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)

URL = "https://elastic.pnld-avaliacao-dev.nees.ufal.br:443"
USER = "elastic"
PASS = "SXTt4rk05uDq"

try:
    print(f"🔌 Conectando em: {URL}...")
    es = Elasticsearch(URL, basic_auth=(USER, PASS), verify_certs=False)
    
    # Tenta contar
    qtd = es.count(index='documentos_ifal')['count']
    print(f"\n🎉 SUCESSO! Total de documentos na nuvem: {qtd}")

except Exception as e:
    print(f"\n❌ DEU ERRO: {e}")
