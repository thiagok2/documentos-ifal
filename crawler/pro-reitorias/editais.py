import requests
from bs4 import BeautifulSoup
import base64
<<<<<<< HEAD
from crawler.config import DOWNLOAD_DIR, es, create_tags_pro, create_ato_documento, INDEX_NAME, cursor, conn, HEADERS, ASSUNTO, ASSUNTO_ID, UNIDADE_ID

def config_geral(ANO=None):
  assuntos = {
    'ensino': {
      'BASE_URL': f"https://www2.ifal.edu.br/o-ifal/ensino/editais/{ANO}",
      'TAGS': ['PROEN'],
      'ASSUNTO_ID': 1,
      'UNIDADE_ID': 19
    },
    'pesquisa': {
      'BASE_URL': f"https://www2.ifal.edu.br/o-ifal/pesquisa-pos-graduacao-e-inovacao/editais/editais-{ANO}",
      'ANTIGOS_URL': f"https://www2.ifal.edu.br/o-ifal/pesquisa-pos-graduacao-e-inovacao/editais/editais-{ANO}",
      'TAGS': ["PRPPI"],
      'ASSUNTO_ID': 2,
      'UNIDADE_ID': 21
    },
    'extensao': {
      'BASE_URL': f"https://www2.ifal.edu.br/o-ifal/extensao/editais/editais-{ANO}",
      'TAGS': ["PROEX"],
      'ASSUNTO_ID': 3,
      'UNIDADE_ID': 20
    },
  }
  return assuntos
=======
from crawler.config import es, create_tags, create_ato_documento, INDEX_NAME, cursor, conn, HEADERS, config_geral, ASSUNTO, ASSUNTO_ID, UNIDADE_ID
>>>>>>> feat-SearchCommandA0

def main(ANO):
    ASSUNTO = ''
    TAGS = config_geral()[ASSUNTO]['TAGS']
    ASSUNTO_ID = config_geral()[ASSUNTO]['ASSUNTO_ID']
    UNIDADE_ID = config_geral()[ASSUNTO]['UNIDADE_ID']
    BASE_URL = config_geral(ANO)[ASSUNTO]['BASE_URL']
    print("Iniciando processo...")

    # Etapa 1: Raspagem dos PDFs
    print("Raspando PDFs da página...")
    response = requests.get(BASE_URL, headers=HEADERS)
    soup = BeautifulSoup(response.content, "html.parser")
    pdfs = []

    for link in soup.find_all("a", href=True):
        pdf_link = link["href"]
        if pdf_link.endswith("/view"):
            pdf_link = pdf_link[:-5]
        if pdf_link.endswith(".pdf"):
            p_tag = link.find_parent("p")
            titulo = p_tag.get_text(strip=True) if p_tag else link.parent.get_text(strip=True)
            pdf_link = pdf_link if pdf_link.startswith("http") else BASE_URL + pdf_link

            pdfs.append({
                "titulo": titulo,
                "url": pdf_link
            })

    # Etapa 2: Processamento dos PDFs em memória
    for pdf in pdfs:
        pdf_url = pdf['url']
        titulo_doc = pdf['titulo']
        try:
            print(f"Baixando e processando {pdf_url}...")
            pdf_response = requests.get(pdf_url)

            if pdf_response.status_code != 200 or "application/pdf" not in pdf_response.headers.get("Content-Type", ""):
                print(f"Arquivo inválido ou não é PDF: {pdf_url}")
                continue

            pdf_content = pdf_response.content
            encoded_pdf = base64.b64encode(pdf_content).decode("utf-8")

<<<<<<< HEAD
            # Criar ato_documento
            tags = create_tags_pro(titulo_doc, TAGS)
            ato_documento = create_ato_documento(os.path.basename(filename), titulo_doc, tags, ANO, BASE_URL)
=======
            tags = create_tags(titulo_doc)
            ato_documento = create_ato_documento(pdf_url.split("/")[-1], titulo_doc, tags, ANO, BASE_URL)
>>>>>>> feat-SearchCommandA0
            print(f"Ato Documento criado: {ato_documento}")

            doc = {
                "filename": pdf_url.split("/")[-1],
                "data": encoded_pdf,
                "ato": ato_documento,
                "attachment": {
                    "content": encoded_pdf
                }
            }
            response = es.index(index=INDEX_NAME, pipeline="attachment", body=doc)
            elastic_id = response["_id"]
            print(f"DOCUMENTO INDEXADO NO Elasticsearch: {elastic_id}")

            query = """
                INSERT INTO documentos (ano, titulo, ementa, arquivo, url, tipo_documento_id, user_id, assunto_id, unidade_id)
                VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)
            """
            cursor.execute(query, (ANO, titulo_doc, titulo_doc, elastic_id, pdf_url, 1, 1, ASSUNTO_ID, UNIDADE_ID))
            conn.commit()
            print(f"SALVO NO BANCO DE DADOS: {pdf_url.split('/')[-1]}")

        except Exception as e:
            print(f"Erro ao processar {pdf_url}: {e}")

    print("Processo concluído.")

for ano in range(2025, 2019, -1):
    main(ano)
