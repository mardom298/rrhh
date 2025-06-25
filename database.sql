-- Crear base de datos en MySQL
CREATE DATABASE sistema_rrhh_ballesteros CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Crear usuario (opcional)
CREATE USER 'rrhh_user'@'localhost' IDENTIFIED BY 'rrhh_password';
GRANT ALL PRIVILEGES ON sistema_rrhh_ballesteros.* TO 'rrhh_user'@'localhost';
FLUSH PRIVILEGES;
