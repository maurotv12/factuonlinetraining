<?php

/**
@grcarvajal grcarvajal@gmail.com **Gildardo Restrepo Carvajal**
12/06/2022 Plataforma Calibelula mostrar Cursos
Modelo de cursos, gestion
 */
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

		$stmt = null;
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

		$stmt = null;
	}

	/*=============================================
	Métodos para gestión de secciones
	=============================================*/
	public static function mdlCrearSeccion($datos)
	{
		$conn = Conexion::conectar();
		$stmt = $conn->prepare("INSERT INTO curso_secciones (id_curso, titulo, descripcion, orden, estado) 
			VALUES (:id_curso, :titulo, :descripcion, :orden, :estado)");

		$stmt->bindParam(":id_curso", $datos["id_curso"], PDO::PARAM_INT);
		$stmt->bindParam(":titulo", $datos["titulo"], PDO::PARAM_STR);
		$stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
		$stmt->bindParam(":orden", $datos["orden"], PDO::PARAM_INT);
		$stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);

		if ($stmt->execute()) {
			return $conn->lastInsertId();
		} else {
			return "error";
		}

		$stmt = null;
	}

	public static function mdlActualizarSeccion($datos)
	{
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

		$stmt = null;
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

		$stmt = null;
	}

	/*=============================================
	Métodos para gestión de contenido de secciones
	=============================================*/
	public static function mdlCrearContenido($datos)
	{
		$stmt = Conexion::conectar()->prepare("INSERT INTO seccion_contenido 
			(id_seccion, titulo, descripcion, tipo, archivo_url, duracion, tamaño_archivo, orden, estado) 
			VALUES (:id_seccion, :titulo, :descripcion, :tipo, :archivo_url, :duracion, :tamaño_archivo, :orden, :estado)");

		$stmt->bindParam(":id_seccion", $datos["id_seccion"], PDO::PARAM_INT);
		$stmt->bindParam(":titulo", $datos["titulo"], PDO::PARAM_STR);
		$stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
		$stmt->bindParam(":tipo", $datos["tipo"], PDO::PARAM_STR);
		$stmt->bindParam(":archivo_url", $datos["archivo_url"], PDO::PARAM_STR);
		$stmt->bindParam(":duracion", $datos["duracion"], PDO::PARAM_STR);
		$stmt->bindParam(":tamaño_archivo", $datos["tamaño_archivo"], PDO::PARAM_INT);
		$stmt->bindParam(":orden", $datos["orden"], PDO::PARAM_INT);
		$stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);

		if ($stmt->execute()) {
			return Conexion::conectar()->lastInsertId();
		} else {
			return "error";
		}

		$stmt = null;
	}

	public static function mdlActualizarContenido($datos)
	{
		$stmt = Conexion::conectar()->prepare("UPDATE seccion_contenido SET 
			titulo = :titulo,
			descripcion = :descripcion,
			tipo = :tipo,
			archivo_url = :archivo_url,
			duracion = :duracion,
			tamaño_archivo = :tamaño_archivo,
			orden = :orden,
			estado = :estado
			WHERE id = :id");

		$stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);
		$stmt->bindParam(":titulo", $datos["titulo"], PDO::PARAM_STR);
		$stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
		$stmt->bindParam(":tipo", $datos["tipo"], PDO::PARAM_STR);
		$stmt->bindParam(":archivo_url", $datos["archivo_url"], PDO::PARAM_STR);
		$stmt->bindParam(":duracion", $datos["duracion"], PDO::PARAM_STR);
		$stmt->bindParam(":tamaño_archivo", $datos["tamaño_archivo"], PDO::PARAM_INT);
		$stmt->bindParam(":orden", $datos["orden"], PDO::PARAM_INT);
		$stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);

		if ($stmt->execute()) {
			return "ok";
		} else {
			return "error";
		}

		$stmt = null;
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

		$stmt = null;
	}
}
