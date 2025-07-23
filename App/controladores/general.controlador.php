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

	public static function ctrRutaVerCurso()
	{
		return "http://localhost/cursosApp/verCurso.php"; //Ruta para ver un curso específico
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

	/*=============================================
	Verificar si el usuario tiene un rol específico
	=============================================*/
	public static function ctrVerificarRolUsuario($idUsuario, $rolesPermitidos)
	{
		require_once "modelos/usuarios.modelo.php";

		// Obtener roles del usuario
		$rolesUsuario = ModeloUsuarios::mdlObtenerRolesPorUsuario($idUsuario);

		// Si no tiene roles, denegar acceso
		if (empty($rolesUsuario)) {
			return false;
		}

		// Verificar si alguno de los roles del usuario está en los roles permitidos
		foreach ($rolesUsuario as $rol) {
			if (in_array($rol['nombre'], $rolesPermitidos)) {
				return true;
			}
		}

		return false;
	}

	/*=============================================
	Controlar acceso a páginas según roles
	=============================================*/
	public static function ctrCargarPaginaConAcceso()
	{
		// Verificar si hay sesión activa (usando ambas variables por compatibilidad)
		$idUsuario = null;
		if (isset($_SESSION['id']) && !empty($_SESSION['id'])) {
			$idUsuario = $_SESSION['id'];
		} elseif (isset($_SESSION['idU']) && !empty($_SESSION['idU'])) {
			$idUsuario = $_SESSION['idU'];
		}

		if (!$idUsuario) {
			return "vistas/paginas/error404.php";
		}

		if (isset($_GET["pagina"])) {
			$pagina = $_GET["pagina"];

			// Seguridad: solo permitimos letras, números, guiones y barras
			if (!preg_match('/^[a-zA-Z0-9\/_-]+$/', $pagina)) {
				return "vistas/paginas/error404.php";
			}

			// Definir restricciones de acceso por página
			$restriccionesPaginas = self::obtenerRestriccionesPaginas();

			// Verificar si la página tiene restricciones de rol
			if (isset($restriccionesPaginas[$pagina])) {
				$rolesPermitidos = $restriccionesPaginas[$pagina];

				// Verificar si el usuario tiene acceso
				if (!self::ctrVerificarRolUsuario($idUsuario, $rolesPermitidos)) {
					return "vistas/paginas/accesoDenegado.php";
				}
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

	/*=============================================
	Definir restricciones de acceso por página
	=============================================*/
	private static function obtenerRestriccionesPaginas()
	{
		return [
			// Páginas solo para administradores
			'superAdmin/usuarios' => ['admin', 'superadmin'],
			'superAdmin/configuracion' => ['admin', 'superadmin'],
			'superAdmin/reportes' => ['admin', 'superadmin'],
			'soporte' => ['admin', 'superadmin'],
			'listadoCursos' => ['admin', 'superadmin'],
			'superAdmin/gestionCursos/listadoCursos' => ['admin', 'superadmin'],

			// Páginas para profesores y administradores
			'cursos' => ['profesor', 'admin', 'superadmin'],
			'profesores' => ['profesor', 'admin', 'superadmin'],

			// Páginas para estudiantes, profesores y administradores
			'misCursos' => ['estudiante', 'profesor', 'admin', 'superadmin'],
			'seguirCurso' => ['estudiante', 'profesor', 'admin', 'superadmin'],
			'inscripciones' => ['estudiante', 'profesor', 'admin', 'superadmin'],

			// Páginas de perfil (todos los usuarios autenticados)
			'perfil' => ['estudiante', 'profesor', 'admin', 'superadmin'],
			'modalPassword' => ['estudiante', 'profesor', 'admin', 'superadmin'],
			'modalFoto' => ['estudiante', 'profesor', 'admin', 'superadmin'],
		];
	}

	/*=============================================
	Verificar acceso antes de mostrar contenido
	=============================================*/
	public static function ctrVerificarAccesoContenido($paginaActual, $contenidoRestringido = null)
	{
		// Verificar diferentes variables de sesión para ID
		$idUsuario = null;
		if (isset($_SESSION['id']) && !empty($_SESSION['id'])) {
			$idUsuario = $_SESSION['id'];
		} elseif (isset($_SESSION['idU']) && !empty($_SESSION['idU'])) {
			$idUsuario = $_SESSION['idU'];
		}

		// Si no hay sesión, denegar acceso
		if (!$idUsuario) {
			return false;
		}

		$restricciones = self::obtenerRestriccionesPaginas();

		// Si la página tiene restricciones
		if (isset($restricciones[$paginaActual])) {
			$rolesPermitidos = $restricciones[$paginaActual];
			return self::ctrVerificarRolUsuario($idUsuario, $rolesPermitidos);
		}

		// Si no hay restricciones específicas, permitir acceso a usuarios autenticados
		return true;
	}

	/*=============================================
	Obtener roles del usuario actual
	=============================================*/
	public static function ctrObtenerRolesUsuarioActual()
	{
		// Verificar diferentes variables de sesión para ID
		$idUsuario = null;
		if (isset($_SESSION['id']) && !empty($_SESSION['id'])) {
			$idUsuario = $_SESSION['id'];
		} elseif (isset($_SESSION['idU']) && !empty($_SESSION['idU'])) {
			$idUsuario = $_SESSION['idU'];
		}

		if (!$idUsuario) {
			return [];
		}

		require_once "modelos/usuarios.modelo.php";
		return ModeloUsuarios::mdlObtenerRolesPorUsuario($idUsuario);
	}

	/*=============================================
	Verificar si el usuario actual tiene un rol específico
	=============================================*/
	public static function ctrUsuarioTieneRol($nombreRol)
	{
		$rolesUsuario = self::ctrObtenerRolesUsuarioActual();
		foreach ($rolesUsuario as $rol) {
			if ($rol['nombre'] === $nombreRol) {
				return true;
			}
		}
		return false;
	}

	/*=============================================
	Verificar si el usuario actual tiene alguno de los roles especificados
	=============================================*/
	public static function ctrUsuarioTieneAlgunRol($rolesPermitidos)
	{
		// Verificar diferentes variables de sesión para ID
		$idUsuario = null;
		if (isset($_SESSION['id']) && !empty($_SESSION['id'])) {
			$idUsuario = $_SESSION['id'];
		} elseif (isset($_SESSION['idU']) && !empty($_SESSION['idU'])) {
			$idUsuario = $_SESSION['idU'];
		}

		if (!$idUsuario) {
			return false;
		}
		return self::ctrVerificarRolUsuario($idUsuario, $rolesPermitidos);
	}

	/*=============================================
	Generar menú dinámico basado en roles
	=============================================*/
	public static function ctrGenerarMenuPorRoles()
	{
		$rolesUsuario = self::ctrObtenerRolesUsuarioActual();
		$menuItems = [];

		// Elementos del menú comunes para todos
		$menuItems[] = [
			'nombre' => 'Inicio',
			'url' => 'inicio',
			'icono' => 'fas fa-home',
			'roles' => ['estudiante', 'profesor', 'admin', 'superadmin']
		];

		$menuItems[] = [
			'nombre' => 'Mi Perfil',
			'url' => 'perfil',
			'icono' => 'fas fa-user',
			'roles' => ['estudiante', 'profesor', 'admin', 'superadmin']
		];

		// Elementos para estudiantes
		$menuItems[] = [
			'nombre' => 'Mis Cursos',
			'url' => 'misCursos',
			'icono' => 'fas fa-book',
			'roles' => ['estudiante', 'profesor', 'admin', 'superadmin']
		];

		$menuItems[] = [
			'nombre' => 'Inscripciones',
			'url' => 'inscripciones',
			'icono' => 'fas fa-clipboard-list',
			'roles' => ['estudiante', 'profesor', 'admin', 'superadmin']
		];

		// Elementos para profesores
		$menuItems[] = [
			'nombre' => 'Gestionar Cursos',
			'url' => 'cursos',
			'icono' => 'fas fa-chalkboard-teacher',
			'roles' => ['profesor', 'admin', 'superadmin']
		];

		$menuItems[] = [
			'nombre' => 'Profesores',
			'url' => 'profesores',
			'icono' => 'fas fa-users',
			'roles' => ['profesor', 'admin', 'superadmin']
		];

		// Elementos para administradores
		$menuItems[] = [
			'nombre' => 'Administración',
			'url' => '#',
			'icono' => 'fas fa-cogs',
			'roles' => ['admin', 'superadmin'],
			'submenu' => [
				[
					'nombre' => 'Usuarios',
					'url' => 'superAdmin/usuarios',
					'icono' => 'fas fa-users-cog'
				],
				[
					'nombre' => 'Configuración',
					'url' => 'superAdmin/configuracion',
					'icono' => 'fas fa-cog'
				],
				[
					'nombre' => 'Reportes',
					'url' => 'superAdmin/reportes',
					'icono' => 'fas fa-chart-bar'
				]
			]
		];

		// Filtrar elementos según los roles del usuario
		$menuFiltrado = [];
		foreach ($menuItems as $item) {
			if (self::ctrUsuarioTieneAlgunRol($item['roles'])) {
				$menuFiltrado[] = $item;
			}
		}

		return $menuFiltrado;
	}
}
