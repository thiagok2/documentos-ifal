# ### TODO
# # twstart

# import requests
# from bs4 import BeautifulSoup
# import os
# import base64
# from crawler.config import DOWNLOAD_DIR, es, create_tags, create_ato_documento, INDEX_NAME, cursor, conn, HEADERS, RUINS_TAGS_URLS

# # URL da página com os PDFs
# UNIDADE_ID = 1
# ASSUNTO_ID = 4
# TAG_TITULO = 'Remoção Professor'
# URL = 'https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/remocao/professor'
# # TAG_TITULO = 'Remoção Técnico'
# # URL = 'https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/remocao/tecnico'

# def main(TAG_TITULO, URL):
#     # Fazer o request da página
#     response = requests.get(URL, headers=HEADERS)
#     soup = BeautifulSoup(response.content, "html.parser")

#     # Encontrar todas as tags <p> (onde estão os anos e os links)
#     paragrafors = soup.find_all("p")
#     ps = paragrafors[4:]
#     # Variáveis para armazenar os resultados
#     pdfs = []
#     ano = None  # Inicializa o ano como None

#     for p in ps:
#         # Verifica se o parágrafo contém um ano (está em <strong>)
#         link = p.find("a", href=True)
#         if link:  # Verifica se o link existe
#             strong = p.find_previous("strong")
#             if strong:
#                 strong_text = strong.text.strip()
#             url = link["href"]
#             ano = strong_text[13:18]
#             numero = strong_text[10:12]
#             if url.endswith("/view"):
#                 url = url[:-5]
#             if url.endswith('.pdf'):
#                 # Pega o título do pai <p> ou do próprio link
#                 p_tag = link.find_parent("p")
#                 titulo = p_tag.get_text(strip=True) if p_tag else link.text.strip()
#                 # Se o link for relativo, adiciona a URL base
#                 if not url.startswith("http"):
#                     url = URL + url
#                 pdfs.append({
#                     "ano": ano,  
#                     "titulo": f'{TAG_TITULO} - {titulo}',
#                     "url": url,
#                     "numero": numero,
#                     'lula': strong_text or 'NADA'
#                 })
 
#     for pdf in pdfs:
#         PDF_URL = pdf['url']
#         TITULO_DOC = pdf['titulo']
#         NUMERO = pdf['numero']
#         ASSUNTO_ID = 1
#         TIPO_ID = pdf['tipo_id']
#         TIPO_NOME = pdf['tipo_nome']
#         UNIDADE_ID = 19
#         EMENTA = pdf['titulo']
#         ANO = pdf['ano']
#         try:
#             # Verificar e criar o diretório para downloads
#             os.makedirs(DOWNLOAD_DIR, exist_ok=True)
#             # Pega o último nome da url que é o titulo do pdf
#             filename = os.path.join(DOWNLOAD_DIR, PDF_URL.split("/")[-1])
#             # Baixar PDF se não existir
#             if not os.path.exists(filename):
#                 print(f"Baixando {PDF_URL}...")
#                 pdf_response = requests.get(PDF_URL)
#                 with open(filename, "wb") as f:
#                     f.write(pdf_response.content)
#             else:
#                 print(f"Arquivo já existe: {filename}. Pulando download.")
#             # Criar ato_documento
#             tags = 'PROEN ' + EMENTA
#             tags = create_tags(tags)
#             ato_documento = create_ato_documento(os.path.basename(filename), TITULO_DOC, tags, ANO, PDF_URL, NUMERO, TIPO_NOME, EMENTA)
#             print(f"Ato Documento criado: {ato_documento}")
#             # Indexar no Elasticsearch
#             with open(filename, "rb") as f:
#                 encoded_pdf = base64.b64encode(f.read()).decode("utf-8")
#             doc = {
#                 "filename": os.path.basename(filename),
#                 "data": encoded_pdf,
#                 "ato": ato_documento,
#                 "attachment": {
#                     "content": encoded_pdf
#                 }
#             }
#             response = es.index(index=INDEX_NAME, pipeline="attachment", body=doc)
#             elastic_id = response["_id"]
#             print(f"DOCUMENTO INDEXADO NO Elasticsearch: {elastic_id}")
#             # Salvar no banco de dados
#             query = """
#                 INSERT INTO documentos (ano, titulo, numero, ementa, arquivo, url, tipo_documento_id, user_id, assunto_id, unidade_id)
#                 VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
#             """
#             cursor.execute(query, (ANO, TITULO_DOC, f'{NUMERO}/{ANO}', EMENTA, elastic_id,PDF_URL, TIPO_ID, 1, ASSUNTO_ID, UNIDADE_ID))
#             conn.commit()
#             print(f"SALVO NO BANCO DE DADOS: {os.path.basename(filename)}")
#         except Exception as e:
#             print(f"Erro ao processar {PDF_URL}: {e}")
#     print("Processo concluído.")

# main(TAG_TITULO, URL)
