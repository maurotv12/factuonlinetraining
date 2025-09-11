<?php

// Incluir modelo de inscripciones - Ruta adaptativa para AJAX
if (file_exists("modelos/inscripciones.modelo.php")) {
	require_once "modelos/inscripciones.modelo.php";
} elseif (file_exists("../modelos/inscripciones.modelo.php")) {
	require_once "../modelos/inscripciones.modelo.php";
} elseif (file_exists("App/modelos/inscripciones.modelo.php")) {
	require_once "App/modelos/inscripciones.modelo.php";
} else {
	require_once __DIR__ . "/../modelos/inscripciones.modelo.php";
}

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class ControladorInscripciones
{
	/*=========================================================
	CONTROLADORES PARA PREINSCRIPCIONES
	===========================================================*/

	/**
	 * Crear una nueva preinscripción
	 * @param array $datos Datos de la preinscripción (idCurso, idEstudiante)
	 * @return array Resultado de la operación
	 */
	public static function ctrCrearPreinscripcion($datos)
	{
		// Validaciones básicas
		if (!isset($datos['idCurso']) || !isset($datos['idEstudiante'])) {
			return [
				'success' => false,
				'mensaje' => 'Faltan datos requeridos: idCurso y idEstudiante'
			];
		}

		// Sanitizar y validar datos
		$idCurso = filter_var($datos['idCurso'], FILTER_VALIDATE_INT);
		$idEstudiante = filter_var($datos['idEstudiante'], FILTER_VALIDATE_INT);

		if (!$idCurso || !$idEstudiante) {
			return [
				'success' => false,
				'mensaje' => 'Los IDs deben ser números válidos'
			];
		}

		// Llamar al modelo
		$respuesta = ModeloInscripciones::mdlCrearPreinscripcion($idCurso, $idEstudiante);

		return $respuesta;
	}

	/**
	 * Mostrar preinscripciones por usuario
	 * @param int $idEstudiante ID del estudiante
	 * @param string $estado Estado a filtrar (opcional)
	 * @return array Resultado de la operación
	 */
	public static function ctrMostrarPreinscripcionesPorUsuario($idEstudiante, $estado = 'preinscrito')
	{
		// Validar ID del estudiante
		$idEstudiante = filter_var($idEstudiante, FILTER_VALIDATE_INT);

		if (!$idEstudiante) {
			return [
				'success' => false,
				'mensaje' => 'ID de estudiante inválido'
			];
		}

		// Validar estado si se proporciona
		if ($estado !== null) {
			$estadosPermitidos = ['preinscrito', 'cancelado', 'convertido', 'expirado'];
			if (!in_array($estado, $estadosPermitidos)) {
				return [
					'success' => false,
					'mensaje' => 'Estado no válido'
				];
			}
		}

		// Llamar al modelo
		$respuesta = ModeloInscripciones::mdlMostrarPreinscripcionesPorUsuario($idEstudiante, $estado);

		return $respuesta;
	}

	/**
	 * Verificar si ya existe una preinscripción
	 * @param int $idCurso ID del curso
	 * @param int $idEstudiante ID del estudiante
	 * @return bool|array Resultado de la verificación
	 */
	public static function ctrVerificarPreinscripcion($idCurso, $idEstudiante)
	{
		// Validar parámetros
		$idCurso = filter_var($idCurso, FILTER_VALIDATE_INT);
		$idEstudiante = filter_var($idEstudiante, FILTER_VALIDATE_INT);

		if (!$idCurso || !$idEstudiante) {
			return false;
		}

		// Llamar al modelo
		return ModeloInscripciones::mdlVerificarPreinscripcion($idCurso, $idEstudiante);
	}

	/**
	 * Cancelar una preinscripción
	 * @param int $idPreinscripcion ID de la preinscripción
	 * @return array Resultado de la operación
	 */
	public static function ctrCancelarPreinscripcion($idPreinscripcion)
	{
		// Validar ID
		$idPreinscripcion = filter_var($idPreinscripcion, FILTER_VALIDATE_INT);

		if (!$idPreinscripcion) {
			return [
				'success' => false,
				'mensaje' => 'ID de preinscripción inválido'
			];
		}

		// Llamar al modelo
		$respuesta = ModeloInscripciones::mdlCancelarPreinscripcion($idPreinscripcion);

		return $respuesta;
	}

	/**
	 * Mostrar todas las preinscripciones (para administradores)
	 * @param string $estado Estado a filtrar (opcional)
	 * @return array Resultado de la operación
	 */
	public static function ctrMostrarTodasPreinscripciones($estado = null)
	{
		// Validar estado si se proporciona
		if ($estado !== null) {
			$estadosPermitidos = ['preinscrito', 'cancelado', 'convertido', 'expirado'];
			if (!in_array($estado, $estadosPermitidos)) {
				return [
					'success' => false,
					'mensaje' => 'Estado no válido'
				];
			}
		}

		// Llamar al modelo
		$respuesta = ModeloInscripciones::mdlMostrarTodasPreinscripciones($estado);

		return $respuesta;
	}

	/**
	 * Contar preinscripciones por curso
	 * @param int $idCurso ID del curso
	 * @param string $estado Estado a filtrar
	 * @return array Resultado de la operación
	 */
	public static function ctrContarPreinscripcionesPorCurso($idCurso, $estado = 'preinscrito')
	{
		// Validar ID del curso
		$idCurso = filter_var($idCurso, FILTER_VALIDATE_INT);

		if (!$idCurso) {
			return [
				'success' => false,
				'mensaje' => 'ID de curso inválido'
			];
		}

		// Validar estado
		if ($estado !== null) {
			$estadosPermitidos = ['preinscrito', 'cancelado', 'convertido', 'expirado'];
			if (!in_array($estado, $estadosPermitidos)) {
				return [
					'success' => false,
					'mensaje' => 'Estado no válido'
				];
			}
		}

		// Llamar al modelo
		$respuesta = ModeloInscripciones::mdlContarPreinscripcionesPorCurso($idCurso, $estado);

		return $respuesta;
	}

	/*=========================================================
	CONTROLADORES PARA INSCRIPCIONES
	===========================================================*/

	/**
	 * Crear una nueva inscripción
	 * @param array $datos Datos de la inscripción
	 * @return array Resultado de la operación
	 */
	public static function ctrCrearInscripcion($datos)
	{
		// Validaciones básicas
		if (!isset($datos['idCurso']) || !isset($datos['idEstudiante'])) {
			return [
				'success' => false,
				'mensaje' => 'Faltan datos requeridos: idCurso y idEstudiante'
			];
		}

		// Sanitizar y validar datos
		$datosLimpios = [];
		$datosLimpios['idCurso'] = filter_var($datos['idCurso'], FILTER_VALIDATE_INT);
		$datosLimpios['idEstudiante'] = filter_var($datos['idEstudiante'], FILTER_VALIDATE_INT);

		if (!$datosLimpios['idCurso'] || !$datosLimpios['idEstudiante']) {
			return [
				'success' => false,
				'mensaje' => 'Los IDs deben ser números válidos'
			];
		}

		// Validar estado si se proporciona
		if (isset($datos['estado'])) {
			$estadosPermitidos = ['pendiente', 'activo', 'suspendido', 'cancelado', 'finalizado'];
			if (!in_array($datos['estado'], $estadosPermitidos)) {
				return [
					'success' => false,
					'mensaje' => 'Estado no válido'
				];
			}
			$datosLimpios['estado'] = $datos['estado'];
		}

		// Llamar al modelo
		$respuesta = ModeloInscripciones::mdlCrearInscripcion($datosLimpios);

		return $respuesta;
	}

	/**
	 * Mostrar inscripciones por usuario
	 * @param int $idEstudiante ID del estudiante
	 * @param string $estado Estado a filtrar (opcional)
	 * @return array Resultado de la operación
	 */
	public static function ctrMostrarInscripcionesPorUsuario($idEstudiante, $estado = null)
	{
		// Validar ID del estudiante
		$idEstudiante = filter_var($idEstudiante, FILTER_VALIDATE_INT);

		if (!$idEstudiante) {
			return [
				'success' => false,
				'mensaje' => 'ID de estudiante inválido'
			];
		}

		// Validar estado si se proporciona
		if ($estado !== null) {
			$estadosPermitidos = ['pendiente', 'activo', 'suspendido', 'cancelado', 'finalizado'];
			if (!in_array($estado, $estadosPermitidos)) {
				return [
					'success' => false,
					'mensaje' => 'Estado no válido'
				];
			}
		}

		// Llamar al modelo
		$respuesta = ModeloInscripciones::mdlMostrarInscripcionesPorUsuario($idEstudiante, $estado);

		return $respuesta;
	}

	/**
	 * Mostrar inscripciones por curso (para profesores)
	 * @param int $idCurso ID del curso
	 * @param string $estado Estado a filtrar (opcional)
	 * @return array Resultado de la operación
	 */
	public static function ctrMostrarInscripcionesPorCurso($idCurso, $estado = null)
	{
		// Validar ID del curso
		$idCurso = filter_var($idCurso, FILTER_VALIDATE_INT);

		if (!$idCurso) {
			return [
				'success' => false,
				'mensaje' => 'ID de curso inválido'
			];
		}

		// Validar estado si se proporciona
		if ($estado !== null) {
			$estadosPermitidos = ['pendiente', 'activo', 'suspendido', 'cancelado', 'finalizado'];
			if (!in_array($estado, $estadosPermitidos)) {
				return [
					'success' => false,
					'mensaje' => 'Estado no válido'
				];
			}
		}

		// Llamar al modelo
		$respuesta = ModeloInscripciones::mdlMostrarInscripcionesPorCurso($idCurso, $estado);

		return $respuesta;
	}

	/**
	 * Verificar si ya existe una inscripción
	 * @param int $idCurso ID del curso
	 * @param int $idEstudiante ID del estudiante
	 * @return bool|array Resultado de la verificación
	 */
	public static function ctrVerificarInscripcion($idCurso, $idEstudiante)
	{
		// Validar parámetros
		$idCurso = filter_var($idCurso, FILTER_VALIDATE_INT);
		$idEstudiante = filter_var($idEstudiante, FILTER_VALIDATE_INT);

		if (!$idCurso || !$idEstudiante) {
			return false;
		}

		// Llamar al modelo
		return ModeloInscripciones::mdlVerificarInscripcion($idCurso, $idEstudiante);
	}

	/**
	 * Actualizar estado de una inscripción
	 * @param int $idInscripcion ID de la inscripción
	 * @param string $estado Nuevo estado
	 * @return array Resultado de la operación
	 */
	public static function ctrActualizarEstadoInscripcion($idInscripcion, $estado)
	{
		// Validar ID
		$idInscripcion = filter_var($idInscripcion, FILTER_VALIDATE_INT);

		if (!$idInscripcion) {
			return [
				'success' => false,
				'mensaje' => 'ID de inscripción inválido'
			];
		}

		// Validar estado
		$estadosPermitidos = ['pendiente', 'activo', 'suspendido', 'cancelado', 'finalizado'];
		if (!in_array($estado, $estadosPermitidos)) {
			return [
				'success' => false,
				'mensaje' => 'Estado no válido'
			];
		}

		// Llamar al modelo
		$respuesta = ModeloInscripciones::mdlActualizarEstadoInscripcion($idInscripcion, $estado);

		return $respuesta;
	}

	/**
	 * Marcar curso como finalizado
	 * @param int $idInscripcion ID de la inscripción
	 * @return string Resultado de la operación
	 */
	public static function ctrMarcarCursoFinalizado($idInscripcion)
	{
		// Validar ID
		$idInscripcion = filter_var($idInscripcion, FILTER_VALIDATE_INT);

		if (!$idInscripcion) {
			return "error";
		}

		// Llamar al modelo
		return ModeloInscripciones::mdlMarcarCursoFinalizado($idInscripcion);
	}

	/**
	 * Mostrar inscripción específica
	 * @param string $tabla Nombre de la tabla
	 * @param string|null $item Campo para filtrar
	 * @param mixed|null $valor Valor para el filtro
	 * @return array|null Resultado de la consulta
	 */
	public static function ctrMostrarInscripcion($tabla, $item, $valor)
	{
		// Validar tabla
		$tablasPermitidas = ['inscripciones', 'preinscripciones'];
		if (!in_array($tabla, $tablasPermitidas)) {
			return null;
		}

		// Llamar al modelo
		return ModeloInscripciones::mdlMostrarInscripcion($tabla, $item, $valor);
	}

	/**
	 * Eliminar inscripción (Solo para administradores)
	 * @param int $idInscripcion ID de la inscripción
	 * @return array Resultado de la operación
	 */
	public static function ctrEliminarInscripcion($idInscripcion)
	{
		// Validar ID
		$idInscripcion = filter_var($idInscripcion, FILTER_VALIDATE_INT);

		if (!$idInscripcion) {
			return [
				'success' => false,
				'mensaje' => 'ID de inscripción inválido'
			];
		}

		// Llamar al modelo
		$respuesta = ModeloInscripciones::mdlEliminarInscripcion($idInscripcion);

		return $respuesta;
	}

	/*=========================================================
	CONTROLADORES PARA REPORTES Y ESTADÍSTICAS
	===========================================================*/

	/**
	 * Contar inscripciones por estado
	 * @param int|null $idCurso ID del curso (opcional)
	 * @return array Resultado de la consulta
	 */
	public static function ctrContarInscripcionesPorEstado($idCurso = null)
	{
		// Validar ID del curso si se proporciona
		if ($idCurso !== null) {
			$idCurso = filter_var($idCurso, FILTER_VALIDATE_INT);
			if (!$idCurso) {
				return [];
			}
		}

		// Llamar al modelo
		return ModeloInscripciones::mdlContarInscripcionesPorEstado($idCurso);
	}

	/**
	 * Obtener estadísticas de inscripciones por instructor
	 * @param int $idInstructor ID del instructor
	 * @return array Resultado de la consulta
	 */
	public static function ctrEstadisticasInscripcionesPorInstructor($idInstructor)
	{
		// Validar ID del instructor
		$idInstructor = filter_var($idInstructor, FILTER_VALIDATE_INT);

		if (!$idInstructor) {
			return [];
		}

		// Llamar al modelo
		return ModeloInscripciones::mdlEstadisticasInscripcionesPorInstructor($idInstructor);
	}

	/**
	 * Mostrar inscripciones pendientes (para administradores)
	 * @return array Resultado de la consulta
	 */
	public static function ctrMostrarInscripcionesPendientes()
	{
		// Llamar al modelo
		return ModeloInscripciones::mdlMostrarInscripcionesPendientes();
	}

	/**
	 * Procesar inscripción desde preinscripción
	 * @param int $idPreinscripcion ID de la preinscripción
	 * @return int|false ID de la inscripción creada o false en caso de error
	 */
	public static function ctrProcesarInscripcionDesdePreinscripcion($idPreinscripcion)
	{
		// Validar ID
		$idPreinscripcion = filter_var($idPreinscripcion, FILTER_VALIDATE_INT);

		if (!$idPreinscripcion) {
			return false;
		}

		// Llamar al modelo
		return ModeloInscripciones::mdlProcesarInscripcionDesdePreinscripcion($idPreinscripcion);
	}

	/**
	 * Validar si un usuario puede inscribirse en un curso
	 * @param int $idCurso ID del curso
	 * @param int $idEstudiante ID del estudiante
	 * @return array Resultado de la validación
	 */
	public static function ctrValidarInscripcion($idCurso, $idEstudiante)
	{
		// Validar parámetros
		$idCurso = filter_var($idCurso, FILTER_VALIDATE_INT);
		$idEstudiante = filter_var($idEstudiante, FILTER_VALIDATE_INT);

		if (!$idCurso || !$idEstudiante) {
			return [
				'success' => false,
				'mensaje' => 'Parámetros inválidos'
			];
		}

		// Llamar al modelo
		$respuesta = ModeloInscripciones::mdlValidarInscripcion($idCurso, $idEstudiante);

		return $respuesta;
	}

	/**
	 * Obtener estadísticas completas de un estudiante
	 * @param int $idEstudiante ID del estudiante
	 * @return array Estadísticas del estudiante
	 */
	public static function ctrObtenerEstadisticasEstudiante($idEstudiante)
	{
		// Validar ID del estudiante
		$idEstudiante = filter_var($idEstudiante, FILTER_VALIDATE_INT);

		if (!$idEstudiante) {
			return [
				'success' => false,
				'mensaje' => 'ID de estudiante inválido'
			];
		}

		// Llamar al modelo
		$respuesta = ModeloInscripciones::mdlObtenerEstadisticasEstudiante($idEstudiante);

		return $respuesta;
	}

	/**
	 * Obtener resumen de actividad reciente de un estudiante
	 * @param int $idEstudiante ID del estudiante
	 * @param int $dias Número de días hacia atrás (por defecto 30)
	 * @return array Actividad reciente
	 */
	public static function ctrObtenerActividadReciente($idEstudiante, $dias = 30)
	{
		// Validar ID del estudiante
		$idEstudiante = filter_var($idEstudiante, FILTER_VALIDATE_INT);

		if (!$idEstudiante) {
			return [
				'success' => false,
				'mensaje' => 'ID de estudiante inválido'
			];
		}

		// Validar días
		$dias = filter_var($dias, FILTER_VALIDATE_INT);
		if (!$dias || $dias < 1) {
			$dias = 30;
		}

		// Llamar al modelo
		$respuesta = ModeloInscripciones::mdlObtenerActividadReciente($idEstudiante, $dias);

		return $respuesta;
	}

	/*=========================================================
	MÉTODOS DE UTILIDAD Y HELPERS
	===========================================================*/

	/**
	 * Convertir preinscripción a inscripción
	 * @param int $idPreinscripcion ID de la preinscripción
	 * @param int $idInscripcion ID de la inscripción
	 * @return string Resultado de la operación
	 */
	public static function ctrConvertirPreinscripcionAInscripcion($idPreinscripcion, $idInscripcion)
	{
		// Validar IDs
		$idPreinscripcion = filter_var($idPreinscripcion, FILTER_VALIDATE_INT);
		$idInscripcion = filter_var($idInscripcion, FILTER_VALIDATE_INT);

		if (!$idPreinscripcion || !$idInscripcion) {
			return "error";
		}

		// Llamar al modelo
		return ModeloInscripciones::mdlConvertirPreinscripcionAInscripcion($idPreinscripcion, $idInscripcion);
	}

	/**
	 * Validar datos de entrada para inscripciones
	 * @param array $datos Datos a validar
	 * @return array Resultado de la validación
	 */
	public static function ctrValidarDatosInscripcion($datos)
	{
		$errores = [];

		// Validar ID del curso
		if (!isset($datos['idCurso']) || !filter_var($datos['idCurso'], FILTER_VALIDATE_INT)) {
			$errores[] = 'ID de curso inválido';
		}

		// Validar ID del estudiante
		if (!isset($datos['idEstudiante']) || !filter_var($datos['idEstudiante'], FILTER_VALIDATE_INT)) {
			$errores[] = 'ID de estudiante inválido';
		}

		// Validar estado si se proporciona
		if (isset($datos['estado'])) {
			$estadosPermitidos = ['pendiente', 'activo', 'suspendido', 'cancelado', 'finalizado'];
			if (!in_array($datos['estado'], $estadosPermitidos)) {
				$errores[] = 'Estado no válido';
			}
		}

		return [
			'valido' => empty($errores),
			'errores' => $errores
		];
	}

	/**
	 * Obtener estados permitidos para inscripciones
	 * @return array Lista de estados permitidos
	 */
	public static function ctrObtenerEstadosPermitidos()
	{
		return [
			'inscripciones' => ['pendiente', 'activo', 'suspendido', 'cancelado', 'finalizado'],
			'preinscripciones' => ['preinscrito', 'cancelado', 'convertido', 'expirado']
		];
	}

	/**
	 * Formatear respuesta para API
	 * @param bool $success Indica si la operación fue exitosa
	 * @param string $mensaje Mensaje de la respuesta
	 * @param array $datos Datos adicionales (opcional)
	 * @return array Respuesta formateada
	 */
	public static function ctrFormatearRespuesta($success, $mensaje, $datos = [])
	{
		$respuesta = [
			'success' => $success,
			'mensaje' => $mensaje,
			'timestamp' => date('Y-m-d H:i:s')
		];

		if (!empty($datos)) {
			$respuesta['datos'] = $datos;
		}

		return $respuesta;
	}
}
