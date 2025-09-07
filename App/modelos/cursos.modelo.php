<?php
require_once "conexion.php";

class ModeloCursos
{
	/*=============================================
	Mostrar Cursos
==============================================*/
	/**
	 * Obtener cursos de la base de datos
	 * @param string $tabla Nombre de la tabla
	 * @param string|null $item Campo por el que se filtra
	 * @param mixed|null $valor Valor del campo para filtrar
	 * @return array|false Array con los cursos encontrados o false si hay error
	 */
	public static function mdlMostrarCursos($tabla, $item, $valor)
	{
		try {
			// Query con JOIN para obtener información relacionada
			$sql = "SELECT 
						c.*, 
						cat.nombre as categoria,
						p.nombre as profesor
					FROM $tabla c
					LEFT JOIN categoria cat ON c.id_categoria = cat.id
					LEFT JOIN persona p ON c.id_persona = p.id";

			if ($item != null && $valor != null) {
				$sql .= " WHERE c.$item = :$item";
				$stmt = Conexion::conectar()->prepare($sql);
				$stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
				$stmt->execute();

				// Para campos únicos (id, url_amiga), devolvemos solo un registro
				if ($item === 'id' || $item === 'url_amiga') {
					$resultado = $stmt->fetch(PDO::FETCH_ASSOC);
					return $resultado ? $resultado : false;
				} else {
					// Para otros criterios, devolvemos todos los resultados que coincidan
					return $stmt->fetchAll(PDO::FETCH_ASSOC);
				}
			} else {
				$stmt = Conexion::conectar()->prepare($sql);
				$stmt->execute();
				return $stmt->fetchAll(PDO::FETCH_ASSOC);
			}
		} catch (Exception $e) {
			return false;
		} finally {
			if (isset($stmt)) {
				$stmt = null;
			}
		}
	}

	public static function mdlCrearCurso($tabla, $datos)
	{
		$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla (url_amiga, nombre, descripcion, lo_que_aprenderas, requisitos, para_quien, banner, promo_video, valor, id_categoria, id_persona, estado) 
        VALUES (:url_amiga, :nombre, :descripcion, :lo_que_aprenderas, :requisitos, :para_quien, :banner, :promo_video, :valor, :id_categoria, :id_persona, :estado)");

		$stmt->bindParam(":url_amiga", $datos["url_amiga"], PDO::PARAM_STR);
		$stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
		$stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
		$stmt->bindParam(":lo_que_aprenderas", $datos["lo_que_aprenderas"], PDO::PARAM_STR);
		$stmt->bindParam(":requisitos", $datos["requisitos"], PDO::PARAM_STR);
		$stmt->bindParam(":para_quien", $datos["para_quien"], PDO::PARAM_STR);
		$stmt->bindParam(":banner", $datos["banner"], PDO::PARAM_STR);
		$stmt->bindParam(":promo_video", $datos["promo_video"], PDO::PARAM_STR);
		$stmt->bindParam(":valor", $datos["valor"], PDO::PARAM_INT);
		$stmt->bindParam(":id_categoria", $datos["id_categoria"], PDO::PARAM_INT);
		$stmt->bindParam(":id_persona", $datos["id_persona"], PDO::PARAM_INT);
		$stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);

