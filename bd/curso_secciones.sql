-- Tabla para las secciones del curso
CREATE TABLE IF NOT EXISTS `curso_secciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_curso` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `orden` int(11) NOT NULL DEFAULT 1,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_curso` (`id_curso`),
  KEY `idx_orden` (`orden`),
  FOREIGN KEY (`id_curso`) REFERENCES `curso` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla para el contenido de cada sección (videos, PDFs, etc.)
CREATE TABLE IF NOT EXISTS `seccion_contenido` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_seccion` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo` enum('video','pdf','texto','enlace') NOT NULL,
  `archivo_url` varchar(500) DEFAULT NULL,
  `duracion` varchar(10) DEFAULT NULL COMMENT 'Duración en formato mm:ss para videos',
  `tamaño_archivo` bigint(20) DEFAULT NULL COMMENT 'Tamaño del archivo en bytes',
  `orden` int(11) NOT NULL DEFAULT 1,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_seccion` (`id_seccion`),
  KEY `idx_orden` (`orden`),
  KEY `idx_tipo` (`tipo`),
  FOREIGN KEY (`id_seccion`) REFERENCES `curso_secciones` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Índices adicionales para optimización
CREATE INDEX `idx_curso_orden` ON `curso_secciones` (`id_curso`, `orden`);
CREATE INDEX `idx_seccion_orden` ON `seccion_contenido` (`id_seccion`, `orden`);
