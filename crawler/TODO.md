### TODO
  # Testar
  # Raspar a p0rr@ toda

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

###
python3 -m crawler.acesso_info.revi_consoli

python3 -m crawler.docs-institucionais.cepe_res
python3 -m crawler.docs-institucionais.consup_res
python3 -m crawler.docs-institucionais.proad_in

python3 -m crawler.gestao-pessoas.contrata_remo
python3 -m crawler.gestao-pessoas.dev_pessoas
python3 -m crawler.gestao-pessoas.legi_norma_in

python3 -m crawler.o-ifal.procu_fed_parecer

python3 -m crawler.pro-reitorias.editais
python3 -m crawler.pro-reitorias.pesquisa.antigos

python3 -m crawler.adm.normas

python3 -m crawler.todo_resto
###


###
https://www2.ifal.edu.br/acesso-a-informacao/institucional/revisao-e-consolidacao-dos-atos-normativos/documentos

https://www2.ifal.edu.br/acesso-a-informacao/institucional/orgaos-colegiados/conselho-de-ensino-pesquisa-e-extensao/resolucoes-1/2025 - 2025-2021

https://www2.ifal.edu.br/acesso-a-informacao/institucional/orgaos-colegiados/conselho-superior/resolucoes/2025 - 2025-2008

https://www2.ifal.edu.br/o-ifal/administracao/normas/orientacoes-normativas

https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/legislacao-e-normas/capacitacao

https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/legislacao-e-normas/instrucoes-normativas

https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/concursos/editais/professor-efetivo
https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/concursos/editais/tecnico-administrativo
https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/concursos/editais/professor-substituto
https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/remocao/professor
https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/remocao/tecnico

https://www2.ifal.edu.br/o-ifal/procuradoria-federal/pareceres-referenciais

https://www2.ifal.edu.br/o-ifal/ensino/editais/2025 - 2025-2020
https://www2.ifal.edu.br/o-ifal/extensao/editais/editais-2020 - 2025-2020
https://www2.ifal.edu.br/o-ifal/pesquisa-pos-graduacao-e-inovacao/editais/2025 - 2025-2020
https://www2.ifal.edu.br/o-ifal/pesquisa-pos-graduacao-e-inovacao/editais/o-ifal/pesquisa-pos-graduacao-e-inovacao/editais/editais-2019 2019-2010

https://www2.ifal.edu.br/o-ifal/administracao/normas/compras
https://www2.ifal.edu.br/o-ifal/administracao/normas/arquivo-e-protocolo
https://www2.ifal.edu.br/o-ifal/administracao/normas/contratos
https://www2.ifal.edu.br/o-ifal/administracao/normas/delegacao-de-competencias-aos-diretores-gerais-dos-campi
https://www2.ifal.edu.br/o-ifal/administracao/normas/cimt
https://www2.ifal.edu.br/o-ifal/administracao/normas/diarias-e-passagens
https://www2.ifal.edu.br/o-ifal/administracao/normas/envio-das-informacoes-dos-campi-para-a-pro-reitoria-de-administracao
https://www2.ifal.edu.br/o-ifal/administracao/normas/orcamento
https://www2.ifal.edu.br/o-ifal/administracao/normas/patrimonio

