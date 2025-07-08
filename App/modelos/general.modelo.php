<?php
/**
@grcarvajal grcarvajal@gmail.com **Gildardo Restrepo Carvajal**
26/04/2022 aplicación PAWers citas de acompañamiento con mascotas
modelo para crear consultas generales para todos sirven
 */
require_once "conexion.php";

class ModeloGeneral
{

/*====================================================================================
	Contar registros en cualquier tabla, se debe enviar la tabla que se desea contar
=====================================================================================*/
static public function mdlContarRegistros($tabla)
{
	$stmt = Conexion::conectar()->prepare("SELECT COUNT(*) total FROM $tabla");
			$stmt -> execute();
			return $stmt -> fetch();
			$stmt-> close();
			$stmt = null;
	}
	
	
}