<?php

/**
 * AJAX para gestión de inscripciones y preinscripciones
 * Maneja todas las operaciones CRUD relacionadas con inscripciones
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
require_once $baseDir . "/controladores/inscripciones.controlador.php";
require_once $baseDir . "/controladores/general.controlador.php";
require_once $baseDir . "/modelos/conexion.php";

// Manejar tanto datos JSON como FormData
$accion = '';
$datos = [];

if (isset($_POST['accion'])) {
    // Datos de FormData
    $accion = $_POST['accion'];
    $datos = $_POST;
} else {
    // Datos JSON
    $input = file_get_contents('php://input');
    $datos = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['success' => false, 'mensaje' => 'Datos JSON inválidos']);
        exit;
    }

    $accion = $datos['accion'] ?? '';
}

// Función auxiliar para validar roles
function validarRolParaAccion($accion)
{
    $rolesPermitidos = [];

    // Acciones para estudiantes
    $accionesEstudiante = [
        'crearPreinscripcion',
        'autoInscripcion',              // <-- nueva acción para que estudiantes se inscriban
        'mostrarMisPreinscripciones',
        'mostrarMisInscripciones',
        'cancelarPreinscripcion',
        'verificarPreinscripcion',
        'verificarInscripcion',
        'validarInscripcion',
        'obtenerMisEstadisticas',
        'obtenerMiActividad',
        'obtenerEstadosPermitidos',     // <-- utilidades para todos
        'validarDatos'
    ];

    // Acciones para profesores
    $accionesProfesores = [
        'mostrarInscripcionesCurso',
        'contarInscripcionesCurso',
        'estadisticasInstructor',
        'contarPreinscripcionesCurso',
        'contarInscripcionesPorEstado',
        'obtenerEstadosPermitidos',     // <-- utilidades para todos
        'validarDatos'
    ];

    // Acciones para administradores
    $accionesAdmin = [
        'crearInscripcion',
        'actualizarEstadoInscripcion',
        'eliminarInscripcion',
        'mostrarTodasPreinscripciones',
        'mostrarInscripcionesPendientes',
        'procesarInscripcionDesdePreinscripcion',
        'contarInscripcionesPorEstado',
        'obtenerEstadisticasEstudiante',
        'obtenerEstadosPermitidos',     // <-- utilidades para todos
        'validarDatos'
    ];

    if (in_array($accion, $accionesEstudiante)) {
        $rolesPermitidos = ['estudiante', 'profesor', 'admin'];
    } elseif (in_array($accion, $accionesProfesores)) {
        $rolesPermitidos = ['profesor', 'admin'];
    } elseif (in_array($accion, $accionesAdmin)) {
        $rolesPermitidos = ['admin'];
    } else {
        return false;
    }

    return ControladorGeneral::ctrUsuarioTieneAlgunRol($rolesPermitidos);
}

// Validar permisos para la acción
if (!validarRolParaAccion($accion)) {
    echo json_encode(['success' => false, 'mensaje' => 'No tienes permisos para realizar esta acción']);
    exit;
}

switch ($accion) {
    /*=========================================================
    PREINSCRIPCIONES
    ===========================================================*/

    case 'crearPreinscripcion':
        // Validar datos requeridos
        if (!isset($datos['idCurso'])) {
            echo json_encode(['success' => false, 'mensaje' => 'ID del curso es requerido']);
            break;
        }

        // Preparar datos para el controlador
        $datosPreinscripcion = [
            'idCurso' => $datos['idCurso'],
            'idEstudiante' => $_SESSION['idU']
        ];

        $respuesta = ControladorInscripciones::ctrCrearPreinscripcion($datosPreinscripcion);
        echo json_encode($respuesta);
        break;

    case 'mostrarMisPreinscripciones':
        // El estudiante ve sus propias preinscripciones
        $estado = $datos['estado'] ?? 'preinscrito';
        $respuesta = ControladorInscripciones::ctrMostrarPreinscripcionesPorUsuario($_SESSION['idU'], $estado);
        echo json_encode($respuesta);
        break;

    case 'cancelarPreinscripcion':
        // Validar datos requeridos
        if (!isset($datos['idPreinscripcion'])) {
            echo json_encode(['success' => false, 'mensaje' => 'ID de preinscripción es requerido']);
            break;
        }

        // Verificar que la preinscripción pertenece al usuario
        $preinscripcion = ControladorInscripciones::ctrMostrarInscripcion('preinscripciones', 'id', $datos['idPreinscripcion']);
        if (!$preinscripcion || $preinscripcion['id_estudiante'] != $_SESSION['idU']) {
            echo json_encode(['success' => false, 'mensaje' => 'No tienes permisos para cancelar esta preinscripción']);
            break;
        }

        $respuesta = ControladorInscripciones::ctrCancelarPreinscripcion($datos['idPreinscripcion']);
        echo json_encode($respuesta);
        break;

    case 'verificarPreinscripcion':
        // Validar datos requeridos
        if (!isset($datos['idCurso'])) {
            echo json_encode(['success' => false, 'mensaje' => 'ID del curso es requerido']);
            break;
        }

        $existe = ControladorInscripciones::ctrVerificarPreinscripcion($datos['idCurso'], $_SESSION['idU']);
        echo json_encode([
            'success' => true,
            'existe' => (bool)$existe,
            'preinscripcion' => $existe
        ]);
        break;

    case 'mostrarTodasPreinscripciones':
        // Solo para administradores
        $estado = $datos['estado'] ?? null;
        $respuesta = ControladorInscripciones::ctrMostrarTodasPreinscripciones($estado);
        echo json_encode($respuesta);
        break;

    case 'contarPreinscripcionesCurso':
        // Para profesores y administradores
        if (!isset($datos['idCurso'])) {
            echo json_encode(['success' => false, 'mensaje' => 'ID del curso es requerido']);
            break;
        }

        // Verificar que el curso pertenece al profesor (si no es admin)
        if (!ControladorGeneral::ctrUsuarioTieneAlgunRol(['admin'])) {
            require_once $baseDir . "/controladores/cursos.controlador.php";
            $curso = ControladorCursos::ctrMostrarCursos('id', $datos['idCurso']);
            if (!$curso || $curso[0]['id_persona'] != $_SESSION['idU']) {
                echo json_encode(['success' => false, 'mensaje' => 'No tienes permisos para ver este curso']);
                break;
            }
        }

        $estado = $datos['estado'] ?? 'preinscrito';
        $respuesta = ControladorInscripciones::ctrContarPreinscripcionesPorCurso($datos['idCurso'], $estado);
        echo json_encode($respuesta);
        break;

    /*=========================================================
    INSCRIPCIONES
    ===========================================================*/

    case 'crearInscripcion':
        // Solo para administradores
        if (!isset($datos['idCurso']) || !isset($datos['idEstudiante'])) {
            echo json_encode(['success' => false, 'mensaje' => 'ID del curso e ID del estudiante son requeridos']);
            break;
        }

        $respuesta = ControladorInscripciones::ctrCrearInscripcion($datos);
        echo json_encode($respuesta);
        break;

    case 'autoInscripcion':
        // Para que estudiantes se inscriban a sí mismos
        if (!isset($datos['idCurso'])) {
            echo json_encode(['success' => false, 'mensaje' => 'ID del curso es requerido']);
            break;
        }

        // Preparar datos para la inscripción automática
        $datosInscripcion = [
            'idCurso' => $datos['idCurso'],
            'idEstudiante' => $_SESSION['idU'], // Usar el ID del usuario actual
            'estado' => 'pendiente'  // Estado inicial de la inscripción
        ];

        $respuesta = ControladorInscripciones::ctrCrearInscripcion($datosInscripcion);
        echo json_encode($respuesta);
        break;

    case 'mostrarMisInscripciones':
        // El usuario ve sus propias inscripciones
        $estado = $datos['estado'] ?? null;
        $respuesta = ControladorInscripciones::ctrMostrarInscripcionesPorUsuario($_SESSION['idU'], $estado);
        echo json_encode($respuesta);
        break;

    case 'mostrarInscripcionesCurso':
        // Para profesores y administradores
        if (!isset($datos['idCurso'])) {
            echo json_encode(['success' => false, 'mensaje' => 'ID del curso es requerido']);
            break;
        }

        // Verificar que el curso pertenece al profesor (si no es admin)
        if (!ControladorGeneral::ctrUsuarioTieneAlgunRol(['admin'])) {
            require_once $baseDir . "/controladores/cursos.controlador.php";
            $curso = ControladorCursos::ctrMostrarCursos('id', $datos['idCurso']);
            if (!$curso || $curso[0]['id_persona'] != $_SESSION['idU']) {
                echo json_encode(['success' => false, 'mensaje' => 'No tienes permisos para ver este curso']);
                break;
            }
        }

        $estado = $datos['estado'] ?? null;
        $respuesta = ControladorInscripciones::ctrMostrarInscripcionesPorCurso($datos['idCurso'], $estado);
        echo json_encode($respuesta);
        break;

    case 'verificarInscripcion':
        // Verificar si el usuario está inscrito en un curso
        if (!isset($datos['idCurso'])) {
            echo json_encode(['success' => false, 'mensaje' => 'ID del curso es requerido']);
            break;
        }

        $existe = ControladorInscripciones::ctrVerificarInscripcion($datos['idCurso'], $_SESSION['idU']);
        echo json_encode([
            'success' => true,
            'existe' => (bool)$existe,
            'inscripcion' => $existe
        ]);
        break;

    case 'actualizarEstadoInscripcion':
        // Solo para administradores
        if (!isset($datos['idInscripcion']) || !isset($datos['estado'])) {
            echo json_encode(['success' => false, 'mensaje' => 'ID de inscripción y estado son requeridos']);
            break;
        }

        $respuesta = ControladorInscripciones::ctrActualizarEstadoInscripcion($datos['idInscripcion'], $datos['estado']);
        echo json_encode($respuesta);
        break;

    case 'marcarCursoFinalizado':
        // Para estudiantes en sus propias inscripciones
        if (!isset($datos['idInscripcion'])) {
            echo json_encode(['success' => false, 'mensaje' => 'ID de inscripción es requerido']);
            break;
        }

        // Verificar que la inscripción pertenece al usuario (si no es admin)
        if (!ControladorGeneral::ctrUsuarioTieneAlgunRol(['admin'])) {
            $inscripcion = ControladorInscripciones::ctrMostrarInscripcion('inscripciones', 'id', $datos['idInscripcion']);
            if (!$inscripcion || $inscripcion['id_estudiante'] != $_SESSION['idU']) {
                echo json_encode(['success' => false, 'mensaje' => 'No tienes permisos para modificar esta inscripción']);
                break;
            }
        }

        $resultado = ControladorInscripciones::ctrMarcarCursoFinalizado($datos['idInscripcion']);
        echo json_encode([
            'success' => $resultado === 'ok',
            'mensaje' => $resultado === 'ok' ? 'Curso marcado como finalizado' : 'Error al marcar el curso como finalizado'
        ]);
        break;

    case 'eliminarInscripcion':
        // Solo para administradores
        if (!isset($datos['idInscripcion'])) {
            echo json_encode(['success' => false, 'mensaje' => 'ID de inscripción es requerido']);
            break;
        }

        $respuesta = ControladorInscripciones::ctrEliminarInscripcion($datos['idInscripcion']);
        echo json_encode($respuesta);
        break;

    /*=========================================================
    VALIDACIONES Y ESTADÍSTICAS
    ===========================================================*/

    case 'validarInscripcion':
        // Validar si un usuario puede inscribirse
        if (!isset($datos['idCurso'])) {
            echo json_encode(['success' => false, 'mensaje' => 'ID del curso es requerido']);
            break;
        }

        $idEstudiante = $datos['idEstudiante'] ?? $_SESSION['idU'];
        $respuesta = ControladorInscripciones::ctrValidarInscripcion($datos['idCurso'], $idEstudiante);
        echo json_encode($respuesta);
        break;

    case 'contarInscripcionesPorEstado':
        // Para administradores y profesores
        $idCurso = $datos['idCurso'] ?? null;

        // Si se especifica un curso, verificar permisos
        if ($idCurso && !ControladorGeneral::ctrUsuarioTieneAlgunRol(['admin'])) {
            require_once $baseDir . "/controladores/cursos.controlador.php";
            $curso = ControladorCursos::ctrMostrarCursos('id', $idCurso);
            if (!$curso || $curso[0]['id_persona'] != $_SESSION['idU']) {
                echo json_encode(['success' => false, 'mensaje' => 'No tienes permisos para ver este curso']);
                break;
            }
        }

        $respuesta = ControladorInscripciones::ctrContarInscripcionesPorEstado($idCurso);
        echo json_encode(['success' => true, 'datos' => $respuesta]);
        break;

    case 'estadisticasInstructor':
        // Para profesores viendo sus propias estadísticas
        $idInstructor = ControladorGeneral::ctrUsuarioTieneAlgunRol(['admin']) ?
            ($datos['idInstructor'] ?? $_SESSION['idU']) : $_SESSION['idU'];

        $respuesta = ControladorInscripciones::ctrEstadisticasInscripcionesPorInstructor($idInstructor);
        echo json_encode(['success' => true, 'datos' => $respuesta]);
        break;

    case 'mostrarInscripcionesPendientes':
        // Solo para administradores
        $respuesta = ControladorInscripciones::ctrMostrarInscripcionesPendientes();
        echo json_encode(['success' => true, 'datos' => $respuesta]);
        break;

    case 'procesarInscripcionDesdePreinscripcion':
        // Solo para administradores
        if (!isset($datos['idPreinscripcion'])) {
            echo json_encode(['success' => false, 'mensaje' => 'ID de preinscripción es requerido']);
            break;
        }

        $idInscripcion = ControladorInscripciones::ctrProcesarInscripcionDesdePreinscripcion($datos['idPreinscripcion']);

        if ($idInscripcion) {
            echo json_encode([
                'success' => true,
                'mensaje' => 'Preinscripción convertida a inscripción exitosamente',
                'idInscripcion' => $idInscripcion
            ]);
        } else {
            echo json_encode(['success' => false, 'mensaje' => 'Error al procesar la preinscripción']);
        }
        break;

    case 'obtenerMisEstadisticas':
        // Para usuarios viendo sus propias estadísticas
        $respuesta = ControladorInscripciones::ctrObtenerEstadisticasEstudiante($_SESSION['idU']);
        echo json_encode($respuesta);
        break;

    case 'obtenerEstadisticasEstudiante':
        // Solo para administradores
        if (!isset($datos['idEstudiante'])) {
            echo json_encode(['success' => false, 'mensaje' => 'ID del estudiante es requerido']);
            break;
        }

        $respuesta = ControladorInscripciones::ctrObtenerEstadisticasEstudiante($datos['idEstudiante']);
        echo json_encode($respuesta);
        break;

    case 'obtenerMiActividad':
        // Para usuarios viendo su propia actividad
        $dias = $datos['dias'] ?? 30;
        $respuesta = ControladorInscripciones::ctrObtenerActividadReciente($_SESSION['idU'], $dias);
        echo json_encode($respuesta);
        break;

    /*=========================================================
    UTILIDADES
    ===========================================================*/

    case 'obtenerEstadosPermitidos':
        // Obtener lista de estados válidos
        $respuesta = ControladorInscripciones::ctrObtenerEstadosPermitidos();
        echo json_encode(['success' => true, 'datos' => $respuesta]);
        break;

    case 'validarDatos':
        // Validar datos de inscripción
        $respuesta = ControladorInscripciones::ctrValidarDatosInscripcion($datos);
        echo json_encode($respuesta);
        break;

    /*=========================================================
    CASO POR DEFECTO
    ===========================================================*/

    default:
        // Listar acciones disponibles
        $accionesDisponibles = [
            // Preinscripciones
            'crearPreinscripcion',
            'mostrarMisPreinscripciones',
            'cancelarPreinscripcion',
            'verificarPreinscripcion',
            'mostrarTodasPreinscripciones',
            'contarPreinscripcionesCurso',
            // Inscripciones
            'crearInscripcion',
            'mostrarMisInscripciones',
            'mostrarInscripcionesCurso',
            'verificarInscripcion',
            'actualizarEstadoInscripcion',
            'marcarCursoFinalizado',
            'eliminarInscripcion',
            // Estadísticas y validaciones
            'validarInscripcion',
            'contarInscripcionesPorEstado',
            'estadisticasInstructor',
            'mostrarInscripcionesPendientes',
            'procesarInscripcionDesdePreinscripcion',
            'obtenerMisEstadisticas',
            'obtenerEstadisticasEstudiante',
            'obtenerMiActividad',
            // Utilidades
            'obtenerEstadosPermitidos',
            'validarDatos'
        ];

        echo json_encode([
            'success' => false,
            'mensaje' => 'Acción no válida. Acciones disponibles: ' . implode(', ', $accionesDisponibles),
            'accion_recibida' => $accion ?: 'undefined'
        ]);
        break;
}

// Función auxiliar para log de errores (opcional)
function logError($mensaje, $datos = [])
{
    $log = [
        'timestamp' => date('Y-m-d H:i:s'),
        'usuario' => $_SESSION['idU'] ?? 'no_auth',
        'mensaje' => $mensaje,
        'datos' => $datos
    ];

    error_log("INSCRIPCIONES_AJAX: " . json_encode($log));
}
