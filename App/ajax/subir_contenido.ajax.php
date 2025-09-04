<?php

/**
 * AJAX para subida de contenido multimedia
 * Maneja la subida de videos HD y archivos PDF para secciones
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
require_once "../controladores/general.controlador.php";
require_once "../modelos/conexion.php";

// Verificar que el usuario sea profesor
if (!ControladorGeneral::ctrUsuarioTieneAlgunRol(['profesor'])) {
    echo json_encode(['success' => false, 'mensaje' => 'No tienes permisos para subir contenido']);
    exit;
}

try {
    $accion = $_POST['accion'] ?? '';
    $seccionId = $_POST['seccionId'] ?? null;
    $cursoId = $_POST['cursoId'] ?? null;
    $idUsuario = $_SESSION['idU'];

    if (!$seccionId || !$cursoId) {
        echo json_encode(['success' => false, 'mensaje' => 'Datos incompletos']);
        exit;
    }

    // Verificar permisos sobre el curso
    $curso = ControladorCursos::ctrMostrarCursos('id', $cursoId);
    if (!$curso || $curso['id_persona'] != $idUsuario) {
        echo json_encode(['success' => false, 'mensaje' => 'No tienes permisos para editar este curso']);
        exit;
    }

    switch ($accion) {
        case 'subirVideo':
            subirVideo();
            break;

        case 'subirPDF':
            subirPDF();
            break;

        default:
            echo json_encode(['success' => false, 'mensaje' => 'Acción no válida']);
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
 * Subir video HD
 */
function subirVideo()
{
    global $seccionId, $cursoId;

    if (!isset($_FILES['video']) || $_FILES['video']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'mensaje' => 'Error al recibir el archivo de video']);
        return;
    }

    $archivo = $_FILES['video'];
    $titulo = $_POST['titulo'] ?? 'Video sin título';
    $descripcion = $_POST['descripcion'] ?? '';

    // Validar archivo
    $validacion = validarVideo($archivo);
    if (!$validacion['valido']) {
        echo json_encode(['success' => false, 'mensaje' => $validacion['mensaje']]);
        return;
    }

    // Crear directorio para videos del curso
    $directorioBase = crearDirectorioVideo($cursoId);
    if (!$directorioBase) {
        echo json_encode(['success' => false, 'mensaje' => 'Error al crear directorio de almacenamiento']);
        return;
    }

    // Generar nombre único
    $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    $nombreArchivo = uniqid() . '_' . time() . '.' . $extension;
    $rutaCompleta = $directorioBase . DIRECTORY_SEPARATOR . $nombreArchivo;

    // Mover archivo
    if (!move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
        echo json_encode(['success' => false, 'mensaje' => 'Error al guardar el video']);
        return;
    }

    // Obtener información del video
    $infoVideo = obtenerInfoVideo($rutaCompleta);

    // Obtener siguiente orden en la sección
    $orden = obtenerSiguienteOrden($seccionId);

    // Guardar en base de datos
    $datosContenido = [
        'id_seccion' => $seccionId,
        'titulo' => $titulo,
        'descripcion' => $descripcion,
        'tipo' => 'video',
        'duracion' => $infoVideo['duracion'],
        'orden' => $orden,
        'estado' => 'activo'
    ];

    $conexion = Conexion::conectar();
    $stmt = $conexion->prepare("
        INSERT INTO seccion_contenido (id_seccion, titulo, descripcion, tipo, duracion, orden, estado)
        VALUES (:id_seccion, :titulo, :descripcion, :tipo, :duracion, :orden, :estado)
    ");

    $stmt->bindParam(':id_seccion', $datosContenido['id_seccion'], PDO::PARAM_INT);
    $stmt->bindParam(':titulo', $datosContenido['titulo'], PDO::PARAM_STR);
    $stmt->bindParam(':descripcion', $datosContenido['descripcion'], PDO::PARAM_STR);
    $stmt->bindParam(':tipo', $datosContenido['tipo'], PDO::PARAM_STR);
    $stmt->bindParam(':duracion', $datosContenido['duracion'], PDO::PARAM_STR);
    $stmt->bindParam(':orden', $datosContenido['orden'], PDO::PARAM_INT);
    $stmt->bindParam(':estado', $datosContenido['estado'], PDO::PARAM_STR);

    if ($stmt->execute()) {
        $contenidoId = $conexion->lastInsertId();

        // Guardar ruta del archivo en tabla assets
        guardarArchivoAsset($contenidoId, $rutaCompleta, 'video', $infoVideo);

        echo json_encode([
            'success' => true,
            'mensaje' => 'Video subido correctamente',
            'id' => $contenidoId,
            'duracion' => $infoVideo['duracion'],
            'resolucion' => $infoVideo['resolucion']
        ]);
    } else {
        // Si falla la base de datos, eliminar el archivo
        unlink($rutaCompleta);
        echo json_encode(['success' => false, 'mensaje' => 'Error al guardar información del video']);
    }
}

