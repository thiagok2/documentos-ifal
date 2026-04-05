==========================================================
 #    DOCUMENTOS IFAL - Sistema de busca e indexação    #
==========================================================

Sistema desenvolvido para catalogar, armazenar e realizar buscas inteligentes em documentos normativos e atos administrativos.

===================
# SOBRE O PROJETO #
===================
O sistema resolve problemas de lentidão e imprecisão na busca por grandes volumes de documentos que possuem conteúdos massivos e de difícil acesso.

===============================
# PRINCIPAIS FUNCIONALIDADES #
===============================
 - Indexação Automática: Utiliza o padrão *Observer* (`DocumentoObserver`). Assim que um documento é salvo no MySQL, ele é automaticamente enviado para o Elasticsearch.
 - Busca Full-Text: Permite pesquisar conteúdo dentro de anexos (PDFs) e textos, com suporte a *highlighting* (destaque do termo encontrado).
 - Crawler Integrado: Scripts em Python (`rodar_tudo.py`) que monitoram e capturam documentos de fontes externas.

======================================================
 # PREPARAÇÃO DO AMBIENTE - INSTALANDO DEPENDÊNCIAS #
======================================================

Antes de iniciar, é necessário ter as seguintes tecnologias:

- Docker Desktop
- WSL 2 (Windows Subsystem for Linux) --> OBRIGATÓRIO PARA WINDOWS!

# 1° Passo: Instalando o Docker Desktop

Para instalar o Docker Desktop, entre no site oficial: https://www.docker.com/products/docker-desktop/
Clique em "Download Docker Desktop" e instale a versão para seu sistema.

# 2° Passo: Instalando WSL (Windows Subsystem for Linux) --> SOMENTE PARA WINDOWS!

1. Instale o PowerShell (https://apps.microsoft.com/detail/9MZ1SNWT0N5D) e execute-o como Administrador.
2. Execute o comando:
   wsl --install
3. Reinicie o computador. Ao ligar, ele pedirá para criar um usuário e senha para o Linux (Ubuntu).
4. A partir de agora, use o terminal do UBUNTU para rodar os comandos do projeto.

# 3° Passo: Instalando as dependências do Laravel (Vendor)

1. Clone o repositório e entre na pasta:
   git clone https://github.com/thiagok2/documentos-ifal.git
   cd documentos-ifal

2. Instale as dependências via Docker (não precisa ter PHP instalado na máquina):
   docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs

3. Verifique se funcionou:
   ls -l
   # Procure por uma pasta chamada `vendor`.

   *Solução de Problemas:* Se a pasta `vendor` não aparecer e você usa Windows, certifique-se de estar rodando o comando dentro do terminal do **WSL (Ubuntu)** e que o Docker Desktop está aberto (Running).

---

# IMPORTANTE: O que NÃO instalar

Para evitar conflitos, siga estas regras:

1.  **Não instale o Elasticsearch manualmente:** Não tente rodar um container local do Elastic. O projeto está configurado para conectar em uma instância centralizada na nuvem.
2.  **Docker Compose:** Se você instalou o Docker Desktop, o Compose já está pronto.

==============================
# INSTALAÇÃO E CONFIGURAÇÃO #
==============================

# 1° Passo: Configure o seu .env

mv .env.example .env

# 2° Passo: Configurando as Variáveis de Ambiente

Abra o arquivo `.env` e ajuste as seguintes seções:

1. **Banco de Dados (Postgres):**
    DB_CONNECTION=pgsql
    DB_HOST=pgsql
    DB_PORT=5432
    DB_NAME=documentos-ifal
    DB_DATABASE=documentos-ifal
    DB_USERNAME=postgres
    DB_PASSWORD=postgres

2. **Busca (Elasticsearch na Nuvem):**
   Solicite o IP/Credenciais para o responsável da equipe e preencha:
    SCOUT_DRIVER=elastic
    ELASTICSEARCH_HOST=
    ELASTIC_URL=
    ELASTICSEARCH_USERNAME=
    ELASTICSEARCH_PASSWORD=
    ELASTICSEARCH_INDEX=documentos_ifal

3. **Drivers Locais:**
   Como estamos rodando localmente, force o uso de arquivos para sessão e cache:
    SESSION_DRIVER="file"
    CACHE_STORE="file"

# 3° Passo: Subindo os serviços

Para construir e subir os containers (Laravel, Nginx, Postgres):

./vendor/bin/sail up -d --build

# 4° Passo: Configuração Inicial do Laravel

Execute os comandos abaixo, um por vez:

# Gera a chave de criptografia
./vendor/bin/sail artisan key:generate

# Cria as tabelas no Banco
./vendor/bin/sail artisan migrate

# Popula o banco com dados de teste
./vendor/bin/sail artisan db:seed

===================================
  # CONFIGURANDO O ELASTICSEARCH #
===================================

Para configurar o plugin de processamento de texto (jq) e rodar scripts de configuração:

1. Instale o JQ no container:
   ./vendor/bin/sail root-shell
   apt-get update && apt-get install -y jq
   exit

2. Rode o script de configuração do Elastic:
   ./vendor/bin/sail exec laravel.test chmod +x config_elastic.sh
   ./vendor/bin/sail exec laravel.test ./config_elastic.sh

===========================
 # SCRIPT DOS DOCUMENTOS #
===========================

Para rodar o script de captura (Crawler) em Python:

1. Acesse o container como root:
   ./vendor/bin/sail root-shell

2. Instale as dependências do Python (apenas na primeira vez):
   apt-get update && apt-get install -y python3-pip
   pip3 install requests beautifulsoup4 "elasticsearch==8.8.0" psycopg2-binary python-dotenv lxml --break-system-packages

3. Execute o script:
   python3 rodar_tudo.py
   exit

==================
  # APLICAÇÃO #
==================

- Acesso ao Sistema: http://localhost:80

============================
 # RESOLUÇÃO DE PROBLEMAS #
============================

- Se houver erro de conexão com o Elastic ou Banco, limpe o cache:
  ./vendor/bin/sail artisan config:clear
  ./vendor/bin/sail artisan cache:clear

============================
 # TECNOLOGIAS UTILIZADAS #
============================
| Tecnologia | Versão | Função |
|---|---|---|
| *PHP* | 8.3 | Backend (Laravel 11) |
| *Elasticsearch* | 8.14.3 | Motor de Busca (Nuvem) |
| *pgSQL* | 17 | Banco de Dados Relacional |
| *Python* | 3.11 | Crawlers e Scripts de Automação |
| *Docker* | - | Containerização do Ambiente |