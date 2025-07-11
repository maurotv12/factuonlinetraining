<?php

/**
// @grcarvajal grcarvajal@gmail.com **Gildardo Restrepo Carvajal**
// 12/06/2022 CursosApp
 */

class ControladorRuta
{

	//Ruta de regreso a inicio

	public static function ctrRutaInicio()
	{
		return "http://localhost/cursosApp";
	}

	public static function ctrRuta()
	{
		return "http://localhost/cursosApp/registro/";
	}
	//generame la ruta para login
	public static function ctrRutaLogin()
	{
		return "http://localhost/cursosApp/registro/login";
	}

	public static function ctrRutaApp()
	{
		return "http://localhost/cursosApp/App/"; //Ruta ingresar al dashboard entorno local

	}

	////Contar registros en la tabla que se pase como parametro
	// public static function ctrContarRegistros($tabla)
	// {
	// 	$respuesta = ModeloUsuarios::mdlContarRegistros($tabla);
	// 	return $respuesta;
	// }

	//funcion de redirecionamiento a la plantilla de inicio o index
	public function ctrPlantilla()
	{
		include "vistas/plantilla.php";
	}
}
