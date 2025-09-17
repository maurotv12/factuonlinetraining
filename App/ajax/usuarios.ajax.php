<?php

require_once "../controladores/general.controlador.php";
require_once "../controladores/usuarios.controlador.php";
require_once "../modelos/usuarios.modelo.php";

/*=============================================
Función auxiliar para eliminar foto anterior
=============================================*/
function eliminarFotoAnteriorAjax($idUsuario)
{
	// Obtener datos actuales del usuario
	$usuario = ControladorUsuarios::ctrMostrarUsuarios("id", $idUsuario);

	if ($usuario && !empty($usuario['foto'])) {
		$fotoActual = $usuario['foto'];

		// No eliminar la foto por defecto
		if (strpos($fotoActual, 'default.png') !== false) {
			return;
		}

		// Construir ruta completa del archivo actual
		$rutaCompleta = $_SERVER['DOCUMENT_ROOT'] . "/cursosApp/" . $fotoActual;

		// Eliminar archivo si existe
		if (file_exists($rutaCompleta) && is_file($rutaCompleta)) {
			unlink($rutaCompleta);
		}
	}
}

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
	Validar usuario existente
	=============================================*/
	public $validarUsuario;
	public function ajaxValidarUsuario()
	{
		$item = "usuarioLink";
		$valor = $this->validarUsuario;
		$respuesta = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);
		echo json_encode($respuesta);
	}

	/*=============================================
	Obtener foto de usuario validada
	=============================================*/
	public $idUsuario;
	public function ajaxObtenerFotoUsuario()
	{
		$usuario = ControladorUsuarios::ctrMostrarUsuarios("id", $this->idUsuario);
		if ($usuario) {
			$fotoValidada = ControladorUsuarios::ctrValidarFotoUsuario($usuario['foto']);
			echo json_encode([
				"success" => true,
				"foto" => $fotoValidada
			]);
		} else {
			echo json_encode([
				"success" => false,
				"foto" => "/cursosApp/storage/public/usuarios/default.png"
			]);
		}
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
Obtener foto de usuario validada
=============================================*/
if (isset($_POST["obtenerFotoUsuario"])) {
	$obtenerFoto = new AjaxUsuarios();
	$obtenerFoto->idUsuario = $_POST["obtenerFotoUsuario"];
	$obtenerFoto->ajaxObtenerFotoUsuario();
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

		if ($campo == 'biografia' && strlen($valor) > 10000) {
			echo json_encode(["success" => false, "message" => "La biografía no puede exceder 10000 caracteres"]);
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

		// Crear directorio del usuario en nueva estructura storage
		$directorioUsuario = $_SERVER['DOCUMENT_ROOT'] . "/cursosApp/storage/public/usuarios/" . $idUsuario;

		if (!file_exists($directorioUsuario)) {
			mkdir($directorioUsuario, 0755, true);
		}

		// Eliminar foto anterior si existe (excepto default.png)
		eliminarFotoAnteriorAjax($idUsuario);

		// Generar nombre único para evitar conflictos de caché
		$aleatorio = mt_rand(100, 999);
		$timestamp = time();
		$extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
		$nombreArchivo = "perfil_" . $timestamp . "_" . $aleatorio . "." . $extension;
		$rutaCompleta = $directorioUsuario . "/" . $nombreArchivo;

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

			// Calcular nuevas dimensiones (cuadrado de 500x500 para consistencia)
			$nuevoTamano = 500;
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
				// Ruta relativa para guardar en BD (sin DOCUMENT_ROOT)
				$rutaBD = "storage/public/usuarios/" . $idUsuario . "/" . $nombreArchivo;

				// Actualizar ruta en base de datos
				$tabla = "persona";
				$respuesta = ModeloUsuarios::mdlActualizarUsuario($tabla, $idUsuario, "foto", $rutaBD);

				if ($respuesta == "ok") {
					echo json_encode([
						"success" => true,
						"message" => "Foto actualizada correctamente",
						"nueva_ruta" => "/cursosApp/" . $rutaBD
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

/*=============================================
Cargar estudiantes con inscripciones pendientes
=============================================*/
if (isset($_POST["accion"]) && $_POST["accion"] == "cargar_estudiantes_pendientes") {
	session_start();

	if (!isset($_SESSION['idU'])) {
		echo json_encode(["success" => false, "message" => "Sesión no válida"]);
		exit;
	}

	// Verificar que el usuario sea profesor
	if (!ControladorGeneral::ctrUsuarioTieneAlgunRol(['profesor', 'admin'])) {
		echo json_encode(["success" => false, "message" => "No tienes permisos para esta acción"]);
		exit;
	}

	$datos = ControladorUsuarios::ctrCargarDatosEstudiantesProfesor($_SESSION['idU']);
	echo json_encode(["success" => true, "data" => $datos]);
}

/*=============================================
Obtener cursos pendientes de un estudiante
=============================================*/
if (isset($_POST["accion"]) && $_POST["accion"] == "obtener_cursos_pendientes") {
	session_start();

	if (!isset($_SESSION['idU']) || !isset($_POST["idEstudiante"])) {
		echo json_encode(["success" => false, "message" => "Datos incompletos"]);
		exit;
	}

	// Verificar que el usuario sea profesor
	if (!ControladorGeneral::ctrUsuarioTieneAlgunRol(['profesor', 'admin'])) {
		echo json_encode(["success" => false, "message" => "No tienes permisos para esta acción"]);
		exit;
	}

	$idEstudiante = $_POST["idEstudiante"];
	$cursos = ControladorUsuarios::ctrObtenerCursosPendientesEstudiante($idEstudiante, $_SESSION['idU']);

	echo json_encode(["success" => true, "cursos" => $cursos]);
}

/*=============================================
Obtener cursos activos de un estudiante
=============================================*/
if (isset($_POST["accion"]) && $_POST["accion"] == "obtener_cursos_activos") {
	session_start();

	if (!isset($_SESSION['idU']) || !isset($_POST["idEstudiante"])) {
		echo json_encode(["success" => false, "message" => "Datos incompletos"]);
		exit;
	}

	// Verificar que el usuario sea profesor
	if (!ControladorGeneral::ctrUsuarioTieneAlgunRol(['profesor', 'admin'])) {
		echo json_encode(["success" => false, "message" => "No tienes permisos para esta acción"]);
		exit;
	}

	$idEstudiante = $_POST["idEstudiante"];
	$cursos = ControladorUsuarios::ctrObtenerCursosActivosEstudiante($idEstudiante, $_SESSION['idU']);

	echo json_encode(["success" => true, "cursos" => $cursos]);
}

/*=============================================
Obtener estudiantes con cursos detallados del profesor
=============================================*/
if (isset($_POST["accion"]) && $_POST["accion"] == "obtener_estudiantes_cursos_profesor") {
	session_start();

	if (!isset($_SESSION['idU'])) {
		echo json_encode(["success" => false, "message" => "No hay sesión activa"]);
		exit;
	}

	// Verificar que el usuario sea profesor
	if (!ControladorGeneral::ctrUsuarioTieneAlgunRol(['profesor', 'admin'])) {
		echo json_encode(["success" => false, "message" => "No tienes permisos para esta acción"]);
		exit;
	}

	$datos = ControladorUsuarios::ctrObtenerEstudiantesConCursosProfesor($_SESSION['idU']);
	echo json_encode(["success" => true, "data" => $datos]);
}

/*=============================================
Activar inscripción de estudiante
=============================================*/
if (isset($_POST["accion"]) && $_POST["accion"] == "activar_inscripcion") {
	session_start();

	if (!isset($_SESSION['idU']) || !isset($_POST["idInscripcion"])) {
		echo json_encode(["success" => false, "message" => "Datos incompletos"]);
		exit;
	}

	// Verificar que el usuario sea profesor
	if (!ControladorGeneral::ctrUsuarioTieneAlgunRol(['profesor', 'admin'])) {
		echo json_encode(["success" => false, "message" => "No tienes permisos para esta acción"]);
		exit;
	}

	// Incluir controlador de inscripciones
	require_once "../controladores/inscripciones.controlador.php";

	$idInscripcion = $_POST["idInscripcion"];
	$respuesta = ControladorInscripciones::ctrActualizarEstadoInscripcion($idInscripcion, 'activo');

	if ($respuesta['success']) {
		echo json_encode(["success" => true, "message" => "Inscripción activada correctamente"]);
	} else {
		echo json_encode(["success" => false, "message" => $respuesta['mensaje']]);
	}
}
