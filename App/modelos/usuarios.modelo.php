<?php

/**
@grcarvajal grcarvajal@gmail.com **Gildardo Restrepo Carvajal**
26/04/2022 aplicación PAWers citas de acompañamiento con mascotas
Modelo de usuarios login, registro y recuperar contraseña
 */
require_once "conexion.php";

class ModeloUsuarios

{

	/*=============================================
	Registro de usuarios
	=============================================*/
	public static function mdlRegistroUsuario($tabla, $datos)
	{
		$foto = "storage/public/usuarios/default.png"; // Nueva ruta por defecto
		$estado = 'activo';
		// Si las políticas están aceptadas, verificacion = 1, sino = 0
		$verificacion = isset($datos["politicas"]) ? 1 : 0;

		$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(`usuario_link`, `nombre`, `email`, `password`, `verificacion`, `foto`, `estado`) VALUES (:usuario_link, :nombre, :email, :password, :verificacion, :foto, :estado)");

		$stmt->bindParam(":usuario_link", $datos["usuario"], PDO::PARAM_STR);
		$stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
		$stmt->bindParam(":email", $datos["email"], PDO::PARAM_STR);
		$stmt->bindParam(":password", $datos["password"], PDO::PARAM_STR);
		$stmt->bindParam(":verificacion", $verificacion, PDO::PARAM_INT);
		$stmt->bindParam(":foto", $foto, PDO::PARAM_STR);
		$stmt->bindParam(":estado", $estado, PDO::PARAM_STR);

		if ($stmt->execute()) {
			return "ok";
		} else {
			return print_r(Conexion::conectar()->errorInfo());
		}
	}
	/*================================================================
	Registro de log de login ingreso del cliente a la aplicacion
=================================================================*/
	public static function mdlRegistroIngresoUsuarios($idU, $navU, $ipU)
	{
		$tabla = "log_ingreso";
		$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla (id_persona, navegador, ip_usuario) VALUES (:idU, :navU, :ipU)");
		$stmt->bindParam(":idU", $idU, PDO::PARAM_INT);
		$stmt->bindParam(":navU", $navU, PDO::PARAM_STR);
		$stmt->bindParam(":ipU", $ipU, PDO::PARAM_STR);

		if ($stmt->execute()) {
			$stmt = null;
			return "ok";
		} else {
			$stmt = null;
			return "error";
		}
	}


	/*=============================================
	Mostrar Usuarios
==============================================*/

