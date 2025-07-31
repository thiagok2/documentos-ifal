# Documentos IFAL

### instalando dependencias;

1. Precisamos instalar as dependencias do laravel sail no seu repositório local
    ```
    docker run --rm \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs
    ```

2. Verifique se a pasta vendor foi criada na raiz do projeto. Se a basta foi criada com sucesso, não há mais nescessidade dessa imagem.

### preparando ambiente;

1. Para o funcionamento do elasticsearch verifique se na .env está `ELASTIC_URL=http://elasticsearch:9200`

2. Já para o postgres 
    ```
        DB_CONNECTION=pgsql
        DB_HOST=pgsql
        DB_PORT=5432
        DB_DATABASE=postgres
        DB_USERNAME=postgres
        DB_PASSWORD=postgres
    ```
3. Verifique se a informações batem com as do docker-compose


### rodando a docker-compose;

1. Rode `docker compose up --build -d` para contruir o container da aplicação.

2. Vá até o container com `docker exec -it documentos-ifal-main-laravel.test-1 bash` e rode
    ```
    php artisan key:generate
    php artisan migrate
    php artisan db:seed

    ```
### configurando o elastic;

1. Ainda dentro do container da aplicação rode `apt-get update && apt-get install -y jq` para poder excutar o script de configuraçã0 `./config_elastic.sh`

2. Caso tenha algum problema de host nulo depois, entre novamente no container de aplicação e rode o comandos
    ```
    php artisan config:clear
    php artisan cache:clear

    ```
