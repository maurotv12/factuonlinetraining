<?php
// Script simple para crear las tablas si no existen
require_once "modelos/conexion.php";

try {
    $conn = Conexion::conectar();

    // SQL para crear tabla curso_secciones
    $sqlSecciones = "
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
      KEY `idx_orden` (`orden`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";

    // SQL para crear tabla seccion_contenido
    $sqlContenido = "
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
      KEY `idx_tipo` (`tipo`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";

    // Ejecutar las queries
    $conn->exec($sqlSecciones);
    echo "✅ Tabla curso_secciones creada/verificada<br>";

    $conn->exec($sqlContenido);
    echo "✅ Tabla seccion_contenido creada/verificada<br>";

    echo "<br>🎉 Tablas configuradas correctamente. Ya puedes usar la funcionalidad de secciones.";
    echo "<br><br><a href='/cursosApp/App/'>← Volver a la aplicación</a>";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
