from elasticsearch import Elasticsearch
import psycopg2
    
def map_keywords_to_tags(titulo, paramentro):
    tags = set()  # Usar set para evitar tags duplicadas
    for keyword, keyword_tags in paramentro.items():
        if keyword.lower() in titulo.lower():
            tags.update(keyword_tags)
    return list(tags)

KEYWORDS_TO_TAGS = {
    'calendário': [
        'Calendário'
    ],
    'Reitoria': [
        'Reitoria'
    ],
    'PIBIC': [
        'PIBIC',
        'Bolsa'
    ],
    'pibiti': [
        'PIBITI',
        'Bolsa'
    ],
    'pesquisa': [
        'Pesquisa'
    ],
    "recurso": [
        'Recurso'
    ],
    "bolsa": [
        "Bolsa",
    ],
    "monitoria": [
        "Monitoria",
    ],
    "extens": [
        "Extensão",
        "Ação Extensionista"
    ],
    "estagi": [
        "Estágio",
        "Estagiário/as"
    ],
    "servi": [
        "Serviço",
        "Prestação de Serviço"
    ],
    "inclusão": [
        "Inclusão Social"
    ],
    "Residência Pedagógica": [
        "Residência Pedagógica",
        "Preceptores/as",
        "Preceptor/a",
        "Bolsistas residentes"
    ],
    "Iniciação à Docência": [
        "Iniciação à Docência",
        "PIBID",
        "Bolsistas de iniciação"
    ],
    "Seleção": [
        "Seleção"
    ],
    "Seleção de Docentes": [
        "Seleção de Docentes",
        "Professores/as Supervisores/as",
        "Professores/as Preceptores/as",
        "Docentes"
    ],
    "reserva": [
        "Reserva",
        "Cadastro reserva"
    ],
    "Resultado Preliminar": [
        "RESULTADO PRELIMINAR"
    ],
    "Resultado Final": [
        "RESULTADO FINAL"
    ],
    "Retificação": [
        "Retificação",
        "Retificado"
    ],
    "Cursos de Formação": [
        "Cursos de Formação",
        "Formação em Serviço",
        "Equipes Técnico-Pedagógicas"
    ],
    "Mediadores Virtuais": [
        "Mediadores virtuais",
        "Atuar como mediadores"
    ],
    "TECNOLOGIA": [
        "Tecnologia",
        "Desenvolvimento Tecnológico",
        "Inovação"
    ],
    "SELEÇÃO": [
        "Seleção",
        "Candidatos Aprovados",
        "Concurso"
    ],
    "programa": [
        "Programa",
    ],
    "resolução":[
        'Resolução'
    ],
    "Relatório":[
        'Relatório'
    ],
    "Plano":[
        'Plano'
    ],
    "Regularmento":[
        'Regularmento'
    ],
    "Decreto": [
        'Decreto'
    ],
    "Instrução Normativa": [
        'Instrução Normativa'
    ],
    "Parecer": [
        'Parecer'
    ],
    "Aprova":[
        'Aprova'
    ],
    "Autoriza":[
        'Autoriza'
    ],
    "Ratifica":[
        'Ratifica'
    ],
    "Homologa":[
        'Homologa'
    ],
    "Altera":[
        'Altera'
    ],
    "PDI":[
        'PDI'
    ],
    "proen":[
        'PROEN'
    ],
    "proex":[
        'PROEX'
    ],
    "prppi":[
        'PRPPI'
    ],
}

CONTRATA_REMOVE_TAGS_URLS = {
    1: [
        'Professor Efetivo',
        'https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/concursos/editais/professor-efetivo'
    ],
    2: [
        'Técnico-Administrativo',
        'https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/concursos/editais/tecnico-administrativo'
    ],
    3: [
        'Professor Substituto',
        'https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/concursos/editais/professor-substituto'
    ],
    4: [
        'Remoção Professor',
        'https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/remocao/professor'
    ],
    5: [
        'Remoção Técnico',
        'https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/remocao/tecnico'
    ],
}

NORMAS_URLS = [
    "https://www2.ifal.edu.br/o-ifal/administracao/normas/compras",
    "https://www2.ifal.edu.br/o-ifal/administracao/normas/arquivo-e-protocolo",
    "https://www2.ifal.edu.br/o-ifal/administracao/normas/contratos",
    "https://www2.ifal.edu.br/o-ifal/administracao/normas/delegacao-de-competencias-aos-diretores-gerais-dos-campi",
    "https://www2.ifal.edu.br/o-ifal/administracao/normas/cimt",
    "https://www2.ifal.edu.br/o-ifal/administracao/normas/diarias-e-passagens",
    "https://www2.ifal.edu.br/o-ifal/administracao/normas/envio-das-informacoes-dos-campi-para-a-pro-reitoria-de-administracao",
    "https://www2.ifal.edu.br/o-ifal/administracao/normas/orcamento",
    "https://www2.ifal.edu.br/o-ifal/administracao/normas/patrimonio",
]

DOWNLOAD_DIR = "./crawler/pdfs"
ELASTIC_URL = "http://elasticsearch:9200"
INDEX_NAME = "documentos_ifal"
DB_CONFIG = {
    "dbname": "postgres",
    "user": "postgres",
    "password": "password",
    "host": "pgsql",
    "port": "5432"
}
HEADERS = {"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36 OPR/116.0.0.0"}
#######

# Conexões
es = Elasticsearch(ELASTIC_URL)
conn = psycopg2.connect(**DB_CONFIG)
cursor = conn.cursor()

def create_tags(titulo):
    tags_novas = map_keywords_to_tags(titulo, KEYWORDS_TO_TAGS)
    return tags_novas

def create_tags_pro(titulo, TAGS):
    tags_novas = map_keywords_to_tags(titulo, KEYWORDS_TO_TAGS)
    tags_combinadas = set(TAGS) | set(tags_novas)  # União dos conjuntos de tags
    return tags_combinadas

def create_ato_documento(filename, titulo, tags, ANO, BASE_URL, numero='00', tipo='Edital', ementa='nada', data='Não Informada'):
    if ementa == 'nada':
        ementa = titulo
    if data == 'Não Informada':
        data = f"1800-01-01"
    
    return {
        "ano": ANO,
        "arquivo": filename,
        "ato_id": "A002",
        "data_publicacao": data,
        "ementa": ementa,
        "fonte": {
            "esfera": "Campus",
            "orgao": "Instituto Federal de Alagoas",
            "sigla": "IFAL",
            "uf": "AL",
            "uf_sigla": "AL",
            "url": BASE_URL
        },
        "numero": f'{numero}/{ANO}',
        "tags": list(tags),
        "tipo_doc": tipo,
        "titulo": titulo,
        "publico": True
    }

