import requests
import re
from bs4 import BeautifulSoup
from urllib.parse import urljoin
import os
import base64
from crawler.service import DOWNLOAD_DIR, es, create_tags, create_ato_documento, INDEX_NAME, cursor, conn, HEADERS

#Talvez data

def main():
    BASE_URL = f"https://www2.ifal.edu.br/acesso-a-informacao/institucional/revisao-e-consolidacao-dos-atos-normativos/documentos"

    response = requests.get(BASE_URL, headers=HEADERS)
    soup = BeautifulSoup(response.content, "html.parser")

    # Procurar a div onde os links estão
    content_div = soup.find("div", id="content-core")

    if not content_div:
        print("Erro: Não foi possível encontrar a div com id='content'.")
        exit()

    pdfs = []

    # Encontrar todos os links dentro da div

    links = content_div.find_all("a", href=True)
    for a in links[:-1]:
        url = urljoin(BASE_URL, a["href"])  # Corrige URLs relativas
        data = ''
        if url.endswith(".pdf"):
            titulo = a.parent.get_text(strip=True)
            titulo_m = titulo.lower()

            if titulo_m.startswith('decreto'): 
                numero_doc = '10.139'
                ano = 2019
                tipo_doc = 4
                tipo_nome = 'Decreto'
                data = '2019-09-28'

            elif titulo_m.startswith('porta'): 
                regex = re.compile(r"\bNº?\s*(\d+[-/]?\d*)\b")
                match = regex.search(a.text)
                if match:
                    numero_doc = match.group(1)
                    ano = numero_doc[-4:]
                    tipo_doc = 10
                    tipo_nome = 'Portaria'
                    numero_doc = numero_doc[:-5]
                if titulo_m.endswith('ifal.'):
                    data = '2023-08-15'
                    
            else:
                numero_doc = '00'
                ano = 2022
                tipo_doc = 0
                tipo_nome = 'Indeterminado'

            pdfs.append({
                'titulo': titulo,
                'url': url,
                'numero': numero_doc,
                'tipo_id': tipo_doc,
                'tipo_nome': tipo_nome,
                'ano': ano,
                'ementa': f'{titulo} - Revisão e Consolidação dos Atos Normativos',
                'data': data or f'{ano}-01-01'
            })
    for pdf in pdfs:
        PDF_URL = pdf['url']
        TITULO_DOC = pdf['titulo']
        NUMERO = pdf['numero']
        ASSUNTO_ID = 4
        TIPO_ID = pdf['tipo_id']
        TIPO_NOME = pdf['tipo_nome']
        UNIDADE_ID = 1
        ANO = pdf['ano']
        EMENTA = pdf['ementa']
        DATA = pdf['data']
        try:
            # Verificar e criar o diretório para downloads
            os.makedirs(DOWNLOAD_DIR, exist_ok=True)
            # Pega o último nome da url que é o titulo do pdf
            filename = os.path.join(DOWNLOAD_DIR, PDF_URL.split("/")[-1])
            # Baixar PDF se não existir
            if not os.path.exists(filename):
                print(f"Baixando {PDF_URL}...")
                pdf_response = requests.get(PDF_URL)
                with open(filename, "wb") as f:
                    f.write(pdf_response.content)
            else:
                print(f"Arquivo já existe: {filename}. Pulando download.")
            # Criar ato_documento
            tags = 'Reitoria ' + EMENTA
            tags = create_tags(tags)
            ato_documento = create_ato_documento(os.path.basename(filename), TITULO_DOC, tags, ANO, BASE_URL, NUMERO, TIPO_NOME, EMENTA, DATA)
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
                INSERT INTO documentos (ano, titulo, numero, ementa, arquivo, url, data_publicacao, tipo_documento_id, user_id, assunto_id, unidade_id)
                VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
            """
            cursor.execute(query, (ANO, TITULO_DOC, f'{NUMERO}/{ANO}', EMENTA, elastic_id, PDF_URL, DATA, TIPO_ID, 1, ASSUNTO_ID, UNIDADE_ID))
            conn.commit()
            print(f"SALVO NO BANCO DE DADOS: {os.path.basename(filename)}")
        except Exception as e:
            print(f"Erro ao processar {PDF_URL}: {e}")
    print("Processo concluído.")

main()
