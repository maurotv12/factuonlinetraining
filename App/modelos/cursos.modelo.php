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
	public static function mdlMostrarCursos($tabla, $item, $valor)
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
		$stmt->close();
		$stmt = null;
	}

	public static function mdlCrearCurso($tabla, $datos)
	{
		$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla (url_amiga, nombre, descripcion, banner, promo_video, valor, id_categoria, id_persona, estado) 
        VALUES (:url_amiga, :nombre, :descripcion, :banner, :promo_video, :valor, :id_categoria, :id_persona, :estado)");

		$stmt->bindParam(":url_amiga", $datos["url_amiga"], PDO::PARAM_STR);
		$stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
		$stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
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
}
