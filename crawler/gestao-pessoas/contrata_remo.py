import requests
from bs4 import BeautifulSoup
import os
import base64
from crawler.crawler_connections import DOWNLOAD_DIR, es, create_tags, create_ato_documento, INDEX_NAME, cursor, conn, HEADERS, CONTRATA_REMOVE_TAGS_URLS

# URL da página com os PDFs
UNIDADE_ID = 1
ASSUNTO_ID = 4

def main_ruins(TAG_TITULO, URL):
    # Fazer o request da página
    response = requests.get(URL, headers=HEADERS)
    soup = BeautifulSoup(response.content, "html.parser")

    # Encontrar todas as tags <p> (onde estão os anos e os links)
    ps = soup.find_all("p")

    # Variáveis para armazenar os resultados
    pdfs = []
    ano = None  # Inicializa o ano como None

    for p in ps:
        # Verifica se o parágrafo contém um ano (está em <strong>)
        strong = p.find("strong")
        if strong:
            strong_text = strong.text.strip()
            if strong_text.isdigit():
                ano = strong_text  # Atualiza o ano atual se for um número puro
            elif strong_text[-4:].isdigit():
                ano = strong_text[-4:]  # Pega os últimos 4 caracteres se forem um número

        # Verifica se há links de PDF dentro do parágrafo
        link = p.find("a", href=True)
        if link and ano:
            pdf_link = link["href"]
            if pdf_link.endswith("/view"):
                pdf_link = pdf_link["href"][:-5]
            if pdf_link.endswith('.pdf'):
                # Pega o título do pai <p> ou do próprio link
                p_tag = link.find_parent("p")
                titulo = p_tag.get_text(strip=True) if p_tag else link.text.strip()

                # Se o link for relativo, adiciona a URL base
                if not pdf_link.startswith("http"):
                    pdf_link = URL + pdf_link

                pdfs.append({
                    "ano": ano,  # Usa o ano atual
                    "titulo": f'{TAG_TITULO} - {titulo}',
                    "url": pdf_link
                })


    for pdf in pdfs:
        ANO = pdf['ano']
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
            tags = 'Reitoria' + titulo_doc
            tags = create_tags(tags)
            ato_documento = create_ato_documento(os.path.basename(filename), titulo_doc, tags, ANO, URL)
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
            

for key, value in CONTRATA_REMOVE_TAGS_URLS.items():
    main_ruins(value[0], value[1])