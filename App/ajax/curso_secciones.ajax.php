<?php

/**
 * AJAX para gestión de secciones de curso
 * Maneja creación, actualización y eliminación de secciones
 */

// Iniciar sesión y configurar headers
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

// Incluir modelos y controladores
require_once "../modelos/conexion.php";
require_once "../modelos/cursos.modelo.php";
require_once "../controladores/cursos.controlador.php";
require_once "../controladores/general.controlador.php";

// Verificar método de solicitud
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'mensaje' => 'Método no permitido']);
    exit;
}

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['idU'])) {
    echo json_encode(['success' => false, 'mensaje' => 'Usuario no autenticado']);
    exit;
}

try {
    // Verificar si es JSON o form data
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

    if (strpos($contentType, 'application/json') !== false) {
        // Obtener datos JSON
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
    } else {
        // Obtener datos de POST tradicional
        $data = $_POST;
    }

    if (!$data) {
        echo json_encode(['success' => false, 'mensaje' => 'Datos inválidos']);
        exit;
    }

    $accion = $data['accion'] ?? $data['action'] ?? '';
    $idUsuario = $_SESSION['idU'];

    // Verificar que el usuario sea profesor
    if (!ControladorGeneral::ctrUsuarioTieneAlgunRol(['profesor'])) {
        echo json_encode(['success' => false, 'mensaje' => 'No tienes permisos para esta acción']);
        exit;
    }

    switch ($accion) {
        case 'crearSeccion':
        case 'crear_seccion':
            crearSeccion($data, $idUsuario);
            break;

        case 'actualizarSeccion':
        case 'actualizar_seccion':
            actualizarSeccion($data, $idUsuario);
            break;

        case 'eliminarSeccion':
        case 'eliminar_seccion':
            eliminarSeccion($data, $idUsuario);
            break;

        case 'obtenerSecciones':
        case 'obtener_secciones':
            obtenerSecciones($data, $idUsuario);
            break;

        default:
            echo json_encode(['success' => false, 'mensaje' => 'Acción no válida: ' . $accion]);
            break;
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'mensaje' => 'Error interno del servidor',
        'error' => $e->getMessage()
    ]);
}

/**
 * Crear nueva sección
 */
function crearSeccion($data, $idUsuario)
{
    $idCurso = $data['idCurso'] ?? $data['id_curso'] ?? null;
    $titulo = trim($data['titulo'] ?? '');
    $descripcion = trim($data['descripcion'] ?? '');

    // Validaciones
    if (!$idCurso || !$titulo) {
        echo json_encode(['success' => false, 'mensaje' => 'Datos incompletos']);
        return;
    }

    // Verificar que el curso pertenece al usuario
    $curso = ControladorCursos::ctrMostrarCursos('id', $idCurso);
    if (!$curso || $curso['id_persona'] != $idUsuario) {
        echo json_encode(['success' => false, 'mensaje' => 'No tienes permisos para editar este curso']);
        return;
    }

    // Obtener el siguiente orden
    $conexion = Conexion::conectar();
    $stmt = $conexion->prepare("SELECT COALESCE(MAX(orden), 0) + 1 as siguiente_orden FROM curso_secciones WHERE id_curso = :id_curso");
    $stmt->bindParam(':id_curso', $idCurso, PDO::PARAM_INT);
    $stmt->execute();
    $orden = $stmt->fetch(PDO::FETCH_ASSOC)['siguiente_orden'];

    // Crear sección
    $datosSeccion = [
        'id_curso' => $idCurso,
        'titulo' => $titulo,
        'descripcion' => $descripcion,
        'orden' => $orden,
        'estado' => 'activo'
    ];

    $resultado = ControladorCursos::ctrCrearSeccion($datosSeccion);

    if ($resultado !== 'error' && is_numeric($resultado)) {
        echo json_encode([
            'success' => true,
            'mensaje' => 'Sección creada correctamente',
            'message' => 'Sección creada correctamente', // Para compatibilidad
            'id' => $resultado
        ]);
    } else {
        echo json_encode(['success' => false, 'mensaje' => 'Error al crear la sección']);
    }
}

/**
 * Actualizar sección existente
 */
