## docker-compose
```bash
(5)   container_name: template_app

(9)   DB_HOST=template_mysql
(10)  DB_DATABASE=template

(31)  container_name: template_nginx
(55)  container_name: template_mysql

(60)  MYSQL_DATABASE=template
(68)  container_name: template_mongo
(80)  container_name: template_redis
(88)  container_name: template_metabase
```
## .env
```bash
(1)   APP_NAME=template
(8)   COMPOSE_PROJECT_NAME=template_app

(16)  DB_HOST=template_mysql
(18)  DB_DATABASE=template

(23)  MONGO_DB_DATABASE=template
```