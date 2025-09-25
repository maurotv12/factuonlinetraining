<?php

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;


require_once $_SERVER['DOCUMENT_ROOT'] . "/factuonlinetraining/App/modelos/usuarios.modelo.php";

class ControladorUsuarios
{
	/*=============================================
	Mostrar Usuarios
	=============================================*/

	public static function ctrMostrarUsuarios($item, $valor)
	{

		$tabla = "persona";
		$respuesta = ModeloUsuarios::mdlMostrarUsuarios($tabla, $item, $valor);
		return $respuesta;
	}

	/*=============================================
	Actualizar Usuario
	=============================================*/

	public static function ctrActualizarUsuario($id, $item, $valor)
	{

		$tabla = "persona";

		$respuesta = ModeloUsuarios::mdlActualizarUsuario($tabla, $id, $item, $valor);

		return $respuesta;
	}

	/*=============================================
	Cambiar foto perfil
	=============================================*/
	public function ctrCambiarFoto()
	{
		if (isset($_POST["idClienteImagen"])) {
			$pagina = $_POST["pagina"]; // pagina de retorno
			$idUsuario = $_POST["idClienteImagen"];

			if (isset($_FILES["nuevaImagen"]["tmp_name"]) && !empty($_FILES["nuevaImagen"]["tmp_name"])) {

				// Validar que sea una imagen
				if (!in_array($_FILES["nuevaImagen"]["type"], ["image/jpeg", "image/png"])) {
					echo '<div class="alert alert-danger">¡Solo formatos de imagen JPG y/o PNG!</div>';
					return;
				}

				list($ancho, $alto) = getimagesize($_FILES["nuevaImagen"]["tmp_name"]);
				$nuevoAncho = 500;
				$nuevoAlto = 500;

				// Crear directorio del usuario en nueva estructura storage
				$documentRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : 'C:\\xampp\\htdocs';
				$directorioUsuario = $documentRoot . "/factuonlinetraining/storage/public/usuarios/" . $idUsuario;
				$directorioUsuario = str_replace('/', DIRECTORY_SEPARATOR, $directorioUsuario);

				if (!file_exists($directorioUsuario)) {
					mkdir($directorioUsuario, 0755, true);
				}

				// Eliminar foto anterior si existe (excepto default.png)
				$this->eliminarFotoAnterior($idUsuario);

				// Generar nombre único para evitar conflictos de caché
				$aleatorio = mt_rand(100, 999);
				$timestamp = time();

				if ($_FILES["nuevaImagen"]["type"] == "image/jpeg") {
					$nombreArchivo = "perfil_" . $timestamp . "_" . $aleatorio . ".jpg";
					$rutaCompleta = $directorioUsuario . DIRECTORY_SEPARATOR . $nombreArchivo;
					$origen = imagecreatefromjpeg($_FILES["nuevaImagen"]["tmp_name"]);
					$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);
					imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);
					imagejpeg($destino, $rutaCompleta);

					// Limpiar memoria
					imagedestroy($origen);
					imagedestroy($destino);
				} else if ($_FILES["nuevaImagen"]["type"] == "image/png") {
					$nombreArchivo = "perfil_" . $timestamp . "_" . $aleatorio . ".png";
					$rutaCompleta = $directorioUsuario . DIRECTORY_SEPARATOR . $nombreArchivo;
					$origen = imagecreatefrompng($_FILES["nuevaImagen"]["tmp_name"]);
					$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

					// Manejar transparencia para PNG
					imagealphablending($destino, FALSE);
					imagesavealpha($destino, TRUE);
					imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);
					imagepng($destino, $rutaCompleta);

					// Limpiar memoria
					imagedestroy($origen);
					imagedestroy($destino);
				}

				// Ruta relativa para guardar en BD (sin DOCUMENT_ROOT)
				$rutaBD = "storage/public/usuarios/" . $idUsuario . "/" . $nombreArchivo;

				// Actualizar en base de datos
				$tabla = "persona";
				$item = "foto";
				$respuesta = ModeloUsuarios::mdlActualizarUsuario($tabla, $idUsuario, $item, $rutaBD);