function actualizarSeccion($data, $idUsuario)
{
    $id = $data['id'] ?? null;
    $titulo = trim($data['titulo'] ?? '');
    $descripcion = trim($data['descripcion'] ?? '');

    // Validaciones
    if (!$id || !$titulo) {
        echo json_encode(['success' => false, 'mensaje' => 'Datos incompletos']);
        return;
    }

    // Verificar que la sección pertenece a un curso del usuario
    $conexion = Conexion::conectar();
    $stmt = $conexion->prepare("
        SELECT cs.*, c.id_persona 
        FROM curso_secciones cs 
        JOIN curso c ON cs.id_curso = c.id 
        WHERE cs.id = :id
    ");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $seccion = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$seccion || $seccion['id_persona'] != $idUsuario) {
        echo json_encode(['success' => false, 'mensaje' => 'No tienes permisos para editar esta sección']);
        return;
    }

    // Actualizar sección
    $datosSeccion = [
        'id' => $id,
        'titulo' => $titulo,
        'descripcion' => $descripcion,
        'orden' => $seccion['orden'], // Mantener orden actual
        'estado' => $seccion['estado'] // Mantener estado actual
    ];

    $resultado = ControladorCursos::ctrActualizarSeccion($datosSeccion);

    if ($resultado === 'ok') {
        echo json_encode([
            'success' => true,
            'mensaje' => 'Sección actualizada correctamente',
            'message' => 'Sección actualizada correctamente' // Para compatibilidad
        ]);
    } else {
        echo json_encode(['success' => false, 'mensaje' => 'Error al actualizar la sección']);
    }
}

/**
 * Eliminar sección
 */
function eliminarSeccion($data, $idUsuario)
{
    $id = $data['id'] ?? null;

    if (!$id) {
        echo json_encode(['success' => false, 'mensaje' => 'ID de sección requerido']);
        return;
    }

    // Verificar permisos
    $conexion = Conexion::conectar();
    $stmt = $conexion->prepare("
        SELECT cs.*, c.id_persona 
        FROM curso_secciones cs 
        JOIN curso c ON cs.id_curso = c.id 
        WHERE cs.id = :id
    ");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $seccion = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$seccion || $seccion['id_persona'] != $idUsuario) {
        echo json_encode(['success' => false, 'mensaje' => 'No tienes permisos para eliminar esta sección']);
        return;
    }

    // Eliminar contenido asociado primero
    $stmt = $conexion->prepare("DELETE FROM seccion_contenido WHERE id_seccion = :id_seccion");
    $stmt->bindParam(':id_seccion', $id, PDO::PARAM_INT);
    $stmt->execute();

    // Eliminar sección
    $resultado = ControladorCursos::ctrEliminarSeccion($id);

    if ($resultado === 'ok') {
        echo json_encode([
            'success' => true,
            'mensaje' => 'Sección eliminada correctamente',
            'message' => 'Sección eliminada correctamente' // Para compatibilidad
        ]);
    } else {
        echo json_encode(['success' => false, 'mensaje' => 'Error al eliminar la sección']);
    }
}

/**
 * Obtener secciones de un curso
 */
function obtenerSecciones($data, $idUsuario)
{
    $idCurso = $data['idCurso'] ?? $data['id_curso'] ?? null;

    if (!$idCurso) {
        echo json_encode(['success' => false, 'mensaje' => 'ID de curso requerido']);
        return;
    }

    // Verificar permisos
    $curso = ControladorCursos::ctrMostrarCursos('id', $idCurso);
    if (!$curso || $curso['id_persona'] != $idUsuario) {
        echo json_encode(['success' => false, 'mensaje' => 'No tienes permisos para ver este curso']);
        return;
    }

    // Obtener secciones con contenido
    $conexion = Conexion::conectar();
    $stmt = $conexion->prepare("
        SELECT cs.*, 
               (SELECT COUNT(*) FROM seccion_contenido sc WHERE sc.id_seccion = cs.id AND sc.estado = 'activo') as total_contenido
        FROM curso_secciones cs 
        WHERE cs.id_curso = :id_curso AND cs.estado = 'activo'
        ORDER BY cs.orden ASC
    ");
    $stmt->bindParam(':id_curso', $idCurso, PDO::PARAM_INT);
    $stmt->execute();
    $secciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'secciones' => $secciones
    ]);
}
?>
'orden' => $_POST['orden'] ?? 1,
'estado' => $_POST['estado'] ?? 'activo'
];

$resultado = ControladorCursos::ctrActualizarSeccion($datos);

if ($resultado == "ok") {
$response['success'] = true;
$response['message'] = 'Sección actualizada correctamente';
} else {
$response['message'] = 'Error al actualizar la sección';
}
break;

