<?php

require_once "../controladores/general.controlador.php";
require_once "../controladores/usuarios.controlador.php";
require_once "../modelos/usuarios.modelo.php";

class AjaxUsuarios
{

	/*=============================================
	Validar email existente
	=============================================*/
	public $validarEmail;
	public function ajaxValidarEmail()
	{
		$item = "email";
		$valor = $this->validarEmail;
		$respuesta = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);
		echo json_encode($respuesta);
	}

	/*=============================================
	Validar email existente
	=============================================*/
	public $validarUsuario;
	public function ajaxValidarUsuario()
	{
		$item = "usuarioLink";
		$valor = $this->validarUsuario;
		$respuesta = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);
		echo json_encode($respuesta);
	}
}

/*=============================================
Validar email existente
=============================================*/
if (isset($_POST["validarEmail"])) {
	$valEmail = new AjaxUsuarios();
	$valEmail->validarEmail = $_POST["validarEmail"];
	$valEmail->ajaxValidarEmail();
}

/*=============================================
Validar usuario existente
=============================================*/
if (isset($_POST["validarUsuario"])) {
	$valUsuario = new AjaxUsuarios();
	$valUsuario->validarUsuario = $_POST["validarUsuario"];
	$valUsuario->ajaxValidarUsuario();
}

/*=============================================
Actualizar campo individual del perfil
=============================================*/
if (isset($_POST["accion"]) && $_POST["accion"] == "actualizar_campo") {

	// Verificar sesión
	session_start();
	if (!isset($_SESSION['idU'])) {
		echo json_encode(["success" => false, "message" => "Sesión no válida"]);
		exit;
	}

	if (isset($_POST["campo"]) && isset($_POST["valor"])) {
		$campo = $_POST["campo"];
		$valor = $_POST["valor"];
		$idUsuario = $_SESSION['idU'];		// Validar campos permitidos
		$camposPermitidos = ['nombre', 'email', 'pais', 'ciudad', 'contenido', 'profesion', 'biografia', 'telefono', 'direccion', 'numero_identificacion'];
		if (!in_array($campo, $camposPermitidos)) {
			echo json_encode(["success" => false, "message" => "Campo no permitido"]);
			exit;
		}

		// Validaciones específicas
		if (in_array($campo, ['nombre', 'email']) && empty(trim($valor))) {
			echo json_encode(["success" => false, "message" => "Este campo es requerido"]);
			exit;
		}

		// Validar formato de email
		if ($campo == 'email' && !filter_var($valor, FILTER_VALIDATE_EMAIL)) {
			echo json_encode(["success" => false, "message" => "Formato de email no válido"]);
			exit;
		}

		// Validar email único (solo si es diferente al actual)
		if ($campo == 'email') {
			$usuarioActual = ControladorUsuarios::ctrMostrarUsuarios("id", $idUsuario);
			if ($usuarioActual['email'] != $valor) {
				$emailExistente = ControladorUsuarios::ctrMostrarUsuarios("email", $valor);
				if ($emailExistente) {
					echo json_encode(["success" => false, "message" => "Este email ya está en uso"]);
					exit;
				}
			}
		}

		if ($campo == 'biografia' && strlen($valor) > 1000) {
			echo json_encode(["success" => false, "message" => "La biografía no puede exceder 1000 caracteres"]);
			exit;
		}

		// Actualizar campo - usar tabla persona en lugar de usuarios
		$tabla = "persona";
		$respuesta = ModeloUsuarios::mdlActualizarUsuario($tabla, $idUsuario, $campo, $valor);

		if ($respuesta == "ok") {
			echo json_encode(["success" => true, "message" => "Campo actualizado correctamente"]);
		} else {
			echo json_encode(["success" => false, "message" => "Error al actualizar el campo"]);
		}
	} else {
		echo json_encode(["success" => false, "message" => "Datos incompletos"]);
	}
}

/*=============================================
Actualizar configuración de privacidad
=============================================*/
if (isset($_POST["accion"]) && $_POST["accion"] == "actualizar_privacidad") {

	// Verificar sesión
	session_start();
	if (!isset($_SESSION['idU'])) {
		echo json_encode(["success" => false, "message" => "Sesión no válida"]);
		exit;
	}

	if (isset($_POST["configuracion"]) && isset($_POST["valor"])) {
		$configuracion = $_POST["configuracion"];
		$valor = (int)$_POST["valor"];
		$idUsuario = $_SESSION['idU'];		// Validar configuraciones permitidas
		$configuracionesPermitidas = ['mostrar_email', 'mostrar_telefono', 'mostrar_identificacion'];
		if (!in_array($configuracion, $configuracionesPermitidas)) {
			echo json_encode(["success" => false, "message" => "Configuración no permitida"]);
			exit;
		}

		// Actualizar configuración
		$tabla = "usuarios";
		$respuesta = ControladorUsuarios::ctrActualizarUsuario($idUsuario, $configuracion, $valor);

		if ($respuesta == "ok") {
			echo json_encode(["success" => true, "message" => "Configuración actualizada correctamente"]);
		} else {
			echo json_encode(["success" => false, "message" => "Error al actualizar la configuración"]);
		}
	} else {
		echo json_encode(["success" => false, "message" => "Datos incompletos"]);
	}
}

