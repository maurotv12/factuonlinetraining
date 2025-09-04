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

try {
    // Obtener datos JSON
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // Log para depuración
    error_log("CURSOS AJAX - Input recibido: " . $input);
    error_log("CURSOS AJAX - Data decodificado: " . print_r($data, true));

    if (!$data) {
        echo json_encode(['success' => false, 'mensaje' => 'Datos inválidos']);
        exit;
    }

    $accion = $data['accion'] ?? '';
    $idCurso = $data['idCurso'] ?? null;
    $idUsuario = $_SESSION['idU'];

    error_log("CURSOS AJAX - Acción: $accion, ID Curso: $idCurso, ID Usuario: $idUsuario");

    // Verificar que el curso pertenece al usuario (para acciones que lo requieran)
    if ($idCurso && $accion !== 'obtenerCategorias') {
        $curso = ControladorCursos::ctrMostrarCursos('id', $idCurso);
        if (!$curso || $curso[0]['id_persona'] != $idUsuario) {
            error_log("CURSOS AJAX - Error de permisos para curso $idCurso y usuario $idUsuario");
            echo json_encode(['success' => false, 'mensaje' => 'No tienes permisos para editar este curso']);
            exit;
        }
        error_log("CURSOS AJAX - Permisos verificados correctamente");
    }

    switch ($accion) {
        case 'actualizarCampo':
            $campo = $data['campo'] ?? '';
            $valor = $data['valor'] ?? '';

            error_log("CURSOS AJAX - Actualizando campo: $campo con valor: $valor");

            if (!$campo) {
                echo json_encode(['success' => false, 'mensaje' => 'Campo no especificado']);
                exit;
            }

            actualizarCampo($idCurso, $campo, $valor, $idUsuario);
            break;

        case 'validarNombre':
            $nombre = $data['nombre'] ?? '';
            if (!$nombre) {
                echo json_encode(['success' => false, 'mensaje' => 'Nombre no especificado']);
                exit;
            }

            require_once "../modelos/cursos.modelo.php";
            $esUnico = ModeloCursos::mdlValidarNombreUnico($nombre, $idCurso);
            echo json_encode(['success' => true, 'esUnico' => $esUnico]);
            break;

        case 'obtenerCategorias':
            obtenerCategorias();
            break;

        default:
            echo json_encode(['success' => false, 'mensaje' => 'Acción no válida']);
            break;
    }
} catch (Exception $e) {
    error_log("CURSOS AJAX - Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'mensaje' => 'Error del servidor: ' . $e->getMessage()]);
}

/**
 * Actualizar campo específico del curso
 */
function actualizarCampo($idCurso, $campo, $valor, $idUsuario)
{
    error_log("ACTUALIZANDO CAMPO - Curso: $idCurso, Campo: $campo, Valor: $valor");

    // Campos permitidos para edición
    $camposPermitidos = [
        'nombre',
        'descripcion',
        'lo_que_aprenderas',
        'requisitos',
        'para_quien',
        'valor',
        'id_categoria'
    ];

    if (!in_array($campo, $camposPermitidos)) {
        echo json_encode(['success' => false, 'mensaje' => 'Campo no permitido para edición']);
        return;
    }

    // Validaciones específicas por campo
    $validacion = validarCampo($campo, $valor, $idCurso);
    if (!$validacion['valido']) {
        echo json_encode(['success' => false, 'mensaje' => $validacion['mensaje']]);
        return;
    }

    // Actualizar usando el modelo
    require_once "../modelos/cursos.modelo.php";
    $resultado = ModeloCursos::mdlActualizarCampoCurso($idCurso, $campo, $valor);

    error_log("ACTUALIZANDO CAMPO - Resultado: $resultado");

    if ($resultado === "ok") {
        // Generar nueva URL amigable si se cambió el nombre
        if ($campo === 'nombre') {
            actualizarUrlAmigable($idCurso, $valor);
        }

        // Formatear valor para la respuesta
        $valorFormateado = formatearValorParaVista($campo, $valor);

        echo json_encode([
            'success' => true,
            'mensaje' => 'Campo actualizado correctamente',
            'valorFormateado' => $valorFormateado
        ]);
    } else {
        echo json_encode(['success' => false, 'mensaje' => 'Error al actualizar el campo']);
    }
}

/**
 * Validar campo específico
 */
function validarCampo($campo, $valor, $idCurso = null)
{
    switch ($campo) {
        case 'nombre':
            if (strlen(trim($valor)) < 10) {
                return ['valido' => false, 'mensaje' => 'El nombre debe tener al menos 10 caracteres'];
            }
            if (strlen(trim($valor)) > 255) {
                return ['valido' => false, 'mensaje' => 'El nombre no puede superar los 255 caracteres'];
            }
            // Verificar nombre único (excluyendo el curso actual)
            require_once "../modelos/cursos.modelo.php";
            if (!ModeloCursos::mdlValidarNombreUnico($valor, $idCurso)) {
                return ['valido' => false, 'mensaje' => 'Ya existe un curso con este nombre'];
            }
            break;

        case 'descripcion':
        case 'lo_que_aprenderas':
        case 'requisitos':
        case 'para_quien':
            if (strlen(trim($valor)) < 10) {
                return ['valido' => false, 'mensaje' => 'Este campo debe tener al menos 10 caracteres'];
            }
            break;

        case 'valor':
            if (!is_numeric($valor) || $valor < 0) {
                return ['valido' => false, 'mensaje' => 'El valor debe ser un número positivo'];
            }
            break;

        case 'id_categoria':
            if (!is_numeric($valor) || $valor <= 0) {
                return ['valido' => false, 'mensaje' => 'Categoría inválida'];
            }
            // Verificar que la categoría existe
            $categorias = ControladorCursos::ctrObtenerCategorias();
            $categoriaValida = false;
            foreach ($categorias as $cat) {
                if ($cat['id'] == $valor) {
                    $categoriaValida = true;
                    break;
                }
            }
            if (!$categoriaValida) {
                return ['valido' => false, 'mensaje' => 'La categoría seleccionada no existe'];
            }
            break;
    }

    return ['valido' => true];
}

/**
 * Actualizar URL amigable cuando cambia el nombre
 */
function actualizarUrlAmigable($idCurso, $nuevoNombre)
{
    $conexion = Conexion::conectar();

    // Generar nueva URL amigable
    $urlAmiga = generarUrlAmigable($nuevoNombre);

    // Verificar que sea única
    $contador = 1;
    $urlAmigaOriginal = $urlAmiga;

    while (true) {
        $stmt = $conexion->prepare("SELECT id FROM curso WHERE url_amiga = :url_amiga AND id != :id");
        $stmt->bindParam(':url_amiga', $urlAmiga, PDO::PARAM_STR);
        $stmt->bindParam(':id', $idCurso, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            break; // URL única encontrada
        }

        $contador++;
        $urlAmiga = $urlAmigaOriginal . '-' . $contador;
    }

    // Actualizar URL amigable
    $stmt = $conexion->prepare("UPDATE curso SET url_amiga = :url_amiga WHERE id = :id");
    $stmt->bindParam(':url_amiga', $urlAmiga, PDO::PARAM_STR);
    $stmt->bindParam(':id', $idCurso, PDO::PARAM_INT);
    $stmt->execute();
}

/**
 * Generar URL amigable
 */
function generarUrlAmigable($texto)
{
    // Convertir a minúsculas
    $texto = strtolower($texto);

    // Reemplazar caracteres especiales
    $buscar = ['á', 'é', 'í', 'ó', 'ú', 'ü', 'ñ', 'ç', 'à', 'è', 'ì', 'ò', 'ù'];
    $reemplazar = ['a', 'e', 'i', 'o', 'u', 'u', 'n', 'c', 'a', 'e', 'i', 'o', 'u'];
    $texto = str_replace($buscar, $reemplazar, $texto);

    // Reemplazar espacios y caracteres especiales por guiones
    $texto = preg_replace('/[^a-z0-9]/', '-', $texto);

    // Eliminar guiones duplicados
    $texto = preg_replace('/-+/', '-', $texto);

    // Eliminar guiones al inicio y final
    $texto = trim($texto, '-');

    return $texto;
}

/**
 * Formatear valor para la vista
 */
function formatearValorParaVista($campo, $valor)
{
    switch ($campo) {
        case 'valor':
            return '$' . number_format($valor, 0, ',', '.');

        case 'id_categoria':
            // Obtener nombre de la categoría
            $categorias = ControladorCursos::ctrObtenerCategorias();
            foreach ($categorias as $cat) {
                if ($cat['id'] == $valor) {
                    return htmlspecialchars($cat['nombre']);
                }
            }
            return 'Sin categoría';

        default:
            return nl2br(htmlspecialchars($valor));
    }
}

/**
 * Obtener categorías disponibles
 */
function obtenerCategorias()
{
    try {
        $categorias = ControladorCursos::ctrObtenerCategorias();
        echo json_encode([
            'success' => true,
            'categorias' => $categorias
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'mensaje' => 'Error al obtener categorías'
        ]);
    }
}
