-- Archivo SQL para Sistema de Incidencias Universitarias v2
-- Base de datos: sistema_incidencias_uni_db

CREATE DATABASE IF NOT EXISTS `sistema_incidencias_uni_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `sistema_incidencias_uni_db`;

-- --------------------------------------------------------
-- Tabla `usuarios` (Mantenida por FK, login es hardcoded)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE COMMENT 'Usuario de login (admin_uni)',
  `password_hash` VARCHAR(255) NOT NULL COMMENT 'No usado para login en esta versión',
  `email` VARCHAR(100) UNIQUE,
  `nombre_completo` VARCHAR(100) COMMENT 'Nombre del administrador o rol',
  `fecha_registro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `rol` ENUM('administrador', 'usuario', 'tecnico') DEFAULT 'usuario' COMMENT 'Rol dentro del sistema (informativo)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar el usuario único para FK
INSERT INTO `usuarios` (`id`, `username`, `password_hash`, `email`, `nombre_completo`, `rol`) VALUES
(1, 'admin_uni', '$2y$10$DummyHashNotUsedForLoginCheck1234567890', 'admin@universidad.edu', 'Admin Universidad', 'administrador')
ON DUPLICATE KEY UPDATE username = 'admin_uni';

-- --------------------------------------------------------
-- Tabla `incidencias` (CON NUEVOS CAMPOS)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `incidencias` (
  `id` INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID único del reporte',
  `titulo` VARCHAR(255) NOT NULL COMMENT 'Asunto breve del reporte',
  `descripcion` TEXT NOT NULL COMMENT 'Descripción detallada',
  `descripcion_corta` VARCHAR(150) NULL COMMENT 'Resumen para vistas rápidas',
  `estado` VARCHAR(50) NOT NULL DEFAULT 'Abierta' COMMENT 'Estado actual (Abierta, En Revision, etc.)',
  `prioridad` ENUM('Baja', 'Media', 'Alta', 'Urgente') DEFAULT 'Media' COMMENT 'Prioridad del reporte',
  `ubicacion` VARCHAR(150) NOT NULL COMMENT 'Lugar específico (Aula 101, Biblioteca, etc.)', -- Campo añadido y requerido
  `categoria` VARCHAR(100) NOT NULL COMMENT 'Tipo de incidencia (IT, Mantenimiento, etc.)', -- Campo añadido y requerido
  `contacto_reportante_email` VARCHAR(100) NULL DEFAULT NULL COMMENT 'Email opcional para contacto', -- Campo añadido
  `contacto_reportante_telefono` VARCHAR(50) NULL DEFAULT NULL COMMENT 'Teléfono opcional para contacto', -- Campo añadido
  `usuario_afectado` VARCHAR(150) NULL DEFAULT NULL COMMENT 'Persona o grupo afectado (opcional)', -- Campo añadido
  `adjunto_path` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Ruta a archivo adjunto (funcionalidad no implementada)', -- Campo añadido (para futuro)
  `fecha_creacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Cuándo se reportó',
  `fecha_actualizacion` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'Última modificación',
  `fecha_cierre` DATETIME NULL DEFAULT NULL COMMENT 'Cuándo se resolvió/cerró',
  `usuario_id_creador` INT NOT NULL COMMENT 'FK a usuarios.id (quién reportó - ID 1)',
  `usuario_id_asignado` INT NULL DEFAULT NULL COMMENT 'FK a usuarios.id (quién está asignado)',
  FOREIGN KEY (`usuario_id_creador`) REFERENCES `usuarios`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (`usuario_id_asignado`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar incidencias de ejemplo universitarias (con nuevos campos)
INSERT INTO `incidencias` (`titulo`, `descripcion`, `descripcion_corta`, `estado`, `prioridad`, `ubicacion`, `categoria`, `usuario_id_creador`, `contacto_reportante_email`) VALUES
('Proyector Aula Magna no funciona', 'El proyector del Aula Magna no enciende desde esta mañana. Se revisaron las conexiones básicas. Urge para clase de las 10am.', 'Proyector Aula Magna OFF', 'Abierta', 'Urgente', 'Aula Magna', 'IT/Computación', 1, 'profesor.x@universidad.edu'),
('Falta de conexión WiFi en Biblioteca Central', 'Varios estudiantes reportan no poder conectarse a la red WiFi "UNI-ESTUDIANTES" en el piso 2 de la Biblioteca Central. Ocurre desde ayer.', 'WiFi Biblioteca P2 caído', 'En Revision', 'Media', 'Biblioteca Central P2', 'IT/Computación', 1, NULL),
('Solicitud Mantenimiento Aire Acondicionado Lab. Química', 'El aire acondicionado del Laboratorio de Química 3B no enfría correctamente, afecta a los equipos sensibles. Huele raro al encender.', 'A/C Lab Química 3B', 'Abierta', 'Alta', 'Laboratorio Química 3B', 'Mantenimiento/Infraestructura', 1, 'lab.quimica@universidad.edu'),
('Papelera llena en pasillo Edificio C', 'La papelera ubicada cerca de la escalera del Edificio C, planta baja, está desbordada desde hace 2 días.', 'Papelera llena Edif C PB', 'Abierta', 'Baja', 'Edificio C Pasillo Escalera PB', 'Servicios Generales', 1, NULL);

-- Índices (Incluyendo nuevos)
ALTER TABLE `incidencias` ADD INDEX `idx_estado` (`estado`);
ALTER TABLE `incidencias` ADD INDEX `idx_prioridad` (`prioridad`);
ALTER TABLE `incidencias` ADD INDEX `idx_fecha_creacion` (`fecha_creacion`);
ALTER TABLE `incidencias` ADD INDEX `idx_usuario_creador` (`usuario_id_creador`);
ALTER TABLE `incidencias` ADD INDEX `idx_usuario_asignado` (`usuario_id_asignado`);
ALTER TABLE `incidencias` ADD INDEX `idx_categoria` (`categoria`);
ALTER TABLE `incidencias` ADD INDEX `idx_ubicacion` (`ubicacion`);

COMMIT;
