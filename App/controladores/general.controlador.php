<?php

/**
@grcarvajal grcarvajal@gmail.com **Gildardo Restrepo Carvajal**
12/06/2022 Plataforma Calibelula mostrar Cursos
Controlador de rutas y plantilla
 */

class ControladorGeneral
{

	public static function ctrRuta()
	{
		return "http://localhost/cursosApp/"; //Ruta de inicio para entorno local


	}

	public static function ctrRutaApp()
	{
		return "http://localhost/cursosApp/registro/App/"; //Ruta ingresar al dashboard entorno local


	}

	// Funcion para redirecionar el flujo de la app a la plantilla
	public function ctrPlantilla()
	{
		include "vistas/plantilla.php";
	}

	////Contar registros en la tabla que se pase como parametro
	public static function ctrContarRegistros($tabla)
	{
		$respuesta = ModeloGeneral::mdlContarRegistros($tabla);
		return $respuesta;
	}
}
