
# Documentos IFAL

### instalando o PHP;

1. Precisamos adicionar o repositório 'ondrej/php' para instalar o php na versão 8.2
    ```bash
    sudo apt update
    sudo apt install software-properties-common -y
    sudo add-apt-repository ppa:ondrej/php -y
    sudo apt update
    ```
2. Instale php e todas as extensões com:
    ```bash
    sudo apt install php8.2 php8.2-cli php8.2-fpm php8.2-pgsql php8.2-xml php8.2-mbstring php8.2-curl php8.2-gd php8.2-zip -y
    ```

3. Verifique a instalação com o comando `php -v`.


### instalando o Composer;

1. composer é um gerenciador de dependencias para um ambiente PHP, siga os passos do [site oficial](https://getcomposer.org/download/) para instalar o composer.

2. Cheque a instalação do composer com o comando `composer --version`, se as informações do composer não aparecer em seu terminal, repita os passos do site.

3. Confimando a instação do composer, abra o projeto e execute os comandos: `composer install` e `composer update`

### instalando o postgresql 

1. Para instalar o postgresql em um ambiente linux/ubuntu rode o comando `sudo apt install postgresql`,

2. Logo após roda os comandos `sudo apt install -y postgresql-common
sudo /usr/share/postgresql-common/pgdg/apt.postgresql.org.sh`

- se a instação do postgres pelo APT der errado tente instalar munualmente seguindo os [passos do site](https://www.postgresql.org/download/linux/ubuntu/).

3. Com o postgresql instalado, é hora de instalar o PGadmin, que vai fornecer uma interface grafica para gerenciar o banco, se utilze o [passo a passo](https://www.pgadmin.org/download/pgadmin-4-apt/) do site para isso.
    - caso "curl" não for encontradodo, basta rodar o comando `sudo snap install curl`


### Configuração do Ambiente e Banco de Dados

1. Renomeie o arquivo `.env.example` para `.env`:

    ```bash
    mv .env.example .env
    ```

2. Modifique as variáveis de ambiente no arquivo `.env` para seu contexto local. **Observação:** Se estiver usando PostgreSQL como banco de dados, altere os valores das variáveis `SESSION_DRIVER` e `CACHE_STORE` para `"file"`.

    - um exemplo da parte de banco de dados .env
        DB_CONNECTION=pgsql
        DB_HOST=127.0.0.1
        DB_PORT=5432
        DB_DATABASE=nome_banco
        DB_USERNAME=postgres
        DB_PASSWORD=sua_senha

3. Gere uma nova chave de aplicação com o comando:

    ```bash
    php artisan key:generate
    ```

4. Execute o comando de migração para o banco de dados:

    ```bash
    php artisan migrate
    ```
    - caso ele não ache o driver basta rodar `sudo apt install php-pgsql php8.2-pgsql -y`
    - na necessidade de refazer o migrate, voce pode forçar o migrate apagando as tabelas e criando novamente, com o comando `php artisan migrate:fresh`

5. Execute o comando de seed para o banco de dados:

    ```bash
    php artisan db:seed
    ```




# configurando elastic 
- garanta que voce tem elasticsearch 8.14 instalado na sua maquina ( https://www.elastic.co/guide/en/elasticsearch/reference/current/install-elasticsearch.html ). 

-Não é preciso a instalação do plugin `Ingest Attachment plugin`, o elasticsearch 8.14 já contem o plugin incluido, [mais informações](https://www.elastic.co/guide/en/elasticsearch/plugins/current/ingest-attachment.html). .

### Desabilitar o Certificado SSL do Elasticsearch

Após a instalação, você precisará desabilitar o certificado SSL do Elasticsearch. Siga os passos abaixo:

#### No Linux

1. Navegue até o diretório de configuração do Elasticsearch:

    ```bash
    cd /
    cd etc/elasticsearch/
    ```

2. Abra o arquivo de configuração `elasticsearch.yml` com um editor de texto:

    ```bash
    sudo nano elasticsearch.yml
    ```

3. No arquivo `elasticsearch.yml`, procure a linha:

    ```yaml
    xpack.security.enabled: true
    ```

    E mude seu valor para:

    ```yaml
    xpack.security.enabled: false
    ```

4. Após a modificação, reinicie o serviço Elasticsearch:

    ```bash
    sudo systemctl restart elasticsearch
    ```

#### Observações

- A pasta `etc/elasticsearch` pode estar protegida para acesso de usuário root. Você pode usar o comando `sudo -s` para obter permissões de root e seguir o passo a passo normalmente.

- Alternativamente, você pode conceder acesso à pasta sem usuário root com o comando:

    ```bash
    chmod 755 etc/elasticsearch
    ```

### Criar o Índice de Documentos no Elasticsearch

#### SCRIPT SHELL 
Agora vamos criar o índice `documentos_ifal` no Elasticsearch. Para isso, siga os passos abaixo:

1. Na pasta raiz do projeto, execute o script de configuração:

    ```bash
    ./config_elastic.sh
    ```

2. Após a execução do script, analise os logs gerados. Se não houver nenhum log indicando erro, você pode prosseguir.

3. Se os logs mostrarem algum erro, verifique novamente se o certificado SSL está desativado e tente reiniciar o Elasticsearch conforme mencionado anteriormente.
    - se o jq não for encotrado rode `sudo apt update & sudo apt install jq`

#### POSTMAN COLLECTION

Você também pode usar a coleção para criar o índice. Para isso, siga os passos abaixo:

1. **Importe o arquivo `Elastic.postman_collection.json` no Postman.**

2. **Tutorial:** Para orientações sobre como importar e exportar coleções no Postman, você pode consultar o [tutorial aqui](https://apidog.com/blog/how-to-import-export-postman-collection-data/?utm_source=google_dsa&utm_medium=g&,=20556541359&utm_content=154844519700&utm_term=&gad_source=1&gclid=Cj0KCQjw2ou2BhCCARIsANAwM2Ew3BdKVzCM5FmxRVXvY_jblybMCcA0OViAv5_hjx8hugrPfonepKgaAhPzEALw_wcB).



### Baixando os PDFs para Indexação

Na pasta `node_amb`, foi criado um script para baixar os documentos PDF de uma página web. Para utilizá-lo, siga os passos abaixo:

1. Execute o comando:

    ```bash
    node node_amb/script_get_pdfs.js <url> <nome_pasta>
    ```

    **Exemplo de uso:**

    ```bash
    node node_amb/script_get_pdfs.js https://www2.ifal.edu.br/campus/riolargo/editais/2024 riolargo-2024
    ```

    Este script baixará todos os PDFs da página e os salvará na pasta `riolargo-2024`.

2. **Observação:** Todos os PDFs são inseridos em uma pasta prefixada chamada `pdfs`. Portanto, a estrutura final será `pdfs/<nome_pasta>/...`.






