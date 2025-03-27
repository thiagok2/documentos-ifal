import requests
from bs4 import BeautifulSoup
from urllib.parse import urljoin
import os
import base64
from crawler.config import DOWNLOAD_DIR, es, create_tags, create_ato_documento, INDEX_NAME, cursor, conn

###Conselho é departamento???

# URL da página contendo as resoluções

print("Iniciando processo...")
print("Raspando PDFs da página...")

# Fazer requisição HTTP
HEADERS = {"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36 OPR/116.0.0.0"}
def main(ANO):
    BASE_URL = f"https://www2.ifal.edu.br/acesso-a-informacao/institucional/orgaos-colegiados/conselho-de-ensino-pesquisa-e-extensao/resolucoes-1/2022-1"

    response = requests.get(BASE_URL, headers=HEADERS)
    soup = BeautifulSoup(response.content, "html.parser")

    # Procurar a div onde os links estão
    content_div = soup.find("div", id="content-core")

    if not content_div:
        print("Erro: Não foi possível encontrar a div com id='content'.")
        exit()

    pdfs = []

    #teste = content_div.find_all("a", href=True)
    #teste = teste[-1].find_parent("p")
    #input(teste.get_text(strip=True))  

    # Encontrar todos os links dentro da div
    for a in content_div.find_all("a", href=True):
        numero_doc = '00'
        tipo_doc = 12  #Resolução
        tipo_nome = 'Resolução'
        PDF_URL = urljoin(BASE_URL, a["href"])  # Corrige URLs relativas

        if PDF_URL.endswith(".pdf"):
            p_tag = a.find_parent("p")  # Pega o pai <p> como título
            titulo = p_tag.get_text(strip=True) if p_tag else a.parent.get_text(strip=True)        
            titulo_m = titulo.lower()
            assunto_doc = 0
            if titulo_m.startswith('reso'): 
                numero_doc = titulo[13:16].strip()
                if numero_doc.endswith(('-', ' ', '.', ')')):
                  numero_doc = numero_doc[:-1]

            elif titulo_m.startswith('res. n') or titulo_m.startswith('res n'):
                numero_doc = titulo[7:10].strip()
                if numero_doc.endswith(('-', ' ', '.', ')')):
                  numero_doc = numero_doc[:-1]

            elif titulo_m.startswith('resn'):
                numero_doc = titulo[6:9].strip()
                if numero_doc.endswith(('-', ' ', '.', ')')):
                  numero_doc = numero_doc[:-1]

            elif titulo_m.startswith('regulamento'):
                tipo_doc = 7
                tipo_nome = 'Regulamento'
            elif titulo_m.startswith('relat'):
                tipo_doc = 11
                tipo_nome = 'Relatório'

            numero_doc = f'0{numero_doc[:-2]}' if ' 2'  in numero_doc else numero_doc #Retira número indesejado em 2016
            numero_doc = f'0{numero_doc[0]}' if '-' in numero_doc else numero_doc #Retira - e adiona o 0 na frente
            pdfs.append({
                'titulo': titulo,
                'url': PDF_URL,
                'numero': numero_doc,
                'assunto_id': assunto_doc,
                'tipo_id': tipo_doc,
                'tipo_nome': tipo_nome,
            })

    for pdf in pdfs:
        PDF_URL = pdf['url']
        TITULO_DOC = pdf['titulo']
        NUMERO = pdf['numero']
        ASSUNTO_ID = pdf['assunto_id']
        TIPO_ID = pdf['tipo_id']
        TIPO_NOME = pdf['tipo_nome']
        UNIDADE_ID = 1
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
            tags = 'Reitoria ' + TITULO_DOC
            tags = create_tags(tags)
            ato_documento = create_ato_documento(os.path.basename(filename), TITULO_DOC, tags, ANO, BASE_URL, NUMERO, TIPO_NOME)
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
            cursor.execute(query, (ANO, TITULO_DOC, f'{NUMERO}/{ANO}', TITULO_DOC, elastic_id, PDF_URL ,TIPO_ID, 1, ASSUNTO_ID, UNIDADE_ID))
            conn.commit()
            print(f"SALVO NO BANCO DE DADOS: {os.path.basename(filename)}")
        except Exception as e:
            print(f"Erro ao processar {PDF_URL}: {e}")
    print("Processo concluído.")
    #print(numero_doc)

for ano in range(2025, 2007, -1):  # De 2025 até 2020
    main(ano)
