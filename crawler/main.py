# TODO
# separar o crawler num diretorio próprio
# Preencer as lacunas no ato e na query do bd
# raspagem de dados em massa

import requests
from bs4 import BeautifulSoup
from elasticsearch import Elasticsearch
import psycopg2
import os
import base64
from itertools import zip_longest

# Configurações
DOWNLOAD_DIR = "crawler/pdfs"
ELASTIC_URL = "http://elasticsearch:9200"
INDEX_NAME = "documentos_ifal"
DB_CONFIG = {
    "dbname": "postgres",
    "user": "postgres",
    "password": "password",
    "host": "pgsql",
    "port": "5432"
}
HEADERS = {"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36 OPR/116.0.0.0"}

##########################################################################################
BASE_URL = "https://www2.ifal.edu.br/o-ifal/ensino/editais/2024"  # URL da página com PDFs
ANO = 2024
TAGS = ["Ensino", "Processo Seletivo", "Seleção", "Projeto de Ensino"]
ASSUNTO = 1
# 0 - ASSUNTO DESCONHECIDO;
# 1 - ENSINO;
# 2 - PESQUISA;
# 3 - EXTENSÃO;
# 4 - RECURSOS HUMANOS;
# 5 - BIBLIOTECA;
# 6 - MONITORIA E AÇÕES INTEGRADAS
##########################################################################################

# Conexões
es = Elasticsearch(ELASTIC_URL)
conn = psycopg2.connect(**DB_CONFIG)
cursor = conn.cursor()

def create_ato_documento(filename, titulo):
    """
    Função para criar os metadados associados ao documento.
    """
    # Personalize os valores abaixo de acordo com os requisitos
    return {
        "ano": ANO,
        "arquivo": filename,
        "ato_id": "A002",
        "data_publicacao": f"{ANO}-01-11",
        "ementa": "Descrição do documento",
        "fonte": {
            "esfera": "Campus",
            "orgao": "Instituto Federal de Alagoas",
            "sigla": "IFAL",
            "uf": "AL",
            "uf_sigla": "AL",
            "url": BASE_URL
        },
        "numero": f"01/{ANO}",
        "tags": TAGS,
        "tipo_doc": "edital",
        "titulo": titulo
    }

# Fluxo Principal
def main():
    print("Iniciando processo...")

    # Etapa 1: Raspagem dos PDFs
    print("Raspando PDFs da página...")
    response = requests.get(BASE_URL, headers=HEADERS)
    soup = BeautifulSoup(response.content, "html.parser")
    pdf_links = []
    titulo_links = []

    for link in soup.find_all("a", href=True):
        href = link["href"]
        if href.endswith(".pdf"):
            titulo_links.append(link.text)
            pdf_links.append(href if href.startswith("http") else BASE_URL + href)

    print(f"Encontrados {len(pdf_links)} PDFs.")

    # Etapa 2: Processamento de cada PDF
    for pdf_url, titulo_doc in zip_longest(pdf_links, titulo_links, fillvalue='Sem-Titulo') :
        try:
            # Verificar e criar o diretório para downloads
            os.makedirs(DOWNLOAD_DIR, exist_ok=True)
            filename = os.path.join(DOWNLOAD_DIR, pdf_url.split("/")[-1])

            # Baixar PDF se não existir
            if not os.path.exists(filename):
                print(f"Baixando {pdf_url}...")
                pdf_response = requests.get(pdf_url)
                with open(filename, "wb") as f:
                    f.write(pdf_response.content)
            else:
                print(f"Arquivo já existe: {filename}. Pulando download.")

            # Criar ato_documento
            ato_documento = create_ato_documento(os.path.basename(filename), titulo_doc)
            print(f"Ato Documento criado: {ato_documento}")

            # Indexar no Elasticsearch
            with open(filename, "rb") as f:
                encoded_pdf = base64.b64encode(f.read()).decode("utf-8")
            doc = {
                "filename": os.path.basename(filename),
                "data": encoded_pdf,
                "ato": ato_documento,
                "attachment": {
                    "content": encoded_pdf
                }
            }
            response = es.index(index=INDEX_NAME, pipeline="attachment", body=doc)
            elastic_id = response["_id"]
            print(f"Documento indexado no Elasticsearch: {elastic_id}")

            # Salvar no banco de dados
            query = f"""
                INSERT INTO documentos (ano, titulo, arquivo, tipo_documento_id, user_id, assunto_id, unidade_id)
                VALUES (%s, %s, %s, %s, %s, %s, %s)
            """
            cursor.execute(query, (ANO, titulo_doc, elastic_id, 1, 1, ASSUNTO, 1))
            conn.commit()
            print(f"Salvo no banco de dados: {os.path.basename(filename)}")

        except Exception as e:
            print(f"Erro ao processar {pdf_url}: {e}")

    print("Processo concluído.")

if __name__ == "__main__":
    main()