		if ($stmt->execute()) {
			return "ok";
		} else {
			return "error";
		}
	}

	/*=============================================
	Actualizar Curso
	=============================================*/
	public static function mdlActualizarCurso($datos)
	{
		$stmt = Conexion::conectar()->prepare("UPDATE curso SET 
			nombre = :nombre,
			descripcion = :descripcion,
			lo_que_aprenderas = :lo_que_aprenderas,
			requisitos = :requisitos,
			para_quien = :para_quien,
			valor = :valor,
			id_categoria = :id_categoria,
			id_persona = :id_persona,
			estado = :estado
			WHERE id = :id");

		$stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);
		$stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
		$stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
		$stmt->bindParam(":lo_que_aprenderas", $datos["lo_que_aprenderas"], PDO::PARAM_STR);
		$stmt->bindParam(":requisitos", $datos["requisitos"], PDO::PARAM_STR);
		$stmt->bindParam(":para_quien", $datos["para_quien"], PDO::PARAM_STR);
		$stmt->bindParam(":valor", $datos["valor"], PDO::PARAM_INT);
		$stmt->bindParam(":id_categoria", $datos["id_categoria"], PDO::PARAM_INT);
		$stmt->bindParam(":id_persona", $datos["id_persona"], PDO::PARAM_INT);
		$stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);

		if ($stmt->execute()) {
			return "ok";
		} else {
			return "error";
		}
	}

	/*=============================================
	Métodos para gestión de secciones
	=============================================*/
	public static function mdlCrearSeccion($datos)
	{
		// Convertir objeto a array si es necesario
		if (is_object($datos)) {
			$datos = (array) $datos;
		}

		// Validar que tenemos los datos mínimos requeridos
		if (!isset($datos['idCurso']) || !isset($datos['titulo'])) {
			return [
				'success' => false,
				'mensaje' => 'Faltan datos requeridos: idCurso y titulo'
			];
		}

		// Validar que el orden sea >= 1
		if (!isset($datos['orden']) || $datos['orden'] < 1) {
			$datos['orden'] = 1;
		}

		$conn = Conexion::conectar();

		try {
			// Obtener el último orden para este curso
			$stmtLastOrden = $conn->prepare("SELECT COALESCE(MAX(orden), 0) AS ultimo FROM curso_secciones WHERE id_curso = :id_curso");
			$stmtLastOrden->bindParam(":id_curso", $datos["idCurso"], PDO::PARAM_INT);
			$stmtLastOrden->execute();

			$resultado = $stmtLastOrden->fetch(PDO::FETCH_ASSOC);
			$nuevoOrden = $resultado['ultimo'] + 1;

			// Insertar la nueva sección
			$stmt = $conn->prepare("INSERT INTO curso_secciones 
								(id_curso, titulo, descripcion, orden, estado) 
								VALUES (:id_curso, :titulo, :descripcion, :orden, :estado)");

			$stmt->bindParam(":id_curso", $datos["idCurso"], PDO::PARAM_INT);
			$stmt->bindParam(":titulo", $datos["titulo"], PDO::PARAM_STR);

			$descripcion = $datos["descripcion"] ?? '';
			$stmt->bindParam(":descripcion", $descripcion, PDO::PARAM_STR);
			$stmt->bindParam(":orden", $nuevoOrden, PDO::PARAM_INT);

			$estado = $datos["estado"] ?? "activo";
			$stmt->bindParam(":estado", $estado, PDO::PARAM_STR);

			if ($stmt->execute()) {
				return [
					'success' => true,
					'id' => $conn->lastInsertId(),
					'orden' => $nuevoOrden,
					'mensaje' => 'Sección creada exitosamente'
				];
			} else {
				return [
					'success' => false,
					'mensaje' => 'Error al ejecutar la consulta SQL'
				];
			}
		} catch (Exception $e) {
			return [
				'success' => false,
				'mensaje' => 'Error de base de datos: ' . $e->getMessage()
			];
		}
	}

	public static function mdlActualizarSeccion($datos)
	{
		// Convertir objeto a array si es necesario
		if (is_object($datos)) {
			$datos = (array) $datos;
		}

		// Validar que tenemos los datos mínimos requeridos
		if (!isset($datos['idCurso']) || !isset($datos['titulo'])) {
			return [
				'success' => false,
				'mensaje' => 'Faltan datos requeridos: idCurso y titulo'
			];
		} // Validar que el orden sea >= 1
		if (!isset($datos['orden']) || $datos['orden'] < 1) {
			$datos['orden'] = 1;
		}
		$stmt = Conexion::conectar()->prepare("UPDATE curso_secciones SET 
			titulo = :titulo,
			descripcion = :descripcion,
			orden = :orden,
			estado = :estado
			WHERE id = :id");

		$stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);
		$stmt->bindParam(":titulo", $datos["titulo"], PDO::PARAM_STR);
		$stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
		$stmt->bindParam(":orden", $datos["orden"], PDO::PARAM_INT);
		$stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);

		if ($stmt->execute()) {
			return "ok";
		} else {
			return "error";
		}
	}

	public static function mdlEliminarSeccion($id)
	{
		$stmt = Conexion::conectar()->prepare("DELETE FROM curso_secciones WHERE id = :id");
		$stmt->bindParam(":id", $id, PDO::PARAM_INT);

		if ($stmt->execute()) {
			return "ok";
		} else {
			return "error";
		}
	}

	/*=============================================
	Métodos para gestión de contenido de secciones 
	=============================================*/
	public static function mdlCrearContenido($datos)
	{
		// Validar que el orden sea >= 1
		if (!isset($datos['orden']) || $datos['orden'] < 1) {
			$datos['orden'] = 1;
		}

		// Validar tipo
		$tiposValidos = ['video', 'pdf'];
		if (!in_array($datos['tipo'], $tiposValidos)) {
			return false;
		}

		$conn = Conexion::conectar();
		$stmt = $conn->prepare("INSERT INTO seccion_contenido 
								(id_seccion, titulo, descripcion, duracion, orden, estado) 
								VALUES (:id_seccion, :titulo, :descripcion, :duracion, :orden, :estado)");

		$stmt->bindParam(":id_seccion", $datos["id_seccion"], PDO::PARAM_INT);
		$stmt->bindParam(":titulo", $datos["titulo"], PDO::PARAM_STR);
		$stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
		$stmt->bindParam(":duracion", $datos["duracion"], PDO::PARAM_STR);
		$stmt->bindParam(":orden", $datos["orden"], PDO::PARAM_INT);
		$stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);

		if ($stmt->execute()) {
			return $conn->lastInsertId();
		} else {
			return false;
		}
	}

	public static function mdlActualizarContenido($datos)
	{
		// Validar que el orden sea >= 1
		if (!isset($datos['orden']) || $datos['orden'] < 1) {
			$datos['orden'] = 1;
		}

		// Validar tipo
		$tiposValidos = ['video', 'pdf'];
		if (!in_array($datos['tipo'], $tiposValidos)) {
			return false;
		}

		$stmt = Conexion::conectar()->prepare("UPDATE seccion_contenido SET 
												titulo = :titulo, 
												descripcion = :descripcion,  
												duracion = :duracion, 
												orden = :orden, 
												estado = :estado 
												WHERE id = :id");

		$stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);
		$stmt->bindParam(":titulo", $datos["titulo"], PDO::PARAM_STR);
		$stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
		$stmt->bindParam(":duracion", $datos["duracion"], PDO::PARAM_STR);
		$stmt->bindParam(":orden", $datos["orden"], PDO::PARAM_INT);
		$stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);

		if ($stmt->execute()) {
			return "ok";
		} else {
			return "error";
		}
	}

	public static function mdlEliminarContenido($id)
	{
		$stmt = Conexion::conectar()->prepare("DELETE FROM seccion_contenido WHERE id = :id");
		$stmt->bindParam(":id", $id, PDO::PARAM_INT);

		if ($stmt->execute()) {
			return "ok";
		} else {
			return "error";
		}
	}

	/*=============================================
	Actualizar rutas de archivos después de organizar por carpetas
	=============================================*/
	public static function mdlActualizarRutasArchivos($datos)
	{
		$campos = [];
		$parametros = [':id' => $datos['id']];

		if (isset($datos['banner'])) {
			$campos[] = 'banner = :banner';
			$parametros[':banner'] = $datos['banner'];
		}

		if (isset($datos['promo_video'])) {
			$campos[] = 'promo_video = :promo_video';
			$parametros[':promo_video'] = $datos['promo_video'];
		}

		if (empty($campos)) {
			return "ok"; // No hay nada que actualizar
		}

		$sql = "UPDATE curso SET " . implode(', ', $campos) . " WHERE id = :id";
		$stmt = Conexion::conectar()->prepare($sql);

		foreach ($parametros as $key => $value) {
			$stmt->bindValue($key, $value);
		}

		if ($stmt->execute()) {
			return "ok";
		} else {
			return "error";
		}
	}

	/*=============================================
	Actualizar estado del curso
	=============================================*/
	public static function mdlActualizarEstadoCurso($idCurso, $nuevoEstado)
	{
		$stmt = Conexion::conectar()->prepare("UPDATE curso SET estado = :estado WHERE id = :id");

		$stmt->bindParam(":id", $idCurso, PDO::PARAM_INT);
		$stmt->bindParam(":estado", $nuevoEstado, PDO::PARAM_STR);

		if ($stmt->execute()) {
			return "ok";
		} else {
			return "error";
		}
	}

	/*=============================================
	Métodos para gestión de assets de contenido
	=============================================*/
	public static function mdlGuardarContenidoAsset($datos)
	{
		$stmt = Conexion::conectar()->prepare("INSERT INTO seccion_contenido_assets 
											(id_contenido, asset_tipo, storage_path, public_url, tamano_bytes, duracion_segundos) 
											VALUES (:id_contenido, :asset_tipo, :storage_path, :public_url, :tamano_bytes, :duracion_segundos)");

		$stmt->bindParam(":id_contenido", $datos["id_contenido"], PDO::PARAM_INT);
		$stmt->bindParam(":asset_tipo", $datos["asset_tipo"], PDO::PARAM_STR);
		$stmt->bindParam(":storage_path", $datos["storage_path"], PDO::PARAM_STR);
		$stmt->bindParam(":public_url", $datos["public_url"], PDO::PARAM_STR);
		$stmt->bindParam(":tamano_bytes", $datos["tamano_bytes"], PDO::PARAM_INT);
		$stmt->bindParam(":duracion_segundos", $datos["duracion_segundos"], PDO::PARAM_INT);

		if ($stmt->execute()) {
			return Conexion::conectar()->lastInsertId();
		} else {
			return false;
		}
	}
	public static function mdlActualizarContenidoAsset($datos)
	{
		$stmt = Conexion::conectar()->prepare("UPDATE seccion_contenido_assets SET
											asset_tipo = :asset_tipo,
											storage_path = :storage_path,
											public_url = :public_url,
											tamano_bytes = :tamano_bytes,
											duracion_segundos = :duracion_segundos
											WHERE id = :id");

		$stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);
		$stmt->bindParam(":asset_tipo", $datos["asset_tipo"], PDO::PARAM_STR);
		$stmt->bindParam(":storage_path", $datos["storage_path"], PDO::PARAM_STR);
		$stmt->bindParam(":public_url", $datos["public_url"], PDO::PARAM_STR);
		$stmt->bindParam(":tamano_bytes", $datos["tamano_bytes"], PDO::PARAM_INT);
		$stmt->bindParam(":duracion_segundos", $datos["duracion_segundos"], PDO::PARAM_INT);

		if ($stmt->execute()) {
			return "ok";
		} else {
			return false;
		}
	}

	/*=============================================
	Métodos para gestión de progreso de contenido
	=============================================*/
	public static function mdlUpsertProgreso($datos)
	{
		// Si porcentaje >= 90, forzar visto = 1 y porcentaje = 100
		if ($datos['porcentaje'] >= 90) {
			$datos['visto'] = 1;
			$datos['porcentaje'] = 100;
		}

		$conn = Conexion::conectar();

		// Verificar si ya existe el registro
		$stmt = $conn->prepare("SELECT id FROM seccion_contenido_progreso 
								WHERE id_contenido = :id_contenido AND id_estudiante = :id_estudiante");
		$stmt->bindParam(":id_contenido", $datos["id_contenido"], PDO::PARAM_INT);
		$stmt->bindParam(":id_estudiante", $datos["id_estudiante"], PDO::PARAM_INT);
		$stmt->execute();

		if ($stmt->rowCount() > 0) {
			// UPDATE
			$stmt = $conn->prepare("UPDATE seccion_contenido_progreso SET 
									visto = :visto, 
									progreso_segundos = :progreso_segundos, 
									porcentaje = :porcentaje
									WHERE id_contenido = :id_contenido AND id_estudiante = :id_estudiante");
		} else {
			// INSERT
			$stmt = $conn->prepare("INSERT INTO seccion_contenido_progreso 
									(id_contenido, id_estudiante, visto, progreso_segundos, porcentaje, primera_vista) 
									VALUES (:id_contenido, :id_estudiante, :visto, :progreso_segundos, :porcentaje, NOW())");
		}

		$stmt->bindParam(":id_contenido", $datos["id_contenido"], PDO::PARAM_INT);
		$stmt->bindParam(":id_estudiante", $datos["id_estudiante"], PDO::PARAM_INT);
		$stmt->bindParam(":visto", $datos["visto"], PDO::PARAM_INT);
		$stmt->bindParam(":progreso_segundos", $datos["progreso_segundos"], PDO::PARAM_INT);
		$stmt->bindParam(":porcentaje", $datos["porcentaje"], PDO::PARAM_INT);

		if ($stmt->execute()) {
			return "ok";
		} else {
			return "error";
		}
	}

	/*=============================================
	Validar nombre único de curso
	=============================================*/
	public static function mdlValidarNombreUnico($nombre, $idCursoExcluir = null)
	{
		$sql = "SELECT COUNT(*) as total FROM curso WHERE nombre = :nombre";
		if ($idCursoExcluir) {
			$sql .= " AND id != :id_excluir";
		}

		$stmt = Conexion::conectar()->prepare($sql);
		$stmt->bindParam(":nombre", $nombre, PDO::PARAM_STR);

		if ($idCursoExcluir) {
			$stmt->bindParam(":id_excluir", $idCursoExcluir, PDO::PARAM_INT);
		}

		$stmt->execute();
		$resultado = $stmt->fetch();

		return $resultado['total'] == 0;
	}

	/*=============================================
	Obtener secciones de un curso
	=============================================*/
	public static function mdlObtenerSecciones($idCurso)
	{
		try {
			$stmt = Conexion::conectar()->prepare("SELECT 
				id,
				titulo,
				descripcion,
				orden,
				estado,
				fecha_creacion,
				fecha_actualizacion
				FROM curso_secciones 
				WHERE id_curso = :id_curso
				ORDER BY orden ASC");

			$stmt->bindParam(":id_curso", $idCurso, PDO::PARAM_INT);
			$stmt->execute();
			$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

			return array(
				"success" => true,
				"mensaje" => "Secciones obtenidas correctamente",
				"secciones" => $resultados
			);
		} catch (Exception $e) {
			error_log("Error en mdlObtenerSecciones: " . $e->getMessage());
			return array(
				"success" => false,
				"mensaje" => "Error al obtener las secciones: " . $e->getMessage()
			);
		}
	}

	/*=============================================
	Obtener secciones con contenido de un curso
	=============================================*/
	public static function mdlObtenerSeccionesConContenido($idCurso)
	{
		$stmt = Conexion::conectar()->prepare("SELECT 
			cs.id as seccion_id,
			cs.titulo as seccion_titulo,
			cs.descripcion as seccion_descripcion,
			cs.orden as seccion_orden,
			cs.estado as seccion_estado,
			sc.id as contenido_id,
			sc.titulo as contenido_titulo,
			sc.descripcion as contenido_descripcion,
			sc.tipo as contenido_tipo,
			sc.duracion as contenido_duracion,
			sc.orden as contenido_orden,
			sc.estado as contenido_estado,
			sca.storage_path,
			sca.public_url,
			sca.tamano_bytes,
			sca.duracion_segundos
			FROM curso_secciones cs
			LEFT JOIN seccion_contenido sc ON cs.id = sc.id_seccion
			LEFT JOIN seccion_contenido_assets sca ON sc.id = sca.id_contenido
			WHERE cs.id_curso = :id_curso
			ORDER BY cs.orden ASC, sc.orden ASC");

		$stmt->bindParam(":id_curso", $idCurso, PDO::PARAM_INT);
		$stmt->execute();
		$resultados = $stmt->fetchAll();

		return $resultados;
	}

	/*=============================================
	Actualizar campo individual del curso
	=============================================*/
	public static function mdlActualizarCampoCurso($idCurso, $campo, $valor)
	{
		// Lista de campos permitidos para seguridad
		$camposPermitidos = ['nombre', 'descripcion', 'lo_que_aprenderas', 'requisitos', 'para_quien', 'valor', 'id_categoria', 'estado'];

		if (!in_array($campo, $camposPermitidos)) {
			return false;
		}

		$sql = "UPDATE curso SET $campo = :valor WHERE id = :id";
		$stmt = Conexion::conectar()->prepare($sql);

		$stmt->bindParam(":valor", $valor);
		$stmt->bindParam(":id", $idCurso, PDO::PARAM_INT);

		if ($stmt->execute()) {
			return "ok";
		} else {
			return "error";
		}
	}

	/*=============================================
	Actualizar video promocional del curso
	=============================================*/
	public static function mdlActualizarVideoPromocional($idCurso, $rutaVideo)
	{
		$stmt = Conexion::conectar()->prepare("UPDATE curso SET promo_video = :promo_video WHERE id = :id");

		$stmt->bindParam(":promo_video", $rutaVideo, PDO::PARAM_STR);
		$stmt->bindParam(":id", $idCurso, PDO::PARAM_INT);

		if ($stmt->execute()) {
			return "ok";
		} else {
			return "error";
		}
	}
}
