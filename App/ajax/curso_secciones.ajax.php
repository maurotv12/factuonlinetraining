<?php

/**
 * AJAX para gestión dinámica de cursos
 * Permite edición de campos individuales, secciones y contenido
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

switch ($accion) {
    case 'crearSeccion':
        $datos = json_decode(file_get_contents('php://input'));

        // Acceder a los datos que identificaste
        $idCurso = $datos->idCurso;
        $titulo = $datos->titulo;
        $descripcion = $datos->descripcion;
        // Código para crear una nueva sección
        break;

    case 'actualizarSeccion':
        // Código para actualizar una sección existente
        break;

    case 'obtenerSecciones':
        // Código para obtener las secciones del curso
        break;

    case 'eliminarSeccion':
        // Código para eliminar una sección
        break;

    default:
        // Manejar caso de acción no válida
        echo json_encode(['success' => false, 'mensaje' => 'Acción no válida.']);
        break;
}
