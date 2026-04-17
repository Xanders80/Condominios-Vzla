-- Este script se ejecutará automáticamente la primera vez que se inicie el contenedor de MariaDB.
-- No necesitas incluir sentencias 'CREATE DATABASE' o 'CREATE USER' aquí si estás usando
-- las variables de entorno MARIADB_DATABASE, MARIADB_USER, etc. en el docker-compose.yml.
-- MariaDB los crea por defecto.

-- Sin embargo, aquí puedes añadir sentencias para crear tablas o insertar datos iniciales, 
-- por ejemplo:

-- USE \`nombre_de_tu_bd\`;
-- CREATE TABLE \`usuarios\` (
--   \`id\` int(11) NOT NULL AUTO_INCREMENT,
--   \`nombre\` varchar(255) NOT NULL,
--   PRIMARY KEY (\`id\`)
-- );