/*=============================================
Actualizar foto de perfil via AJAX
=============================================*/
if (isset($_POST["accion"]) && $_POST["accion"] == "actualizar_foto") {

	// Verificar sesión
	session_start();
	if (!isset($_SESSION['idU'])) {
		echo json_encode(["success" => false, "message" => "Sesión no válida"]);
		exit;
	}

	if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
		$idUsuario = $_SESSION['idU'];
		$archivo = $_FILES['imagen'];

		// Validar tipo de archivo
		$tiposPermitidos = ['image/jpeg', 'image/png'];
		if (!in_array($archivo['type'], $tiposPermitidos)) {
			echo json_encode(["success" => false, "message" => "Solo se permiten archivos JPG y PNG"]);
			exit;
		}

		// Validar tamaño (máximo 5MB)
		if ($archivo['size'] > 5 * 1024 * 1024) {
			echo json_encode(["success" => false, "message" => "El archivo es muy grande. Máximo 5MB permitido."]);
			exit;
		}

		// Crear directorio si no existe
		$directorio = "vistas/img/usuarios/" . $idUsuario;
		if (!file_exists($directorio)) {
			mkdir($directorio, 0755, true);
		}

		// Generar nombre único para el archivo
		$extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
		$nombreArchivo = uniqid() . '.' . $extension;
		$rutaCompleta = $directorio . "/" . $nombreArchivo;

		// Procesar y redimensionar imagen
		$imagen = null;
		if ($archivo['type'] == 'image/jpeg') {
			$imagen = imagecreatefromjpeg($archivo['tmp_name']);
		} elseif ($archivo['type'] == 'image/png') {
			$imagen = imagecreatefrompng($archivo['tmp_name']);
		}

		if ($imagen) {
			// Obtener dimensiones originales
			$anchoOriginal = imagesx($imagen);
			$altoOriginal = imagesy($imagen);

			// Calcular nuevas dimensiones (cuadrado de 200x200)
			$nuevoTamano = 200;
			$imagenRedimensionada = imagecreatetruecolor($nuevoTamano, $nuevoTamano);

			// Preservar transparencia para PNG
			if ($archivo['type'] == 'image/png') {
				imagealphablending($imagenRedimensionada, false);
				imagesavealpha($imagenRedimensionada, true);
			}

			// Redimensionar imagen
			imagecopyresampled(
				$imagenRedimensionada,
				$imagen,
				0,
				0,
				0,
				0,
				$nuevoTamano,
				$nuevoTamano,
				$anchoOriginal,
				$altoOriginal
			);

			// Guardar imagen
			$guardado = false;
			if ($archivo['type'] == 'image/jpeg') {
				$guardado = imagejpeg($imagenRedimensionada, $rutaCompleta, 90);
			} elseif ($archivo['type'] == 'image/png') {
				$guardado = imagepng($imagenRedimensionada, $rutaCompleta);
			}

			// Limpiar memoria
			imagedestroy($imagen);
			imagedestroy($imagenRedimensionada);

			if ($guardado) {
				// Actualizar ruta en base de datos
				$tabla = "persona";
				$respuesta = ModeloUsuarios::mdlActualizarUsuario($tabla, $idUsuario, "foto", $rutaCompleta);

				if ($respuesta == "ok") {
					echo json_encode([
						"success" => true,
						"message" => "Foto actualizada correctamente",
						"nueva_ruta" => $rutaCompleta
					]);
				} else {
					echo json_encode(["success" => false, "message" => "Error al actualizar la foto en base de datos"]);
				}
			} else {
				echo json_encode(["success" => false, "message" => "Error al procesar la imagen"]);
			}
		} else {
			echo json_encode(["success" => false, "message" => "Error al leer el archivo de imagen"]);
		}
	} else {
		echo json_encode(["success" => false, "message" => "No se recibió ningún archivo válido"]);
	}
}

/*=============================================
Actualizar configuración de privacidad
=============================================*/
if (isset($_POST["action"]) && $_POST["action"] == "actualizar_privacidad") {

	// Verificar sesión
	session_start();
	if (!isset($_SESSION['idU'])) {
		echo json_encode(["success" => false, "message" => "Sesión no válida"]);
		exit;
	}

	if (isset($_POST["campo"]) && isset($_POST["valor"])) {
		$campo = $_POST["campo"];
		$valor = (int)$_POST["valor"];
		$idUsuario = $_SESSION['idU'];

		// Validar configuraciones permitidas
		$configuracionesPermitidas = ['mostrar_email', 'mostrar_telefono', 'mostrar_identificacion'];
		if (!in_array($campo, $configuracionesPermitidas)) {
			echo json_encode(["success" => false, "message" => "Configuración no permitida"]);
			exit;
		}

		// Actualizar configuración en tabla persona
		$tabla = "persona";
		$respuesta = ModeloUsuarios::mdlActualizarUsuario($tabla, $idUsuario, $campo, $valor);

		if ($respuesta == "ok") {
			echo json_encode(["success" => true, "message" => "Configuración actualizada correctamente"]);
		} else {
			echo json_encode(["success" => false, "message" => "Error al actualizar la configuración"]);
		}
	} else {
		echo json_encode(["success" => false, "message" => "Datos incompletos"]);
	}
}
