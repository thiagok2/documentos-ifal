import requests
from bs4 import BeautifulSoup
import os
import base64
from crawler.config import DOWNLOAD_DIR, es, create_tags, create_ato_documento, INDEX_NAME, cursor, conn, HEADERS, config_geral, ASSUNTO, ASSUNTO_ID, UNIDADE_ID

# DEPOIS E ANTES DE RASPAR VERIFICAR CREATE_TAGS


RESTO_URLS = [
#    'https://www2.ifal.edu.br/acesso-a-informacao/auditorias/boletim-informativo',
#    'https://www2.ifal.edu.br/acesso-a-informacao/auditorias/Normas%2C%20Fluxos%20e%20Modelos',
#    'https://www2.ifal.edu.br/acesso-a-informacao/auditorias/relatorios-de-auditoria-interna',
#    'https://www2.ifal.edu.br/acesso-a-informacao/auditorias/relatorios-cgu-1',
#    'https://www2.ifal.edu.br/acesso-a-informacao/auditorias/levantamento-de-governanca',
#    'https://www2.ifal.edu.br/acesso-a-informacao/institucional/comissao-de-etica/atas',
#    'https://www2.ifal.edu.br/acesso-a-informacao/institucional/comissao-de-etica/editais-1/editais?_authenticator=4aefdfa06fdb78a455e7168b526b1171496674c7',
#    'https://www2.ifal.edu.br/acesso-a-informacao/institucional/comissao-de-etica/infoethos',
#    'https://www2.ifal.edu.br/acesso-a-informacao/institucional/comissao-de-etica/informativo-minuto-da-etica/',
#    'https://www2.ifal.edu.br/acesso-a-informacao/institucional/comissao-de-etica/comunicados',
#    'https://www2.ifal.edu.br/acesso-a-informacao/institucional/comissao-de-etica/relatorios-anuais-de-trabalho',
#    'https://www2.ifal.edu.br/acesso-a-informacao/institucional/comissao-de-etica/legislacao',
#    'https://www2.ifal.edu.br/acesso-a-informacao/institucional/comissao-de-etica/agenda',
#    'https://www2.ifal.edu.br/acesso-a-informacao/institucional/pei/avaliacao-de-cursos-superiores/avaliacao-de-cursos-superiores',
#    'https://www2.ifal.edu.br/acesso-a-informacao/institucional/pei/indicadores-de-qualidade-superior',
#    'https://www2.ifal.edu.br/acesso-a-informacao/institucional/pei/enade-exame-nacional-de-desempenho-de-estudantes',
#    'https://www2.ifal.edu.br/acesso-a-informacao/institucional/corregedoria/legislacao',
#    'https://www2.ifal.edu.br/acesso-a-informacao/institucional/corregedoria/normas-internas',
#    'https://www2.ifal.edu.br/acesso-a-informacao/institucional/corregedoria/relatorios-anuais/view',
#    'https://www2.ifal.edu.br/acesso-a-informacao/institucional/ouvidoria/planos-ouvidoria',
#    'https://www2.ifal.edu.br/acesso-a-informacao/institucional/ouvidoria/legislacao-e-normas',
#    'https://www2.ifal.edu.br/acesso-a-informacao/institucional/ouvidoria/relatorios',
#    'https://www2.ifal.edu.br/o-ifal/procuradoria-federal/atos-normativos',
#    'https://www2.ifal.edu.br/o-ifal/procuradoria-federal/cartilhas-da-procuradoria',
#    'https://www2.ifal.edu.br/o-ifal/procuradoria-federal/pareceres-referenciais',
#    'https://www2.ifal.edu.br/acesso-a-informacao/institucional/revisao-e-consolidacao-dos-atos-normativos/atos-normativos-revogados/portarias',
#    'https://www2.ifal.edu.br/acesso-a-informacao/institucional/revisao-e-consolidacao-dos-atos-normativos/atos-normativos-revogados/resolucoes-1',
#    'https://www2.ifal.edu.br/acesso-a-informacao/institucional/revisao-e-consolidacao-dos-atos-normativos/atos-normativos-vigentes/portarias/portarias',
#    'https://www2.ifal.edu.br/acesso-a-informacao/institucional/revisao-e-consolidacao-dos-atos-normativos/atos-normativos-vigentes/deliberacoes',
#    'https://www2.ifal.edu.br/acesso-a-informacao/institucional/revisao-e-consolidacao-dos-atos-normativos/atos-normativos-vigentes/resolucoes',
#    'https://www2.ifal.edu.br/acesso-a-informacao/auditorias/planos-anuais',
#    'https://www2.ifal.edu.br/acesso-a-informacao/auditorias/relatorios-de-auditoria-anual-de-contas-da-cgu',
#    'https://www2.ifal.edu.br/acesso-a-informacao/auditorias/rol-de-responsaveis',
#    'https://www2.ifal.edu.br/acesso-a-informacao/auditorias/relatorios-anuais-de-atividades',
#    'https://www2.ifal.edu.br/acesso-a-informacao/auditorias/relatorios-de-auditoria-interna',
#    'https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/flexibilizacao/ambientes-flexibilizados',
#    'https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/flexibilizacao/legislacao',
#    'https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/flexibilizacao/comissoes',
#    'https://www2.ifal.edu.br/acesso-a-informacao/dados-abertos/atosnorm/atosnormativos',
#    'https://www2.ifal.edu.br/acesso-a-informacao/dados-abertos/relatorios/relatorios-de-monitoramento',
    'https://www2.ifal.edu.br/acesso-a-informacao/dados-abertos/plano-de-dados-abertos-pda/pda',
    'https://www2.ifal.edu.br/acesso-a-informacao/dados-abertos/fidm/documentos/formularios-pda-2022-2024',
    'https://www2.ifal.edu.br/acesso-a-informacao/dados-abertos/fidm/documentos/formularios-pda-2022-2024?b_start:int=20',
    'https://www2.ifal.edu.br/acesso-a-informacao/dados-abertos/fidm/documentos/formularios-pda-2022-2024?b_start:int=40',
    'https://www2.ifal.edu.br/acesso-a-informacao/dados-abertos/fidm/documentos/formularios-pda-2024-2026',
    'https://www2.ifal.edu.br/acesso-a-informacao/dados-abertos/fidm/documentos/formularios-pda-2024-2026?b_start:int=20',
    'https://www2.ifal.edu.br/acesso-a-informacao/dados-abertos/fidm/documentos/formularios-pda-2024-2026?b_start:int=40',
#    'https://www2.ifal.edu.br/acesso-a-informacao/servico-informacao-cidadao-sic',
#    'https://www2.ifal.edu.br/o-ifal/relacoes-internacionais/legislacao-e-normas',
#    'https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/plano-de-carreira-dos-taes-pcctae',
#    'https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/exercicio-compartilhado',
#    'https://www2.ifal.edu.br/o-ifal/tecnologia-da-informacao/politicas-e-planos',
#    'https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/documentos-para-posse-contratacao',
#    'https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/desenvolvimento',
#    'https://www2.ifal.edu.br/acesso-a-informacao/programa-de-integridade'

]

