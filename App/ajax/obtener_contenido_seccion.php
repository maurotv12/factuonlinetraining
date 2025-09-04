<?php

/**
 * AJAX para obtener contenido de una sección específica
 */

session_start();
header('Content-Type: application/json');

// Verificar autenticación
if (!isset($_SESSION['idU'])) {
    echo json_encode(['success' => false, 'mensaje' => 'Usuario no autenticado']);
    exit;
}

// Incluir controladores necesarios
require_once "../controladores/general.controlador.php";
require_once "../modelos/conexion.php";

// Verificar que el usuario sea profesor
if (!ControladorGeneral::ctrUsuarioTieneAlgunRol(['profesor'])) {
    echo json_encode(['success' => false, 'mensaje' => 'No tienes permisos']);
    exit;
}

try {
    // Obtener datos JSON
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    $seccionId = $data['seccionId'] ?? null;
    $idUsuario = $_SESSION['idU'];

    if (!$seccionId) {
        echo json_encode(['success' => false, 'mensaje' => 'ID de sección requerido']);
        exit;
    }

    // Verificar permisos - que la sección pertenezca a un curso del profesor
    $conexion = Conexion::conectar();
    $stmt = $conexion->prepare("
        SELECT cs.id 
        FROM curso_secciones cs 
        JOIN curso c ON cs.id_curso = c.id 
        WHERE cs.id = :seccion_id AND c.id_persona = :id_usuario
    ");
    $stmt->bindParam(':seccion_id', $seccionId, PDO::PARAM_INT);
    $stmt->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        echo json_encode(['success' => false, 'mensaje' => 'No tienes permisos para ver esta sección']);
        exit;
    }

    // Obtener contenido de la sección
    $stmt = $conexion->prepare("
        SELECT sc.*, sca.storage_path, sca.public_url, sca.tamano_bytes, sca.duracion_segundos
        FROM seccion_contenido sc
        LEFT JOIN seccion_contenido_assets sca ON sc.id = sca.id_contenido
        WHERE sc.id_seccion = :seccion_id AND sc.estado = 'activo'
        ORDER BY sc.orden ASC
    ");
    $stmt->bindParam(':seccion_id', $seccionId, PDO::PARAM_INT);
    $stmt->execute();

    $contenido = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Procesar datos adicionales
    foreach ($contenido as &$item) {
        if (!empty($item['tamano_bytes'])) {
            $item['tamano_formateado'] = formatearTamaño($item['tamano_bytes']);
        }
        if (!empty($item['duracion_segundos'])) {
            $item['duracion_formateada'] = gmdate("H:i:s", $item['duracion_segundos']);
        }
    }

    echo json_encode([
        'success' => true,
        'contenido' => $contenido
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'mensaje' => 'Error al obtener contenido',
        'error' => $e->getMessage()
    ]);
}

/**
 * Formatear tamaño de archivo
 */
function formatearTamaño($bytes)
{
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= pow(1024, $pow);

    return round($bytes, 2) . ' ' . $units[$pow];
}
