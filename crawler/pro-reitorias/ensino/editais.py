import requests
from bs4 import BeautifulSoup
import os
import base64
from crawler.config import DOWNLOAD_DIR, es, create_tags, create_ato_documento, INDEX_NAME, cursor, conn, HEADERS

pdfs = []
def main(ano):
    response = requests.get(f"https://www2.ifal.edu.br/o-ifal/ensino/editais/{ano}", headers=HEADERS)
    soup = BeautifulSoup(response.content, 'html.parser')

    content_div = soup.find("div", id="content-core")
    if not content_div:
        print("Erro: Não foi possível encontrar a div com id='content'.")
        exit()
    for a in content_div.find_all("a", href=True):
        href = a["href"]
        if ano == 2020:
            p_tag = a.find_parent("p")  # Pega o pai <p> como título
            titulo = p_tag.get_text(strip=True) if p_tag else a.parent.get_text(strip=True)             
            if titulo.startswith('Edital'):
                numero = titulo[10:14]
                if numero.endswith(('-', '/', '.', ')')):
                  numero = numero[:-1]
                numero = numero.strip()
                tipo_nome = 'Edital'
                tipo_id = 1
                input(numero)
                #Continuar daqui

        if href.endswith('.pdf'):
            numero = None
            # p_callout = a.find_previous("p", class_='callout')  # Pega o pai <p> como título
            # strong_tag = p_callout.find('strong')
            # ementa = strong_tag.find_next_sibling(string=True)
            p_tag = a.find_parent("p")  # Pega o pai <p> como título
            titulo = p_tag.get_text(strip=True) if p_tag else a.parent.get_text(strip=True)             

            if "edital" in titulo.lower():
                index_edital = titulo.lower().index("edital")  
                numero = titulo[index_edital+10 : index_edital + 13]
                numero = numero.strip()
                tipo_nome = 'Edital'
                tipo_id = 1
            elif "portaria" in titulo.lower():
                index_edital = titulo.lower().index("portaria") 
                numero = titulo[index_edital+11 : index_edital + 17]
                numero = numero.strip()
                tipo_nome = 'Portaria'
                tipo_id = 10
            if numero and numero.endswith(('-', '/', '.', ')')):
                numero = numero[:-1]
            if numero and numero.endswith('/2'):
                numero = numero[:-2]
            if numero == '00':
                numero = '700'
            if (ano == 2021) and (numero == '68'):
                numero = '268'
            if (ano == 2021) and (numero == '23'):
                numero = '123'

            pdfs.append({
                "numero": numero,
                "ano": ano,
                "url": href,
                "tipo_nome": tipo_nome,
                "tipo_id": tipo_id,
                "titulo": titulo,
            })
    for pdf in pdfs:
      print(pdf['numero'], pdf['titulo'])
    # for pdf in pdfs:
    #     PDF_URL = pdf['url']
    #     TITULO_DOC = pdf['titulo']
    #     NUMERO = pdf['numero']
    #     ASSUNTO_ID = 1
    #     TIPO_ID = pdf['tipo_id']
    #     TIPO_NOME = pdf['tipo_nome']
    #     UNIDADE_ID = 19
    #     EMENTA = pdf['titulo']
    #     ANO = pdf['ano']
    #     try:
    #         # Verificar e criar o diretório para downloads
    #         os.makedirs(DOWNLOAD_DIR, exist_ok=True)
    #         # Pega o último nome da url que é o titulo do pdf
    #         filename = os.path.join(DOWNLOAD_DIR, PDF_URL.split("/")[-1])
    #         # Baixar PDF se não existir
    #         if not os.path.exists(filename):
    #             print(f"Baixando {PDF_URL}...")
    #             pdf_response = requests.get(PDF_URL)
    #             with open(filename, "wb") as f:
    #                 f.write(pdf_response.content)
    #         else:
    #             print(f"Arquivo já existe: {filename}. Pulando download.")
    #         # Criar ato_documento
    #         tags = 'PROEN ' + EMENTA
    #         tags = create_tags(tags)
    #         ato_documento = create_ato_documento(os.path.basename(filename), TITULO_DOC, tags, ANO, PDF_URL, NUMERO, TIPO_NOME, EMENTA)
    #         print(f"Ato Documento criado: {ato_documento}")
    #         # Indexar no Elasticsearch
    #         with open(filename, "rb") as f:
    #             encoded_pdf = base64.b64encode(f.read()).decode("utf-8")
    #         doc = {
    #             "filename": os.path.basename(filename),
    #             "data": encoded_pdf,
    #             "ato": ato_documento,
    #             "attachment": {
    #                 "content": encoded_pdf
    #             }
    #         }
    #         response = es.index(index=INDEX_NAME, pipeline="attachment", body=doc)
    #         elastic_id = response["_id"]
    #         print(f"DOCUMENTO INDEXADO NO Elasticsearch: {elastic_id}")
    #         # Salvar no banco de dados
    #         query = """
    #             INSERT INTO documentos (ano, titulo, numero, ementa, arquivo, url, tipo_documento_id, user_id, assunto_id, unidade_id)
    #             VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
    #         """
    #         cursor.execute(query, (ANO, TITULO_DOC, f'{NUMERO}/{ANO}', EMENTA, elastic_id,PDF_URL, TIPO_ID, 1, ASSUNTO_ID, UNIDADE_ID))
    #         conn.commit()
    #         print(f"SALVO NO BANCO DE DADOS: {os.path.basename(filename)}")
    #     except Exception as e:
    #         print(f"Erro ao processar {PDF_URL}: {e}")
    # print("Processo concluído.")
main(2020)
         