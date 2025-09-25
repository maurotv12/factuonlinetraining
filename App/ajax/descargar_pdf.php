<?php

/**
 * Servicio de descarga de PDFs
 * Con modo diagnóstico integrado (Ctrl+Click en botón)
 */

// Configuración inicial para evitar problemas
error_reporting(0);
ini_set('display_errors', 0);

// Limpiar output buffers
while (ob_get_level()) {
    ob_end_clean();
}

// Iniciar sesión para verificación
session_start();

// Verificar autenticación básica
if (!isset($_SESSION['idU'])) {
    http_response_code(401);
    exit('No autenticado');
}

// Obtener parámetros
$assetId = filter_input(INPUT_GET, 'asset_id', FILTER_VALIDATE_INT);
$cursoId = filter_input(INPUT_GET, 'curso_id', FILTER_VALIDATE_INT);
$diagnostico = isset($_GET['diagnostico']) ? true : false;

if (!$assetId || !$cursoId) {
    http_response_code(400);
    exit('Parámetros inválidos');
}

// Incluir modelos
$baseDir = dirname(dirname(__FILE__));
require_once $baseDir . "/controladores/cursos.controlador.php";
require_once $baseDir . "/controladores/general.controlador.php";
require_once $baseDir . "/modelos/conexion.php";

try {
    // Obtener información del asset
    $asset = ModeloCursos::mdlObtenerAssetPorId($assetId);

    if (!$asset) {
        http_response_code(404);
        exit('Archivo no encontrado en BD');
    }

    // Verificar que sea PDF
    if ($asset['asset_tipo'] !== 'pdf') {
        http_response_code(400);
        exit('No es un archivo PDF');
    }

    // Construir ruta del archivo
    // Si la ruta ya es absoluta, usarla directamente
    if (strpos($asset['storage_path'], ':') !== false || strpos($asset['storage_path'], '/') === 0) {
        $rutaCompleta = $asset['storage_path'];
    } else {
        // Si es relativa, construir desde document root
        $rutaCompleta = $_SERVER['DOCUMENT_ROOT'] . '/factuonlinetraining/' . $asset['storage_path'];
    }

    // Si es diagnóstico, mostrar información
    if ($diagnostico) {
        header('Content-Type: text/html; charset=utf-8');
        echo "<h3>Diagnóstico PDF</h3>";
        echo "<p><strong>Asset ID:</strong> " . htmlspecialchars($assetId) . "</p>";
        echo "<p><strong>Curso ID:</strong> " . htmlspecialchars($cursoId) . "</p>";
        echo "<p><strong>Usuario ID:</strong> " . htmlspecialchars($_SESSION['idU']) . "</p>";
        echo "<p><strong>Storage Path:</strong> " . htmlspecialchars($asset['storage_path']) . "</p>";
        echo "<p><strong>Ruta Completa:</strong> " . htmlspecialchars($rutaCompleta) . "</p>";
        echo "<p><strong>Archivo Existe:</strong> " . (file_exists($rutaCompleta) ? 'SÍ' : 'NO') . "</p>";

        if (file_exists($rutaCompleta)) {
            echo "<p><strong>Tamaño:</strong> " . number_format(filesize($rutaCompleta)) . " bytes</p>";
            echo "<p><strong>Es Legible:</strong> " . (is_readable($rutaCompleta) ? 'SÍ' : 'NO') . "</p>";
            echo "<p><strong>MIME Type:</strong> " . mime_content_type($rutaCompleta) . "</p>";

            // Botón para descarga directa
            echo "<hr>";
            echo "<p><a href='?asset_id={$assetId}&curso_id={$cursoId}' class='btn' style='background:#007bff;color:white;padding:10px;text-decoration:none;border-radius:5px;'>Descargar Archivo</a></p>";
        }

        echo "<p><a href='javascript:history.back()'>← Volver</a></p>";
        exit;
    }

    // Verificar permisos básicos
    if (!ControladorGeneral::ctrUsuarioTieneAlgunRol(['profesor', 'estudiante'])) {
        http_response_code(403);
        exit('Sin permisos');
    }

    // Verificar existencia física del archivo
    if (!file_exists($rutaCompleta)) {
        http_response_code(404);
        exit('Archivo físico no existe: ' . $asset['storage_path']);
    }

    if (!is_readable($rutaCompleta)) {
        http_response_code(403);
        exit('Archivo no es legible');
    }

    // Obtener información del archivo
    $nombreArchivo = basename($asset['storage_path']);
    $tamanoArchivo = filesize($rutaCompleta);

    // Limpiar nombre para descarga
    $nombreDescarga = preg_replace('/[^a-zA-Z0-9._-]/', '_', $nombreArchivo);
    if (!str_ends_with(strtolower($nombreDescarga), '.pdf')) {
        $nombreDescarga .= '.pdf';
    }

    // Limpiar buffers una vez más antes de enviar headers
    while (ob_get_level()) {
        ob_end_clean();
    }

    // Headers para forzar descarga
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $nombreDescarga . '"');
    header('Content-Length: ' . $tamanoArchivo);
    header('Content-Transfer-Encoding: binary');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Leer y enviar archivo
    $handle = fopen($rutaCompleta, 'rb');
    if ($handle === false) {
        http_response_code(500);
        exit('No se puede abrir el archivo');
    }

    // Enviar archivo en chunks
    while (!feof($handle)) {
        $buffer = fread($handle, 8192);
        echo $buffer;
        flush();
    }

    fclose($handle);
} catch (Exception $e) {
    error_log("Error descarga PDF: " . $e->getMessage());
    http_response_code(500);
    exit('Error interno: ' . $e->getMessage());
}

exit;