				if ($respuesta == "ok") {
					$rutaApp = ControladorGeneral::ctrRutaApp();
					echo '<script>
						window.location = "' . $rutaApp . '' . $pagina . '";
					</script>';
				} else {
					echo '<div class="alert alert-danger">Error al actualizar la foto de perfil.</div>';
				}
			}
		}
	}

	/*=============================================
	Eliminar foto anterior del usuario
	=============================================*/
	private function eliminarFotoAnterior($idUsuario)
	{
		// Obtener datos actuales del usuario
		$usuario = self::ctrMostrarUsuarios("id", $idUsuario);

		if ($usuario && !empty($usuario['foto'])) {
			$fotoActual = $usuario['foto'];

			// No eliminar la foto por defecto
			if (strpos($fotoActual, 'default.png') !== false) {
				return;
			}

			// Construir ruta completa del archivo actual
			$documentRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : 'C:\\xampp\\htdocs';
			$rutaCompleta = $documentRoot . "/factuonlinetraining/" . $fotoActual;
			$rutaCompleta = str_replace('/', DIRECTORY_SEPARATOR, $rutaCompleta);

			// Eliminar archivo si existe
			if (file_exists($rutaCompleta) && is_file($rutaCompleta)) {
				unlink($rutaCompleta);
			}
		}
	}	/*=============================================
	Actualizar Usuario completar datos perfil
=============================================*/
	public function ctrActualizarPerfilUsuario()
	{
		if (isset($_POST["nombreUsuario"])) {
			$rutaApp = ControladorGeneral::ctrRutaApp();
			$tabla = "persona";
			$datos = array(
				"id" => $_POST["idUsuario"],
				"nombre" => $_POST["nombreUsuario"],
				"email" => $_POST["email"],
				"pais" => $_POST["pais"],
				"ciudad" => $_POST["ciudad"],
				"biografia" => $_POST["biografia"]
			);
			$respuesta = ModeloUsuarios::mdlActualizarPerfilUsuario($tabla, $datos);
			if ($respuesta == "ok") {
				echo '<script>
						window.location = "' . $rutaApp . 'perfil";
					</script>';
			}
		}
	}

	/*--==========================================
	Procesar biografía del usuario para mostrar Ver más/Ver menos
	============================================--*/
	public static function ctrProcesarBiografiaUsuario($biografia, $maxWords = 15, $maxChars = 100)
	{
		if (empty($biografia)) {
			return [
				'bioShort' => '',
				'bioFull' => '',
				'showVerMas' => false
			];
		}

		$bioFull = $biografia;
		$bioShort = $biografia;
		$showVerMas = false;

		// Verificar si excede los límites
		if (str_word_count($biografia) > $maxWords || strlen($biografia) > $maxChars) {
			// Cortar por caracteres primero
			$bioShort = mb_substr($biografia, 0, $maxChars);

			// Luego verificar por palabras
			$words = explode(' ', $bioShort);
			if (count($words) > $maxWords) {
				$bioShort = implode(' ', array_slice($words, 0, $maxWords));
			}

			$showVerMas = true;
		}

		return [
			'bioShort' => $bioShort,
			'bioFull' => $bioFull,
			'showVerMas' => $showVerMas
		];
	}

	/*--==========================================
	Actualizar roles de usuario
	============================================--*/
	public static function ctrActualizarRolesUsuario()
	{
		if (isset($_POST['idUsuario']) && isset($_POST['roles'])) {
			$idUsuario = $_POST['idUsuario'];
			$rolesSeleccionados = $_POST['roles'];

			$respuesta = ModeloUsuarios::mdlActualizarRolesUsuario($idUsuario, $rolesSeleccionados);

			if ($respuesta == "ok") {
				return "ok";
			} else {
				return "error";
			}
		}
	}

	/*--==========================================
	Cargar datos para administración de usuarios
	============================================--*/
	public static function ctrCargarDatosUsuariosAdmin()
	{
		// Obtener todos los usuarios
		$usuarios = self::ctrMostrarusuarios(null, null);
		if (!$usuarios) {
			$usuarios = [];
		}

		// Obtener cursos para mostrar información adicional
		require_once "controladores/cursos.controlador.php";
		$cursos = ControladorCursos::ctrMostrarCursos(null, null);

		// Crear un array asociativo para facilitar el acceso a los cursos por profesor
		$cursosPorProfesor = [];
		if ($cursos) {
			// Si es un solo curso, convertirlo en array
			if (isset($cursos['id'])) {
				$cursos = [$cursos];
			}

			foreach ($cursos as $curso) {
				if (!isset($cursosPorProfesor[$curso['id_persona']])) {
					$cursosPorProfesor[$curso['id_persona']] = [];
				}
				$cursosPorProfesor[$curso['id_persona']][] = $curso;
			}
		}

		// Obtener todos los roles disponibles
		$roles = ModeloUsuarios::mdlObtenerRoles();

		// Obtener los roles por usuario
		$rolesPorUsuario = [];
		foreach ($usuarios as $usuario) {
			$rolesPorUsuario[$usuario["id"]] = ModeloUsuarios::mdlObtenerRolesPorUsuario($usuario["id"]);
		}

		return [
			'usuarios' => $usuarios,
			'cursosPorProfesor' => $cursosPorProfesor,
			'roles' => $roles,
			'rolesPorUsuario' => $rolesPorUsuario
		];
	}

	/*=============================================
	Validar y obtener URL de foto de usuario
	=============================================*/
	public static function ctrValidarFotoUsuario($rutaFoto)
	{
		// Si no hay foto asignada, devolver imagen por defecto de storage
		if (empty($rutaFoto) || $rutaFoto === null) {
			return '/factuonlinetraining/storage/public/usuarios/default.png';
		}

		// Construir la ruta completa del archivo
		// Determinar la ruta base del proyecto
		$documentRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : 'C:\\xampp\\htdocs';

		// Si la ruta ya incluye storage/, usar tal como está
		if (strpos($rutaFoto, 'storage/') === 0) {
			$rutaCompleta = $documentRoot . '/factuonlinetraining/' . $rutaFoto;
			$rutaPublica = '/factuonlinetraining/' . $rutaFoto;
		} else {
			// Para compatibilidad con rutas antiguas
			$rutaCompleta = $documentRoot . '/factuonlinetraining/App/' . $rutaFoto;
			$rutaPublica = '/factuonlinetraining/App/' . $rutaFoto;
		}

		// Convertir barras para Windows si es necesario
		$rutaCompleta = str_replace('/', DIRECTORY_SEPARATOR, $rutaCompleta);

		// Verificar si el archivo existe
		if (file_exists($rutaCompleta) && is_file($rutaCompleta)) {
			// Verificar que sea una imagen válida
			$infoImagen = @getimagesize($rutaCompleta);
			if ($infoImagen !== false) {
				return $rutaPublica; // La imagen existe y es válida
			}
		}

		// Si llegamos aquí, la imagen no existe o no es válida
		return '/factuonlinetraining/storage/public/usuarios/default.png';
	}

	/*=============================================
	Migrar fotos existentes a nueva estructura storage
	=============================================*/
	public static function ctrMigrarFotosUsuarios()
	{
		// Obtener todos los usuarios con fotos
		$usuarios = self::ctrMostrarUsuarios(null, null);

		if (!$usuarios) {
			return [
				'total_usuarios' => 0,
				'migrados' => 0,
				'errores' => []
			];
		}

		$migrados = 0;
		$errores = [];

		foreach ($usuarios as $usuario) {
			// Solo migrar si tiene foto y no está ya en storage
			if (!empty($usuario['foto']) && strpos($usuario['foto'], 'storage/') !== 0) {
				$documentRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : 'C:\\xampp\\htdocs';
				$rutaAntigua = $documentRoot . '/factuonlinetraining/App/' . $usuario['foto'];
				$rutaAntigua = str_replace('/', DIRECTORY_SEPARATOR, $rutaAntigua);

				if (file_exists($rutaAntigua)) {
					// Crear directorio del usuario
					$directorioNuevo = $documentRoot . '/factuonlinetraining/storage/public/usuarios/' . $usuario['id'];
					$directorioNuevo = str_replace('/', DIRECTORY_SEPARATOR, $directorioNuevo);

					if (!file_exists($directorioNuevo)) {
						mkdir($directorioNuevo, 0755, true);
					}

					// Generar nuevo nombre
					$extension = pathinfo($usuario['foto'], PATHINFO_EXTENSION);
					$nombreArchivo = 'perfil_migrado_' . time() . '.' . $extension;
					$rutaNueva = $directorioNuevo . DIRECTORY_SEPARATOR . $nombreArchivo;

					if (copy($rutaAntigua, $rutaNueva)) {
						// Actualizar ruta en base de datos
						$rutaBD = 'storage/public/usuarios/' . $usuario['id'] . '/' . $nombreArchivo;
						$respuesta = ModeloUsuarios::mdlActualizarUsuario('persona', $usuario['id'], 'foto', $rutaBD);

						if ($respuesta === 'ok') {
							$migrados++;
						} else {
							$errores[] = "No se pudo actualizar BD para usuario {$usuario['id']}";
						}
					} else {
						$errores[] = "No se pudo copiar foto del usuario {$usuario['id']}";
					}
				}
			}
		}

		return [
			'total_usuarios' => count($usuarios),
			'migrados' => $migrados,
			'errores' => $errores
		];
	}

	/*=============================================
	Obtener estudiantes con inscripciones pendientes para el profesor
	=============================================*/
	public static function ctrObtenerEstudiantesInscripcionesPendientes($idProfesor)
	{
		return ModeloUsuarios::mdlObtenerEstudiantesInscripcionesPendientes($idProfesor);
	}

	/*=============================================
	Obtener cursos con inscripciones pendientes de un estudiante
	=============================================*/
	public static function ctrObtenerCursosPendientesEstudiante($idEstudiante, $idProfesor)
	{
		return ModeloUsuarios::mdlObtenerCursosPendientesEstudiante($idEstudiante, $idProfesor);
	}

	/*=============================================
	Obtener cursos activos de un estudiante
	=============================================*/
	public static function ctrObtenerCursosActivosEstudiante($idEstudiante, $idProfesor)
	{
		return ModeloUsuarios::mdlObtenerCursosActivosEstudiante($idEstudiante, $idProfesor);
	}

	/*=============================================
	Cargar datos para gestión de estudiantes del profesor
	=============================================*/
	public static function ctrCargarDatosEstudiantesProfesor($idProfesor)
	{
		// Obtener estudiantes con inscripciones pendientes
		$estudiantes = self::ctrObtenerEstudiantesInscripcionesPendientes($idProfesor);

		// Para cada estudiante, obtener cursos pendientes y activos
		foreach ($estudiantes as &$estudiante) {
			$estudiante['cursos_pendientes'] = self::ctrObtenerCursosPendientesEstudiante($estudiante['id'], $idProfesor);
			$estudiante['cursos_activos'] = self::ctrObtenerCursosActivosEstudiante($estudiante['id'], $idProfesor);
			$estudiante['foto_validada'] = self::ctrValidarFotoUsuario($estudiante['foto']);
		}

		return [
			'estudiantes' => $estudiantes,
			'total_estudiantes' => count($estudiantes)
		];
	}

	/*=============================================
	Obtener estudiantes con preinscripciones e inscripciones detalladas
	=============================================*/
	public static function ctrObtenerEstudiantesConCursosProfesor($idProfesor)
	{
		$datos = ModeloUsuarios::mdlObtenerEstudiantesConCursosProfesor($idProfesor);

		// Procesar las fotos de los estudiantes y formatear datos
		foreach ($datos as &$registro) {
			$registro['estudiante_foto_validada'] = self::ctrValidarFotoUsuario($registro['estudiante_foto']);

			// Formatear fecha para mostrar
			$registro['fecha_formateada'] = date('d/m/Y H:i', strtotime($registro['fecha_registro']));

			// Crear iniciales para el avatar si no hay foto
			$nombres = explode(' ', $registro['estudiante_nombre']);
			$iniciales = '';
			foreach ($nombres as $nombre) {
				if (!empty($nombre)) {
					$iniciales .= strtoupper(substr($nombre, 0, 1));
				}
			}
			$registro['iniciales'] = substr($iniciales, 0, 2);
		}

		return $datos;
	}
}
