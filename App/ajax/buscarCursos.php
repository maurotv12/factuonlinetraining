<?php

/**
 * AJAX - Buscar cursos
 * Busca cursos por nombre, instructor o categoría
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
    $query = $_POST['query'] ?? '';
    $categoria = $_POST['categoria'] ?? null;

    // Validar que hay un término de búsqueda
    if (empty(trim($query))) {
        $response = [
            'success' => false,
            'error' => 'Término de búsqueda requerido',
            'courses' => [],
            'total' => 0
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    // Obtener todos los cursos
    $todosCursos = ControladorCursos::ctrMostrarCursos(null, null);

    // Filtrar cursos por término de búsqueda
    $cursosEncontrados = [];
    if ($todosCursos && is_array($todosCursos)) {
        $queryLower = strtolower(trim($query));

        foreach ($todosCursos as $curso) {
            $coincide = false;

            // Buscar en nombre del curso
            if (strpos(strtolower($curso['nombre']), $queryLower) !== false) {
                $coincide = true;
            }

            // Buscar en profesor
            if (isset($curso['profesor']) && strpos(strtolower($curso['profesor']), $queryLower) !== false) {
                $coincide = true;
            }

            // Buscar en categoría
            if (isset($curso['categoria']) && strpos(strtolower($curso['categoria']), $queryLower) !== false) {
                $coincide = true;
            }

            // Buscar en descripción
            if (isset($curso['descripcion']) && strpos(strtolower($curso['descripcion']), $queryLower) !== false) {
                $coincide = true;
            }

            // Filtrar por categoría si se especifica
            if ($coincide && $categoria && $categoria !== 'null') {
                if ($curso['id_categoria'] != $categoria) {
                    $coincide = false;
                }
            }

            if ($coincide) {
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
                    'esNuevo' => false,
                    'esPopular' => false
                ];

                $cursosEncontrados[] = $cursoFormateado;
            }
        }
    }

    // Respuesta exitosa
    $response = [
        'success' => true,
        'courses' => $cursosEncontrados,
        'total' => count($cursosEncontrados),
        'query' => $query,
        'message' => 'Búsqueda completada'
    ];

    header('Content-Type: application/json');
    echo json_encode($response);
} catch (Exception $e) {
    // Error en el proceso
    http_response_code(500);
    $response = [
        'success' => false,
        'error' => 'Error en la búsqueda: ' . $e->getMessage(),
        'courses' => [],
        'total' => 0
    ];

    header('Content-Type: application/json');
    echo json_encode($response);
}
