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
	// public static function ctrCargarPagina()
	// {
	// 	if (isset($_GET["pagina"])) {

	// 		$paginasValidas = [
	// 			"inicio",
	// 			"suscripciones",
	// 			"misCursos",
	// 			"seguirCurso",
	// 			"perfil",
	// 			"profesores",
	// 			"usuarios",
	// 			"suscripcionesAdmin",
	// 			"soporte",
	// 			"salir"
	// 		];

	// 		if (in_array($_GET["pagina"], $paginasValidas)) {
	// 			return "paginas/" . $_GET["pagina"] . ".php";
	// 		} else {
	// 			return "paginas/error404.php";
	// 		}
	// 	} else {
	// 		return "paginas/inicio.php";
	// 	}
	// }

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
