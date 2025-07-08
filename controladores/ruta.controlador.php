<?php
/**
@grcarvajal grcarvajal@gmail.com **Gildardo Restrepo Carvajal**
12/06/2022 CursosApp
 */

class ControladorRuta{

	//Ruta de regreso a inicio

	static public function ctrRutaInicio(){
		return "http://localhost/cursosApp";
	}

	static public function ctrRuta(){
		return "http://localhost/cursosApp/registro/";
	}

	static public function ctrRutaApp(){
		return "http://localhost/cursosApp/App/"; //Ruta ingresar al dashboard entorno local

	}

////Contar registros en la tabla que se pase como parametro
	static public function ctrContarRegistros($tabla)
		{
		$respuesta = ModeloUsuarios::mdlContarRegistros($tabla);
		 return $respuesta;
		}

//funcion de redirecionamiento a la plantilla de inicio o index
	public function ctrPlantilla()
	{
		include "vistas/plantilla.php";
	}

}