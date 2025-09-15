<?php

require_once "conexion.php";

class ModeloInscripciones
{
	/*=========================================================
	PREINSCRIPCIONES - CRUD
	===========================================================*/

	/**
	 * Crear una nueva preinscripción
	 * @param int $idCurso ID del curso
	 * @param int $idEstudiante ID del estudiante
	 * @return array Resultado de la operación
	 */

	/*=============================================
	Crear Preinscripción
	==============================================*/
	public static function mdlCrearPreinscripcion($idCurso, $idEstudiante)
	{
		// Validaciones básicas
		if (!$idCurso || !$idEstudiante) {
			return [
				'success' => false,
				'mensaje' => 'Faltan datos requeridos: idCurso y idEstudiante'
			];
		}

		try {
			// Verificar si ya existe una preinscripción activa
			$verificacion = self::mdlVerificarPreinscripcion($idCurso, $idEstudiante);
			if ($verificacion) {
				return [
					'success' => false,
					'mensaje' => 'Ya existe una preinscripción activa para este curso'
				];
			}

			// Verificar si ya está inscrito
			$inscripcion = self::mdlVerificarInscripcion($idCurso, $idEstudiante);
			if ($inscripcion) {
				return [
					'success' => false,
					'mensaje' => 'El usuario ya está inscrito en este curso'
				];
			}

			// Verificar si existe una preinscripción cancelada para reactivarla
			$stmt = Conexion::conectar()->prepare("SELECT id FROM preinscripciones WHERE id_curso = :id_curso AND id_estudiante = :id_estudiante AND estado = 'cancelado'");
			$stmt->bindParam(":id_curso", $idCurso, PDO::PARAM_INT);
			$stmt->bindParam(":id_estudiante", $idEstudiante, PDO::PARAM_INT);
			$stmt->execute();
			$preinscripcionCancelada = $stmt->fetch(PDO::FETCH_ASSOC);

			if ($preinscripcionCancelada) {
				// Reactivar la preinscripción cancelada
				$stmtUpdate = Conexion::conectar()->prepare("UPDATE preinscripciones SET estado = 'preinscrito', fecha_actualizacion = NOW() WHERE id = :id");
				$stmtUpdate->bindParam(":id", $preinscripcionCancelada['id'], PDO::PARAM_INT);

				if ($stmtUpdate->execute()) {
					return [
						'success' => true,
						'mensaje' => 'Preinscripción reactivada exitosamente',
						'id' => $preinscripcionCancelada['id'],
						'reactivada' => true
					];
				} else {
					return [
						'success' => false,
						'mensaje' => 'Error al reactivar la preinscripción'
					];
				}
			} else {
				// Crear nueva preinscripción
				$stmtInsert = Conexion::conectar()->prepare("INSERT INTO preinscripciones (id_curso, id_estudiante, estado, fecha_preinscripcion) VALUES (:id_curso, :id_estudiante, 'preinscrito', NOW())");
				$stmtInsert->bindParam(":id_curso", $idCurso, PDO::PARAM_INT);
				$stmtInsert->bindParam(":id_estudiante", $idEstudiante, PDO::PARAM_INT);

				if ($stmtInsert->execute()) {
					return [
						'success' => true,
						'mensaje' => 'Preinscripción creada exitosamente',
						'id' => Conexion::conectar()->lastInsertId(),
						'reactivada' => false
					];
				} else {
					return [
						'success' => false,
						'mensaje' => 'Error al crear la preinscripción'
					];
				}
			}
		} catch (Exception $e) {
			return [
				'success' => false,
				'mensaje' => 'Error de base de datos: ' . $e->getMessage()
			];
		}
	}

