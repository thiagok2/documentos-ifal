import re
import requests
from bs4 import BeautifulSoup
import os
import base64
from crawler.crawler_connections import DOWNLOAD_DIR, es, create_tags, create_ato_documento, INDEX_NAME, cursor, conn, HEADERS, NORMAS_URLS

def main(url):
    response = requests.get(url, headers=HEADERS)
    if response.status_code != 200:
        print(f"Erro ao acessar a página. Status Code: {response.status_code}")
        return []
    soup = BeautifulSoup(response.text, 'html.parser')
    pdfs = []
    
    aga1 = soup.find('h1').get_text()
    paragraphs = soup.find_all('p')
    for i in range(len(paragraphs)):
        p = paragraphs[i]
        link = p.find('a', href=True)
        if link and link['href'].endswith('.pdf'):
            pdf_url = link['href']
            titulo = link.get_text(strip=True)
            ementa = p.get_text(strip=True).replace(titulo, '').strip()
            
            # Verifica se o próximo parágrafo contém apenas a descrição
            if i + 1 < len(paragraphs) and not paragraphs[i + 1].find('a'):
                ementa += " " + paragraphs[i + 1].get_text(strip=True)
            
            match = re.search(r"\b(19|20)\d{2}\b", titulo)
            ano = match.group(0) if match else '1000'
                
            pdfs.append({
                "url": pdf_url, 
                "ementa": f"{ementa or titulo} - {aga1}", 
                "titulo": titulo,
                "ano": ano,
            })

    for pdf in pdfs:
        PDF_URL = pdf['url']
        TITULO_DOC = pdf['titulo']
        NUMERO = "00"
        ASSUNTO_ID = 0
        TIPO_ID = 0
        TIPO_NOME = 'Indeterminado'
        UNIDADE_ID = 22
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
            cursor.execute(query, (ANO, TITULO_DOC, f'{NUMERO}/{ANO}', EMENTA, elastic_id, PDF_URL,TIPO_ID, 1, ASSUNTO_ID, UNIDADE_ID))
            conn.commit()
            print(f"SALVO NO BANCO DE DADOS: {os.path.basename(filename)}")
        except Exception as e:
            print(f"Erro ao processar {PDF_URL}: {e}")
    print("Processo concluído.")
              

for url in NORMAS_URLS:
    main(url)
#python3 -m crawler.adm.compras