case 'eliminar_seccion':
$id = $_POST['id'];
$resultado = ControladorCursos::ctrEliminarSeccion($id);

if ($resultado == "ok") {
$response['success'] = true;
$response['message'] = 'Sección eliminada correctamente';
} else {
$response['message'] = 'Error al eliminar la sección';
}
break;

case 'guardar_contenido':
// Determinar si es crear o actualizar
$es_edicion = !empty($_POST['idContenido']);

if ($es_edicion) {
// ACTUALIZAR CONTENIDO EXISTENTE
$archivo_url = $_POST['archivo_url_actual'] ?? '';
$tamaño_archivo = $_POST['tamaño_archivo_actual'] ?? 0;

if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] == 0) {
$tipo = $_POST['tipo'];
$nuevo_archivo = ControladorCursos::ctrSubirArchivoContenido($_FILES['archivo'], $tipo);

if ($nuevo_archivo) {
// Eliminar archivo anterior si existe
if ($archivo_url && file_exists($archivo_url)) {
unlink($archivo_url);
}
$archivo_url = $nuevo_archivo;
$tamaño_archivo = $_FILES['archivo']['size'];
}
}

$datos = [
'id' => $_POST['idContenido'],
'titulo' => $_POST['titulo'],
'descripcion' => $_POST['descripcion'] ?? '',
'tipo' => $_POST['tipo'],
'archivo_url' => $archivo_url,
'duracion' => $_POST['duracion'] ?? null,
'tamaño_archivo' => $tamaño_archivo,
'orden' => $_POST['orden'] ?? 1,
'estado' => $_POST['estado'] ?? 'activo'
];

$resultado = ControladorCursos::ctrActualizarContenido($datos);

if ($resultado == "ok") {
$response['success'] = true;
$response['message'] = 'Contenido actualizado correctamente';
} else {
$response['message'] = 'Error al actualizar el contenido';
}
} else {
// CREAR NUEVO CONTENIDO
$archivo_url = '';
$tamaño_archivo = 0;

if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] == 0) {
$tipo = $_POST['tipo'];
$archivo_url = ControladorCursos::ctrSubirArchivoContenido($_FILES['archivo'], $tipo);
$tamaño_archivo = $_FILES['archivo']['size'];

if (!$archivo_url) {
$response['message'] = 'Error al subir el archivo';
echo json_encode($response);
exit;
}
}

$datos = [
'id_seccion' => $_POST['idSeccion'],
'titulo' => $_POST['titulo'],
'descripcion' => $_POST['descripcion'] ?? '',
'tipo' => $_POST['tipo'],
'archivo_url' => $archivo_url,
'duracion' => $_POST['duracion'] ?? null,
'tamaño_archivo' => $tamaño_archivo,
'orden' => $_POST['orden'] ?? 1,
'estado' => 'activo'
];

$resultado = ControladorCursos::ctrCrearContenido($datos);

if ($resultado != "error") {
$response['success'] = true;
$response['message'] = 'Contenido creado correctamente';
$response['id'] = $resultado;
} else {
$response['message'] = 'Error al crear el contenido';
}
}
break;

case 'crear_contenido':
// Manejar subida de archivo si existe
$archivo_url = '';
$tamaño_archivo = 0;

if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] == 0) {
$tipo = $_POST['tipo'];
$archivo_url = ControladorCursos::ctrSubirArchivoContenido($_FILES['archivo'], $tipo);
$tamaño_archivo = $_FILES['archivo']['size'];

if (!$archivo_url) {
$response['message'] = 'Error al subir el archivo';
echo json_encode($response);
exit;
}
}

$datos = [
'id_seccion' => $_POST['id_seccion'],
'titulo' => $_POST['titulo'],
'descripcion' => $_POST['descripcion'] ?? '',
'tipo' => $_POST['tipo'],
'archivo_url' => $archivo_url,
'duracion' => $_POST['duracion'] ?? null,
'tamaño_archivo' => $tamaño_archivo,
'orden' => $_POST['orden'] ?? 1,
'estado' => 'activo'
];

$resultado = ControladorCursos::ctrCrearContenido($datos);

if ($resultado != "error") {
$response['success'] = true;
$response['message'] = 'Contenido creado correctamente';
$response['id'] = $resultado;
} else {
$response['message'] = 'Error al crear el contenido';
}
break;

