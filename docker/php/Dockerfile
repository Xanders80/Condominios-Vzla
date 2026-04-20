# ============================================
# Dockerfile para PHP 8.5 (última versión)
# ============================================
FROM php:8.5.5-fpm-alpine3.22

# Metadatos
LABEL maintainer="xanders80@gmail.com"
LABEL php.version="8.5.5"

# Actualizar índice de paquetes
RUN apk update

# Instalar dependencias del sistema
RUN apk add --no-cache \
    git \
    unzip \
    curl \
    libpng-dev \
    jpeg-dev \
    freetype-dev \
    libzip-dev \
    oniguruma-dev \
    libxml2-dev \
    icu-dev \
    postgresql-dev \
    openssl-dev \
    gettext-dev \
    libffi-dev \
    mariadb-dev \
    libwebp-dev \
    && rm -rf /var/cache/apk/*

# Instalar extensiones PHP una por una
RUN docker-php-ext-install pdo && \
    docker-php-ext-install pdo_mysql && \
    docker-php-ext-install mysqli && \
    docker-php-ext-install zip && \
    docker-php-ext-install mbstring && \
    docker-php-ext-install exif && \
    docker-php-ext-install pcntl && \
    docker-php-ext-install bcmath && \
    docker-php-ext-install intl && \
    docker-php-ext-install opcache && \
    docker-php-ext-install xml && \
    docker-php-ext-install openssl && \
    docker-php-ext-install tokenizer && \
    docker-php-ext-install sodium

# Instalar GD por separado (requiere configuración especial)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd

# Instalar Composer (última versión estable)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar OPcache para Laravel
RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.enable_cli=1'; \
    echo 'opcache.memory_consumption=256'; \
    echo 'opcache.interned_strings_buffer=16'; \
    echo 'opcache.max_accelerated_files=10000'; \
    echo 'opcache.revalidate_freq=2'; \
    echo 'opcache.fast_shutdown=1'; \
    echo 'opcache.optimization_level=0x7FFFBFFF'; \
} > /usr/local/etc/php/conf.d/opcache-recommended.ini

# Copiar php.ini personalizado
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

# Directorio de trabajo
WORKDIR /var/www/html

# Puerto por defecto de PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]