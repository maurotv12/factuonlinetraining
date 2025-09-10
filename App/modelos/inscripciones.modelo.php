<?php

require_once "conexion.php";

class ModeloInscripciones
{
	/*=========================================================
	PREINSCRIPCIONES - CRUD
	===========================================================*/

	/*=============================================
	Crear Preinscripción
	==============================================*/
	public static function mdlCrearPreinscripcion($idCurso, $idEstudiante)
	{
		$stmt = Conexion::conectar()->prepare("INSERT INTO preinscripciones (id_curso, id_estudiante, estado, fecha_preinscripcion) VALUES (:id_curso, :id_estudiante, 'preinscrito', NOW())");

		$stmt->bindParam(":id_curso", $idCurso, PDO::PARAM_INT);
		$stmt->bindParam(":id_estudiante", $idEstudiante, PDO::PARAM_INT);

		if ($stmt->execute()) {
			return "ok";
		} else {
			return "error";
		}
	}

	/*=============================================
	Mostrar Preinscripciones por Usuario
	==============================================*/
	public static function mdlMostrarPreinscripcionesPorUsuario($idEstudiante)
	{
		$stmt = Conexion::conectar()->prepare("SELECT p.*, c.nombre as nombre_curso, c.banner, c.valor, c.url_amiga, cat.nombre as categoria 
											   FROM preinscripciones p 
											   INNER JOIN curso c ON p.id_curso = c.id 
											   LEFT JOIN categoria cat ON c.id_categoria = cat.id 
											   WHERE p.id_estudiante = :id_estudiante 
											   AND p.estado = 'preinscrito' 
											   ORDER BY p.fecha_preinscripcion DESC");

		$stmt->bindParam(":id_estudiante", $idEstudiante, PDO::PARAM_INT);
		$stmt->execute();

		return $stmt->fetchAll();
	}

	/*=============================================
	Verificar si ya está preinscrito
	==============================================*/
	public static function mdlVerificarPreinscripcion($idCurso, $idEstudiante)
	{
		$stmt = Conexion::conectar()->prepare("SELECT id FROM preinscripciones WHERE id_curso = :id_curso AND id_estudiante = :id_estudiante AND estado = 'preinscrito'");

		$stmt->bindParam(":id_curso", $idCurso, PDO::PARAM_INT);
		$stmt->bindParam(":id_estudiante", $idEstudiante, PDO::PARAM_INT);
		$stmt->execute();

		return $stmt->fetch();
	}

	/*=============================================
	Cancelar Preinscripción
	==============================================*/
	public static function mdlCancelarPreinscripcion($idPreinscripcion)
	{
		$stmt = Conexion::conectar()->prepare("UPDATE preinscripciones SET estado = 'cancelado', fecha_actualizacion = NOW() WHERE id = :id");

		$stmt->bindParam(":id", $idPreinscripcion, PDO::PARAM_INT);

		if ($stmt->execute()) {
			return "ok";
		} else {
			return "error";
		}
	}

	/*=============================================
	Convertir Preinscripción a Inscripción
	==============================================*/
	public static function mdlConvertirPreinscripcionAInscripcion($idPreinscripcion, $idInscripcion)
	{
		$stmt = Conexion::conectar()->prepare("UPDATE preinscripciones SET estado = 'convertido', id_inscripcion = :id_inscripcion, fecha_actualizacion = NOW() WHERE id = :id");

		$stmt->bindParam(":id", $idPreinscripcion, PDO::PARAM_INT);
		$stmt->bindParam(":id_inscripcion", $idInscripcion, PDO::PARAM_INT);

		if ($stmt->execute()) {
			return "ok";
		} else {
			return "error";
		}
	}

	/*=========================================================
	INSCRIPCIONES - CRUD
	===========================================================*/

	/*=============================================
	Crear Inscripción
	==============================================*/
	public static function mdlCrearInscripcion($idCurso, $idEstudiante, $estado = 'pendiente')
	{
		$stmt = Conexion::conectar()->prepare("INSERT INTO inscripciones (id_curso, id_estudiante, estado, fecha_registro) VALUES (:id_curso, :id_estudiante, :estado, NOW())");

		$stmt->bindParam(":id_curso", $idCurso, PDO::PARAM_INT);
		$stmt->bindParam(":id_estudiante", $idEstudiante, PDO::PARAM_INT);
		$stmt->bindParam(":estado", $estado, PDO::PARAM_STR);

		if ($stmt->execute()) {
			return Conexion::conectar()->lastInsertId();
		} else {
			return false;
		}
	}

	/*=============================================
	Mostrar Inscripciones por Usuario
	==============================================*/
	public static function mdlMostrarInscripcionesPorUsuario($idEstudiante, $estado = null)
	{
		$whereEstado = "";
		if ($estado !== null) {
			$whereEstado = "AND i.estado = :estado";
		}

		$stmt = Conexion::conectar()->prepare("SELECT i.*, c.nombre as nombre_curso, c.banner, c.valor, c.url_amiga, cat.nombre as categoria, p.nombre as instructor_nombre
											   FROM inscripciones i 
											   INNER JOIN curso c ON i.id_curso = c.id 
											   LEFT JOIN categoria cat ON c.id_categoria = cat.id 
											   LEFT JOIN persona p ON c.id_persona = p.id
											   WHERE i.id_estudiante = :id_estudiante $whereEstado
											   ORDER BY i.fecha_registro DESC");

		$stmt->bindParam(":id_estudiante", $idEstudiante, PDO::PARAM_INT);
		if ($estado !== null) {
			$stmt->bindParam(":estado", $estado, PDO::PARAM_STR);
		}
		$stmt->execute();

		return $stmt->fetchAll();
	}

	/*=============================================
	Mostrar Inscripciones por Curso (para profesores)
	==============================================*/
	public static function mdlMostrarInscripcionesPorCurso($idCurso, $estado = null)
	{
		$whereEstado = "";
		if ($estado !== null) {
			$whereEstado = "AND i.estado = :estado";
		}

		$stmt = Conexion::conectar()->prepare("SELECT i.*, p.nombre, p.email, p.foto, p.telefono 
											   FROM inscripciones i 
											   INNER JOIN persona p ON i.id_estudiante = p.id 
											   WHERE i.id_curso = :id_curso $whereEstado
											   ORDER BY i.fecha_registro DESC");

		$stmt->bindParam(":id_curso", $idCurso, PDO::PARAM_INT);
		if ($estado !== null) {
			$stmt->bindParam(":estado", $estado, PDO::PARAM_STR);
		}
		$stmt->execute();

		return $stmt->fetchAll();
	}

	/*=============================================
	Verificar si ya está inscrito
	==============================================*/
	public static function mdlVerificarInscripcion($idCurso, $idEstudiante)
	{
		$stmt = Conexion::conectar()->prepare("SELECT * FROM inscripciones WHERE id_curso = :id_curso AND id_estudiante = :id_estudiante");

		$stmt->bindParam(":id_curso", $idCurso, PDO::PARAM_INT);
		$stmt->bindParam(":id_estudiante", $idEstudiante, PDO::PARAM_INT);
		$stmt->execute();

		return $stmt->fetch();
	}

	/*=============================================
	Actualizar Estado de Inscripción
	==============================================*/
	public static function mdlActualizarEstadoInscripcion($idInscripcion, $estado)
	{
		$stmt = Conexion::conectar()->prepare("UPDATE inscripciones SET estado = :estado WHERE id = :id");

		$stmt->bindParam(":id", $idInscripcion, PDO::PARAM_INT);
		$stmt->bindParam(":estado", $estado, PDO::PARAM_STR);

		if ($stmt->execute()) {
			return "ok";
		} else {
			return "error";
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
		$stmt = Conexion::conectar()->prepare("DELETE FROM inscripciones WHERE id = :id");

		$stmt->bindParam(":id", $idInscripcion, PDO::PARAM_INT);

		if ($stmt->execute()) {
			return "ok";
		} else {
			return "error";
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
			$preinscripcion = $stmt->fetch();

			if (!$preinscripcion) {
				$conexion->rollBack();
				return false;
			}

			// Crear inscripción
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
			$conexion->rollBack();
			return false;
		}
	}

	/*=============================================
	Mostrar todas las preinscripciones (para admin)
	==============================================*/
	public static function mdlMostrarTodasPreinscripciones($estado = null)
	{
		$whereEstado = "";
		if ($estado !== null) {
			$whereEstado = "WHERE p.estado = :estado";
		}

		$stmt = Conexion::conectar()->prepare("SELECT p.*, c.nombre as nombre_curso, per.nombre as estudiante_nombre, per.email 
											   FROM preinscripciones p 
											   INNER JOIN curso c ON p.id_curso = c.id 
											   INNER JOIN persona per ON p.id_estudiante = per.id 
											   $whereEstado
											   ORDER BY p.fecha_preinscripcion DESC");

		if ($estado !== null) {
			$stmt->bindParam(":estado", $estado, PDO::PARAM_STR);
		}
		$stmt->execute();

		return $stmt->fetchAll();
	}

	/*=============================================
	Contar preinscripciones por curso
	==============================================*/
	public static function mdlContarPreinscripcionesPorCurso($idCurso)
	{
		$stmt = Conexion::conectar()->prepare("SELECT COUNT(*) as total FROM preinscripciones WHERE id_curso = :id_curso AND estado = 'preinscrito'");

		$stmt->bindParam(":id_curso", $idCurso, PDO::PARAM_INT);
		$stmt->execute();

		$resultado = $stmt->fetch();
		return $resultado['total'];
	}
}
