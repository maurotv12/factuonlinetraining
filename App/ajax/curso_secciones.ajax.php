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

$datos = json_decode(file_get_contents('php://input'), true); // ← Agregar true para array asociativo
$accion = $datos['accion'] ?? '';

switch ($accion) {
    case 'crearSeccion':
        // Acceder a los datos que identificaste
        $idCurso = $datos['idCurso'];
        $titulo = $datos['titulo'];
        $descripcion = $datos['descripcion'];

        $respuesta = ControladorCursos::ctrCrearSeccion($datos);

        // Devolver la respuesta como JSON
        echo json_encode($respuesta);
        break;

    case 'actualizarSeccion':
        $respuesta = ControladorCursos::ctrActualizarSeccion($datos);
        echo json_encode($respuesta);
        break;

    case 'obtenerSecciones':
        $idCurso = $datos['idCurso'] ?? null;
        if ($idCurso) {
            $respuesta = ControladorCursos::ctrObtenerSecciones($idCurso);
            echo json_encode($respuesta);
        } else {
            echo json_encode(['success' => false, 'mensaje' => 'ID de curso requerido']);
        }
        break;

    case 'eliminarSeccion':
        $idSeccion = $datos['idSeccion'] ?? null;
        if ($idSeccion) {
            $respuesta = ControladorCursos::ctrEliminarSeccion($idSeccion);
            echo json_encode($respuesta);
        } else {
            echo json_encode(['success' => false, 'mensaje' => 'ID de sección requerido']);
        }
        break;

    default:
        // Manejar caso de acción no válida
        echo json_encode(['success' => false, 'mensaje' => 'Acción no válida.']);
        break;
}
