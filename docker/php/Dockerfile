# Imagen base con PHP 8.4 y Apache
FROM php:8.4-apache

# Instalar dependencias del sistema y extensiones PHP
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        mysqli \
        zip \
        gd \
        mbstring \
        exif \
        pcntl \
        bcmath \
        opcache

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Habilitar mod_rewrite para URL amigables
RUN a2enmod rewrite

# Configurar directorio de trabajo
WORKDIR /var/www/html

# Copiar configuración personalizada de PHP
COPY php.ini /usr/local/etc/php/php.ini

# Exponer puerto 80 para Apache
EXPOSE 80
