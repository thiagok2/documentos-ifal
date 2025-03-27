### TODO
  # Raspar a p0rr@ toda

  https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/guia-de-procedimentos/guia-de-procedimentos - Tem que analisar, vai ser um dos últimos
  https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/desenvolvimento - 0
  https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/documentos-para-posse-contratacao - 0
  https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/exercicio-compartilhado - 1
  https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/flexibilizacao - 2
  https://www2.ifal.edu.br/o-ifal/gestao-de-pessoas/plano-de-carreira-dos-taes-pcctae - 1

  https://www2.ifal.edu.br/o-ifal/tecnologia-da-informacao/politicas-e-planos - 1
  https://www2.ifal.edu.br/o-ifal/assistencia-estudantil/assistencia-estudantil/legislacao/legislacao-e-normas - 1
  https://www2.ifal.edu.br/o-ifal/relacoes-internacionais/legislacao-e-normas - 1

  https://www2.ifal.edu.br/acesso-a-informacao/institucional/revisao-e-consolidacao-dos-atos-normativos/revisao-e-consolidacao-dos-atos-normativos/ - 2
  https://www2.ifal.edu.br/acesso-a-informacao/auditorias - 2
  https://www2.ifal.edu.br/acesso-a-informacao/servico-informacao-cidadao-sic - 1
  https://www2.ifal.edu.br/acesso-a-informacao/dados-abertos - 1
  https://www2.ifal.edu.br/acesso-a-informacao/orgaos-de-assessoramento-geral - 3
  
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
python3 -m crawler.acesso_info.programa_integridade - incompleto
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
###


###
https://www2.ifal.edu.br/acesso-a-informacao/programa-de-integridade - incompleto

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
###