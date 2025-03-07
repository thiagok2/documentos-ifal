# 0, ASSUNTO DESCONHECIDO
# 1, ENSINO
# 2, PESQUISA
# 3, EXTENSÃO
# 4, RECURSOS HUMANOS
# 5, BIBLIOTECA
# 6, MONITORIA E AÇÕES INTEGRADAS

1. Ensino - OK

BASE_URL = f"https://www2.ifal.edu.br/o-ifal/ensino/editais/{ANO}"
TAGS = ["PROEN", "Ensino", "Processo Seletivo", "Seleção", "Projeto de Ensino", "Ensino a Distância", "EAD"]
ASSUNTO_ID = 1
UNIDADE_ID = 19
2025 - 2020


2. Pesquisa - ...

BASE_URL = f"https://www2.ifal.edu.br/o-ifal/pesquisa-pos-graduacao-e-inovacao/editais/editais-{ANO}"
TAGS = ["PRPPI", "Pesquisa", "PIBIC", "PIBITI", "Processo de Seleção", "Pós-Graduação", "Inovação", "Bolsas de Estudo"]
ASSUNTO_ID = 2
UNIDADE_ID = 21
2024 - 2020

#TODO
Editais antigos

3. Extensão - OK

BASE_URL = f"https://www2.ifal.edu.br/o-ifal/extensao/editais/editais-{ANO}"
TAGS = ["PROEX", "Extensão", "Estagiário", "Prestação de Serviço", "Proex", "Monitoria", "Inclusão Social", "Ação Extensionista"]
ASSUNTO_ID = 3
UNIDADE_ID = 20
2025 - 2020


apt update && apt install -y python3-pip
pip install requests beautifulsoup4 elasticsearch psycopg2-binary
