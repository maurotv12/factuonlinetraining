<?php

/**
 * AJAX - Obtener cursos
 * Devuelve los cursos disponibles opcionalmente filtrados por categoría
 */

// Incluir los controladores necesarios
require_once "../controladores/cursos.controlador.php";

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

try {
    // Obtener parámetros
    $categoria = $_POST['category'] ?? null;

    // Obtener cursos
    if ($categoria && $categoria !== 'null') {
        $cursos = ControladorCursos::ctrMostrarCursos('id_categoria', $categoria);
    } else {
        $cursos = ControladorCursos::ctrMostrarCursos(null, null);
    }

    // Procesar cursos para agregar información adicional
    $cursosFormateados = [];
    if ($cursos && is_array($cursos)) {
        foreach ($cursos as $curso) {
            // Verificar y corregir la ruta de la imagen
            $banner = '/cursosApp/App/vistas/img/cursos/default/defaultCurso.png'; // Imagen por defecto

            if ($curso['banner']) {
                // Si la ruta comienza con "vistas/", construir ruta completa desde App/
                if (strpos($curso['banner'], 'vistas/') === 0) {
                    $imagePath = '/cursosApp/App/' . $curso['banner'];
                } else {
                    $imagePath = $curso['banner'];
                }

                // Verificar si la imagen física existe
                $fullPath = $_SERVER['DOCUMENT_ROOT'] . $imagePath;
                if (file_exists($fullPath)) {
                    $banner = $imagePath;
                }
            }

            $cursoFormateado = [
                'id' => $curso['id'],
                'nombre' => $curso['nombre'],
                'banner' => $banner,
                'valor' => $curso['valor'] ?? 0,
                'profesor' => $curso['profesor'] ?? 'Instructor',
                'categoria' => $curso['categoria'] ?? 'Sin categoría',
                'descripcion' => $curso['descripcion'] ?? '',
                'esNuevo' => false, // Aquí puedes agregar lógica para determinar si es nuevo
                'esPopular' => false // Aquí puedes agregar lógica para determinar si es popular
            ];

            $cursosFormateados[] = $cursoFormateado;
        }
    }

    // Respuesta exitosa
    $response = [
        'success' => true,
        'courses' => $cursosFormateados,
        'total' => count($cursosFormateados),
        'message' => 'Cursos obtenidos correctamente'
    ];

    header('Content-Type: application/json');
    echo json_encode($response);
} catch (Exception $e) {
    // Error en el proceso
    http_response_code(500);
    $response = [
        'success' => false,
        'error' => 'Error al obtener cursos: ' . $e->getMessage(),
        'courses' => [],
        'total' => 0
    ];

    header('Content-Type: application/json');
    echo json_encode($response);
}