	/*=============================================
	Mostrar Preinscripciones por Usuario
	==============================================*/
	public static function mdlMostrarPreinscripcionesPorUsuario($idEstudiante, $estado = 'preinscrito')
	{
		// Validaciones
		if (!$idEstudiante) {
			return [
				'success' => false,
				'mensaje' => 'ID de estudiante requerido',
				'data' => []
			];
		}

		try {
			$whereEstado = "";
			$parametros = [':id_estudiante' => $idEstudiante];

			if ($estado !== null) {
				$whereEstado = "AND p.estado = :estado";
				$parametros[':estado'] = $estado;
			}

			$stmt = Conexion::conectar()->prepare("SELECT p.*, c.nombre as nombre_curso, c.banner, c.valor, c.url_amiga, cat.nombre as categoria, per.nombre as instructor_nombre
												   FROM preinscripciones p 
												   INNER JOIN curso c ON p.id_curso = c.id 
												   LEFT JOIN categoria cat ON c.id_categoria = cat.id 
												   LEFT JOIN persona per ON c.id_persona = per.id
												   WHERE p.id_estudiante = :id_estudiante $whereEstado
												   ORDER BY p.fecha_preinscripcion DESC");

			foreach ($parametros as $key => $value) {
				$stmt->bindValue($key, $value);
			}
			$stmt->execute();

			$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

			return [
				'success' => true,
				'data' => $resultados,
				'total' => count($resultados)
			];
		} catch (Exception $e) {
			return [
				'success' => false,
				'mensaje' => 'Error al obtener preinscripciones: ' . $e->getMessage(),
				'data' => []
			];
		}
	}

	/*=============================================
	Verificar si ya está preinscrito
	==============================================*/
	public static function mdlVerificarPreinscripcion($idCurso, $idEstudiante)
	{
		// Validaciones
		if (!$idCurso || !$idEstudiante) {
			return false;
		}

		try {
			$stmt = Conexion::conectar()->prepare("SELECT * FROM preinscripciones WHERE id_curso = :id_curso AND id_estudiante = :id_estudiante AND estado = 'preinscrito'");

			$stmt->bindParam(":id_curso", $idCurso, PDO::PARAM_INT);
			$stmt->bindParam(":id_estudiante", $idEstudiante, PDO::PARAM_INT);
			$stmt->execute();

			return $stmt->fetch(PDO::FETCH_ASSOC);
		} catch (Exception $e) {
			error_log("Error en mdlVerificarPreinscripcion: " . $e->getMessage());
			return false;
		}
	}

	/*=============================================
	Verificar preinscripción con cualquier estado
	==============================================*/
	public static function mdlVerificarPreinscripcionTodosEstados($idCurso, $idEstudiante, $estado = null)
	{
		// Validaciones
		if (!$idCurso || !$idEstudiante) {
			return false;
		}

		try {
			$sql = "SELECT * FROM preinscripciones WHERE id_curso = :id_curso AND id_estudiante = :id_estudiante";
			$parametros = [
				':id_curso' => $idCurso,
				':id_estudiante' => $idEstudiante
			];

			if ($estado !== null) {
				$sql .= " AND estado = :estado";
				$parametros[':estado'] = $estado;
			}

			$stmt = Conexion::conectar()->prepare($sql);

			foreach ($parametros as $key => $value) {
				$stmt->bindValue($key, $value);
			}

			$stmt->execute();

			return $stmt->fetch(PDO::FETCH_ASSOC);
		} catch (Exception $e) {
			error_log("Error en mdlVerificarPreinscripcionTodosEstados: " . $e->getMessage());
			return false;
		}
	}

	/*=============================================
	Cancelar Preinscripción
	==============================================*/
	public static function mdlCancelarPreinscripcion($idPreinscripcion)
	{
		// Validaciones
		if (!$idPreinscripcion) {
			return [
				'success' => false,
				'mensaje' => 'ID de preinscripción requerido'
			];
		}

		try {
			// Verificar que la preinscripción existe y está activa
			$stmt = Conexion::conectar()->prepare("SELECT estado FROM preinscripciones WHERE id = :id");
			$stmt->bindParam(":id", $idPreinscripcion, PDO::PARAM_INT);
			$stmt->execute();
			$preinscripcion = $stmt->fetch(PDO::FETCH_ASSOC);

			if (!$preinscripcion) {
				return [
					'success' => false,
					'mensaje' => 'Preinscripción no encontrada'
				];
			}

			if ($preinscripcion['estado'] !== 'preinscrito') {
				return [
					'success' => false,
					'mensaje' => 'La preinscripción no puede ser cancelada. Estado actual: ' . $preinscripcion['estado']
				];
			}

			$stmt = Conexion::conectar()->prepare("UPDATE preinscripciones SET estado = 'cancelado', fecha_actualizacion = NOW() WHERE id = :id");
			$stmt->bindParam(":id", $idPreinscripcion, PDO::PARAM_INT);

			if ($stmt->execute()) {
				return [
					'success' => true,
					'mensaje' => 'Preinscripción cancelada exitosamente'
				];
			} else {
				return [
					'success' => false,
					'mensaje' => 'Error al cancelar la preinscripción'
				];
			}
		} catch (Exception $e) {
			return [
				'success' => false,
				'mensaje' => 'Error de base de datos: ' . $e->getMessage()
			];
		}
	}

	/*=============================================
	Convertir Preinscripción a Inscripción
	==============================================*/
	public static function mdlConvertirPreinscripcionAInscripcion($idPreinscripcion, $idInscripcion)
	{
		// Validaciones básicas
		if (!$idPreinscripcion || !$idInscripcion) {
			return [
				'success' => false,
				'mensaje' => 'ID de preinscripción e ID de inscripción son requeridos'
			];
		}

		try {
			// Verificar que la preinscripción existe y está en estado 'preinscrito'
			$stmtVerificar = Conexion::conectar()->prepare("SELECT estado FROM preinscripciones WHERE id = :id");
			$stmtVerificar->bindParam(":id", $idPreinscripcion, PDO::PARAM_INT);
			$stmtVerificar->execute();
			$preinscripcion = $stmtVerificar->fetch(PDO::FETCH_ASSOC);

			if (!$preinscripcion) {
				return [
					'success' => false,
					'mensaje' => 'Preinscripción no encontrada'
				];
			}

			if ($preinscripcion['estado'] !== 'preinscrito') {
				return [
					'success' => false,
					'mensaje' => 'La preinscripción no puede ser convertida. Estado actual: ' . $preinscripcion['estado']
				];
			}

			// Actualizar la preinscripción
			$stmt = Conexion::conectar()->prepare("UPDATE preinscripciones SET estado = 'convertido', id_inscripcion = :id_inscripcion, fecha_actualizacion = NOW() WHERE id = :id");

			$stmt->bindParam(":id", $idPreinscripcion, PDO::PARAM_INT);
			$stmt->bindParam(":id_inscripcion", $idInscripcion, PDO::PARAM_INT);

			if ($stmt->execute()) {
				return [
					'success' => true,
					'mensaje' => 'Preinscripción convertida exitosamente'
				];
			} else {
				return [
					'success' => false,
					'mensaje' => 'Error al convertir la preinscripción'
				];
			}
		} catch (Exception $e) {
			return [
				'success' => false,
				'mensaje' => 'Error de base de datos: ' . $e->getMessage()
			];
		}
	}

	/*=========================================================
	INSCRIPCIONES - CRUD
	===========================================================*/

	/*=============================================
	Crear Inscripción
	==============================================*/
	public static function mdlCrearInscripcion($datos)
	{
		// Convertir objeto a array si es necesario
		if (is_object($datos)) {
			$datos = (array) $datos;
		}

		// Validaciones básicas
		if (!isset($datos['idCurso']) || !isset($datos['idEstudiante'])) {
			return [
				'success' => false,
				'mensaje' => 'Faltan datos requeridos: idCurso y idEstudiante'
			];
		}

		// Establecer valores por defecto
		$estado = $datos['estado'] ?? 'pendiente';

		try {
			$conexion = Conexion::conectar();
			$conexion->beginTransaction();

			// Verificar si ya está inscrito
			$inscripcionExistente = self::mdlVerificarInscripcion($datos['idCurso'], $datos['idEstudiante']);
			if ($inscripcionExistente) {
				$conexion->rollBack();
				return [
					'success' => false,
					'mensaje' => 'El usuario ya está inscrito en este curso',
					'inscripcion_existente' => $inscripcionExistente
				];
			}

			// Verificar si existe una preinscripción activa para convertir
			$preinscripcionActiva = self::mdlVerificarPreinscripcion($datos['idCurso'], $datos['idEstudiante']);

			// Crear la inscripción
			$stmt = $conexion->prepare("INSERT INTO inscripciones (id_curso, id_estudiante, estado, fecha_registro) VALUES (:id_curso, :id_estudiante, :estado, NOW())");

			$stmt->bindParam(":id_curso", $datos['idCurso'], PDO::PARAM_INT);
			$stmt->bindParam(":id_estudiante", $datos['idEstudiante'], PDO::PARAM_INT);
			$stmt->bindParam(":estado", $estado, PDO::PARAM_STR);

			if ($stmt->execute()) {
				$idInscripcion = $conexion->lastInsertId();

				// Si existe una preinscripción activa, actualizarla a 'convertido'
				if ($preinscripcionActiva) {
					$stmtUpdate = $conexion->prepare("UPDATE preinscripciones SET estado = 'convertido', id_inscripcion = :id_inscripcion, fecha_actualizacion = NOW() WHERE id = :id_preinscripcion");
					$stmtUpdate->bindParam(":id_inscripcion", $idInscripcion, PDO::PARAM_INT);
					$stmtUpdate->bindParam(":id_preinscripcion", $preinscripcionActiva['id'], PDO::PARAM_INT);
					$stmtUpdate->execute();
				}

				$conexion->commit();

				return [
					'success' => true,
					'mensaje' => 'Inscripción creada exitosamente' . ($preinscripcionActiva ? ' y preinscripción convertida' : ''),
					'id' => $idInscripcion,
					'preinscripcion_convertida' => (bool)$preinscripcionActiva
				];
			} else {
				$conexion->rollBack();
				return [
					'success' => false,
					'mensaje' => 'Error al crear la inscripción'
				];
			}
		} catch (Exception $e) {
			if (isset($conexion)) {
				$conexion->rollBack();
			}
			return [
				'success' => false,
				'mensaje' => 'Error de base de datos: ' . $e->getMessage()
			];
		}
	}

	/*=============================================
	Mostrar Inscripciones por Usuario
	==============================================*/
	public static function mdlMostrarInscripcionesPorUsuario($idEstudiante, $estado = null)
	{
		// Validaciones
		if (!$idEstudiante) {
			return [
				'success' => false,
				'mensaje' => 'ID de estudiante requerido',
				'data' => []
			];
		}

		try {
			$whereEstado = "";
			$parametros = [':id_estudiante' => $idEstudiante];

			if ($estado !== null) {
				$whereEstado = "AND i.estado = :estado";
				$parametros[':estado'] = $estado;
			}

			$stmt = Conexion::conectar()->prepare("SELECT i.*, c.nombre as nombre_curso, c.banner, c.valor, c.url_amiga, cat.nombre as categoria, p.nombre as instructor_nombre
												   FROM inscripciones i 
												   INNER JOIN curso c ON i.id_curso = c.id 
												   LEFT JOIN categoria cat ON c.id_categoria = cat.id 
												   LEFT JOIN persona p ON c.id_persona = p.id
												   WHERE i.id_estudiante = :id_estudiante $whereEstado
												   ORDER BY i.fecha_registro DESC");

			foreach ($parametros as $key => $value) {
				$stmt->bindValue($key, $value);
			}
			$stmt->execute();

			$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

			return [
				'success' => true,
				'data' => $resultados,
				'total' => count($resultados)
			];
		} catch (Exception $e) {
			return [
				'success' => false,
				'mensaje' => 'Error al obtener inscripciones: ' . $e->getMessage(),
				'data' => []
			];
		}
	}

	/*=============================================
	Mostrar Inscripciones por Curso (para profesores)
	==============================================*/
	public static function mdlMostrarInscripcionesPorCurso($idCurso, $estado = null)
	{
		// Validaciones
		if (!$idCurso) {
			return [
				'success' => false,
				'mensaje' => 'ID de curso requerido',
				'data' => []
			];
		}

		try {
			$whereEstado = "";
			$parametros = [':id_curso' => $idCurso];

			if ($estado !== null) {
				$whereEstado = "AND i.estado = :estado";
				$parametros[':estado'] = $estado;
			}

			$stmt = Conexion::conectar()->prepare("SELECT i.*, p.nombre, p.email, p.foto, p.telefono, p.profesion, p.ciudad, p.pais
												   FROM inscripciones i 
												   INNER JOIN persona p ON i.id_estudiante = p.id 
												   WHERE i.id_curso = :id_curso $whereEstado
												   ORDER BY i.fecha_registro DESC");

			foreach ($parametros as $key => $value) {
				$stmt->bindValue($key, $value);
			}
			$stmt->execute();

			$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

			return [
				'success' => true,
				'data' => $resultados,
				'total' => count($resultados)
			];
		} catch (Exception $e) {
			return [
				'success' => false,
				'mensaje' => 'Error al obtener inscripciones del curso: ' . $e->getMessage(),
				'data' => []
			];
		}
	}

	/*=============================================
	Verificar si ya está inscrito
	==============================================*/
	public static function mdlVerificarInscripcion($idCurso, $idEstudiante)
	{
		// Validaciones
		if (!$idCurso || !$idEstudiante) {
			return false;
		}

		try {
			$stmt = Conexion::conectar()->prepare("SELECT * FROM inscripciones WHERE id_curso = :id_curso AND id_estudiante = :id_estudiante");

			$stmt->bindParam(":id_curso", $idCurso, PDO::PARAM_INT);
			$stmt->bindParam(":id_estudiante", $idEstudiante, PDO::PARAM_INT);
			$stmt->execute();

			return $stmt->fetch(PDO::FETCH_ASSOC);
		} catch (Exception $e) {
			error_log("Error en mdlVerificarInscripcion: " . $e->getMessage());
			return false;
		}
	}

	/*=============================================
	Actualizar Estado de Inscripción
	==============================================*/
	public static function mdlActualizarEstadoInscripcion($idInscripcion, $estado)
	{
		// Validaciones
		if (!$idInscripcion || !$estado) {
			return [
				'success' => false,
				'mensaje' => 'ID de inscripción y estado requeridos'
			];
		}

		// Validar estados permitidos
		$estadosPermitidos = ['pendiente', 'activo', 'suspendido', 'cancelado', 'finalizado'];
		if (!in_array($estado, $estadosPermitidos)) {
			return [
				'success' => false,
				'mensaje' => 'Estado no válido. Estados permitidos: ' . implode(', ', $estadosPermitidos)
			];
		}

		try {
			// Verificar que la inscripción existe
			$stmt = Conexion::conectar()->prepare("SELECT estado FROM inscripciones WHERE id = :id");
			$stmt->bindParam(":id", $idInscripcion, PDO::PARAM_INT);
			$stmt->execute();
			$inscripcion = $stmt->fetch(PDO::FETCH_ASSOC);

			if (!$inscripcion) {
				return [
					'success' => false,
					'mensaje' => 'Inscripción no encontrada'
				];
			}

			$stmt = Conexion::conectar()->prepare("UPDATE inscripciones SET estado = :estado WHERE id = :id");
			$stmt->bindParam(":id", $idInscripcion, PDO::PARAM_INT);
			$stmt->bindParam(":estado", $estado, PDO::PARAM_STR);

			if ($stmt->execute()) {
				return [
					'success' => true,
					'mensaje' => 'Estado de inscripción actualizado exitosamente',
					'estado_anterior' => $inscripcion['estado'],
					'estado_nuevo' => $estado
				];
			} else {
				return [
					'success' => false,
					'mensaje' => 'Error al actualizar el estado de la inscripción'
				];
			}
		} catch (Exception $e) {
			return [
				'success' => false,
				'mensaje' => 'Error de base de datos: ' . $e->getMessage()
			];
		}
	}

	/*=============================================
	Marcar Curso como Finalizado
	==============================================*/
	public static function mdlMarcarCursoFinalizado($idInscripcion)
	{
		$stmt = Conexion::conectar()->prepare("UPDATE inscripciones SET finalizado = 1 WHERE id = :id");

		$stmt->bindParam(":id", $idInscripcion, PDO::PARAM_INT);

		if ($stmt->execute()) {
			return "ok";
		} else {
			return "error";
		}
	}

	/*=============================================
	Mostrar Inscripción Específica
	==============================================*/
	public static function mdlMostrarInscripcion($tabla, $item, $valor)
	{
		if ($item != null) {
			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item");
			$stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
			$stmt->execute();
			return $stmt->fetch();
		} else {
			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla");
			$stmt->execute();
			return $stmt->fetchAll();
		}
	}

	/*=============================================
	Eliminar Inscripción (Solo para administradores)
	==============================================*/
	public static function mdlEliminarInscripcion($idInscripcion)
	{
		// Validaciones
		if (!$idInscripcion) {
			return [
				'success' => false,
				'mensaje' => 'ID de inscripción requerido'
			];
		}

		try {
			$conexion = Conexion::conectar();
			$conexion->beginTransaction();

			// Verificar que la inscripción existe
			$stmt = $conexion->prepare("SELECT i.*, c.nombre as curso_nombre, p.nombre as estudiante_nombre 
									   FROM inscripciones i 
									   INNER JOIN curso c ON i.id_curso = c.id 
									   INNER JOIN persona p ON i.id_estudiante = p.id 
									   WHERE i.id = :id");
			$stmt->bindParam(":id", $idInscripcion, PDO::PARAM_INT);
			$stmt->execute();
			$inscripcion = $stmt->fetch(PDO::FETCH_ASSOC);

			if (!$inscripcion) {
				$conexion->rollBack();
				return [
					'success' => false,
					'mensaje' => 'Inscripción no encontrada'
				];
			}

			// Eliminar progreso relacionado
			$stmt = $conexion->prepare("DELETE FROM seccion_contenido_progreso WHERE id_estudiante = :id_estudiante 
									   AND id_contenido IN (
										   SELECT sc.id FROM seccion_contenido sc 
										   INNER JOIN curso_secciones cs ON sc.id_seccion = cs.id 
										   WHERE cs.id_curso = :id_curso
									   )");
			$stmt->bindParam(":id_estudiante", $inscripcion['id_estudiante'], PDO::PARAM_INT);
			$stmt->bindParam(":id_curso", $inscripcion['id_curso'], PDO::PARAM_INT);
			$stmt->execute();

			// Actualizar preinscripciones relacionadas si existen
			$stmt = $conexion->prepare("UPDATE preinscripciones SET id_inscripcion = NULL 
									   WHERE id_inscripcion = :id_inscripcion");
			$stmt->bindParam(":id_inscripcion", $idInscripcion, PDO::PARAM_INT);
			$stmt->execute();

			// Eliminar la inscripción
			$stmt = $conexion->prepare("DELETE FROM inscripciones WHERE id = :id");
			$stmt->bindParam(":id", $idInscripcion, PDO::PARAM_INT);
			$stmt->execute();

			$conexion->commit();

			return [
				'success' => true,
				'mensaje' => 'Inscripción eliminada exitosamente',
				'datos_eliminados' => [
					'curso' => $inscripcion['curso_nombre'],
					'estudiante' => $inscripcion['estudiante_nombre']
				]
			];
		} catch (Exception $e) {
			$conexion->rollBack();
			return [
				'success' => false,
				'mensaje' => 'Error al eliminar la inscripción: ' . $e->getMessage()
			];
		}
	}

	/*=========================================================
	MÉTODOS ADICIONALES PARA REPORTES Y ESTADÍSTICAS
	===========================================================*/

	/*=============================================
	Contar Inscripciones por Estado
	==============================================*/
	public static function mdlContarInscripcionesPorEstado($idCurso = null)
	{
		$whereCurso = "";
		if ($idCurso !== null) {
			$whereCurso = "WHERE id_curso = :id_curso";
		}

		$stmt = Conexion::conectar()->prepare("SELECT estado, COUNT(*) as total FROM inscripciones $whereCurso GROUP BY estado");

		if ($idCurso !== null) {
			$stmt->bindParam(":id_curso", $idCurso, PDO::PARAM_INT);
		}
		$stmt->execute();

		return $stmt->fetchAll();
	}

	/*=============================================
	Obtener Estadísticas de Inscripciones por Instructor
	==============================================*/
	public static function mdlEstadisticasInscripcionesPorInstructor($idInstructor)
	{
		$stmt = Conexion::conectar()->prepare("SELECT 
											   c.nombre as curso_nombre,
											   COUNT(i.id) as total_inscripciones,
											   SUM(CASE WHEN i.estado = 'activo' THEN 1 ELSE 0 END) as inscripciones_activas,
											   SUM(CASE WHEN i.finalizado = 1 THEN 1 ELSE 0 END) as cursos_finalizados
											   FROM curso c 
											   LEFT JOIN inscripciones i ON c.id = i.id_curso 
											   WHERE c.id_persona = :id_instructor 
											   GROUP BY c.id, c.nombre");

		$stmt->bindParam(":id_instructor", $idInstructor, PDO::PARAM_INT);
		$stmt->execute();

		return $stmt->fetchAll();
	}

	/*=============================================
	Mostrar Inscripciones Pendientes (para administradores)
	==============================================*/
	public static function mdlMostrarInscripcionesPendientes()
	{
		$stmt = Conexion::conectar()->prepare("SELECT i.*, c.nombre as nombre_curso, p.nombre as estudiante_nombre, p.email 
											   FROM inscripciones i 
											   INNER JOIN curso c ON i.id_curso = c.id 
											   INNER JOIN persona p ON i.id_estudiante = p.id 
											   WHERE i.estado = 'pendiente' 
											   ORDER BY i.fecha_registro DESC");

		$stmt->execute();

		return $stmt->fetchAll();
	}

	/*=============================================
	Proceso Completo: Preinscripción a Inscripción
	==============================================*/
	public static function mdlProcesarInscripcionDesdePreinscripcion($idPreinscripcion)
	{
		try {
			$conexion = Conexion::conectar();
			$conexion->beginTransaction();

			// Obtener datos de la preinscripción
			$stmt = $conexion->prepare("SELECT * FROM preinscripciones WHERE id = :id AND estado = 'preinscrito'");
			$stmt->bindParam(":id", $idPreinscripcion, PDO::PARAM_INT);
			$stmt->execute();
			$preinscripcion = $stmt->fetch(PDO::FETCH_ASSOC);

			if (!$preinscripcion) {
				$conexion->rollBack();
				return false;
			}

			// Verificar si ya existe una inscripción para este curso y estudiante
			$inscripcionExistente = self::mdlVerificarInscripcion($preinscripcion['id_curso'], $preinscripcion['id_estudiante']);
			if ($inscripcionExistente) {
				// Si ya existe una inscripción, solo actualizar la preinscripción
				$stmt = $conexion->prepare("UPDATE preinscripciones SET estado = 'convertido', id_inscripcion = :id_inscripcion, fecha_actualizacion = NOW() WHERE id = :id");
				$stmt->bindParam(":id", $idPreinscripcion, PDO::PARAM_INT);
				$stmt->bindParam(":id_inscripcion", $inscripcionExistente['id'], PDO::PARAM_INT);
				$stmt->execute();

				$conexion->commit();
				return $inscripcionExistente['id'];
			}

			// Crear nueva inscripción
			$stmt = $conexion->prepare("INSERT INTO inscripciones (id_curso, id_estudiante, estado, fecha_registro) VALUES (:id_curso, :id_estudiante, 'activo', NOW())");
			$stmt->bindParam(":id_curso", $preinscripcion["id_curso"], PDO::PARAM_INT);
			$stmt->bindParam(":id_estudiante", $preinscripcion["id_estudiante"], PDO::PARAM_INT);
			$stmt->execute();

			$idInscripcion = $conexion->lastInsertId();

			// Actualizar preinscripción
			$stmt = $conexion->prepare("UPDATE preinscripciones SET estado = 'convertido', id_inscripcion = :id_inscripcion, fecha_actualizacion = NOW() WHERE id = :id");
			$stmt->bindParam(":id", $idPreinscripcion, PDO::PARAM_INT);
			$stmt->bindParam(":id_inscripcion", $idInscripcion, PDO::PARAM_INT);
			$stmt->execute();

			$conexion->commit();
			return $idInscripcion;
		} catch (Exception $e) {
			if (isset($conexion)) {
				$conexion->rollBack();
			}
			error_log("Error en mdlProcesarInscripcionDesdePreinscripcion: " . $e->getMessage());
			return false;
		}
	}

	/*=============================================
	Mostrar todas las preinscripciones (para admin)
	==============================================*/
	public static function mdlMostrarTodasPreinscripciones($estado = null)
	{
		try {
			$whereEstado = "";
			$parametros = [];

			if ($estado !== null) {
				$whereEstado = "WHERE p.estado = :estado";
				$parametros[':estado'] = $estado;
			}

			$stmt = Conexion::conectar()->prepare("SELECT p.*, c.nombre as nombre_curso, c.valor as precio_curso, per.nombre as estudiante_nombre, per.email as estudiante_email, cat.nombre as categoria_curso
												   FROM preinscripciones p 
												   INNER JOIN curso c ON p.id_curso = c.id 
												   LEFT JOIN categoria cat ON c.id_categoria = cat.id
												   INNER JOIN persona per ON p.id_estudiante = per.id 
												   $whereEstado
												   ORDER BY p.fecha_preinscripcion DESC");

			foreach ($parametros as $key => $value) {
				$stmt->bindValue($key, $value);
			}
			$stmt->execute();

			$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

			return [
				'success' => true,
				'data' => $resultados,
				'total' => count($resultados)
			];
		} catch (Exception $e) {
			return [
				'success' => false,
				'mensaje' => 'Error al obtener preinscripciones: ' . $e->getMessage(),
				'data' => []
			];
		}
	}

	/*=============================================
	Contar preinscripciones por curso
	==============================================*/
	public static function mdlContarPreinscripcionesPorCurso($idCurso, $estado = 'preinscrito')
	{
		// Validaciones
		if (!$idCurso) {
			return [
				'success' => false,
				'mensaje' => 'ID de curso requerido',
				'total' => 0
			];
		}

		try {
			$whereEstado = "";
			$parametros = [':id_curso' => $idCurso];

			if ($estado !== null) {
				$whereEstado = "AND estado = :estado";
				$parametros[':estado'] = $estado;
			}

			$stmt = Conexion::conectar()->prepare("SELECT COUNT(*) as total FROM preinscripciones WHERE id_curso = :id_curso $whereEstado");

			foreach ($parametros as $key => $value) {
				$stmt->bindValue($key, $value);
			}
			$stmt->execute();

			$resultado = $stmt->fetch(PDO::FETCH_ASSOC);

			return [
				'success' => true,
				'total' => (int)$resultado['total'],
				'estado_filtrado' => $estado
			];
		} catch (Exception $e) {
			return [
				'success' => false,
				'mensaje' => 'Error al contar preinscripciones: ' . $e->getMessage(),
				'total' => 0
			];
		}
	}

	/**
	 * Validar si un usuario puede inscribirse en un curso
	 * @param int $idCurso ID del curso
	 * @param int $idEstudiante ID del estudiante
	 * @return array Resultado de la validación
	 */
	public static function mdlValidarInscripcion($idCurso, $idEstudiante)
	{
		try {
			// Verificar si el curso existe y está activo
			$stmt = Conexion::conectar()->prepare("SELECT estado FROM curso WHERE id = :id_curso");
			$stmt->bindParam(":id_curso", $idCurso, PDO::PARAM_INT);
			$stmt->execute();
			$curso = $stmt->fetch(PDO::FETCH_ASSOC);

			if (!$curso) {
				return [
					'success' => false,
					'mensaje' => 'El curso no existe'
				];
			}

			if ($curso['estado'] !== 'activo') {
				return [
					'success' => false,
					'mensaje' => 'El curso no está disponible para inscripciones'
				];
			}

			// Verificar si ya está inscrito
			$inscripcion = self::mdlVerificarInscripcion($idCurso, $idEstudiante);
			if ($inscripcion) {
				return [
					'success' => false,
					'mensaje' => 'Ya está inscrito en este curso',
					'inscripcion' => $inscripcion
				];
			}

			return [
				'success' => true,
				'mensaje' => 'Puede inscribirse en el curso'
			];
		} catch (Exception $e) {
			return [
				'success' => false,
				'mensaje' => 'Error al validar inscripción: ' . $e->getMessage()
			];
		}
	}

	/**
	 * Obtener estadísticas completas de un estudiante
	 * @param int $idEstudiante ID del estudiante
	 * @return array Estadísticas del estudiante
	 */
	public static function mdlObtenerEstadisticasEstudiante($idEstudiante)
	{
		try {
			$estadisticas = [];

			// Contar preinscripciones por estado
			$stmt = Conexion::conectar()->prepare("SELECT estado, COUNT(*) as total FROM preinscripciones WHERE id_estudiante = :id_estudiante GROUP BY estado");
			$stmt->bindParam(":id_estudiante", $idEstudiante, PDO::PARAM_INT);
			$stmt->execute();
			$estadisticas['preinscripciones'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

			// Contar inscripciones por estado
			$stmt = Conexion::conectar()->prepare("SELECT estado, COUNT(*) as total FROM inscripciones WHERE id_estudiante = :id_estudiante GROUP BY estado");
			$stmt->bindParam(":id_estudiante", $idEstudiante, PDO::PARAM_INT);
			$stmt->execute();
			$estadisticas['inscripciones'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

			// Contar cursos finalizados
			$stmt = Conexion::conectar()->prepare("SELECT COUNT(*) as total FROM inscripciones WHERE id_estudiante = :id_estudiante AND finalizado = 1");
			$stmt->bindParam(":id_estudiante", $idEstudiante, PDO::PARAM_INT);
			$stmt->execute();
			$resultado = $stmt->fetch(PDO::FETCH_ASSOC);
			$estadisticas['cursos_finalizados'] = (int)$resultado['total'];

			return [
				'success' => true,
				'data' => $estadisticas
			];
		} catch (Exception $e) {
			return [
				'success' => false,
				'mensaje' => 'Error al obtener estadísticas: ' . $e->getMessage()
			];
		}
	}

	/**
	 * Obtener resumen de actividad reciente de un estudiante
	 * @param int $idEstudiante ID del estudiante
	 * @param int $dias Número de días hacia atrás (por defecto 30)
	 * @return array Actividad reciente
	 */
	public static function mdlObtenerActividadReciente($idEstudiante, $dias = 30)
	{
		try {
			$actividad = [];

			// Preinscripciones recientes
			$stmt = Conexion::conectar()->prepare("SELECT 'preinscripcion' as tipo, p.fecha_preinscripcion as fecha, c.nombre as curso_nombre, p.estado
												   FROM preinscripciones p
												   INNER JOIN curso c ON p.id_curso = c.id
												   WHERE p.id_estudiante = :id_estudiante 
												   AND p.fecha_preinscripcion >= DATE_SUB(NOW(), INTERVAL :dias DAY)
												   ORDER BY p.fecha_preinscripcion DESC");
			$stmt->bindParam(":id_estudiante", $idEstudiante, PDO::PARAM_INT);
			$stmt->bindParam(":dias", $dias, PDO::PARAM_INT);
			$stmt->execute();
			$preinscripciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

			// Inscripciones recientes
			$stmt = Conexion::conectar()->prepare("SELECT 'inscripcion' as tipo, i.fecha_registro as fecha, c.nombre as curso_nombre, i.estado
												   FROM inscripciones i
												   INNER JOIN curso c ON i.id_curso = c.id
												   WHERE i.id_estudiante = :id_estudiante 
												   AND i.fecha_registro >= DATE_SUB(NOW(), INTERVAL :dias DAY)
												   ORDER BY i.fecha_registro DESC");
			$stmt->bindParam(":id_estudiante", $idEstudiante, PDO::PARAM_INT);
			$stmt->bindParam(":dias", $dias, PDO::PARAM_INT);
			$stmt->execute();
			$inscripciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

			// Combinar y ordenar por fecha
			$actividad = array_merge($preinscripciones, $inscripciones);
			usort($actividad, function ($a, $b) {
				return strtotime($b['fecha']) - strtotime($a['fecha']);
			});

			return [
				'success' => true,
				'data' => $actividad,
				'periodo_dias' => $dias
			];
		} catch (Exception $e) {
			return [
				'success' => false,
				'mensaje' => 'Error al obtener actividad reciente: ' . $e->getMessage()
			];
		}
	}
}
