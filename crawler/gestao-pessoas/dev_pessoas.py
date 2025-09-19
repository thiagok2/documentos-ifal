import re
import requests
from bs4 import BeautifulSoup
import os
import base64
from crawler.crawler_connections import DOWNLOAD_DIR, es, create_tags, create_ato_documento, INDEX_NAME, cursor, conn, HEADERS

response = requests.get('https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/legislacao-e-normas/capacitacao', headers=HEADERS)
soup = BeautifulSoup(response.content, "html.parser")

pdfs = []
def main():
    for callout in soup.find_all('p', class_='callout'):
        link_tag = callout.find('a')
        if link_tag:
            titulo = link_tag.text.strip()
            url = link_tag['href']
            if titulo.endswith('2014'):
                ano = 2014
                numero = '2.909'
                data = '2014-11-27'
                tipo_nome = 'Portaria'
                tipo_id = 10
            elif titulo.startswith('Resolu'):
                ano = 2019
                numero = '28-CS'
                data = '2019-10-31'
                tipo_nome = 'Resolução'
                tipo_id = 12
            elif titulo.endswith('2019') and titulo.startswith('Decreto'):
                ano = 2019
                numero = '9.991'
                data = '2019-08-28'
                tipo_nome = 'Decreto'
                tipo_id = 4
            elif titulo.endswith('ME'):
                ano = 2021
                numero = '21'
                data = '2021-02-01'
                tipo_nome = 'Instrução Normativa'
                tipo_id = 7
            elif titulo.endswith('2022'):
                ano = 2022
                numero = '06'
                data = '2022-02-01'
                tipo_nome = 'Portaria'
                tipo_id = 10
            elif titulo.endswith('2025'):
                ano = 2025
                numero = '12.374'
                data = '2025-02-01'
                tipo_nome = 'Decreto'
                tipo_id = 4

            # Pegando o resumo que está logo abaixo do link
            ementa_tag = callout.find_next_sibling(['p', 'span'])
            ementa = ementa_tag.text.strip() if ementa_tag else ""

            pdfs.append({
                'titulo': titulo,
                'numero': numero,
                'ano': ano,
                'ementa': f'{titulo} - {ementa} - Desenvolvimento de Pessoas',
                'url': url,
                'data': data,
                'tipo_nome': tipo_nome,
                'tipo_id': tipo_id,
            })

    for pdf in pdfs:
        PDF_URL = pdf['url']
        TITULO_DOC = pdf['titulo']
        NUMERO = pdf['numero']
        ASSUNTO_ID = 4
        TIPO_ID = pdf['tipo_id']
        TIPO_NOME = pdf['tipo_nome']
        UNIDADE_ID = 1
        EMENTA = pdf['ementa']
        ANO = pdf['ano']
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
            ato_documento = create_ato_documento(os.path.basename(filename), TITULO_DOC, tags, ANO, PDF_URL, NUMERO, TIPO_NOME, EMENTA, DATA)
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
            print(doc)
            response = es.index(index=INDEX_NAME, pipeline="attachment", body=doc)
            
            elastic_id = response["_id"]
            print(f"DOCUMENTO INDEXADO NO Elasticsearch: {elastic_id}")
            # Salvar no banco de dados
            query = """
                INSERT INTO documentos (ano, titulo, numero, ementa, arquivo, url, data_publicacao, tipo_documento_id, user_id, assunto_id, unidade_id)
                VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
            """
            cursor.execute(query, (ANO, TITULO_DOC, f'{NUMERO}/{ANO}', EMENTA, elastic_id,PDF_URL, DATA, TIPO_ID, 1, ASSUNTO_ID, UNIDADE_ID))
            conn.commit()
            print(f"SALVO NO BANCO DE DADOS: {os.path.basename(filename)}")
        except Exception as e:
            print(f"Erro ao processar {PDF_URL}: {e}")
    print("Processo concluído.")
         
main()