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
		$conn = Conexion::conectar();

		try {
			// Iniciar transacción
			$conn->beginTransaction();

			// Primero eliminar todos los assets relacionados
			$stmt1 = $conn->prepare("
				DELETE sca FROM seccion_contenido_assets sca
				INNER JOIN seccion_contenido sc ON sca.id_contenido = sc.id
				WHERE sc.id_seccion = :id_seccion
			");
			$stmt1->bindParam(":id_seccion", $id, PDO::PARAM_INT);
			$stmt1->execute();

			// Luego eliminar todo el contenido de la sección
			$stmt2 = $conn->prepare("DELETE FROM seccion_contenido WHERE id_seccion = :id_seccion");
			$stmt2->bindParam(":id_seccion", $id, PDO::PARAM_INT);
			$stmt2->execute();

			// Finalmente eliminar la sección
			$stmt3 = $conn->prepare("DELETE FROM curso_secciones WHERE id = :id");
			$stmt3->bindParam(":id", $id, PDO::PARAM_INT);
			$stmt3->execute();

			// Confirmar transacción
			$conn->commit();

			return "ok";
		} catch (Exception $e) {
			// Rollback en caso de error
			$conn->rollback();
			return "error";
		}
	}

	/*=============================================
	Métodos para gestión de contenido de secciones 
	=============================================*/
	public static function mdlCrearContenido($datos)
	{
		// Convertir objeto a array si es necesario
		if (is_object($datos)) {
			$datos = (array) $datos;
		}

		// Validaciones
		if (!isset($datos['idSeccion']) || !isset($datos['titulo'])) {
			return [
				'success' => false,
				'mensaje' => 'Faltan datos requeridos: id_seccion y titulo'
			];
		}

		// Validar que el orden sea >= 1, si no existe obtener el siguiente
		if (!isset($datos['orden']) || $datos['orden'] < 1) {
			$conn = Conexion::conectar();
			$stmtOrden = $conn->prepare("SELECT COALESCE(MAX(orden), 0) + 1 AS siguiente_orden 
										FROM seccion_contenido WHERE id_seccion = :id_seccion");
			$stmtOrden->bindParam(":id_seccion", $datos["id_seccion"], PDO::PARAM_INT);
			$stmtOrden->execute();
			$resultado = $stmtOrden->fetch(PDO::FETCH_ASSOC);
			$datos['orden'] = $resultado['siguiente_orden'];
		}

		// Establecer valores por defecto
		$duracion = $datos["duracion"] ?? '00:00:00';
		$estado = $datos["estado"] ?? 'activo';

		try {
			$conn = Conexion::conectar();
			$stmt = $conn->prepare("INSERT INTO seccion_contenido 
									(id_seccion, titulo, duracion, orden, estado) 
									VALUES (:id_seccion, :titulo, :duracion, :orden, :estado)");

			$stmt->bindParam(":id_seccion", $datos["idSeccion"], PDO::PARAM_INT);
			$stmt->bindParam(":titulo", $datos["titulo"], PDO::PARAM_STR);
			$stmt->bindParam(":duracion", $duracion, PDO::PARAM_STR);
			$stmt->bindParam(":orden", $datos["orden"], PDO::PARAM_INT);
			$stmt->bindParam(":estado", $estado, PDO::PARAM_STR);

			if ($stmt->execute()) {
				return [
					'success' => true,
					'id' => $conn->lastInsertId(),
					'orden' => $datos["orden"],
					'mensaje' => 'Contenido creado exitosamente'
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

	public static function mdlActualizarContenido($datos)
	{
		// Convertir objeto a array si es necesario
		if (is_object($datos)) {
			$datos = (array) $datos;
		}

		// Validaciones
		if (!isset($datos['id']) || !isset($datos['titulo'])) {
			return [
				'success' => false,
				'mensaje' => 'Faltan datos requeridos: id y titulo'
			];
		}

		// Validar que el orden sea >= 1
		if (!isset($datos['orden']) || $datos['orden'] < 1) {
			$datos['orden'] = 1;
		}

		// Calcular duración automáticamente basada en los assets
		$resultadoDuracion = self::mdlCalcularDuracionTotalContenido($datos['id']);
		if ($resultadoDuracion['success']) {
			$duracion = $resultadoDuracion['duracion_formateada'];
		} else {
			// Si hay error al calcular, usar valor por defecto
			$duracion = $datos["duracion"] ?? '00:00:00';
		}

		// Establecer valores por defecto
		$estado = $datos["estado"] ?? 'activo';

		try {
			$stmt = Conexion::conectar()->prepare("UPDATE seccion_contenido SET 
													titulo = :titulo, 
													duracion = :duracion, 
													orden = :orden, 
													estado = :estado 
													WHERE id = :id");

			$stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);
			$stmt->bindParam(":titulo", $datos["titulo"], PDO::PARAM_STR);
			$stmt->bindParam(":duracion", $duracion, PDO::PARAM_STR);
			$stmt->bindParam(":orden", $datos["orden"], PDO::PARAM_INT);
			$stmt->bindParam(":estado", $estado, PDO::PARAM_STR);

			if ($stmt->execute()) {
				return [
					'success' => true,
					'duracion_calculada' => $duracion,
					'mensaje' => 'Contenido actualizado exitosamente'
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

	public static function mdlEliminarContenido($id)
	{
		try {
			$conn = Conexion::conectar();

			// Primero eliminar los assets asociados
			$stmtAssets = $conn->prepare("DELETE FROM seccion_contenido_assets WHERE id_contenido = :id");
			$stmtAssets->bindParam(":id", $id, PDO::PARAM_INT);
			$stmtAssets->execute();

			// Luego eliminar el progreso asociado
			$stmtProgreso = $conn->prepare("DELETE FROM seccion_contenido_progreso WHERE id_contenido = :id");
			$stmtProgreso->bindParam(":id", $id, PDO::PARAM_INT);
			$stmtProgreso->execute();

			// Finalmente eliminar el contenido
			$stmt = $conn->prepare("DELETE FROM seccion_contenido WHERE id = :id");
			$stmt->bindParam(":id", $id, PDO::PARAM_INT);

			if ($stmt->execute()) {
				return [
					'success' => true,
					'mensaje' => 'Contenido eliminado exitosamente'
				];
			} else {
				return [
					'success' => false,
					'mensaje' => 'Error al eliminar el contenido'
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
		// Convertir objeto a array si es necesario
		if (is_object($datos)) {
			$datos = (array) $datos;
		}

		// Validaciones
		if (!isset($datos['id_contenido']) || !isset($datos['asset_tipo']) || !isset($datos['storage_path'])) {
			return [
				'success' => false,
				'mensaje' => 'Faltan datos requeridos: id_contenido, asset_tipo y storage_path'
			];
		}

		// Validar tipos de asset permitidos
		$tiposPermitidos = ['video', 'pdf', 'attachment'];
		if (!in_array($datos['asset_tipo'], $tiposPermitidos)) {
			return [
				'success' => false,
				'mensaje' => 'Tipo de asset no válido. Permitidos: ' . implode(', ', $tiposPermitidos)
			];
		}

		// Establecer valores por defecto
		$publicUrl = $datos["public_url"] ?? null;
		$tamanoBytes = $datos["tamano_bytes"] ?? null;
		$duracionSegundos = $datos["duracion_segundos"] ?? null;

		try {
			$conn = Conexion::conectar();
			$stmt = $conn->prepare("INSERT INTO seccion_contenido_assets 
												(id_contenido, asset_tipo, storage_path, public_url, tamano_bytes, duracion_segundos) 
												VALUES (:id_contenido, :asset_tipo, :storage_path, :public_url, :tamano_bytes, :duracion_segundos)");

			$stmt->bindParam(":id_contenido", $datos["id_contenido"], PDO::PARAM_INT);
			$stmt->bindParam(":asset_tipo", $datos["asset_tipo"], PDO::PARAM_STR);
			$stmt->bindParam(":storage_path", $datos["storage_path"], PDO::PARAM_STR);
			$stmt->bindParam(":public_url", $publicUrl, PDO::PARAM_STR);
			$stmt->bindParam(":tamano_bytes", $tamanoBytes, PDO::PARAM_INT);
			$stmt->bindParam(":duracion_segundos", $duracionSegundos, PDO::PARAM_INT);

			if ($stmt->execute()) {
				$assetId = $conn->lastInsertId();

				// Actualizar automáticamente la duración del contenido
				$actualizacionDuracion = self::mdlActualizarDuracionContenido($datos["id_contenido"]);

				return [
					'success' => true,
					'id' => $assetId,
					'duracion_contenido_actualizada' => $actualizacionDuracion['success'] ? $actualizacionDuracion['duracion_actualizada'] : null,
					'mensaje' => 'Asset guardado exitosamente'
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
	public static function mdlActualizarContenidoAsset($datos)
	{
		// Convertir objeto a array si es necesario
		if (is_object($datos)) {
			$datos = (array) $datos;
		}

		// Validaciones
		if (!isset($datos['id']) || !isset($datos['id_contenido'])) {
			return [
				'success' => false,
				'mensaje' => 'Faltan datos requeridos: id del asset e id_contenido'
			];
		}

		// Validar tipos de asset permitidos si se está actualizando
		if (isset($datos['asset_tipo'])) {
			$tiposPermitidos = ['video', 'pdf', 'attachment'];
			if (!in_array($datos['asset_tipo'], $tiposPermitidos)) {
				return [
					'success' => false,
					'mensaje' => 'Tipo de asset no válido. Permitidos: ' . implode(', ', $tiposPermitidos)
				];
			}
		}

		try {
			$conn = Conexion::conectar();

			// Construir consulta dinámica
			$campos = [];
			$parametros = [':id' => $datos['id']];

			if (isset($datos['asset_tipo'])) {
				$campos[] = "asset_tipo = :asset_tipo";
				$parametros[':asset_tipo'] = $datos['asset_tipo'];
			}
			if (isset($datos['storage_path'])) {
				$campos[] = "storage_path = :storage_path";
				$parametros[':storage_path'] = $datos['storage_path'];
			}
			if (isset($datos['public_url'])) {
				$campos[] = "public_url = :public_url";
				$parametros[':public_url'] = $datos['public_url'];
			}
			if (isset($datos['tamano_bytes'])) {
				$campos[] = "tamano_bytes = :tamano_bytes";
				$parametros[':tamano_bytes'] = $datos['tamano_bytes'];
			}
			if (isset($datos['duracion_segundos'])) {
				$campos[] = "duracion_segundos = :duracion_segundos";
				$parametros[':duracion_segundos'] = $datos['duracion_segundos'];
			}

			if (empty($campos)) {
				return [
					'success' => false,
					'mensaje' => 'No hay campos para actualizar'
				];
			}

			$sql = "UPDATE seccion_contenido_assets SET " . implode(', ', $campos) . " WHERE id = :id";
			$stmt = $conn->prepare($sql);

			if ($stmt->execute($parametros)) {
				// Actualizar automáticamente la duración del contenido
				$actualizacionDuracion = self::mdlActualizarDuracionContenido($datos["id_contenido"]);

				return [
					'success' => true,
					'duracion_contenido_actualizada' => $actualizacionDuracion['success'] ? $actualizacionDuracion['duracion_actualizada'] : null,
					'mensaje' => 'Asset actualizado exitosamente'
				];
			} else {
				return [
					'success' => false,
					'mensaje' => 'Error al actualizar el asset'
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
	Eliminar asset de contenido
	=============================================*/
	public static function mdlEliminarContenidoAsset($id, $idContenido = null)
	{
		try {
			$conn = Conexion::conectar();

			// Si no se proporciona el id_contenido, lo obtenemos primero
			if ($idContenido === null) {
				$stmt = $conn->prepare("SELECT id_contenido FROM seccion_contenido_assets WHERE id = :id");
				$stmt->bindParam(":id", $id, PDO::PARAM_INT);
				$stmt->execute();
				$asset = $stmt->fetch(PDO::FETCH_ASSOC);

				if (!$asset) {
					return [
						'success' => false,
						'mensaje' => 'Asset no encontrado'
					];
				}

				$idContenido = $asset['id_contenido'];
			}

			$stmt = $conn->prepare("DELETE FROM seccion_contenido_assets WHERE id = :id");
			$stmt->bindParam(":id", $id, PDO::PARAM_INT);

			if ($stmt->execute()) {
				// Actualizar automáticamente la duración del contenido
				$actualizacionDuracion = self::mdlActualizarDuracionContenido($idContenido);

				return [
					'success' => true,
					'duracion_contenido_actualizada' => $actualizacionDuracion['success'] ? $actualizacionDuracion['duracion_actualizada'] : null,
					'mensaje' => 'Asset eliminado exitosamente'
				];
			} else {
				return [
					'success' => false,
					'mensaje' => 'Error al eliminar el asset'
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
	Obtener assets de un contenido
	=============================================*/
	public static function mdlObtenerAssetsContenido($idContenido)
	{
		try {
			$stmt = Conexion::conectar()->prepare("
				SELECT 
					sca.*,
					sc.id as contenido_id,
					sc.id_seccion as contenido_id_seccion,
					sc.titulo as contenido_titulo,
					sc.duracion as contenido_duracion,
					sc.orden as contenido_orden,
					sc.estado as contenido_estado,
					sc.fecha_creacion as contenido_fecha_creacion,
					sc.fecha_actualizacion as contenido_fecha_actualizacion
				FROM seccion_contenido_assets sca
				INNER JOIN seccion_contenido sc ON sca.id_contenido = sc.id
				WHERE sca.id_contenido = :id_contenido 
				ORDER BY sca.created_at ASC
			");
			$stmt->bindParam(":id_contenido", $idContenido, PDO::PARAM_INT);
			$stmt->execute();
			$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

			// Extraer información del contenido del primer resultado
			$contenido = null;
			if (!empty($resultados)) {
				$contenido = [
					'id' => $resultados[0]['contenido_id'],
					'id_seccion' => $resultados[0]['contenido_id_seccion'],
					'titulo' => $resultados[0]['contenido_titulo'],
					'duracion' => $resultados[0]['contenido_duracion'],
					'orden' => $resultados[0]['contenido_orden'],
					'estado' => $resultados[0]['contenido_estado'],
					'fecha_creacion' => $resultados[0]['contenido_fecha_creacion'],
					'fecha_actualizacion' => $resultados[0]['contenido_fecha_actualizacion']
				];

				// Limpiar los resultados de assets para no duplicar información
				foreach ($resultados as &$asset) {
					unset($asset['contenido_id']);
					unset($asset['contenido_id_seccion']);
					unset($asset['contenido_titulo']);
					unset($asset['contenido_duracion']);
					unset($asset['contenido_orden']);
					unset($asset['contenido_estado']);
					unset($asset['contenido_fecha_creacion']);
					unset($asset['contenido_fecha_actualizacion']);
				}
			}

			return [
				'success' => true,
				'assets' => $resultados,
				'contenido' => $contenido,
				'mensaje' => 'Assets obtenidos correctamente'
			];
		} catch (Exception $e) {
			return [
				'success' => false,
				'mensaje' => 'Error de base de datos: ' . $e->getMessage()
			];
		}
	}

	/*=============================================
	Obtener un asset específico por ID
	=============================================*/
	public static function mdlObtenerAssetPorId($id)
	{
		try {
			$stmt = Conexion::conectar()->prepare("
				SELECT 
					id, 
					storage_path, 
					asset_tipo, 
					public_url, 
					id_contenido,
					tamano_bytes,
					duracion_segundos,
					created_at
				FROM seccion_contenido_assets
				WHERE id = :id
			");
			$stmt->bindParam(":id", $id, PDO::PARAM_INT);
			$stmt->execute();

			return $stmt->fetch(PDO::FETCH_ASSOC);
		} catch (Exception $e) {
			return null;
		}
	}

	/*=============================================
	Obtener todos los assets de una sección
	=============================================*/
	public static function mdlObtenerAssetsSeccion($idSeccion)
	{
		try {
			$stmt = Conexion::conectar()->prepare("
				SELECT sca.id, sca.storage_path, sca.asset_tipo, sca.public_url
				FROM seccion_contenido_assets sca
				INNER JOIN seccion_contenido sc ON sca.id_contenido = sc.id
				WHERE sc.id_seccion = :id_seccion
			");
			$stmt->bindParam(":id_seccion", $idSeccion, PDO::PARAM_INT);
			$stmt->execute();

			$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return $resultados;
		} catch (Exception $e) {
			return [];
		}
	}

	/*=============================================
	Calcular duración total de assets de un contenido
	=============================================*/
	public static function mdlCalcularDuracionTotalContenido($idContenido)
	{
		try {
			$stmt = Conexion::conectar()->prepare("SELECT SUM(duracion_segundos) as duracion_total 
												FROM seccion_contenido_assets 
												WHERE id_contenido = :id_contenido");
			$stmt->bindParam(":id_contenido", $idContenido, PDO::PARAM_INT);
			$stmt->execute();
			$resultado = $stmt->fetch(PDO::FETCH_ASSOC);

			$duracionSegundos = $resultado['duracion_total'] ?? 0;

			// Convertir segundos a formato HH:MM:SS
			$horas = floor($duracionSegundos / 3600);
			$minutos = floor(($duracionSegundos % 3600) / 60);
			$segundos = $duracionSegundos % 60;

			$duracionFormateada = sprintf('%02d:%02d:%02d', $horas, $minutos, $segundos);

			return [
				'success' => true,
				'duracion_segundos' => $duracionSegundos,
				'duracion_formateada' => $duracionFormateada,
				'mensaje' => 'Duración calculada correctamente'
			];
		} catch (Exception $e) {
			return [
				'success' => false,
				'mensaje' => 'Error de base de datos: ' . $e->getMessage()
			];
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
	Actualizar solo la duración de un contenido
	=============================================*/
	public static function mdlActualizarDuracionContenido($idContenido, $duracion = null)
	{
		try {
			// Si no se proporciona duración, calcularla automáticamente
			if ($duracion === null) {
				$resultadoDuracion = self::mdlCalcularDuracionTotalContenido($idContenido);
				if ($resultadoDuracion['success']) {
					$duracion = $resultadoDuracion['duracion_formateada'];
				} else {
					$duracion = '00:00:00';
				}
			}

			$stmt = Conexion::conectar()->prepare("UPDATE seccion_contenido SET 
													duracion = :duracion 
													WHERE id = :id");

			$stmt->bindParam(":id", $idContenido, PDO::PARAM_INT);
			$stmt->bindParam(":duracion", $duracion, PDO::PARAM_STR);

			if ($stmt->execute()) {
				return [
					'success' => true,
					'duracion_actualizada' => $duracion,
					'mensaje' => 'Duración actualizada exitosamente'
				];
			} else {
				return [
					'success' => false,
					'mensaje' => 'Error al actualizar la duración'
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
	Actualizar duraciones de todos los contenidos de una sección
	=============================================*/
	public static function mdlActualizarDuracionesSeccion($idSeccion)
	{
		try {
			$conn = Conexion::conectar();

			// Obtener todos los contenidos de la sección
			$stmt = $conn->prepare("SELECT id FROM seccion_contenido WHERE id_seccion = :id_seccion");
			$stmt->bindParam(":id_seccion", $idSeccion, PDO::PARAM_INT);
			$stmt->execute();
			$contenidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

			$actualizaciones = [];
			$errores = [];

			foreach ($contenidos as $contenido) {
				$resultado = self::mdlActualizarDuracionContenido($contenido['id']);
				if ($resultado['success']) {
					$actualizaciones[] = [
						'id_contenido' => $contenido['id'],
						'duracion' => $resultado['duracion_actualizada']
					];
				} else {
					$errores[] = [
						'id_contenido' => $contenido['id'],
						'error' => $resultado['mensaje']
					];
				}
			}

			return [
				'success' => true,
				'actualizaciones' => $actualizaciones,
				'errores' => $errores,
				'total_contenidos' => count($contenidos),
				'total_actualizados' => count($actualizaciones),
				'mensaje' => 'Proceso de actualización completado'
			];
		} catch (Exception $e) {
			return [
				'success' => false,
				'mensaje' => 'Error de base de datos: ' . $e->getMessage()
			];
		}
	}

	/*=============================================
	Actualizar duraciones de todos los contenidos de un curso
	=============================================*/
	public static function mdlActualizarDuracionesCurso($idCurso)
	{
		try {
			$conn = Conexion::conectar();

			// Obtener todas las secciones del curso
			$stmt = $conn->prepare("SELECT id FROM curso_secciones WHERE id_curso = :id_curso");
			$stmt->bindParam(":id_curso", $idCurso, PDO::PARAM_INT);
			$stmt->execute();
			$secciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

			$actualizacionesCurso = [];
			$erroresCurso = [];
			$totalContenidos = 0;
			$totalActualizados = 0;

			foreach ($secciones as $seccion) {
				$resultado = self::mdlActualizarDuracionesSeccion($seccion['id']);
				$actualizacionesCurso[$seccion['id']] = $resultado;
				$totalContenidos += $resultado['total_contenidos'];
				$totalActualizados += $resultado['total_actualizados'];

				if (!empty($resultado['errores'])) {
					$erroresCurso = array_merge($erroresCurso, $resultado['errores']);
				}
			}

			return [
				'success' => true,
				'secciones_procesadas' => count($secciones),
				'total_contenidos' => $totalContenidos,
				'total_actualizados' => $totalActualizados,
				'actualizaciones_por_seccion' => $actualizacionesCurso,
				'errores' => $erroresCurso,
				'mensaje' => 'Proceso de actualización del curso completado'
			];
		} catch (Exception $e) {
			return [
				'success' => false,
				'mensaje' => 'Error de base de datos: ' . $e->getMessage()
			];
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
			sc.duracion as contenido_duracion,
			sc.orden as contenido_orden,
			sc.estado as contenido_estado,
			sca.asset_tipo,
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
	Obtener contenido de una sección con sus assets
	=============================================*/
	public static function mdlObtenerContenidoSeccionConAssets($idSeccion)
	{
		$stmt = Conexion::conectar()->prepare("
			SELECT 
				sc.id,
				sc.titulo,
				sc.duracion,
				sc.orden,
				sc.estado,
				sc.id_seccion,
				sca.id as asset_id,
				sca.asset_tipo,
				sca.storage_path,
				sca.public_url,
				sca.tamano_bytes,
				sca.duracion_segundos,
				sca.created_at as fecha_subida
			FROM seccion_contenido sc
			LEFT JOIN seccion_contenido_assets sca ON sc.id = sca.id_contenido
			WHERE sc.id_seccion = :id_seccion AND sc.estado = 'activo'
			ORDER BY sc.orden ASC, sca.asset_tipo ASC
		");

		$stmt->bindParam(":id_seccion", $idSeccion, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
