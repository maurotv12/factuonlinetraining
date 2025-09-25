<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/factuonlinetraining/App/modelos/conexion.php";

class ModeloCursosInicio
{
	/*=============================================
	Mostrar cursos en inicio
=============================================*/
	public static function mdlMostrarCursosInicio($tabla, $item, $valor)
	{
		if ($item != null && $valor != null) {
			$stmt = Conexion::conectar()->prepare("SELECT c.*, p.nombre as nombre_profesor FROM $tabla c LEFT JOIN persona p ON c.id_persona = p.id WHERE c.$item = :$item AND c.estado = 'activo'");
			$stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
			$stmt->execute();
			return $stmt->fetchAll();
		} else {
			$stmt = Conexion::conectar()->prepare("SELECT c.*, p.nombre as nombre_profesor FROM $tabla c LEFT JOIN persona p ON c.id_persona = p.id WHERE c.estado = 'activo'");
			$stmt->execute();
			return $stmt->fetchAll();
		}
	}

	/*=============================================
	Mostrar UN SOLO curso en inicio
=============================================*/
	public static function mdlMostrarUnCursoInicio($tabla, $item, $valor)
	{
		$stmt = Conexion::conectar()->prepare("SELECT c.*, p.nombre as nombre_profesor FROM $tabla c LEFT JOIN persona p ON c.id_persona = p.id WHERE c.$item = :$item AND c.estado = 'activo'");
		$stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
		$stmt->execute();
		return $stmt->fetch();
	}

	/*==========================================================================
	Back end
	Contar registros en la tabla que envien como parametro mostrar infoAdmin
=============================================================================*/
	static public function mdlContarRegistros($tabla)
	{
		$stmt = Conexion::conectar()->prepare("SELECT COUNT(*) total FROM $tabla");
		$stmt->execute();
		return $stmt->fetch();
	}
	/*==============================================
	 Consultar los datos de un curso en inicio
==============================================*/
	static public function mdlConsultarUnCursoInicio($item, $valor, $tabla)
	{
		$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item");
		$stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
		$stmt->execute();
		return $stmt->fetch();
	}

	/**
	 * Obtiene los cursos destacados para mostrar en el carrusel
	 * @param string $tabla Nombre de la tabla
	 * @param int $limite Cantidad máxima de cursos a obtener
	 * @return array Lista de cursos destacados
	 */
	static public function mdlObtenerCursosDestacados($tabla, $limite = 3)
	{
		try {
			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE destacado = 1 AND estado = 'activo' ORDER BY fecha_creacion DESC LIMIT :limite");
			$stmt->bindParam(":limite", $limite, PDO::PARAM_INT);
			$stmt->execute();
			$resultado = $stmt->fetchAll();
			return $resultado;
		} catch (PDOException $e) {
			// Registrar error si es necesario
			return [];
		}
	}
}
