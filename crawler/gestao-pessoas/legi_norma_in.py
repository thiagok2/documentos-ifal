import re
import requests
from bs4 import BeautifulSoup
import os
import base64
from crawler.service import DOWNLOAD_DIR, es, create_tags, create_ato_documento, INDEX_NAME, cursor, conn, HEADERS

response = requests.get('https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/legislacao-e-normas/instrucoes-normativas', headers=HEADERS)
soup = BeautifulSoup(response.content, "html.parser")

pdfs = []
def main():
    for callout in soup.find_all('p', class_='callout'):
        link_tag = callout.find('a')
        if link_tag:
            titulo = link_tag.text.strip()
            url = link_tag['href']

            # Extraindo o número e o ano do documento
            match = re.search(r'\b(?:N[º°]\s*)?(\d{1,3})\s*/\s*(\d{4})\b', titulo)
            if match:
                numero, ano = match.groups()
            else:
                numero, ano = None, None

            # Pegando o resumo que está logo abaixo do link
            ementa_tag = callout.find_next_sibling('p')
            ementa = ementa_tag.text.strip() if ementa_tag else ""

            pdfs.append({
                'titulo': titulo,
                'numero': numero,
                'ano': ano,
                'ementa': f'{titulo} - {ementa}',
                'url': url
            })

# Exibindo os resultados
    for pdf in pdfs:
        PDF_URL = pdf['url']
        TITULO_DOC = pdf['titulo']
        NUMERO = pdf['numero']
        ASSUNTO_ID = 0
        TIPO_ID = 7
        TIPO_NOME = 'Instrução Normativa'
        UNIDADE_ID = 1
        EMENTA = pdf['ementa']
        ANO = pdf['ano']
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
            ato_documento = create_ato_documento(os.path.basename(filename), TITULO_DOC, tags, ANO, PDF_URL, NUMERO, TIPO_NOME, EMENTA)
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
                INSERT INTO documentos (ano, titulo, numero, ementa, arquivo, url, tipo_documento_id, user_id, assunto_id, unidade_id)
                VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
            """
            cursor.execute(query, (ANO, TITULO_DOC, f'{NUMERO}/{ANO}', EMENTA, elastic_id, PDF_URL ,TIPO_ID, 1, ASSUNTO_ID, UNIDADE_ID))
            conn.commit()
            print(f"SALVO NO BANCO DE DADOS: {os.path.basename(filename)}")
        except Exception as e:
            print(f"Erro ao processar {PDF_URL}: {e}")
    print("Processo concluído.")
              
main()