import requests
from bs4 import BeautifulSoup
import os
import base64
import sys

# Adjust path to import from parent dir
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '..')))
from crawler_connections import DOWNLOAD_DIR, es, create_tags, create_ato_documento, INDEX_NAME, cursor, conn, HEADERS

def main(ano):
    URLS = [
        f"https://www2.ifal.edu.br/o-ifal/ensino/editais/{ano}",
        f"https://www2.ifal.edu.br/o-ifal/extensao/editais/editais-{ano}",
        f"https://www2.ifal.edu.br/o-ifal/pesquisa-pos-graduacao-e-inovacao/editais/editais-{ano}-1",
        "https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/concursos",
        "https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/remocao"
    ]

    for BASE_URL in URLS:
        print(f"Iniciando processo para {ano} em {BASE_URL}...")

        # Etapa 1: Raspagem dos PDFs
        print("Raspando PDFs da página...")
        response = requests.get(BASE_URL, headers=HEADERS)
        if response.status_code != 200:
            print(f"Erro ao acessar {BASE_URL}: status code {response.status_code}")
            continue

        soup = BeautifulSoup(response.content, "html.parser")
        pdfs = []

        for a in soup.find_all("a", href=True):
            pdf_link = a["href"]
            if pdf_link.endswith("/view"):
                pdf_link = pdf_link[:-5]
            if pdf_link.endswith(".pdf"):
                p_tag = a.find_parent("p")
                titulo = p_tag.get_text(strip=True) if p_tag else a.parent.get_text(strip=True)
                pdf_link = pdf_link if pdf_link.startswith("http") else BASE_URL + pdf_link

                pdfs.append({
                    "titulo": titulo,
                    "url": pdf_link,
                    "ementa": f'{titulo} - Editais Gerais IFAL'
                })
        
        print(f"Foram encontrados {len(pdfs)} PDFs na página.")

        # Etapa 2: Processamento de cada PDF
        for pdf in pdfs:
            pdf_url = pdf['url']
            titulo_doc = pdf['titulo']
            ementa = pdf['ementa']

            try:
                os.makedirs(DOWNLOAD_DIR, exist_ok=True)
                filename = os.path.join(DOWNLOAD_DIR, pdf_url.split("/")[-1])

                if not os.path.exists(filename):
                    print(f"Baixando {pdf_url}...")
                    pdf_response = requests.get(pdf_url)
                    with open(filename, "wb") as f:
                        f.write(pdf_response.content)
                else:
                    print(f"Arquivo já existe: {filename}. Pulando download.")

                # Criar ato_documento e injetar Oficial/Score
                tags = create_tags(ementa)
                ato_documento = create_ato_documento(os.path.basename(filename), titulo_doc, tags, str(ano), BASE_URL, '00', 'Edital', ementa)
                ato_documento['manual_score'] = 1000
                ato_documento['oficial'] = True
                
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

                # Salvar no banco de dados (tipo_documento_id=1, user_id=1, assunto_id=0, unidade_id=1)
                query = """
                    INSERT INTO documentos (titulo, ementa, arquivo, url, tipo_documento_id, user_id, assunto_id, unidade_id, manual_score, oficial)
                    VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
                """
                cursor.execute(query, (titulo_doc, ementa, elastic_id, pdf_url, 1, 1, 0, 1, 1000, True))
                conn.commit()
                print(f"SALVO NO BANCO DE DADOS: {os.path.basename(filename)}")

            except Exception as e:
                print(f"Erro ao processar {pdf_url}: {e}")
                conn.rollback()

    print("Processo concluído.")

# Chamando para 2026 conforme requisitado
main(2026)
