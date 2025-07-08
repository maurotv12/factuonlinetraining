<?php
/**
@grcarvajal grcarvajal@gmail.com **Gildardo Restrepo Carvajal**
26/05/2022 CursosApp
 */
require_once "conexion.php";

class ModeloCursosInicio
{
/*=============================================
	Mostrar cursos en inicio
=============================================*/
	static public function mdlMostrarCursosInicio($tabla, $item, $valor){
		if($item != null && $valor != null){
			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item");
			$stmt->bindParam(":".$item, $valor, PDO::PARAM_STR);
			$stmt -> execute();
			return $stmt -> fetch();
		}else{
			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla");
			$stmt -> execute();
			return $stmt -> fetchAll();
		}
		$stmt-> close();
		$stmt = null;
	}

/*==========================================================================
	Back end
	Contar registros en la tabla que envien como parametro mostrar infoAdmin
=============================================================================*/
static public function mdlContarRegistros($tabla)
{
	$stmt = Conexion::conectar()->prepare("SELECT COUNT(*) total FROM $tabla");
			$stmt -> execute();
			return $stmt -> fetch();
			$stmt-> close();
			$stmt = null;
	}
/*==============================================
	 Consultar los datos de un curso en inicio
==============================================*/
	static public function mdlConsultarUnCursoInicio($item, $valor, $tabla){
		$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item");
		$stmt->bindParam(":".$item, $valor, PDO::PARAM_STR);
		$stmt -> execute();
		return $stmt -> fetch();
		$stmt-> close();
		$stmt = null;
	}

}