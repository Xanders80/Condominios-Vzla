# Guía de Entorno de Desarrollo con Docker

Esta guía explica cómo ejecutar, gestionar y detener el entorno de desarrollo local basado en **Apache, PHP 8.4 y MariaDB** utilizando Docker Compose.

## Requisitos Previos

Antes de comenzar, asegúrate de tener instalados los siguientes programas en tu sistema:
- [Docker](https://docs.docker.com/get-docker/)
- [Docker Compose](https://docs.docker.com/compose/install/)

## 1. Configuración Inicial

Si es la primera vez que vas a levantar el entorno o has hecho cambios en el archivo `.env` o en los `Dockerfile`:

1. Asegúrate de tener configurado tu archivo `.env` en la raíz del proyecto con las variables de base de datos necesarias:
   ```env
   DB_ROOT_PASSWORD=secret
   DB_NAME=mi_base_datos
   DB_USER=mi_usuario
   DB_PASSWORD=mi_password
   ```
2. Construye las imágenes necesarias ejecutando el siguiente comando:
   ```bash
   docker compose build
   ```

## 2. Ejecutar el Entorno

Para levantar los contenedores de Apache/PHP y MariaDB, ejecuta:

```bash
docker compose up -d
```
*El flag `-d` (detached mode) permite que los contenedores se ejecuten en segundo plano, dejándote la terminal libre.*

### Acceso a la Aplicación
Una vez que los contenedores estén en ejecución (`Up`), podrás acceder a la aplicación desde tu navegador web en la siguiente dirección:

**http://localhost:8080**

## 3. Comandos Útiles

Aquí tienes una lista de los comandos más frecuentes que utilizarás durante el desarrollo:

- **Ver el estado de los contenedores:**
  ```bash
  docker compose ps
  ```

- **Ver los logs (registros) en tiempo real:**
  ```bash
  docker compose logs -f
  ```
  *(Para ver los logs de un servicio específico, añade su nombre al final, ej: `docker compose logs -f php` o `docker compose logs -f mariadb`)*

- **Detener los contenedores (sin borrar datos):**
  ```bash
  docker compose stop
  ```

- **Detener y eliminar los contenedores (no afecta los volúmenes persistentes):**
  ```bash
  docker compose down
  ```

- **Ejecutar comandos de Composer o PHP dentro del contenedor:**
  Si necesitas instalar dependencias, puedes entrar al contenedor de PHP así:
  ```bash
  docker compose exec php bash
  ```
  O ejecutar un comando directamente (por ejemplo, instalar dependencias de Composer):
  ```bash
  docker compose exec php composer install
  ```

## 4. Persistencia de Datos y Base de Datos

- **Volúmenes:** Los datos de tu base de datos MariaDB se guardan en un volumen persistente llamado `mariadb_data`. Esto significa que si detienes y borras el contenedor (`docker compose down`), **tus datos seguirán ahí** la próxima vez que lo levantes.
- **Inicialización:** Si la base de datos es nueva y está vacía, puedes colocar scripts `.sql` en el directorio `docker/mariadb/init-db/`. Estos scripts se ejecutarán automáticamente al arrancar MariaDB por primera vez, creando tus tablas iniciales.

## 5. Solución de Problemas (Troubleshooting)

- **Cambios en el código no se reflejan:** Como el código fuente (`./src`) está montado como un volumen en `/var/www/html`, los cambios en tus archivos locales deberían reflejarse inmediatamente. Si no es así, verifica si tienes caché activada en el navegador o en tu código.
- **Error "Port is already allocated":** Esto significa que otro servicio en tu computadora ya está usando el puerto `8080` (o `3306` para MariaDB). Deberás detener el otro servicio o cambiar el puerto en el archivo `docker-compose.yml`.
- **Reconstrucción forzada:** Si modificas el `Dockerfile`, instalas una nueva extensión de PHP, o cambias configuraciones del servidor, debes reconstruir la imagen:
  ```bash
  docker compose build --no-cache
  docker compose up -d
  ```
