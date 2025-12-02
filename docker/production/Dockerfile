# Estágio 1: Builder de Assets (Frontend)
FROM node:20-alpine AS frontend-builder

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm install
COPY . .
RUN npm run build

# Estágio 2: Produção (FrankenPHP)
FROM dunglas/frankenphp:1.10.1-php8.3 as final

# Copia os assets compilados do estágio de build
COPY --from=frontend-builder /app/public/build ./public/build

  ## Diretório da aplicação
ARG APP_DIR=/var/www/app

  ## Versão da Lib do Redis para PHP
ARG REDIS_LIB_VERSION=5.3.7

  ### apt-utils é um extensão de recursos do gerenciador de pacotes APT
RUN apt-get update -y && apt-get install -y --no-install-recommends \
apt-utils \
supervisor

  # dependências recomendadas de desenvolviment para ambiente linux
RUN apt-get update && apt-get install -y \
zlib1g-dev \
libzip-dev \
unzip \
libpng-dev \
libpq-dev \
libxml2-dev

RUN docker-php-ext-install mysqli pdo pdo_mysql pdo_pgsql pgsql session xml

  # habilita instalação do Redis
RUN pecl install redis-${REDIS_LIB_VERSION} \
&& docker-php-ext-enable redis

RUN docker-php-ext-install zip iconv simplexml pcntl gd fileinfo

  # Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY ./docker/supervisord/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
  ### Supervisor permite monitorar e controlar vários processos (LINUX)
  ### Bastante utilizado para manter processos em Daemon, ou seja, executando em segundo plano

WORKDIR $APP_DIR
RUN cd $APP_DIR
RUN chown www-data:www-data $APP_DIR

COPY --chown=www-data:www-data  . .
RUN rm -rf vendor
RUN composer install --no-interaction --no-dev

RUN apt-get clean && rm -rf /var/lib/apt/lists/*
# RUN apt update -y && apt install nano git -y
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# O FrankenPHP escutará na porta 80 por padrão
EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