'https://www2.ifal.edu.br/acesso-a-informacao/auditorias/boletim-informativo',
'https://www2.ifal.edu.br/acesso-a-informacao/auditorias/Normas%2C%20Fluxos%20e%20Modelos',
'https://www2.ifal.edu.br/acesso-a-informacao/auditorias/relatorios-de-auditoria-interna',
'https://www2.ifal.edu.br/acesso-a-informacao/auditorias/relatorios-cgu-1',
'https://www2.ifal.edu.br/acesso-a-informacao/auditorias/levantamento-de-governanca',
'https://www2.ifal.edu.br/acesso-a-informacao/institucional/comissao-de-etica/atas',
'https://www2.ifal.edu.br/acesso-a-informacao/institucional/comissao-de-etica/editais-1/editais?_authenticator=4aefdfa06fdb78a455e7168b526b1171496674c7',
'https://www2.ifal.edu.br/acesso-a-informacao/institucional/comissao-de-etica/infoethos',
'https://www2.ifal.edu.br/acesso-a-informacao/institucional/comissao-de-etica/informativo-minuto-da-etica/',
'https://www2.ifal.edu.br/acesso-a-informacao/institucional/comissao-de-etica/comunicados',
'https://www2.ifal.edu.br/acesso-a-informacao/institucional/comissao-de-etica/relatorios-anuais-de-trabalho',
'https://www2.ifal.edu.br/acesso-a-informacao/institucional/comissao-de-etica/legislacao',
'https://www2.ifal.edu.br/acesso-a-informacao/institucional/comissao-de-etica/agenda',
'https://www2.ifal.edu.br/acesso-a-informacao/institucional/pei/avaliacao-de-cursos-superiores/avaliacao-de-cursos-superiores',
'https://www2.ifal.edu.br/acesso-a-informacao/institucional/pei/indicadores-de-qualidade-superior',
'https://www2.ifal.edu.br/acesso-a-informacao/institucional/pei/enade-exame-nacional-de-desempenho-de-estudantes',
'https://www2.ifal.edu.br/acesso-a-informacao/institucional/corregedoria/legislacao',
'https://www2.ifal.edu.br/acesso-a-informacao/institucional/corregedoria/normas-internas',
'https://www2.ifal.edu.br/acesso-a-informacao/institucional/corregedoria/relatorios-anuais/view',
'https://www2.ifal.edu.br/acesso-a-informacao/institucional/ouvidoria/planos-ouvidoria',
'https://www2.ifal.edu.br/acesso-a-informacao/institucional/ouvidoria/legislacao-e-normas',
'https://www2.ifal.edu.br/acesso-a-informacao/institucional/ouvidoria/relatorios',
'https://www2.ifal.edu.br/o-ifal/procuradoria-federal/atos-normativos',
'https://www2.ifal.edu.br/o-ifal/procuradoria-federal/cartilhas-da-procuradoria',
'https://www2.ifal.edu.br/o-ifal/procuradoria-federal/pareceres-referenciais',
'https://www2.ifal.edu.br/acesso-a-informacao/institucional/revisao-e-consolidacao-dos-atos-normativos/atos-normativos-revogados/portarias',
'https://www2.ifal.edu.br/acesso-a-informacao/institucional/revisao-e-consolidacao-dos-atos-normativos/atos-normativos-revogados/resolucoes-1',
'https://www2.ifal.edu.br/acesso-a-informacao/institucional/revisao-e-consolidacao-dos-atos-normativos/atos-normativos-vigentes/portarias/portarias',
'https://www2.ifal.edu.br/acesso-a-informacao/institucional/revisao-e-consolidacao-dos-atos-normativos/atos-normativos-vigentes/deliberacoes',
'https://www2.ifal.edu.br/acesso-a-informacao/institucional/revisao-e-consolidacao-dos-atos-normativos/atos-normativos-vigentes/resolucoes',
'https://www2.ifal.edu.br/acesso-a-informacao/auditorias/planos-anuais',
'https://www2.ifal.edu.br/acesso-a-informacao/auditorias/relatorios-de-auditoria-anual-de-contas-da-cgu',
'https://www2.ifal.edu.br/acesso-a-informacao/auditorias/rol-de-responsaveis',
'https://www2.ifal.edu.br/acesso-a-informacao/auditorias/relatorios-anuais-de-atividades',
'https://www2.ifal.edu.br/acesso-a-informacao/auditorias/relatorios-de-auditoria-interna',
'https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/flexibilizacao/ambientes-flexibilizados',
'https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/flexibilizacao/legislacao',
'https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/flexibilizacao/comissoes',
'https://www2.ifal.edu.br/acesso-a-informacao/dados-abertos/atosnorm/atosnormativos',
'https://www2.ifal.edu.br/acesso-a-informacao/dados-abertos/relatorios/relatorios-de-monitoramento',
'https://www2.ifal.edu.br/acesso-a-informacao/dados-abertos/fidm/fidma',
'https://www2.ifal.edu.br/acesso-a-informacao/dados-abertos/plano-de-dados-abertos-pda/pda',
'https://www2.ifal.edu.br/acesso-a-informacao/servico-informacao-cidadao-sic',
'https://www2.ifal.edu.br/o-ifal/relacoes-internacionais/legislacao-e-normas',
'https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/plano-de-carreira-dos-taes-pcctae',
'https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/exercicio-compartilhado',
'https://www2.ifal.edu.br/o-ifal/tecnologia-da-informacao/politicas-e-planos',
'https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/documentos-para-posse-contratacao',
'https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/desenvolvimento',
'https://www2.ifal.edu.br/acesso-a-informacao/programa-de-integridade'
###