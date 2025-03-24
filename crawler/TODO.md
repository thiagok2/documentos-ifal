### TODO
  # Refazer, tentando automatizar menos
  # Organizar em diretorios
  # Mudar os nomes dos arquivos
  # Criar as respctivas unidades
  # Tratar data de publicação quando for nula
  # Raspar a p0rr@ toda

  https://www2.ifal.edu.br/o-ifal/administracao

  https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/desenvolvimento
  https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/documentos-para-posse-contratacao
  https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/exercicio-compartilhado
  https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/flexibilizacao
  https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/plano-de-carreira-dos-taes-pcctae
  https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/guia-de-procedimentos/guia-de-procedimentos - Tem que analisar, vai ser um dos últimos

  https://www2.ifal.edu.br/o-ifal/tecnologia-da-informacao/politicas-e-planos
  https://www2.ifal.edu.br/o-ifal/planejamento-institucional - Prioridade
  https://www2.ifal.edu.br/o-ifal/assistencia-estudantil/assistencia-estudantil/legislacao/legislacao-e-normas  https://www2.ifal.edu.br/o-ifal/relacoes-internacionais
  https://www2.ifal.edu.br/o-ifal/procuradoria-federal - Prioridade

  https://www2.ifal.edu.br/acesso-a-informacao/institucional/revisao-e-consolidacao-dos-atos-normativos/revisao-e-consolidacao-dos-atos-normativos/
  https://www2.ifal.edu.br/acesso-a-informacao/acoes-e-programas
  https://www2.ifal.edu.br/acesso-a-informacao/auditorias
  https://www2.ifal.edu.br/acesso-a-informacao/servico-informacao-cidadao-sic
  https://www2.ifal.edu.br/acesso-a-informacao/dados-abertos
  https://www2.ifal.edu.br/acesso-a-informacao/programa-de-integridade - Prioridade
  https://www2.ifal.edu.br/acesso-a-informacao/orgaos-de-assessoramento-geral
  
###


# 0, ASSUNTO DESCONHECIDO
# 1, ENSINO
# 2, PESQUISA
# 3, EXTENSÃO
# 4, RECURSOS HUMANOS
# 5, BIBLIOTECA
# 6, MONITORIA E AÇÕES INTEGRADAS


apt update && apt install -y python3-pip
pip install requests beautifulsoup4 elasticsearch psycopg2-binary

python3 -m crawler.main
python3 -m crawler.antigos
python3 -m crawler.ruins

python3 -m crawler.oifal.procu_fed_parecer