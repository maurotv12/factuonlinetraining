<?php
class ControladorRuta
{

	//Ruta de regreso a inicio

	public static function ctrRutaInicio()
	{
		return "http://localhost/cursosApp";
	}

	public static function ctrRuta()
	{
		return "http://localhost/cursosApp/register";
	}
	//generame la ruta para login
	public static function ctrRutaLogin()
	{
		// Ruta exacta al login
		return "http://localhost/cursosApp/login";
	}


	public static function ctrRutaApp()
	{
		return "http://localhost/cursosApp/App/"; //Ruta ingresar al dashboard entorno local

	}

	public static function ctrRutaForgotPassword()
	{
		return "http://localhost/cursosApp/forgot-password";
	}

	// Unificación de lógica: retorna la ruta de la vista principal (curso, inicio o error404)
	public static function ctrCargarPagina()
	{
		return self::obtenerRutaVistaPrincipal();
	}

	// Método para cargar la vista de curso o inicio según la url_amiga
	public static function cargarVistaCursoInicio()
	{
		$ruta = self::obtenerRutaVistaPrincipal();
		if ($ruta && file_exists($ruta)) {
			include $ruta;
		} else {
			include $_SERVER['DOCUMENT_ROOT'] . "/cursosApp/publico/vistas/paginas/error404.php";
		}
	}

	// Método privado auxiliar para decidir la ruta de la vista principal
	private static function obtenerRutaVistaPrincipal()
	{
		// Si la URL corresponde a un curso (url_amiga), priorizar esa lógica
		if (isset($_GET["pagina"])) {
			$item = "url_amiga";
			$valor = $_GET["pagina"];
			if (class_exists('ControladorCursosInicio')) {
				$curso = ControladorCursosInicio::ctrMostrarUnCursoInicio($item, $valor);
				if (isset($curso["url_amiga"])) {
					return $_SERVER['DOCUMENT_ROOT'] . "/cursosApp/publico/curso.php";
				}
			}
		}
		// Si no es curso, buscar la vista tradicional
		$basePath = $_SERVER['DOCUMENT_ROOT'] . "/cursosApp/publico/";
		if (isset($_GET["pagina"])) {
			$pagina = $_GET["pagina"];
			// Seguridad: solo permitimos letras, números, guiones y barras
			if (!preg_match('/^[a-zA-Z0-9\/_-]+$/', $pagina)) {
				return $basePath . "vistas/paginas/error404.php";
			}
			// Búsqueda recursiva en todas las subcarpetas de vistas/paginas
			$directorioBase = $_SERVER['DOCUMENT_ROOT'] . "/cursosApp/publico/vistas/paginas";
			$archivoBuscado = $pagina . ".php";
			$ruta = self::buscarArchivo($directorioBase, $archivoBuscado);
			if ($ruta) {
				return $ruta; // $ruta ya es absoluta
			} else {
				return $directorioBase . "/error404.php";
			}
		} else {
			return $basePath . "inicio.php";
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


	//funcion de redirecionamiento a la plantilla de inicio o index
	public function ctrPlantilla()
	{
		// include "vistas/paginas/registro/vista/plantilla.php";
		include $_SERVER['DOCUMENT_ROOT'] . "/cursosApp/publico/vistas/paginas/registro/vista/plantilla.php";
	}
}