case 'actualizar_contenido':
// Manejar subida de archivo si existe
$archivo_url = $_POST['archivo_url_actual'] ?? '';
$tamaño_archivo = $_POST['tamaño_archivo_actual'] ?? 0;

if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] == 0) {
$tipo = $_POST['tipo'];
$nuevo_archivo = ControladorCursos::ctrSubirArchivoContenido($_FILES['archivo'], $tipo);

if ($nuevo_archivo) {
// Eliminar archivo anterior si existe
if ($archivo_url && file_exists($archivo_url)) {
unlink($archivo_url);
}
$archivo_url = $nuevo_archivo;
$tamaño_archivo = $_FILES['archivo']['size'];
}
}

$datos = [
'id' => $_POST['id'],
'titulo' => $_POST['titulo'],
'descripcion' => $_POST['descripcion'] ?? '',
'tipo' => $_POST['tipo'],
'archivo_url' => $archivo_url,
'duracion' => $_POST['duracion'] ?? null,
'tamaño_archivo' => $tamaño_archivo,
'orden' => $_POST['orden'] ?? 1,
'estado' => $_POST['estado'] ?? 'activo'
];

$resultado = ControladorCursos::ctrActualizarContenido($datos);

if ($resultado == "ok") {
$response['success'] = true;
$response['message'] = 'Contenido actualizado correctamente';
} else {
$response['message'] = 'Error al actualizar el contenido';
}
break;

case 'eliminar_contenido':
$id = $_POST['id'];

// Obtener datos del contenido para eliminar archivo
$conn = Conexion::conectar();
$stmt = $conn->prepare("SELECT archivo_url FROM seccion_contenido WHERE id = ?");
$stmt->execute([$id]);
$contenido = $stmt->fetch(PDO::FETCH_ASSOC);

$resultado = ControladorCursos::ctrEliminarContenido($id);

if ($resultado == "ok") {
// Eliminar archivo físico si existe
if ($contenido && $contenido['archivo_url'] && file_exists($contenido['archivo_url'])) {
unlink($contenido['archivo_url']);
}

$response['success'] = true;
$response['message'] = 'Contenido eliminado correctamente';
} else {
$response['message'] = 'Error al eliminar el contenido';
}
break;

case 'obtener_contenido':
$id = $_POST['id'];
$conn = Conexion::conectar();
$stmt = $conn->prepare("SELECT * FROM seccion_contenido WHERE id = ?");
$stmt->execute([$id]);
$contenido = $stmt->fetch(PDO::FETCH_ASSOC);

if ($contenido) {
$response['success'] = true;
$response['contenido'] = $contenido;
} else {
$response['message'] = 'Contenido no encontrado';
}
break;

case 'actualizar_curso':
try {
require_once "../controladores/usuarios.controlador.php";

// Validar que el nombre sea único
$nombreValido = ControladorCursos::ctrValidarNombreUnico($_POST['nombre'], $_POST['id']);
if (!$nombreValido) {
$response['message'] = 'El nombre del curso ya está en uso';
break;
}

$datos = [
'id' => $_POST['id'],
'nombre' => $_POST['nombre'],
'descripcion' => $_POST['descripcion'],
'lo_que_aprenderas' => $_POST['lo_que_aprenderas'],
'requisitos' => $_POST['requisitos'],
'para_quien' => $_POST['para_quien'],
'valor' => $_POST['valor'],
'id_categoria' => $_POST['id_categoria'],
'id_persona' => $_POST['id_persona'],
'estado' => $_POST['estado']
];

// Manejar archivos si se subieron
if (isset($_FILES['banner']) && $_FILES['banner']['error'] === UPLOAD_ERR_OK) {
$datos['banner'] = $_FILES['banner'];
}

if (isset($_FILES['promo_video']) && $_FILES['promo_video']['error'] === UPLOAD_ERR_OK) {
$datos['promo_video'] = $_FILES['promo_video'];
}

$resultado = ControladorCursos::ctrActualizarDatosCurso($datos);

if (!$resultado['error']) {
$response['success'] = true;
$response['message'] = 'Curso actualizado correctamente';
} else {
$response['message'] = $resultado['mensaje'];
}
} catch (Exception $e) {
$response['message'] = 'Error: ' . $e->getMessage();
}
break;

default:
$response['message'] = 'Acción no válida';
}
} else {
$response['message'] = 'Método no permitido';
}

echo json_encode($response);