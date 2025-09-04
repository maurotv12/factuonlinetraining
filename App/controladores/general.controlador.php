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
		// Verificar si hay sesión activa
		$idUsuario = null;
		if (isset($_SESSION['idU']) && !empty($_SESSION['idU'])) {
			$idUsuario = $_SESSION['idU'];
		} elseif (isset($_SESSION['id']) && !empty($_SESSION['id'])) {
			$idUsuario = $_SESSION['id'];
		}

		// Si no hay usuario logueado, redirigir al área pública
		if (!$idUsuario) {
			return "http://localhost/cursosApp/";
		}

		// Obtener roles del usuario desde la sesión o base de datos
		$rolesUsuario = [];
		if (isset($_SESSION['rolesU']) && !empty($_SESSION['rolesU'])) {
			$rolesUsuario = $_SESSION['rolesU'];
		} else {
			// Si no están en sesión, obtenerlos de la base de datos
			if (file_exists("modelos/usuarios.modelo.php")) {
				require_once "modelos/usuarios.modelo.php";
			} else {
				require_once "../modelos/usuarios.modelo.php";
			}
			$rolesUsuario = ModeloUsuarios::mdlObtenerRolesPorUsuario($idUsuario);
			$_SESSION['rolesU'] = $rolesUsuario; // Guardar en sesión
		}

		// Si no tiene roles, redirigir a área pública
		if (empty($rolesUsuario)) {
			return "http://localhost/cursosApp/";
		}

		// Extraer solo los nombres de los roles
		$nombresRoles = array_column($rolesUsuario, 'nombre');

		// Determinar ruta según prioridad de roles:
		// 1. Admin tiene máxima prioridad
		if (in_array('admin', $nombresRoles) || in_array('superadmin', $nombresRoles)) {
			return "http://localhost/cursosApp/App/usuarios";
		}

		// 2. Profesor (si no es admin)
		if (in_array('profesor', $nombresRoles)) {
			return "http://localhost/cursosApp/App/listadoCursosProfe";
		}

		// 3. Estudiante (si no tiene roles superiores)
		if (in_array('estudiante', $nombresRoles)) {
			return "http://localhost/cursosApp/App/inicioEstudiante";
		}

		// Fallback: dashboard general
		return "http://localhost/cursosApp/App/dashboard";
	}

	public static function ctrRutaVerCurso()
	{
		return "http://localhost/cursosApp/verCurso.php"; //Ruta para ver un curso específico
	}


	// Método auxiliar para buscar archivos recursivamente
	private static function buscarArchivo($directorio, $archivoBuscado)
	{
		// Obtener la ruta absoluta del directorio base
		$directorioCompleto = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . $directorio;
		$directorioCompleto = realpath($directorioCompleto);

		if (!$directorioCompleto || !is_dir($directorioCompleto)) {
			return false;
		}

		// Construir la ruta completa del archivo que buscamos
		$rutaCompleta = $directorioCompleto . DIRECTORY_SEPARATOR . $archivoBuscado;
		$rutaCompleta = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $rutaCompleta);

		// Verificar si el archivo existe directamente
		if (file_exists($rutaCompleta)) {
			// Devolver la ruta relativa desde la carpeta App
			return $directorio . DIRECTORY_SEPARATOR . $archivoBuscado;
		}

		// Si no existe, usar el método original como fallback
		$iterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($directorioCompleto),
			RecursiveIteratorIterator::LEAVES_ONLY
		);

		foreach ($iterator as $archivo) {
			if ($archivo->isFile() && $archivo->getFilename() == basename($archivoBuscado)) {
				// Convertir a ruta relativa desde la carpeta App
				$rutaRelativa = str_replace($directorioCompleto . DIRECTORY_SEPARATOR, $directorio . DIRECTORY_SEPARATOR, $archivo->getPathname());
				return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $rutaRelativa);
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
		if (file_exists("modelos/usuarios.modelo.php")) {
			require_once "modelos/usuarios.modelo.php";
		} else {
			require_once "../modelos/usuarios.modelo.php";
		}

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

			// Verificar si es una ruta de editar curso (editarCurso/url-amiga)
			if (preg_match('/^editarCurso\/(.+)$/', $pagina, $matches)) {
				$urlAmiga = $matches[1];

				// Buscar el curso por URL amigable y obtener su ID
				if (file_exists("modelos/cursos.modelo.php")) {
					require_once "modelos/cursos.modelo.php";
				} else {
					require_once "../modelos/cursos.modelo.php";
				}
				$curso = ModeloCursos::mdlMostrarCursos("curso", "url_amiga", $urlAmiga);

				if ($curso) {
					// Establecer tanto el ID como la URL amigable para que la página los use
					$_GET['identificador'] = $urlAmiga;
					$pagina = "superAdmin/gestionCursos/editarCurso";
				} else {
					return "vistas/paginas/error404.php";
				}
			}
			// Verificar si es una ruta de ver curso (verCurso/url-amiga)
			elseif (preg_match('/^verCurso\/(.+)$/', $pagina, $matches)) {
				$urlAmiga = $matches[1];

				// Buscar el curso por URL amigable y obtener su ID
				require_once "modelos/cursos.modelo.php";
				$curso = ModeloCursos::mdlMostrarCursos("curso", "url_amiga", $urlAmiga);

				if ($curso) {
					// Establecer tanto el ID como la URL amigable para que la página los use
					$_GET['identificador'] = $urlAmiga;
					$pagina = "superAdmin/gestionCursos/verCurso";
				} else {
					return "vistas/paginas/error404.php";
				}
			}
			// Verificar si es una ruta de editar curso para profesores (editarCursoProfe/url-amiga) - REDIRIGIR A verCursoProfe
			elseif (preg_match('/^editarCursoProfe\/(.+)$/', $pagina, $matches)) {
				$urlAmiga = $matches[1];

				// Buscar el curso por URL amigable y obtener su ID
				require_once "modelos/cursos.modelo.php";
				$curso = ModeloCursos::mdlMostrarCursos("curso", "url_amiga", $urlAmiga);

				if ($curso) {
					// Establecer tanto el ID como la URL amigable para que la página los use
					$_GET['identificador'] = $urlAmiga;
					$pagina = "profesores/gestionCursosPr/verCursoProfe";
				} else {
					return "vistas/paginas/error404.php";
				}
			}
			// Verificar si es una ruta de ver curso para profesores (verCursoProfe/url-amiga)
			elseif (preg_match('/^verCursoProfe\/(.+)$/', $pagina, $matches)) {
				$urlAmiga = $matches[1];

				// Buscar el curso por URL amigable y obtener su ID
				require_once "modelos/cursos.modelo.php";
				$curso = ModeloCursos::mdlMostrarCursos("curso", "url_amiga", $urlAmiga);

				if ($curso) {
					// Establecer tanto el ID como la URL amigable para que la página los use
					$_GET['identificador'] = $urlAmiga;
					$pagina = "profesores/gestionCursosPr/verCursoProfe";
				} else {
					return "vistas/paginas/error404.php";
				}
			}
			// Si es la ruta directa a editarCurso con ID, mantenerla
			elseif (preg_match('/^superAdmin\/gestionCursos\/editarCurso$/', $pagina) && isset($_GET['id'])) {
				// Ya tiene el ID, establecer también el identificador para consistencia
				$_GET['identificador'] = $_GET['id'];
			}
			// Si es la ruta directa a verCurso con ID, mantenerla
			elseif (preg_match('/^superAdmin\/gestionCursos\/verCurso$/', $pagina) && isset($_GET['id'])) {
				// Ya tiene el ID, establecer también el identificador para consistencia
				$_GET['identificador'] = $_GET['id'];
			}
			// Si es la ruta directa a editarCursoProfe con ID, REDIRIGIR A verCursoProfe
			elseif (preg_match('/^profesores\/gestionCursosPr\/editarCursoProfe$/', $pagina) && isset($_GET['id'])) {
				// Ya tiene el ID, establecer también el identificador para consistencia
				$_GET['identificador'] = $_GET['id'];
				$pagina = "profesores/gestionCursosPr/verCursoProfe";
			}
			// Si es la ruta directa a verCursoProfe con ID, mantenerla
			elseif (preg_match('/^profesores\/gestionCursosPr\/verCursoProfe$/', $pagina) && isset($_GET['id'])) {
				// Ya tiene el ID, establecer también el identificador para consistencia
				$_GET['identificador'] = $_GET['id'];
			}

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
			'superAdmin/gestionUsuarios/solicitudesInstructores' => ['admin'],
			'superAdmin/gestionUsuarios/usuarios' => ['admin'],
			'superAdmin/gestionUsuarios/verCurso' => ['admin'],
			'superAdmin/gestionCursos/listadoCursos' => ['admin'],
			'superAdmin/gestionCursos/crearCurso' => ['admin', 'profesor'],
			'superAdmin/gestionCursos/editarCurso' => ['admin', 'profesor'],

			'superAdmin/configuracion' => ['admin'],
			'superAdmin/reportes' => ['admin'],
			'soporte' => ['admin'],
			'listadoCursos' => ['admin'],

			// Páginas para profesores y administradores
			'cursos' => ['profesor', 'admin'],
			'profesores' => ['profesor', 'admin'],
			'profesores/gestionPerfil/perfilProfesor' => ['profesor'],

			// Páginas para estudiantes, profesores y administradores
			'misCursos' => ['estudiante', 'profesor', 'admin'],
			'seguirCurso' => ['estudiante', 'profesor', 'admin'],
			'inscripciones' => ['estudiante', 'profesor', 'admin'],

			// Páginas de perfil (todos los usuarios autenticados)
			'perfil' => ['estudiante', 'profesor', 'admin'],
			'modalPassword' => ['estudiante', 'profesor', 'admin'],
			'modalFoto' => ['estudiante', 'profesor', 'admin'],
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
