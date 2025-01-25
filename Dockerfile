FROM php:8.4.3-fpm-alpine

# Atualiza pacotes e instala dependências necessárias
RUN apk update && apk add --no-cache \
    autoconf \
    build-base \
    libpq-dev \
    shadow \
    openssl \
    bash \
    nginx \
    git \
    curl \
    mysql-client

# Instala as extensões PHP PDO e pdo_mysql, que são necessárias para a comunicação com bancos de dados MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Instalando Redis e dependências necessárias
RUN pecl install redis && docker-php-ext-enable redis

# Crie o diretório home do usuário www-data
RUN mkdir -p /var/www/home/www-data

# Baixa e instala o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Define o diretório de trabalho
WORKDIR /var/www

# Executa comandos de permissão
RUN chown -R www-data:www-data /var/www
COPY --chown=www-data:www-data ./ .

# Instala dependências do Composer
RUN rm -rf vendor
RUN composer install --no-interaction

# Copia os arquivos de configuração do PHP-FPM para o container
COPY ./.docker/php-fpm/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY ./.docker/php-fpm/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf
