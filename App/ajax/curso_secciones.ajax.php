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

// Manejar tanto datos JSON como FormData
$accion = '';
$datos = [];

if (isset($_POST['accion'])) {
    // Datos de FormData (para subida de archivos)
    $accion = $_POST['accion'];
    $datos = $_POST;
} else {
    // Datos JSON
    $datos = json_decode(file_get_contents('php://input'), true);
    $accion = $datos['accion'] ?? '';
    $datos['id_seccion'] = $datos['idSeccion'] ?? null; // Normalizar nombre
}

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

    // ========== GESTIÓN DE CONTENIDO DE SECCIONES ==========

    case 'crearContenido':
        // Validar campos requeridos
        if (!isset($datos['idSeccion']) || !isset($datos['titulo'])) {
            echo json_encode([
                'success' => false,
                'mensaje' => 'Faltan campos requeridos: idSeccion y titulo'
            ]);
            break;
        }

        $respuesta = ControladorCursos::ctrCrearContenido($datos);
        echo json_encode($respuesta);
        break;

    case 'actualizarContenido':
        // Validar campos requeridos
        if (!isset($datos['id']) || !isset($datos['titulo'])) {
            echo json_encode([
                'success' => false,
                'mensaje' => 'Faltan campos requeridos: id y titulo'
            ]);
            break;
        }

        $respuesta = ControladorCursos::ctrActualizarContenido($datos);
        echo json_encode($respuesta);
        break;

    case 'eliminarContenido':
        $idContenido = $datos['id'] ?? null;
        if ($idContenido) {
            $respuesta = ControladorCursos::ctrEliminarContenido($idContenido);
            echo json_encode($respuesta);
        } else {
            echo json_encode(['success' => false, 'mensaje' => 'ID de contenido requerido']);
        }
        break;

    case 'obtenerContenidoSeccion':
        $idSeccion = $datos['idSeccion'] ?? null;
        if ($idSeccion) {
            $respuesta = ControladorCursos::ctrObtenerContenidoSeccionConAssets($idSeccion);
            echo json_encode($respuesta);
        } else {
            echo json_encode(['success' => false, 'mensaje' => 'ID de sección requerido']);
        }
        break;

    case 'obtenerVideoContenido':
        $idContenido = $datos['idContenido'] ?? null;
        if ($idContenido) {
            // Obtener información específica del video
            $conexion = Conexion::conectar();
            $stmt = $conexion->prepare("
                SELECT sc.titulo, sca.public_url, sca.duracion_segundos, sca.tamano_bytes
                FROM seccion_contenido sc
                JOIN seccion_contenido_assets sca ON sc.id = sca.id_contenido
                WHERE sc.id = :id_contenido AND sca.asset_tipo = 'video'
                LIMIT 1
            ");
            $stmt->bindParam(':id_contenido', $idContenido, PDO::PARAM_INT);
            $stmt->execute();

            $video = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($video) {
                echo json_encode([
                    'success' => true,
                    'video' => $video
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'mensaje' => 'Video no encontrado'
                ]);
            }
        } else {
            echo json_encode(['success' => false, 'mensaje' => 'ID de contenido requerido']);
        }
        break;    // ========== GESTIÓN DE ASSETS DE CONTENIDO ==========

    case 'obtenerAssetsContenido':
        $idContenido = $datos['idContenido'] ?? null;
        if ($idContenido) {
            $respuesta = ControladorCursos::ctrObtenerAssetsContenido($idContenido);
            echo json_encode($respuesta);
        } else {
            echo json_encode(['success' => false, 'mensaje' => 'ID de contenido requerido']);
        }
        break;

    case 'eliminarAsset':
        $idAsset = $datos['idAsset'] ?? null;
        $idContenido = $datos['idContenido'] ?? null;

        if ($idAsset) {
            $respuesta = ControladorCursos::ctrEliminarContenidoAsset($idAsset);

            // Si se eliminó correctamente y se proporcionó el ID del contenido, actualizar duración
            if ($respuesta['success'] && $idContenido) {
                ControladorCursos::ctrActualizarDuracionContenido($idContenido);
            }

            echo json_encode($respuesta);
        } else {
            echo json_encode(['success' => false, 'mensaje' => 'ID de asset requerido']);
        }
        break;

    case 'calcularDuracionContenido':
        $idContenido = $datos['idContenido'] ?? null;
        if ($idContenido) {
            $respuesta = ControladorCursos::ctrCalcularDuracionTotalContenido($idContenido);
            echo json_encode($respuesta);
        } else {
            echo json_encode(['success' => false, 'mensaje' => 'ID de contenido requerido']);
        }
        break;

    case 'actualizarDuracionContenido':
        $idContenido = $datos['idContenido'] ?? null;
        if ($idContenido) {
            $respuesta = ControladorCursos::ctrActualizarDuracionContenido($idContenido);
            echo json_encode($respuesta);
        } else {
            echo json_encode(['success' => false, 'mensaje' => 'ID de contenido requerido']);
        }
        break;

    // ========== VALIDACIONES DE ARCHIVOS ==========

    case 'validarVideoMP4':
        if (!isset($_FILES['video'])) {
            echo json_encode([
                'success' => false,
                'mensaje' => 'No se recibió el archivo de video'
            ]);
            break;
        }

        $respuesta = ControladorCursos::ctrValidarVideoMP4($_FILES['video']);
        echo json_encode($respuesta);
        break;

    case 'validarPDF':
        if (!isset($_FILES['pdf'])) {
            echo json_encode([
                'success' => false,
                'mensaje' => 'No se recibió el archivo PDF'
            ]);
            break;
        }

        $respuesta = ControladorCursos::ctrValidarPDF($_FILES['pdf']);
        echo json_encode($respuesta);
        break;

    // ========== SUBIDA COMPLETA DE ASSETS ==========

    case 'subirVideoContenido':
        // Validar que se recibieron todos los datos necesarios
        if (!isset($_FILES['video']) || !isset($_POST['idContenido']) || !isset($_POST['idCurso']) || !isset($_POST['idSeccion'])) {
            echo json_encode([
                'success' => false,
                'mensaje' => 'Faltan datos requeridos: video, idContenido, idCurso, idSeccion'
            ]);
            break;
        }

        $archivo = $_FILES['video'];
        $idContenido = intval($_POST['idContenido']);
        $idCurso = intval($_POST['idCurso']);
        $idSeccion = intval($_POST['idSeccion']);

        // Procesar subida del video (reemplaza automáticamente si existe uno anterior)
        $respuesta = ControladorCursos::ctrProcesarSubidaAsset($archivo, $idContenido, 'video', $idCurso, $idSeccion);
        echo json_encode($respuesta);
        break;

    case 'subirPDFContenido':
        // Validar que se recibieron todos los datos necesarios
        if (!isset($_FILES['pdf']) || !isset($_POST['idContenido']) || !isset($_POST['idCurso']) || !isset($_POST['idSeccion'])) {
            echo json_encode([
                'success' => false,
                'mensaje' => 'Faltan datos requeridos: pdf, idContenido, idCurso, idSeccion'
            ]);
            break;
        }

        $archivo = $_FILES['pdf'];
        $idContenido = intval($_POST['idContenido']);
        $idCurso = intval($_POST['idCurso']);
        $idSeccion = intval($_POST['idSeccion']);

        // Procesar subida del PDF (se permiten múltiples PDFs)
        $respuesta = ControladorCursos::ctrProcesarSubidaAsset($archivo, $idContenido, 'pdf', $idCurso, $idSeccion);
        echo json_encode($respuesta);
        break;

    // ========== GESTIÓN AVANZADA DE ASSETS ==========

    case 'crearEstructuraDirectorios':
        // Validar campos requeridos
        if (!isset($datos['idCurso']) || !isset($datos['idSeccion']) || !isset($datos['idContenido'])) {
            echo json_encode([
                'success' => false,
                'mensaje' => 'Faltan campos requeridos: idCurso, idSeccion, idContenido'
            ]);
            break;
        }

        $respuesta = ControladorCursos::ctrCrearEstructuraDirectoriosAssets(
            $datos['idCurso'],
            $datos['idSeccion'],
            $datos['idContenido']
        );
        echo json_encode($respuesta);
        break;

    case 'guardarAsset':
        // Validar campos requeridos
        $camposRequeridos = ['id_contenido', 'asset_tipo', 'storage_path'];
        foreach ($camposRequeridos as $campo) {
            if (!isset($datos[$campo])) {
                echo json_encode([
                    'success' => false,
                    'mensaje' => "Falta el campo requerido: $campo"
                ]);
                break 2; // Salir del foreach y del case
            }
        }

        $respuesta = ControladorCursos::ctrGuardarContenidoAsset($datos);
        echo json_encode($respuesta);
        break;

    case 'actualizarAsset':
        // Validar campos requeridos
        $camposRequeridos = ['id', 'asset_tipo', 'storage_path'];
        foreach ($camposRequeridos as $campo) {
            if (!isset($datos[$campo])) {
                echo json_encode([
                    'success' => false,
                    'mensaje' => "Falta el campo requerido: $campo"
                ]);
                break 2; // Salir del foreach y del case
            }
        }

        $respuesta = ControladorCursos::ctrActualizarContenidoAsset($datos);
        echo json_encode($respuesta);
        break;

    // ========== GESTIÓN DE VIDEO PROMOCIONAL ==========
    case 'subirVideoPromocional':
        // Validar que se recibieron todos los datos necesarios
        if (!isset($_FILES['video']) || !isset($_POST['idCurso'])) {
            echo json_encode([
                'success' => false,
                'mensaje' => 'Faltan datos: video e idCurso son requeridos'
            ]);
            break;
        }

        $archivo = $_FILES['video'];
        $idCurso = intval($_POST['idCurso']);

        // Verificar que el curso pertenece al usuario
        $curso = ControladorCursos::ctrMostrarCursos('id', $idCurso);
        if (!$curso || $curso[0]['id_persona'] != $_SESSION['idU']) {
            echo json_encode([
                'success' => false,
                'mensaje' => 'No tienes permisos para editar este curso'
            ]);
            break;
        }

        // Procesar subida del video promocional
        $respuesta = procesarVideoPromocional($archivo, $idCurso);
        echo json_encode($respuesta);
        break;

    // ========== REEMPLAZAR ASSET EXISTENTE ==========
    case 'reemplazarAsset':
        // Validar que se recibieron todos los datos necesarios
        if (!isset($_FILES['archivo']) || !isset($_POST['idAsset']) || !isset($_POST['assetTipo'])) {
            echo json_encode([
                'success' => false,
                'mensaje' => 'Faltan datos requeridos: archivo, idAsset, assetTipo'
            ]);
            break;
        }

        $archivo = $_FILES['archivo'];
        $idAsset = intval($_POST['idAsset']);
        $assetTipo = $_POST['assetTipo'];

        // Obtener información del asset actual
        $assetActual = ModeloCursos::mdlObtenerAssetPorId($idAsset);
        if (!$assetActual) {
            echo json_encode([
                'success' => false,
                'mensaje' => 'Asset no encontrado'
            ]);
            break;
        }

        // Validar archivo según tipo
        if ($assetTipo === 'video') {
            $validacion = ControladorCursos::ctrValidarVideoMP4($archivo);
        } else if ($assetTipo === 'pdf') {
            $validacion = ControladorCursos::ctrValidarPDF($archivo);
        } else {
            echo json_encode([
                'success' => false,
                'mensaje' => 'Tipo de asset no válido'
            ]);
            break;
        }

        if (!$validacion['success']) {
            echo json_encode($validacion);
            break;
        }

        // Generar nueva ruta para el archivo
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nombreArchivo = uniqid() . '_' . time() . '.' . $extension;
        $directorioAnterior = dirname($assetActual['storage_path']);
        $nuevaRuta = $directorioAnterior . '/' . $nombreArchivo;

        // Mover el nuevo archivo
        if (move_uploaded_file($archivo['tmp_name'], $nuevaRuta)) {
            // Actualizar datos en la base de datos (esto elimina el archivo anterior automáticamente)
            $datosActualizar = [
                'id' => $idAsset,
                'asset_tipo' => $assetTipo,
                'storage_path' => $nuevaRuta,
                'tamano_bytes' => $archivo['size'],
                'duracion_segundos' => $validacion['duracion_segundos'] ?? null
            ];

            $respuesta = ControladorCursos::ctrActualizarContenidoAsset($datosActualizar);
            echo json_encode($respuesta);
        } else {
            echo json_encode([
                'success' => false,
                'mensaje' => 'Error al subir el nuevo archivo'
            ]);
        }
        break;

    default:
        // Manejar caso de acción no válida
        $accionesValidas = [
            // Gestión de secciones
            'crearSeccion',
            'actualizarSeccion',
            'obtenerSecciones',
            'eliminarSeccion',
            // Gestión de contenido
            'crearContenido',
            'actualizarContenido',
            'eliminarContenido',
            'obtenerContenidoSeccion',
            // Gestión de assets
            'obtenerAssetsContenido',
            'eliminarAsset',
            'calcularDuracionContenido',
            'actualizarDuracionContenido',
            // Validaciones
            'validarVideoMP4',
            'validarPDF',
            // Subida de assets
            'subirVideoContenido',
            'subirPDFContenido',
            // Gestión avanzada
            'crearEstructuraDirectorios',
            'guardarAsset',
            'actualizarAsset',
            // Video promocional
            'subirVideoPromocional',
            // Reemplazar assets
            'reemplazarAsset'
        ];

        echo json_encode([
            'success' => false,
            'mensaje' => 'Acción no válida. Acciones disponibles: ' . implode(', ', $accionesValidas),
            'accion_recibida' => $accion ?? 'undefined'
        ]);
        break;
}