	public static function mdlMostrarUsuarios($tabla, $item, $valor)
	{
		if ($item != null && $valor != null) {
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
	Actualizar usuario
	=============================================*/

	public static function mdlActualizarUsuario($tabla, $id, $item, $valor)
	{
		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET $item = :$item WHERE id = :id");

		$stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
		$stmt->bindParam(":id", $id, PDO::PARAM_INT);
		if ($stmt->execute()) {
			return "ok";
		} else {
			return print_r(Conexion::conectar()->errorInfo());
		}
	}

	/*=============================================
Actualizar usuario completar datos perfil
=============================================*/
	public static function mdlActualizarPerfilUsuario($tabla, $datos)
	{

		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET nombre = :nombre, email = :email, pais = :pais, ciudad = :ciudad, biografia = :biografia WHERE id = :idUsuario");

		$stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
		$stmt->bindParam(":email", $datos["email"], PDO::PARAM_STR);
		$stmt->bindParam(":pais", $datos["pais"], PDO::PARAM_STR);
		$stmt->bindParam(":ciudad", $datos["ciudad"], PDO::PARAM_STR);
		$stmt->bindParam(":biografia", $datos["biografia"], PDO::PARAM_STR);
		$stmt->bindParam(":idUsuario", $datos["id"], PDO::PARAM_INT);
		if ($stmt->execute()) {
			return "ok";
		} else {
			return print_r(Conexion::conectar()->errorInfo());
		}
	}
	public static function mdlActualizarRol($idPersona, $nuevoRol)
	{
		$conexion = Conexion::conectar();

		// 1. Obtener el ID del rol por nombre
		$stmtRol = $conexion->prepare("SELECT id FROM roles WHERE nombre = :nombre");
		$stmtRol->bindParam(":nombre", $nuevoRol, PDO::PARAM_STR);
		$stmtRol->execute();
		$rol = $stmtRol->fetch(PDO::FETCH_ASSOC);

		if (!$rol) {
			return false; // El rol no existe
		}

		$idRol = $rol["id"];

		// 2. Eliminar roles anteriores (si solo se permite un rol por persona)
		$stmtDelete = $conexion->prepare("DELETE FROM persona_roles WHERE id_persona = :idPersona");
		$stmtDelete->bindParam(":idPersona", $idPersona, PDO::PARAM_INT);
		$stmtDelete->execute();

		// 3. Insertar nuevo rol
		$stmtInsert = $conexion->prepare("INSERT INTO persona_roles (id_persona, id_rol) VALUES (:idPersona, :idRol)");
		$stmtInsert->bindParam(":idPersona", $idPersona, PDO::PARAM_INT);
		$stmtInsert->bindParam(":idRol", $idRol, PDO::PARAM_INT);

		return $stmtInsert->execute();
	}

	/*=============================================
	Obtener roles por usuario
	=============================================*/
	public static function mdlObtenerRolesPorUsuario($idPersona)
	{
		$conexion = Conexion::conectar();

		$stmt = $conexion->prepare(
			"SELECT r.id, r.nombre 
         FROM roles r 
         INNER JOIN persona_roles pr ON r.id = pr.id_rol 
         WHERE pr.id_persona = :idPersona"
		);

		$stmt->bindParam(":idPersona", $idPersona, PDO::PARAM_INT);
		$stmt->execute();

		return $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve un array de roles
	}

	/*=============================================
	Obtener todos los roles disponibles
	=============================================*/
	public static function mdlObtenerRoles()
	{
		$conexion = Conexion::conectar();
		$stmt = $conexion->prepare("SELECT id, nombre FROM roles ORDER BY nombre ASC");
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	/*=============================================
	Actualizar roles de usuario
	=============================================*/
	public static function mdlActualizarRolesUsuario($idPersona, $rolesSeleccionados)
	{
		$conexion = Conexion::conectar();

		try {
			$conexion->beginTransaction();

			// 1. Eliminar roles actuales
			$stmtDelete = $conexion->prepare("DELETE FROM persona_roles WHERE id_persona = :idPersona");
			$stmtDelete->bindParam(":idPersona", $idPersona, PDO::PARAM_INT);
			$stmtDelete->execute();

			// 2. Insertar nuevos roles seleccionados
			if (!empty($rolesSeleccionados)) {
				$stmtInsert = $conexion->prepare("INSERT INTO persona_roles (id_persona, id_rol) VALUES (:idPersona, :idRol)");

				foreach ($rolesSeleccionados as $idRol) {
					$stmtInsert->bindParam(":idPersona", $idPersona, PDO::PARAM_INT);
					$stmtInsert->bindParam(":idRol", $idRol, PDO::PARAM_INT);
					$stmtInsert->execute();
				}
			}

			$conexion->commit();
			return "ok";
		} catch (Exception $e) {
			$conexion->rollBack();
			return "error: " . $e->getMessage();
		}
	}

	/*=============================================
	Obtener estudiantes con inscripciones pendientes en cursos del profesor
	=============================================*/
	public static function mdlObtenerEstudiantesInscripcionesPendientes($idProfesor)
	{
		$conexion = Conexion::conectar();

		$stmt = $conexion->prepare("
			SELECT DISTINCT 
				p.id,
				p.nombre,
				p.email,
				p.foto,
				p.fecha_registro,
				COUNT(DISTINCT i.id) as inscripciones_pendientes,
				COUNT(DISTINCT i2.id) as inscripciones_activas
			FROM persona p
			INNER JOIN persona_roles pr ON p.id = pr.id_persona
			INNER JOIN roles r ON pr.id_rol = r.id
			INNER JOIN inscripciones i ON p.id = i.id_estudiante
			INNER JOIN curso c ON i.id_curso = c.id
			LEFT JOIN inscripciones i2 ON p.id = i2.id_estudiante AND i2.estado = 'activo'
			LEFT JOIN curso c2 ON i2.id_curso = c2.id AND c2.id_persona = :idProfesor
			WHERE r.nombre = 'estudiante'
			AND c.id_persona = :idProfesor
			AND i.estado = 'pendiente'
			AND p.estado = 'activo'
			GROUP BY p.id, p.nombre, p.email, p.foto, p.fecha_registro
			ORDER BY p.nombre ASC
		");

		$stmt->bindParam(":idProfesor", $idProfesor, PDO::PARAM_INT);
		$stmt->execute();

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	/*=============================================
	Obtener cursos con inscripciones pendientes de un estudiante para un profesor
	=============================================*/
	public static function mdlObtenerCursosPendientesEstudiante($idEstudiante, $idProfesor)
	{
		$conexion = Conexion::conectar();

		$stmt = $conexion->prepare("
			SELECT 
				i.id as inscripcion_id,
				i.estado,
				i.fecha_registro as fecha_inscripcion,
				c.id as curso_id,
				c.nombre as curso_nombre,
				c.banner as curso_banner,
				c.valor as curso_valor,
				cat.nombre as categoria_nombre
			FROM inscripciones i
			INNER JOIN curso c ON i.id_curso = c.id
			LEFT JOIN categoria cat ON c.id_categoria = cat.id
			WHERE i.id_estudiante = :idEstudiante
			AND c.id_persona = :idProfesor
			AND i.estado = 'pendiente'
			ORDER BY i.fecha_registro DESC
		");

		$stmt->bindParam(":idEstudiante", $idEstudiante, PDO::PARAM_INT);
		$stmt->bindParam(":idProfesor", $idProfesor, PDO::PARAM_INT);
		$stmt->execute();

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	/*=============================================
	Obtener cursos activos de un estudiante para un profesor
	=============================================*/
	public static function mdlObtenerCursosActivosEstudiante($idEstudiante, $idProfesor)
	{
		$conexion = Conexion::conectar();

		$stmt = $conexion->prepare("
			SELECT 
				i.id as inscripcion_id,
				i.estado,
				i.fecha_registro as fecha_inscripcion,
				c.id as curso_id,
				c.nombre as curso_nombre,
				c.banner as curso_banner,
				c.valor as curso_valor,
				cat.nombre as categoria_nombre
			FROM inscripciones i
			INNER JOIN curso c ON i.id_curso = c.id
			LEFT JOIN categoria cat ON c.id_categoria = cat.id
			WHERE i.id_estudiante = :idEstudiante
			AND c.id_persona = :idProfesor
			AND i.estado = 'activo'
			ORDER BY i.fecha_registro DESC
		");

		$stmt->bindParam(":idEstudiante", $idEstudiante, PDO::PARAM_INT);
		$stmt->bindParam(":idProfesor", $idProfesor, PDO::PARAM_INT);
		$stmt->execute();

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	/*=============================================
	Actualizar contraseña de usuario
	=============================================*/
	public static function mdlActualizarPassword($datos)
	{
		$stmt = Conexion::conectar()->prepare("UPDATE persona SET password = :password WHERE id = :id");

		$stmt->bindParam(":password", $datos["password"], PDO::PARAM_STR);
		$stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);

		if ($stmt->execute()) {
			return "ok";
		} else {
			return "error";
		}
	}

	/*=============================================
	Obtener estudiantes con preinscripciones e inscripciones por profesor
	Devuelve un registro por cada curso al que el estudiante esté preinscrito o inscrito
	=============================================*/
	public static function mdlObtenerEstudiantesConCursosProfesor($idProfesor)
	{
		$conexion = Conexion::conectar();

		$stmt = $conexion->prepare("
			(SELECT 
				p.id as estudiante_id,
				p.nombre as estudiante_nombre,
				p.email as estudiante_email,
				p.foto as estudiante_foto,
				c.id as curso_id,
				c.nombre as curso_nombre,
				cat.nombre as categoria_nombre,
				'preinscrito' as tipo,
				pr.estado as estado,
				pr.fecha_preinscripcion as fecha_registro,
				pr.id as registro_id
			FROM persona p
			INNER JOIN persona_roles prol ON p.id = prol.id_persona
			INNER JOIN roles r ON prol.id_rol = r.id
			INNER JOIN preinscripciones pr ON p.id = pr.id_estudiante
			INNER JOIN curso c ON pr.id_curso = c.id
			LEFT JOIN categoria cat ON c.id_categoria = cat.id
			WHERE r.nombre = 'estudiante'
			AND c.id_persona = :idProfesor1
			AND pr.estado = 'preinscrito'
			AND p.estado = 'activo')
			
			UNION ALL
			
			(SELECT 
				p.id as estudiante_id,
				p.nombre as estudiante_nombre,
				p.email as estudiante_email,
				p.foto as estudiante_foto,
				c.id as curso_id,
				c.nombre as curso_nombre,
				cat.nombre as categoria_nombre,
				'inscrito' as tipo,
				i.estado as estado,
				i.fecha_registro as fecha_registro,
				i.id as registro_id
			FROM persona p
			INNER JOIN persona_roles prol ON p.id = prol.id_persona
			INNER JOIN roles r ON prol.id_rol = r.id
			INNER JOIN inscripciones i ON p.id = i.id_estudiante
			INNER JOIN curso c ON i.id_curso = c.id
			LEFT JOIN categoria cat ON c.id_categoria = cat.id
			WHERE r.nombre = 'estudiante'
			AND c.id_persona = :idProfesor2
			AND i.estado IN ('pendiente', 'activo')
			AND p.estado = 'activo')
			
			ORDER BY fecha_registro DESC
		");

		$stmt->bindParam(":idProfesor1", $idProfesor, PDO::PARAM_INT);
		$stmt->bindParam(":idProfesor2", $idProfesor, PDO::PARAM_INT);
		$stmt->execute();

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
}
