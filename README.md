<div align='center'>
    <h1>Template</h1>
</div>

## Ambiente

- PHP 8.4.3
- Nginx 1.27.3
- MySQL 8.4
- PostgreSQL 17.2
- MongoDB 1.27.3
- Redis 7.2

> __Obs__:
Cada implementação de funcionalidades novas estão separados por diferentes [commits](https://github.com/leonardoakio/ms-laravel_template/commits/master/)

## Adaptando o template

1. Mantenha no `docker-compose.yml` apenas os serviços que deseja e a depender da escolha, terá que ser realizado ajustes nos arquivos, por exemplo apagar o **PostgreSQL**
   2. `.env`: Apagar as credenciais do serviço, exemplo 
    ```text
    # Database
    PGSQL_CONNECTION=pgsql
    
    # Database: PgSQL
    PGSQL_HOST=template_postgresql
    PGSQL_PORT=5432
    PGSQL_DATABASE=template
    PGSQL_USERNAME=root
    PGSQL_PASSWORD=root
    ```
   3. `Dockerfile`: Excluir do build as dependências 
    ```dockerfile
   RUN docker-php-ext-install pdo pgsql pdo_pgsql
   ```
   4. `.docker/`: Deletar a configuração para cada serviço, exemplo 
   ```text
   .docker/postgresql/postgresql.env` 
    ```
    5. `src/Http/Controllers/Utils/HealthHandler.php`: Apagar a validação do servico no `liveness`
   ```text
    protected function testPostgreSqlConnection()
    {...}
    2. ```
2. No arquivo `docker-compose.yml` altere os nomes dos containeres para o nome real da aplicação seguidos do serviço, exemplo: `intranet_app`
3. No `.env` deve ser mudado o nome da aplicação e o nome que será refletido na stack do **docker compose**
    ```php
    # Application
    - APP_NAME=template
   
    # Docker compose
    - COMPOSE_PROJECT_NAME=template_api
    ```
4. `storage/api-docs/api-docs-v1.yaml`: Crie a documentação voltado para o contexto da aplicação
   ```text
    openapi: "3.0.0"
    info:
        title: "Template"
        description: "Listagem de endpoints disponíveis na aplicação"
        version: "1.0"
    ...
   ```

## Utilizando o PostgreSQL

Alteramos a `.env` para definir o database principal como o pgsql
```php
# Database
 DB_CONNECTION=pgsql

# Database: PgSQL
 DB_HOST=template_postgresql
 DB_PORT=5432
 DB_DATABASE=template
 DB_USERNAME=root
 DB_PASSWORD=Lfp6w7sIp1lf
```

No `docker-compose.yml`, alteramos o serviço `application` para o host do pgsql
```text
# Database
    application:
        ...
        environment:
            - DB_HOST=template_postgresql
            - DB_DATABASE=template
            - DB_USERNAME=root
            - DB_PASSWORD=Lfp6w7sIp1lf
        volumes:
            ...
        depends_on:
            postgresql:
                condition: service_healthy
```

No `Dockerfile` instalamos a lib e extensão voltada para o Postgres
```dockerfile
# Instala as extensões PHP PDO e pdo_pgsql, que são necessárias para a comunicação com bancos de dados MySQL
RUN docker-php-ext-install pdo pgsql pdo_pgsql
```

No arquivo de teste da saúde da aplicacao `HealthHandler`, adicionamos o método para validar o Postgres
```php
    public function liveness(): JsonResponse
    {
        $pgsqlStatus = $this->testPostgreSqlConnection();

        return response()->json([
            'pgsql' => $pgsqlStatus,
        ]);
    }
```
```php
    protected function testPostgreSqlConnection()
    {
        $results = [];

        $driver = config('database.connections.pgsql');
        $start = microtime(true);

        try {
            DB::connection('pgsql')->select("SELECT 'Health check' AS test");
            $results[] = [
                'alive' => true,
                'host' => $driver['host'],
                'duration' => $this->calculateTime($start) . ' milliseconds',
            ];
        } catch (\Exception $e) {
            $results[] = [
                'alive' => false,
                'host' => $driver['host'],
                'error' => $e->getMessage(),
                'duration' => $this->calculateTime($start) . ' milliseconds',
            ];
        }

        return $results;
    }
```
## Utilizando o MongoDB

Alteramos a `.env` para definir o database principal como o mongodb
> __Obs__:
Podemos ao invés de definir como o banco de dados principal, criar uma nova conexão no arquivo `config/database.php` e nela definir as variáveis de ambiente exclusivas para o mongo e utilizá-las no `.env`
```php
# Database
DB_CONNECTION=mongodb

# Database: MongoDB
DB_HOST=template_mongo
DB_PORT=27017
DB_DATABASE=template
DB_USERNAME=admin
DB_PASSWORD=F85Oj793SYtK


# MongoDB URI (alternativa)
MONGODB_URI=mongodb://admin:F85Oj793SYtK@template_mongo:27017/template?authSource=admin
```

No `docker-compose.yml`, alteramos o servico `application` para o host do mongodb
```php
# Database
    application:
        ...
        environment:
            - DB_CONNECTION=mongodb
            - DB_HOST=template_mongo
            - DB_PORT=27017
            - DB_DATABASE=template
            - DB_USERNAME=admin
            - DB_PASSWORD=F85Oj793SYtK
        volumes:
            ...
        depends_on:
            mongo:
                condition: service_healthy
```

No `Dockerfile` instalamos a lib e extensão voltada para o MongoDB
```dockerfile
RUN apk update && apk add --no-cache \
    ...
    openssl-dev \
    cyrus-sasl-dev
    
# Instalação da extensão mongodb
RUN pecl install mongodb && docker-php-ext-enable mongodb
```
> __Obs__:
Caso neste momento seja necessário instalar o pacote do mongodb, entre dentro do container e instale: `docker exec -it template_app composer require jenssegers/mongodb    `

No arquivo de teste da saúde da aplicacao `HealthHandler`, adicionamos o método para validar o MongoDB
```php
    public function liveness(): JsonResponse
    {
        $mongoStatus = $this->testMongoConnection();

        return response()->json([
            'mongodb' => $mongoStatus
        ]);
    }
```
```php
    protected function testMongoConnection()
    {
        $results = [];
        $driver = config('database.connections.mongodb');
        $start = microtime(true);
    
        try {
            $connection = DB::connection('mongodb')->getMongoDB();
            $command = new \MongoDB\Driver\Command(['ping' => 1]);
            $cursor = $connection->command($command);
    
            // Converter o cursor para array
            $response = current($cursor->toArray());
    
            $results[] = [
                'alive' => isset($response->ok) && $response->ok == 1,
                'host' => $driver['host'],
                'duration' => $this->calculateTime($start) . ' milliseconds',
            ];
        } catch (\Throwable $e) {
            $results[] = [
                'alive' => false,
                'host' => $driver['host'],
                'error' => $e->getMessage(),
                'duration' => $this->calculateTime($start) . ' milliseconds',
            ];
        }
    
        return $results;
    }
```

## Iniciando o projeto

Criar o arquivo `.env` no projeto e preencher as credenciais no arquivo de configuração principal
```bash
php -r "copy('.env.example', '.env');"
```
Criar uma network (caso não esteja criada)
```bash
docker network create application-network
```
Faça o build dos containeres no `docker-compose` no diretório raiz:
```bash
docker-compose up -d --build
```
> __Obs__:
Pode ser necessário instalar as dependências de forma manual com `composer install`

## Iniciando o projeto

Usuário para teste com autenticação JWT
```bash
| email   =>   test@example.com  |
| senha   =>   password          |
```

## Executando testes automatizados

Execute as migrations com os dados das seeds
```bash
php artisan migrate:fresh --seed
```
Rode o script para execução dos testes automatizados
```bash
php artisan test
```
Execute o comando para gerar um diretório `coverage-report` e um relatório em `HTML` com a cobertura de testes
```bash
php artisan test --coverage-html=coverage-report
```
Abrir o relatório no seu navegador ou com o comando
```bash
open coverage-report/index.html
```

## Serviços e Portas

| Container           | Host Port | Container Port (Internal) |
|---------------------|-----------|---------------------------|
| template_api        | `9500`    | `9500`                    |
| template_nginx      | `8001`    | `80`                      |
| template_mysql      | `3306`    | `3306`                    |
| template_postgresql | `5432`    | `5432`                    |
| template_mongodb    | `27017`   | `27017`                   |
| mongo-express       | `8081`    | `8081`                    |
| template_redis      | `6379`    | `6379`                    |
| metabase            | `4000`    | `3000`                    |

## Health

Endpoint que validam a saúde da aplicação e dos serviços:

- `http://localhost:8001/up`
- `http://localhost:8001/api/health`
- `http://localhost:8001/api/liveness`

## Documentação

Endpoint da aplicação: `http://localhost:8001/documentation`

A documentação da API deve ser realizada no formato YAML e são armazenados no diretório `storage/api-docs` pelo nome `api-docs-v1.yml`

## Monitoramento

Endpoint de monitoramento de Queues e Jobs: `http://localhost:8001/horizon/dashboard`

<!-- FOTO DO SWAGGER DA APLICACAO -->
<!-- ![Swagger Image](/storage/external/swagger.png) -->

**Referências:**

- [Especificação OpenAPI - Swagger](https://swagger.io/specification/)

## Servicos Auxiliares
### Metabase
Acesse o [Metabase](http://localhost:4000/setup.) através da porta **4000**

### Mongo Express
Acesse o [Mongo Express](http://localhost:8081/) através da porta **8081**