def main(url):
    BASE_URL = url

    print("Iniciando processo...")

    # Etapa 1: Raspagem dos PDFs
    print("Raspando PDFs da página...")
    response = requests.get(BASE_URL, headers=HEADERS)
    soup = BeautifulSoup(response.content, "html.parser")
    pdfs = []

    heading = soup.find('h1')
    content_div = soup.find("div", id="content-core")
    if not content_div:
        print("Erro: Não foi possível encontrar a div com id='content'.")
        exit()
    for a in content_div.find_all("a", href=True):
        pdf_link = a["href"]
        if pdf_link.endswith("/view"):
            pdf_link = pdf_link[:-5]
        if pdf_link.endswith(".pdf"):
            p_tag = a.find_parent("p")  # Pega o pai <p> como título
            titulo = p_tag.get_text(strip=True) if p_tag else a.parent.get_text(strip=True)
            pdf_link = pdf_link if pdf_link.startswith("http") else BASE_URL + pdf_link

            pdfs.append({
                "titulo": titulo,
                "url": pdf_link,
                "ementa": f'{titulo} - {heading.get_text()}'
            })
    

    # Etapa 2: Processamento de cada PDF
    for pdf in pdfs:
        pdf_url = pdf['url']
        titulo_doc = pdf['titulo']
        ementa = pdf['ementa']

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
            tags = create_tags(ementa)
            ato_documento = create_ato_documento(os.path.basename(filename), titulo_doc, tags, '00', BASE_URL, '00', 'Indefinido', ementa)
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
                INSERT INTO documentos (titulo, ementa, arquivo, url, tipo_documento_id, user_id, assunto_id, unidade_id)
                VALUES (%s, %s, %s, %s, %s, %s, %s, %s)
            """
            cursor.execute(query, (titulo_doc, ementa, elastic_id, pdf_url, 1, 1, 0, 1))
            conn.commit()
            print(f"SALVO NO BANCO DE DADOS: {os.path.basename(filename)}")

        except Exception as e:
            print(f"Erro ao processar {pdf_url}: {e}")

    print("Processo concluído.")

for url in RESTO_URLS:  # De 2025 até 2020
    main(url)
