import requests
from bs4 import BeautifulSoup
import os
import base64
from crawler.config import *

# Fluxo Principal
def main(ANO):
    BASE_URL = config_geral(ANO)[ASSUNTO]['ANTIGOS_URL']

    print("Iniciando processo...")

    # Etapa 1: Raspagem dos PDFs
    print("Raspando PDFs da página...")
    response = requests.get(BASE_URL, headers=HEADERS)
    soup = BeautifulSoup(response.content, "html.parser")
    pdfs = []

    for link in soup.find_all("a", href=True):
        pdf_link = link["href"]
        if pdf_link.endswith("/view"):
            pdf_link = pdf_link[:-5]
        if pdf_link.endswith(".pdf"):
            p_tag = link.find_parent("p")  # Pega o pai <p> como título
            titulo = p_tag.get_text(strip=True) if p_tag else link.parent.get_text(strip=True)
            pdf_link = pdf_link if pdf_link.startswith("http") else BASE_URL + pdf_link

            pdfs.append({
                "titulo": titulo,
                "url": pdf_link
            })
    

    # Etapa 2: Processamento de cada PDF
    for pdf in pdfs:
        pdf_url = pdf['url']
        titulo_doc = pdf['titulo']
        try:
            # Verificar e criar o diretório para downloads
            os.makedirs(DOWNLOAD_DIR, exist_ok=True)

            # Pega o último nome da url que é o titulo do pdf
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
            tags = create_tags(titulo_doc)
            ato_documento = create_ato_documento(os.path.basename(filename), titulo_doc, tags, ANO, BASE_URL)
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
            print(f"DOCUMENTO INDEXADO NO Elasticsearch: {elastic_id}")

            # Salvar no banco de dados
            query = """
                INSERT INTO documentos (ano, titulo, ementa, arquivo, tipo_documento_id, user_id, assunto_id, unidade_id)
                VALUES (%s, %s, %s, %s, %s, %s, %s, %s)
            """
            cursor.execute(query, (ANO, titulo_doc, titulo_doc, elastic_id, 1, 1, ASSUNTO_ID, UNIDADE_ID))
            conn.commit()
            print(f"SALVO NO BANCO DE DADOS: {os.path.basename(filename)}")

        except Exception as e:
            print(f"Erro ao processar {pdf_url}: {e}")

    print("Processo concluído.")

for ano in range(2025, 2019, -1):  # De 2025 até 2020
    main(ano)
