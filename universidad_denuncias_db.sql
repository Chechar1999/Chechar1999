-- 1. Crear la Base de Datos (Si no existe)
-- Puedes elegir otro nombre si lo prefieres
CREATE DATABASE IF NOT EXISTS `universidad_denuncias_db`
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 2. Seleccionar la Base de Datos para usarla
USE `universidad_denuncias_db`;

-- 3. Crear la Tabla 'denuncias'
CREATE TABLE IF NOT EXISTS `denuncias` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,                     -- Identificador único para cada denuncia
  `timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,         -- Fecha y hora de registro automático
  `complainant_name` VARCHAR(150) DEFAULT NULL,            -- Nombre del denunciante (puede ser NULO o 'Anónimo')
  `complainant_role` VARCHAR(50) NOT NULL,                -- Rol (Estudiante, Docente, etc.)
  `complaint_type` VARCHAR(100) NOT NULL,                 -- Tipo de denuncia (Académica, Acoso, etc.)
  `description` TEXT NOT NULL,                             -- Descripción detallada (TEXT permite texto largo)
  `involved_parties` VARCHAR(255) DEFAULT NULL,           -- Personas involucradas (puede ser NULO)
  `status` VARCHAR(50) NOT NULL DEFAULT 'Recibida'      -- Estado actual de la denuncia (ej: Recibida, En Proceso, Resuelta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Opcional: Añadir un índice al estado para búsquedas más rápidas si hay muchas denuncias
-- CREATE INDEX idx_status ON denuncias(status);

-- Usar la base de datos existente
USE `universidad_denuncias_db`;

-- Crear la tabla 'usuarios' para el login
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,           -- Nombre de usuario para login (único)
  `password` VARCHAR(255) NOT NULL,                 -- Contraseña HASHED (MUY IMPORTANTE)
  `full_name` VARCHAR(100) DEFAULT NULL,            -- Nombre completo (opcional)
  `role` VARCHAR(30) NOT NULL DEFAULT 'viewer',     -- Rol del usuario (ej: viewer, admin - para futuras mejoras)
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP  -- Fecha de creación del usuario
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Opcional: Añadir un índice al username para búsquedas rápidas
-- CREATE INDEX idx_username ON usuarios(username);


