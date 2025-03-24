###TODO
#Testar

import requests
from bs4 import BeautifulSoup
import os
import base64
from crawler.config import DOWNLOAD_DIR, es, create_tags, create_ato_documento, INDEX_NAME, cursor, conn

def limpa_ementa(ementa):
    """Remove textos indesejados no resumo."""
    if ementa:
        ementa = ementa.split("(link para")[0].strip()  # Remove qualquer coisa depois de "(link para"
        ementa = ementa.split("(l")[0].strip()  # Remove trechos cortados como "(l"
    return ementa

def main():
    # Fazer o request da página
    response = requests.get('https://www2.ifal.edu.br/o-ifal/administracao/normas/orientacoes-normativas')
    soup = BeautifulSoup(response.content, "html.parser")

    content_div = soup.find("div", id="content-core")

    if not content_div:
        print("Erro: Não foi possível encontrar a div com id='content-core'.")
        exit()

    pdfs = []
    ano = None
    elements = list(content_div.find_all(["p", "ul"]))

    for i, element in enumerate(elements):
        # Se for um parágrafo com a classe "callout", define o ano atual
        if element.name == "p" and "callout" in element.get("class", []):
            ano = element.get_text(strip=True)

        # Se for uma lista não ordenada (ul), busca os links dos documentos
        if element.name == "ul":
            for li in element.find_all("li"):
                a = li.find("a")
                if a:
                    titulo = a.get_text(strip=True)
                    url = a["href"]

                    # Extrai o número do documento do nome
                    numero = None
                    if "nº" in titulo:
                        parts = titulo.split("nº")
                        if len(parts) > 1:
                            numero = parts[1].split("/")[0].strip()

                    # Procura o próximo <p> como resumo
                    ementa = None
                    next_p = element.find_next_sibling("p")
                    if next_p:
                        ementa = limpa_ementa(next_p.get_text(strip=True))

                    pdfs.append({
                        "ano": ano,
                        "titulo": titulo,
                        "numero": numero,
                        "url": url,
                        "ementa":  f'{titulo} - {ementa}'
                    })
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