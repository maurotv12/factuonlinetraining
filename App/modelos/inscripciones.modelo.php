<?php

/**
@grcarvajal grcarvajal@gmail.com **Gildardo Restrepo Carvajal**
10/05/2022 aplicación PAWers citas de acompañamiento con mascotas
Modelo gestion de citas registro inicio cita
 */
require_once "conexion.php";

class ModeloInscripciones

{

	/*=========================================================
	Mostrar Inscripciones pendientes de cada Usuariov
===========================================================*/

	public static function mdlMostrarInscripcionesPendientessCU($tabla, $item, $valor)
	{
		if ($item != null && $valor != null) {
			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item AND estado = 1");
			$stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
			$stmt->execute();
			return $stmt->fetchAll();
		} else {
			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE estado = 1");
			$stmt->execute();
			return $stmt->fetchAll();
		}
		$stmt->close();
		$stmt = null;
	}


	/*=============================================
	Mostrar Citas
==============================================*/
	public static function mdlMostrarInscripciones($tabla, $item, $valor)
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
}
