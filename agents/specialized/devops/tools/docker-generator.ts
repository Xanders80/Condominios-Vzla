/**
 * Docker Generator Utility
 * 
 * Generate Docker configuration for Laravel projects:
 * - Dockerfile for PHP-FPM
 * - docker-compose.yml with MySQL, Redis, Mailhog
 * - Nginx configuration
 * - Sail customization
 */

import { writeFile, fileExists } from '../../shared/file-operations';

interface DockerConfig {
  appName: string;
  phpVersion: string;
  mysqlVersion: string;
  redisEnabled: boolean;
  mailhogEnabled: boolean;
  nodeVersion: string;
}

/**
 * Generate Dockerfile for PHP-FPM
 */
export function generateDockerfile(basePath: string, config: DockerConfig): string {
  const filePath = `${basePath}/docker/app/Dockerfile`;

  const content = `FROM php:${config.phpVersion}-fpm

ARG user=www-data
ARG uid=1000

# Install system dependencies
RUN apt-get update && apt-get install -y \\
    git \\
    curl \\
    libpng-dev \\
    libonig-dev \\
    libxml2-dev \\
    libzip-dev \\
    zip \\
    unzip \\
    libmysqlclient-dev \\
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Redis extension
${config.redisEnabled ? 'RUN pecl install redis && docker-php-ext-enable redis' : ''}

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \\
    chown -R $user:$user /home/$user

# Set working directory
WORKDIR /var/www/html

COPY --chown=$user:$user . /var/www/html

USER $user
`;

  writeFile(filePath, content);
  return filePath;
}

/**
 * Generate docker-compose.yml
 */
export function generateDockerCompose(basePath: string, config: DockerConfig): string {
  const filePath = `${basePath}/docker-compose.yml`;

  const services = `version: '3.8'

services:
  app:
    build:
      context: ./docker/app
      dockerfile: Dockerfile
    container_name: ${config.appName}-app
    restart: unless-stopped
    working_dir: /var/www/html
    volumes:
      - ./:/var/www/html
    networks:
      - ${config.appName}-network
    depends_on:
      - mysql
${config.redisEnabled ? '      - redis' : ''}

  nginx:
    image: nginx:alpine
    container_name: ${config.appName}-nginx
    restart: unless-stopped
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./:/var/www/html
      - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf
    networks:
      - ${config.appName}-network
    depends_on:
      - app

  mysql:
    image: mysql:${config.mysqlVersion}
    container_name: ${config.appName}-mysql
    restart: unless-stopped
    environment:
      MYSQL_DATABASE: ${config.appName}
      MYSQL_ROOT_PASSWORD: root
      MYSQL_USER: ${config.appName}
      MYSQL_PASSWORD: secret
    ports:
      - "3306:3306"
    volumes:
      - mysql-data:/var/lib/mysql
    networks:
      - ${config.appName}-network

${config.redisEnabled ? `
  redis:
    image: redis:alpine
    container_name: ${config.appName}-redis
    restart: unless-stopped
    ports:
      - "6379:6379"
    volumes:
      - redis-data:/data
    networks:
      - ${config.appName}-network
` : ''}
${config.mailhogEnabled ? `
  mailhog:
    image: mailhog/mailhog
    container_name: ${config.appName}-mailhog
    restart: unless-stopped
    ports:
      - "1025:1025"
      - "8025:8025"
    networks:
      - ${config.appName}-network
` : ''}
  node:
    image: node:${config.nodeVersion}-alpine
    container_name: ${config.appName}-node
    working_dir: /var/www/html
    volumes:
      - ./:/var/www/html
    networks:
      - ${config.appName}-network

volumes:
  mysql-data:
  ${config.redisEnabled ? 'redis-data:' : ''}

networks:
  ${config.appName}-network:
    driver: bridge
`;

  writeFile(filePath, services);
  return filePath;
}

/**
 * Generate Nginx configuration for Docker
 */
export function generateNginxConfig(basePath: string, appName: string): string {
  const dir = `${basePath}/docker/nginx`;
  const filePath = `${dir}/default.conf`;

  const content = `server {
    listen 80;
    server_name localhost;
    root /var/www/html/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \\.php$ {
        fastcgi_pass app:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\\. {
        deny all;
    }
}
`;

  writeFile(filePath, content);
  return filePath;
}
