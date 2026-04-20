# Guía de Entorno de Desarrollo con Docker

Esta guía explica cómo ejecutar, gestionar y detener el entorno de desarrollo local basado en **Nginx, PHP 8.5.5-FPM y MariaDB compartida** utilizando Docker Compose.

## Estructura del Entorno

```
┌─────────────────────────────────────────────┐
│         projects-network (externa)            │
│  ┌─────────────┐    ┌─────────────────┐   │
│  │   Nginx    │───▶│  PHP-FPM 8.5    │   │
│  │ :8080 (exp)│    │   :9000 (int)   │   │
│  └─────────────┘    └─────────────────┘   │
│                              │            │
│         mariadb-shared ◀───────┘            │
└─────────────────────────────────────────────┘
```

## Requisitos Previos

Antes de comenzar, asegúrate de tener instalados los siguientes programas en tu sistema:
- [Docker](https://docs.docker.com/get-docker/)
- [Docker Compose](https://docs.docker.com/compose/install/)
- La red externa `projects-network` debe estar creada

## 1. Configuración de Red Externa

Antes de levantar el entorno, verifica que la red externa exista:

```bash
# Crear la red si no existe
docker network create projects-network 2>/dev/null || true
```

## 2. Configuración Inicial

Si es la primera vez que vas a levantar el entorno o has hecho cambios:

1. Asegúrate de tener configurado tu archivo `.env`:
   ```env
   DB_HOST=mariadb-shared
   DB_DATABASE=tu_base_datos
   DB_USERNAME=tu_usuario
   DB_PASSWORD=tu_password
   ```

2. Construye las imágenes necesarias:
   ```bash
   docker compose build
   ```

## 3. Ejecutar el Entorno

Para levantar los contenedores de Nginx y PHP-FPM:

```bash
docker compose up -d
```

*El flag `-d` (detached mode) permite que los contenedores se ejecuten en segundo plano.*

### Acceso a la Aplicación

**http://localhost:8080**

## 4. Comandos Útiles

- **Ver el estado de los contenedores:**
  ```bash
  docker compose ps
  ```

- **Ver los logs en tiempo real:**
  ```bash
  docker compose logs -f
  ```
  *(Para un servicio específico: `docker compose logs -f php`)*

- **Ver logs de Nginx:**
  ```bash
  docker compose logs -f nginx
  ```

- **Detener los contenedores (sin borrar datos):**
  ```bash
  docker compose stop
  ```

- **Detener y eliminar los contenedores:**
  ```bash
  docker compose down
  ```

- **Ejecutar comandos dentro del contenedor PHP:**
  ```bash
  docker compose exec php bash
  ```

- **Ejecutar comandos de Composer:**
  ```bash
  docker compose exec php composer install
  docker compose exec php artisan migrate
  ```

- **Reconstrucción forzada:**
  ```bash
  docker compose build --no-cache
  docker compose up -d
  ```

## 5. Persistencia de Datos

- **Código fuente:** Montado en `./src:/var/www/html`
- **Logs:** Disponible en los contenedores
  - Nginx: `docker compose logs nginx`
  - PHP: `docker compose logs php`

## 6. Archivos de Configuración

| Archivo | Descripción |
|---------|-------------|
| `docker-compose.yml` | Servicios Nginx + PHP-FPM |
| `docker/php/Dockerfile` | Imagen PHP 8.5.5-FPM |
| `docker/php/php.ini` | Configuración PHP personalizada |
| `docker/nginx/default.conf` | Configuración Nginx |

## 7. Solución de Problemas

- **Cambios en el código no se reflejan:** El código está montado como volumen, los cambios deberían reflejarse inmediatamente. Verifica el caché del navegador.

- **Error "Port is already allocated":** Otro servicio está usando el puerto `8080`. Detén el otro servicio o cambia el puerto en `docker-compose.yml`.

- **Error de conexión a MariaDB:** Verifica que `mariadb-shared` esté corriendo y que la red `projects-network` exista:
  ```bash
  docker network ls
  docker ps -a --filter "name=mariadb"
  ```

- **Error al construir:** Si hay errores de extensión, rebuild con:
  ```bash
  docker compose build --no-cache --pull
  docker compose up -d
  ```