/**
 * Procesar subida de video promocional
 */
function procesarVideoPromocional($archivo, $idCurso)
{
    // Validar archivo
    if (!isset($archivo) || $archivo['error'] !== UPLOAD_ERR_OK) {
        return [
            'success' => false,
            'mensaje' => 'Error al subir el archivo'
        ];
    }

    // Validar tipo de archivo
    $tiposPermitidos = ['video/mp4'];
    if (!in_array($archivo['type'], $tiposPermitidos)) {
        return [
            'success' => false,
            'mensaje' => 'Solo se permiten archivos MP4'
        ];
    }

    // Validar tamaño (100MB máximo)
    $tamanosMaximo = 100 * 1024 * 1024; // 100MB en bytes
    if ($archivo['size'] > $tamanosMaximo) {
        return [
            'success' => false,
            'mensaje' => 'El archivo no puede superar los 100MB'
        ];
    }

    try {
        // Crear directorio si no existe
        $directorioDestino = "../../storage/public/promoVideos/";
        if (!file_exists($directorioDestino)) {
            mkdir($directorioDestino, 0755, true);
        }

        // Generar nombre único para el archivo
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nombreArchivo = uniqid() . '_' . time() . '.' . $extension;
        $rutaCompleta = $directorioDestino . $nombreArchivo;
        $rutaStorage = "storage/public/promoVideos/" . $nombreArchivo;

        // Mover archivo
        if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
            // Actualizar base de datos usando el controlador que maneja la eliminación del anterior
            $resultado = ControladorCursos::ctrActualizarVideoPromocional($idCurso, $rutaStorage);

            if ($resultado['success']) {
                return [
                    'success' => true,
                    'mensaje' => 'Video promocional subido exitosamente',
                    'ruta' => $rutaStorage
                ];
            } else {
                // Eliminar archivo si no se pudo guardar en BD
                unlink($rutaCompleta);
                return [
                    'success' => false,
                    'mensaje' => $resultado['mensaje']
                ];
            }
        } else {
            return [
                'success' => false,
                'mensaje' => 'Error al mover el archivo'
            ];
        }
    } catch (Exception $e) {
        return [
            'success' => false,
            'mensaje' => 'Error del servidor: ' . $e->getMessage()
        ];
    }
}

