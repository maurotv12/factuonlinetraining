<?php

/**
 * AJAX para gestión dinámica de cursos
 * Permite edición de campo de secciones y contenido
 */

session_start();
header('Content-Type: application/json');

// Verificar autenticación
if (!isset($_SESSION['idU'])) {
    echo json_encode(['success' => false, 'mensaje' => 'Usuario no autenticado']);
    exit;
}

// Incluir controladores necesarios con rutas absolutas
$baseDir = dirname(dirname(__FILE__));
require_once $baseDir . "/controladores/cursos.controlador.php";
require_once $baseDir . "/controladores/general.controlador.php";
require_once $baseDir . "/modelos/conexion.php";

// Verificar que el usuario sea profesor
if (!ControladorGeneral::ctrUsuarioTieneAlgunRol(['profesor'])) {
    echo json_encode(['success' => false, 'mensaje' => 'No tienes permisos para editar cursos']);
    exit;
}
