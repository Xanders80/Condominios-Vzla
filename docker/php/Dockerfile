# ============================================
# Dockerfile para PHP 8.5 (última versión)
# ============================================
FROM php:8.5.5-fpm-alpine3.22

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
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# Configurar e instalar extensiones PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        mysqli \
        gd \
        zip \
        mbstring \
        exif \
        pcntl \
        bcmath \
        intl \
        opcache \
        xml

# Instalar Composer (última versión estable)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar OPcache para rendimiento
RUN { \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=4000'; \
    echo 'opcache.revalidate_freq=2'; \
    echo 'opcache.fast_shutdown=1'; \
    echo 'opcache.enable_cli=1'; \
} > /usr/local/etc/php/conf.d/opcache-recommended.ini

# Directorio de trabajo
WORKDIR /var/www/html

# Puerto por defecto de PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]
