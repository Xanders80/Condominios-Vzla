# ============================================
# Dockerfile para PHP 8.5 (última versión)
# ============================================
FROM php:8.5.5-fpm-bookworm

# Metadatos
LABEL maintainer="xanders80@gmail.com"
LABEL php.version="8.5.5"

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    libpq-dev \
    libssl-dev \
    gettext \
    libffi-dev \
    libmariadb-dev \
    libwebp-dev \
    redis-server \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP necesarias
RUN docker-php-ext-install gd pdo pdo_mysql

# Instalar extensión Redis vía PECL
RUN pecl install redis && docker-php-ext-enable redis

# Instalar GD por separado (requiere configuración especial)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd

# Instalar Composer (última versión estable)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

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

# Crear usuario no-root
RUN usermod -u 1000 www-data

USER www-data

CMD ["php-fpm"]