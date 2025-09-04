<?php

/**
 * AJAX para obtener categorías disponibles
 */

session_start();
header('Content-Type: application/json');

// Verificar autenticación
if (!isset($_SESSION['idU'])) {
    echo json_encode(['success' => false, 'mensaje' => 'Usuario no autenticado']);
    exit;
}

// Incluir controladores necesarios
require_once "../controladores/cursos.controlador.php";

try {
    $categorias = ControladorCursos::ctrObtenerCategorias();

    echo json_encode([
        'success' => true,
        'categorias' => $categorias
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'mensaje' => 'Error al obtener categorías',
        'error' => $e->getMessage()
    ]);
}
