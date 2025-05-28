import re
import requests
from bs4 import BeautifulSoup
import os
import base64
from crawler.service import DOWNLOAD_DIR, es, create_tags, create_ato_documento, INDEX_NAME, cursor, conn, HEADERS

# Requisição para obter o conteúdo da página
def main():
    response = requests.get("https://www2.ifal.edu.br/o-ifal/procuradoria-federal/pareceres-referenciais", headers=HEADERS)
    soup = BeautifulSoup(response.content, "html.parser")

    pdfs = []
    seen = set()

    # Encontrar todos os artigos
    for artigo in soup.find_all("article", class_="tileItem"):
        titulo_tag = artigo.find("h2", class_="tileHeadline")
        if titulo_tag:
            link_tag = titulo_tag.find("a", class_="summary url")
            if not link_tag:
                continue
            
            titulo = link_tag.text.strip()
            link = link_tag["href"]
            
            if link in seen:
                continue
            seen.add(link)
            
            # Extraindo o número do parecer
            numero_match = re.search(r'\d{2,4}[-/.]\d{2,4}', titulo)
            numero = (numero_match.group(0)).strip() if numero_match else "01/2024"
            # Extraindo a data de publicação
            # data_tag = artigo.find("i", class_="icon-day")
            # data = data_tag.find_next_sibling(text=True).strip()
            # data = datetime.strptime(data, "%d/%m/%Y").strftime("%Y-%m-%d")

            ano_match = re.search(r'\b\d{4}\b', numero)
            ano = ano_match.group() if ano_match else '2024'
            if ano == '0002':
                ano = '2025'

            pdfs.append({
                "titulo": titulo,
                "numero": numero[:-5],
                "url": link[:-5],
                "ano": int(ano),
                })

# Exibir os resultados
    for pdf in pdfs:
        PDF_URL = pdf['url']
        TITULO_DOC = pdf['titulo']
        NUMERO = pdf['numero']
        ASSUNTO_ID = 4
        TIPO_ID = 9
        TIPO_NOME = 'Parecer'
        UNIDADE_ID = 1
        EMENTA = pdf['titulo']
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
            cursor.execute(query, (ANO, TITULO_DOC, f'{NUMERO}/{ANO}', EMENTA, elastic_id,PDF_URL, TIPO_ID, 1, ASSUNTO_ID, UNIDADE_ID))
            conn.commit()
            print(f"SALVO NO BANCO DE DADOS: {os.path.basename(filename)}")
        except Exception as e:
            print(f"Erro ao processar {PDF_URL}: {e}")
    print("Processo concluído.")
         
main()