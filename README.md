<div align='right'>
    <a href="./README.md">Inglês |</a>
    <a href="./PORTUGUESE.md">Português</a>
</div>

<div align='center'>
    <h1>Template</h1>
    <a href="https://www.linkedin.com/in/leonardo-akio/" target="_blank"><img src="https://img.shields.io/badge/LinkedIn%20-blue?style=flat&logo=linkedin&labelColor=blue" target="_blank"></a> 
    <img src="https://img.shields.io/badge/version-v0.1-blue"/>
    <img src="https://img.shields.io/github/contributors/akioleo/MoneyTransaction_v2"/>
    <img src="https://img.shields.io/github/stars/akioleo/MoneyTransaction_v2?style=sociale"/>
    <img src="https://img.shields.io/github/forks/akioleo/MoneyTransaction_v2?style=social"/>
</div>


## Compatibilidade

- PHP >= 8.1
- Laravel >= 10.22.0
- Composer >= 2.5.8

## Ambiente

- PHP 8.2.9
- Nginx 1.24.0
- PostgreSQL 16.1
- MongoDB 7.0
- Redis 7.2.0

## Iniciando o projeto

Criar o arquivo `.env` no projeto
```bash
php -r "copy('.env.example', '.env');"
```
Criar uma network (caso não esteja criada)
```bash
docker network create kong-net
```
Faça o build dos containeres no `docker-compose` no diretório raiz:
```bash
docker-compose up -d --build
```

## Iniciando o projeto

Usuário para teste com autenticação JWT
```bash
| email   =>   teste@teste.com   |
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

| Container                   | Host Port | Container Port (Internal) |
| --------------------------- | --------- | ------------------------- |
| customer_manager_app        | `9501`    | `9501`                    |
| customer_manager_nginx      | `8080`    | `80`                      |
| customer_manager_postgres   | `5432`    | `5432`                    |
| customer_manager_mongodb    | `27017`   | `27017`                   |
| customer_manager_redis      | `6379`    | `6379`                    |
| customer_manager_metabase   | `3000`    | `3000`                    |

## Health

Endpoint que validam a saúde da aplicação e dos serviços:

- `http://localhost:8080/health`
- `http://localhost:8080/liveness`

## Documentação 

Endpoint da aplicação: `http://localhost:8080/documentation`

A documentação da API deve ser realizada no formato YAML e são armazenados no diretório `storage/api-docs` pelo nome `api-docs-v1.yml`

## Monitoramento

Endpoint de monitoramento de Queues e Jobs: `http://localhost:8080/horizon/dashboard`

<!-- FOTO DO SWAGGER DA APLICACAO -->
<!-- ![Swagger Image](/storage/external/swagger.png) -->

**Referências:**

- [Especificação OpenAPI - Swagger](https://swagger.io/specification/)
