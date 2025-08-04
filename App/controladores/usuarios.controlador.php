<?php



use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;


require_once $_SERVER['DOCUMENT_ROOT'] . "/cursosApp/App/modelos/usuarios.modelo.php";

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

		$tabla = "usuarios";

		$respuesta = ModeloUsuarios::mdlActualizarUsuario($tabla, $id, $item, $valor);

		return $respuesta;
	}

	/*=============================================
	Cambiar foto perfil
=============================================*/
	public function ctrCambiarFoto()
	{
		if (isset($_POST["idClienteImagen"])) {
			$pagina = $_POST["pagina"]; // pagina de retorono
			if (isset($_FILES["nuevaImagen"]["tmp_name"]) && !empty($_FILES["nuevaImagen"]["tmp_name"])) {
				list($ancho, $alto) = getimagesize($_FILES["nuevaImagen"]["tmp_name"]);
				$nuevoAncho = 500;
				$nuevoAlto = 500;
				/*=============================================
CREAMOS EL DIRECTORIO DONDE VAMOS A GUARDAR LA FOTO DEL USUARIO
=============================================*/
				$directorio = "vistas/img/usuarios/" . $_POST["idClienteImagen"];
				/*=============================================
PRIMERO PREGUNTAMOS SI EXISTE OTRA IMAGEN EN LA BD Y EL CARPETA
=============================================*/
				// if($ruta != ""){
				// 	unlink($ruta);
				// }else{
				if (!file_exists($directorio)) {
					mkdir($directorio, 0755);
				}
				//}


				/*=============================================
DE ACUERDO AL TIPO DE IMAGEN APLICAMOS LAS FUNCIONES POR DEFECTO DE PHP
=============================================*/
				if ($_FILES["nuevaImagen"]["type"] == "image/jpeg") {
					$aleatorio = mt_rand(100, 999);
					$ruta = $directorio . "/" . $aleatorio . ".jpg";
					$origen = imagecreatefromjpeg($_FILES["nuevaImagen"]["tmp_name"]);
					$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);
					imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);
					imagejpeg($destino, $ruta);
				} else if ($_FILES["nuevaImagen"]["type"] == "image/png") {
					$aleatorio = mt_rand(100, 999);
					$ruta = $directorio . "/" . $aleatorio . ".png";
					$origen = imagecreatefrompng($_FILES["nuevaImagen"]["tmp_name"]);
					$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);
					imagealphablending($destino, FALSE);
					imagesavealpha($destino, TRUE);
					imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);
					imagepng($destino, $ruta);
				} else {
					echo '<div class="alert alert-danger">¡Solo formatos de imagen JPG y/o PNG!</div>';
					return;
				}
				// final condicion
				$rutaApp = ControladorGeneral::ctrRutaApp();
				$tabla = "usuarios";
				$id = $_POST["idClienteImagen"];
				$item = "foto";
				$valor = $ruta;
				$respuesta = ModeloUsuarios::mdlActualizarUsuario($tabla, $id, $item, $valor);
				if ($respuesta == "ok") {
					echo '<script>
						window.location = "' . $rutaApp . '' . $pagina . '";
					</script>';
				}
			}
		}
	}

	/*=============================================
	Actualizar Usuario completar datos perfil
=============================================*/
	public function ctrActualizarPerfilUsuario()
	{
		if (isset($_POST["nombreUsuario"])) {
			$rutaApp = ControladorGeneral::ctrRutaApp();
			$tabla = "usuarios";
			$datos = array(
				"id" => $_POST["idUsuario"],
				"nombre" => $_POST["nombreUsuario"],
				"email" => $_POST["email"],
				"pais" => $_POST["pais"],
				"ciudad" => $_POST["ciudad"],
				"contenido" => $_POST["contenido"]
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
}