/**
 * Subir archivo PDF
 */
function subirPDF()
{
    global $seccionId, $cursoId;

    if (!isset($_FILES['pdf']) || $_FILES['pdf']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'mensaje' => 'Error al recibir el archivo PDF']);
        return;
    }

    $archivo = $_FILES['pdf'];
    $titulo = $_POST['titulo'] ?? 'Documento PDF';
    $descripcion = $_POST['descripcion'] ?? '';

    // Validar archivo
    $validacion = validarPDF($archivo);
    if (!$validacion['valido']) {
        echo json_encode(['success' => false, 'mensaje' => $validacion['mensaje']]);
        return;
    }

    // Crear directorio para PDFs del curso
    $directorioBase = crearDirectorioPDF($cursoId);
    if (!$directorioBase) {
        echo json_encode(['success' => false, 'mensaje' => 'Error al crear directorio de almacenamiento']);
        return;
    }

    // Generar nombre único
    $nombreArchivo = uniqid() . '_' . time() . '.pdf';
    $rutaCompleta = $directorioBase . DIRECTORY_SEPARATOR . $nombreArchivo;

    // Mover archivo
    if (!move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
        echo json_encode(['success' => false, 'mensaje' => 'Error al guardar el PDF']);
        return;
    }

    // Obtener siguiente orden en la sección
    $orden = obtenerSiguienteOrden($seccionId);

    // Guardar en base de datos
    $datosContenido = [
        'id_seccion' => $seccionId,
        'titulo' => $titulo,
        'descripcion' => $descripcion,
        'tipo' => 'pdf',
        'orden' => $orden,
        'estado' => 'activo'
    ];

    $conexion = Conexion::conectar();
    $stmt = $conexion->prepare("
        INSERT INTO seccion_contenido (id_seccion, titulo, descripcion, tipo, orden, estado)
        VALUES (:id_seccion, :titulo, :descripcion, :tipo, :orden, :estado)
    ");

    $stmt->bindParam(':id_seccion', $datosContenido['id_seccion'], PDO::PARAM_INT);
    $stmt->bindParam(':titulo', $datosContenido['titulo'], PDO::PARAM_STR);
    $stmt->bindParam(':descripcion', $datosContenido['descripcion'], PDO::PARAM_STR);
    $stmt->bindParam(':tipo', $datosContenido['tipo'], PDO::PARAM_STR);
    $stmt->bindParam(':orden', $datosContenido['orden'], PDO::PARAM_INT);
    $stmt->bindParam(':estado', $datosContenido['estado'], PDO::PARAM_STR);

    if ($stmt->execute()) {
        $contenidoId = $conexion->lastInsertId();

        // Guardar ruta del archivo en tabla assets
        guardarArchivoAsset($contenidoId, $rutaCompleta, 'pdf', ['tamaño' => $archivo['size']]);

        echo json_encode([
            'success' => true,
            'mensaje' => 'PDF subido correctamente',
            'id' => $contenidoId,
            'tamaño' => formatearTamaño($archivo['size'])
        ]);
    } else {
        // Si falla la base de datos, eliminar el archivo
        unlink($rutaCompleta);
        echo json_encode(['success' => false, 'mensaje' => 'Error al guardar información del PDF']);
    }
}

/**
 * Validar archivo de video
 */
function validarVideo($archivo)
{
    $tiposPermitidos = ['video/mp4', 'video/avi', 'video/mov', 'video/quicktime'];
    $tamañoMaximo = 100 * 1024 * 1024; // 100MB

    if (!in_array($archivo['type'], $tiposPermitidos)) {
        return ['valido' => false, 'mensaje' => 'Solo se permiten videos MP4, AVI o MOV'];
    }

    if ($archivo['size'] > $tamañoMaximo) {
        return ['valido' => false, 'mensaje' => 'El video no puede superar los 100MB'];
    }

    // Validar propiedades del video
    $infoVideo = obtenerInfoVideo($archivo['tmp_name']);

    if ($infoVideo['duracion_segundos'] > 600) { // 10 minutos
        return ['valido' => false, 'mensaje' => 'El video no puede superar los 10 minutos de duración'];
    }

    if ($infoVideo['ancho'] > 1280 || $infoVideo['alto'] > 720) {
        return ['valido' => false, 'mensaje' => 'La resolución máxima permitida es HD (1280x720)'];
    }

    return ['valido' => true];
}

/**
 * Validar archivo PDF
 */
