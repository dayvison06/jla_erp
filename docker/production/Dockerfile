# Usa FrankenPHP como base
FROM dunglas/frankenphp:1.10.1-php8.3

WORKDIR /app

# Instala dependências do sistema e Node.js + npm
RUN apt-get update -y && apt-get install -y --no-install-recommends \
    apt-utils \
    supervisor \
    zlib1g-dev \
    libzip-dev \
    unzip \
    libpng-dev \
    libpq-dev \
    libxml2-dev \
    curl \
    ca-certificates \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Instala extensões PHP e Redis
RUN docker-php-ext-install zip iconv simplexml pcntl gd fileinfo mysqli pdo pdo_mysql pdo_pgsql pgsql session xml
ARG REDIS_LIB_VERSION=5.3.7
RUN pecl install redis-${REDIS_LIB_VERSION} && docker-php-ext-enable redis

# Instala Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copia arquivos do projeto
COPY . .

# Instala dependências PHP e JS, e faz o build do frontend
RUN composer install --no-interaction --no-dev \
    && npm install \
    && npm run build

# Ajusta permissões
RUN chown -R www-data:www-data /app

# Configura Supervisor
COPY ./docker/production/supervisord/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Limpa cache do apt
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
