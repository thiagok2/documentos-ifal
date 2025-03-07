###TODO
# Melhorar o dicionario de tags
# Raspagem em massa

def config_geral(ANO=None):
  CONFIG = {
    'ensino': {
      'BASE_URL': f"https://www2.ifal.edu.br/o-ifal/ensino/editais/{ANO}",
      'TAGS': ['PROEN'],
      'ASSUNTO_ID': 1,
      'UNIDADE_ID': 19
    },
    'pesquisa': {
      'BASE_URL': f"https://www2.ifal.edu.br/o-ifal/pesquisa-pos-graduacao-e-inovacao/editais/editais-{ANO}",
      'TAGS': ["PRPPI"],
      'ASSUNTO_ID': 2,
      'UNIDADE_ID': 21
    },
    'extensao': {
      'BASE_URL': f"https://www2.ifal.edu.br/o-ifal/extensao/editais/editais-{ANO}",
      'TAGS': ["PROEX"],
      'ASSUNTO_ID': 3,
      'UNIDADE_ID': 20
    }
  }
  return CONFIG
    
def map_keywords_to_tags(titulo, paramentro):
    tags = set()  # Usar set para evitar tags duplicadas
    for keyword, keyword_tags in paramentro.items():
        if keyword.lower() in titulo.lower():
            tags.update(keyword_tags)
    return list(tags)

# Dicionário de Palavras-Chave para Tags Dinâmicas
KEYWORDS_TO_TAGS = {
    "bolsa": [
        "Bolsa",
        "Monitoria"
    ],
    "extensão": [
        "Extensão",
        "Ação Extensionista"
    ],
    "estágio": [
        "Estágio",
        "Estagiário"
    ],
    "serviço": [
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
    "Seleção de Docentes": [
        "Seleção de Docentes",
        "Professores/as Supervisores/as",
        "Professores/as Preceptores/as",
        "Docentes"
    ],
    "Processo Seletivo": [
        "Processo Seletivo",
        "Composição de quadro reserva",
        "Cadastro reserva"
    ],
    "Resultado Preliminar": [
        "RESULTADO PRELIMINAR"
    ],
    "Resultado Final": [
        "RESULTADO FINAL",
        "RESULTADO"
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
    "RETIFICAÇÃO": [
        "retificação",
        "alteração",
        "atualização"
    ],
    "TECNOLOGIA": [
        "tecnologia",
        "desenvolvimento tecnológico",
        "inovação"
    ],
    "SELEÇÃO": [
        "seleção",
        "candidatos aprovados",
        "concurso"
    ],
    "programa": [
        "Programa",
        "Vinculado à um Programa"],

}
