<?php
/**
@grcarvajal grcarvajal@gmail.com **Gildardo Restrepo Carvajal**
12/06/2022 Plataforma Calibelula mostrar Cursos
Controlador de rutas y plantilla
 */

class ControladorGeneral{

	static public function ctrRuta(){
		return "http://localhost/cursosApp/"; //Ruta de inicio para entorno local
		

	}

	static public function ctrRutaApp(){
		return "http://localhost/cursosApp/registro/App/"; //Ruta ingresar al dashboard entorno local
		

	}

// Funcion para redirecionar el flujo de la app a la plantilla
	public function ctrPlantilla()
	{
		include "vistas/plantilla.php";
	}

////Contar registros en la tabla que se pase como parametro
	static public function ctrContarRegistros($tabla)
		{
		$respuesta = ModeloGeneral::mdlContarRegistros($tabla);
		 return $respuesta;
		}
	
}