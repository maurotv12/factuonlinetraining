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

        // Validar que el contenido no tenga ya un video
        $assetsExistentes = ControladorCursos::ctrObtenerAssetsContenido($idContenido);
        if ($assetsExistentes['success']) {
            foreach ($assetsExistentes['assets'] as $asset) {
                if ($asset['asset_tipo'] === 'video') {
                    echo json_encode([
                        'success' => false,
                        'mensaje' => 'Este contenido ya tiene un video. Solo se permite un video por contenido.'
                    ]);
                    break 2; // Salir del foreach y del case
                }
            }
        }

        // Procesar subida del video
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
            'actualizarAsset'
        ];

        echo json_encode([
            'success' => false,
            'mensaje' => 'Acción no válida. Acciones disponibles: ' . implode(', ', $accionesValidas),
            'accion_recibida' => $accion ?? 'undefined'
        ]);
        break;
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