/**
 * DOCUMENTACIÓN DE NUEVAS FUNCIONES AGREGADAS:
 * 
 * GESTIÓN DE CONTENIDO:
 * - crearContenido: Crea nuevo contenido en una sección
 * - actualizarContenido: Actualiza contenido existente
 * - eliminarContenido: Elimina contenido y sus assets
 * - obtenerContenidoSeccion: Lista contenido de una sección (placeholder)
 * 
 * GESTIÓN DE ASSETS:
 * - obtenerAssetsContenido: Lista assets de un contenido específico
 * - eliminarAsset: Elimina un asset específico
 * - calcularDuracionContenido: Calcula duración total del contenido
 * - actualizarDuracionContenido: Actualiza duración en BD
 * 
 * VALIDACIONES:
 * - validarVideoMP4: Valida formato y límites de video
 * - validarPDF: Valida formato y tamaño de PDF
 * 
 * SUBIDA DE ASSETS:
 * - subirVideoContenido: Sube video (1 por contenido máximo)
 * - subirPDFContenido: Sube PDF (múltiples permitidos)
 * 
 * GESTIÓN AVANZADA:
 * - crearEstructuraDirectorios: Crea estructura de folders
 * - guardarAsset: Guarda registro de asset en BD
 * - actualizarAsset: Actualiza datos de asset
 * 
 * NOTA: Para subidas masivas o complejas usar subir_contenido.ajax.php
 */