function validarPDF($archivo)
{
    $tiposPermitidos = ['application/pdf'];
    $tamañoMaximo = 10 * 1024 * 1024; // 10MB

    if (!in_array($archivo['type'], $tiposPermitidos)) {
        return ['valido' => false, 'mensaje' => 'Solo se permiten archivos PDF'];
    }

    if ($archivo['size'] > $tamañoMaximo) {
        return ['valido' => false, 'mensaje' => 'El PDF no puede superar los 10MB'];
    }

    return ['valido' => true];
}

/**
 * Obtener información del video
 */
function obtenerInfoVideo($rutaArchivo)
{
    $info = [
        'duracion' => '00:00',
        'duracion_segundos' => 0,
        'ancho' => 0,
        'alto' => 0,
        'resolucion' => '0x0'
    ];

    // Usar getID3 si está disponible, sino usar métodos básicos
    if (extension_loaded('ffmpeg')) {
        // Usar FFmpeg si está disponible
        $command = "ffprobe -v quiet -print_format json -show_format -show_streams " . escapeshellarg($rutaArchivo);
        $output = shell_exec($command);

        if ($output) {
            $data = json_decode($output, true);

            if (isset($data['format']['duration'])) {
                $info['duracion_segundos'] = (int)$data['format']['duration'];
                $info['duracion'] = gmdate("H:i:s", $info['duracion_segundos']);
            }

            if (isset($data['streams'][0]['width']) && isset($data['streams'][0]['height'])) {
                $info['ancho'] = $data['streams'][0]['width'];
                $info['alto'] = $data['streams'][0]['height'];
                $info['resolucion'] = $info['ancho'] . 'x' . $info['alto'];
            }
        }
    }

    return $info;
}

/**
 * Crear directorio para videos
 */
function crearDirectorioVideo($cursoId)
{
    $documentRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : 'C:\\xampp\\htdocs';
    $rutaBase = $documentRoot . "/cursosApp/storage/public/courses/" . $cursoId . "/videos";

    if (!file_exists($rutaBase)) {
        if (!mkdir($rutaBase, 0755, true)) {
            return false;
        }
    }

    return $rutaBase;
}

/**
 * Crear directorio para PDFs
 */
function crearDirectorioPDF($cursoId)
{
    $documentRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : 'C:\\xampp\\htdocs';
    $rutaBase = $documentRoot . "/cursosApp/storage/public/courses/" . $cursoId . "/documentos";

    if (!file_exists($rutaBase)) {
        if (!mkdir($rutaBase, 0755, true)) {
            return false;
        }
    }

    return $rutaBase;
}

/**
 * Obtener siguiente orden en la sección
 */
function obtenerSiguienteOrden($seccionId)
{
    $conexion = Conexion::conectar();
    $stmt = $conexion->prepare("SELECT COALESCE(MAX(orden), 0) + 1 as siguiente_orden FROM seccion_contenido WHERE id_seccion = :id_seccion");
    $stmt->bindParam(':id_seccion', $seccionId, PDO::PARAM_INT);
    $stmt->execute();

    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    return $resultado['siguiente_orden'];
}

/**
 * Guardar archivo en tabla assets
 */
function guardarArchivoAsset($contenidoId, $rutaArchivo, $tipo, $metadata = [])
{
    $conexion = Conexion::conectar();

    // Convertir ruta absoluta a relativa para almacenamiento
    $rutaRelativa = str_replace($_SERVER['DOCUMENT_ROOT'] . '/cursosApp/', '', $rutaArchivo);

    // Obtener tamaño del archivo
    $tamanoBytes = filesize($rutaArchivo);

    // Determinar asset_tipo basado en el tipo
    $assetTipo = ($tipo === 'video') ? 'attachment' : 'pdf';

    // URL pública para acceso
    $publicUrl = '/cursosApp/' . $rutaRelativa;

    $stmt = $conexion->prepare("
        INSERT INTO seccion_contenido_assets 
        (id_contenido, asset_tipo, storage_path, public_url, tamano_bytes, duracion_segundos, created_at)
        VALUES (:id_contenido, :asset_tipo, :storage_path, :public_url, :tamano_bytes, :duracion_segundos, NOW())
    ");

    $duracionSegundos = null;
    if (isset($metadata['duracion_segundos'])) {
        $duracionSegundos = $metadata['duracion_segundos'];
    }

    $stmt->bindParam(':id_contenido', $contenidoId, PDO::PARAM_INT);
    $stmt->bindParam(':asset_tipo', $assetTipo, PDO::PARAM_STR);
    $stmt->bindParam(':storage_path', $rutaRelativa, PDO::PARAM_STR);
    $stmt->bindParam(':public_url', $publicUrl, PDO::PARAM_STR);
    $stmt->bindParam(':tamano_bytes', $tamanoBytes, PDO::PARAM_INT);
    $stmt->bindParam(':duracion_segundos', $duracionSegundos, PDO::PARAM_INT);

    return $stmt->execute();
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
