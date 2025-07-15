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

	public static function ctrCargarPagina()
	{
		if (isset($_GET["pagina"])) {

			$pagina = $_GET["pagina"];

			// Seguridad: solo permitimos letras, números, guiones y barras
			if (!preg_match('/^[a-zA-Z0-9\/_-]+$/', $pagina)) {
				return "vistas/paginas/error404.php";
			}

			// Búsqueda recursiva en todas las subcarpetas de vistas/paginas
			$directorioBase = "vistas/paginas";
			$archivoBuscado = $pagina . ".php";

			$ruta = self::buscarArchivo($directorioBase, $archivoBuscado);

			if ($ruta) {
				return $ruta;
			} else {
				return "vistas/paginas/error404.php";
			}
		} else {
			return "vistas/paginas/inicio.php";
		}
	}

	// Método auxiliar para buscar archivos recursivamente
	private static function buscarArchivo($directorio, $archivoBuscado)
	{
		$iterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($directorio),
			RecursiveIteratorIterator::LEAVES_ONLY
		);

		foreach ($iterator as $archivo) {
			if ($archivo->isFile() && $archivo->getFilename() == $archivoBuscado) {
				return $archivo->getPathname();
			}
		}

		return false;
	}




	//Contar registros en la tabla que se pase como parametro
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
