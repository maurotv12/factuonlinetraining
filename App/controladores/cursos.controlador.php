<?php

/**
@grcarvajal grcarvajal@gmail.com **Gildardo Restrepo Carvajal**
12/06/2022 Plataforma Calibelula mostrar Cursos
Controlador de cursos registro
 */
class ControladorCursos
{

	/*=============================================
	Mostrar Cursos
=============================================*/
	public static function ctrMostrarCursos($item, $valor)
	{
		$tabla = "curso";
		$respuesta = ModeloCursos::mdlMostrarCursos($tabla, $item, $valor);
		return $respuesta;
	}

	public static function ctrObtenerCategorias()
	{
		$conn = Conexion::conectar();
		$stmt = $conn->query("SELECT id, nombre FROM categoria");
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * Obtener usuarios con rol de profesor
	 * @return array Lista de profesores con su información
	 */
	public static function ctrObtenerProfesores()
	{
		// Verificar si existe el modelo de usuarios, si no, incluirlo
		if (!class_exists('ModeloUsuarios')) {
			require_once $_SERVER['DOCUMENT_ROOT'] . "/cursosApp/App/modelos/usuarios.modelo.php";
		}

		// Obtener conexión a base de datos
		$conexion = Conexion::conectar();

		// Consulta SQL para obtener usuarios con rol de profesor
		// Esta consulta obtiene los datos de las personas que tienen el rol de profesor
		$stmt = $conexion->prepare(
			"SELECT p.id, p.nombre, p.email, p.foto
			 FROM persona p 
			 INNER JOIN persona_roles pr ON p.id = pr.id_persona
			 INNER JOIN roles r ON pr.id_rol = r.id
			 WHERE r.nombre = 'profesor'
			 ORDER BY p.nombre ASC"
		);

		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}


	public static function ctrCrearCurso($datos)
	{
		// Inicializar valores predeterminados para banner y video
		$datos['banner'] = null;
		$datos['promo_video'] = null;

		// Procesar la imagen del curso si existe y con validación de tamaño
		if (isset($datos['imagen']) && $datos['imagen']['error'] == 0) {
			$dimensiones = getimagesize($datos['imagen']['tmp_name']);
			$ancho = $dimensiones[0];
			$alto = $dimensiones[1];

			if ($ancho == 600 && $alto == 400) {
				$directorio = "vistas/img/cursos/";
				if (!file_exists($directorio)) mkdir($directorio, 0777, true);
				$nombreImg = uniqid() . "_" . $datos['imagen']['name'];
				$rutaImg = $directorio . $nombreImg;

				if (move_uploaded_file($datos['imagen']['tmp_name'], $rutaImg)) {
					$datos['banner'] = $rutaImg;
				}
			} else {
				// Si la imagen no cumple con las dimensiones requeridas
				return "error_dimensiones";
			}
		}



		// if (isset($datos['imagen']) && $datos['imagen']['error'] == 0) {
		// 	$directorio = "vistas/img/cursos/";
		// 	if (!file_exists($directorio)) mkdir($directorio, 0777, true);
		// 	$nombreImg = uniqid() . "_" . $datos['imagen']['name'];
		// 	$rutaImg = $directorio . $nombreImg;
		// 	if (move_uploaded_file($datos['imagen']['tmp_name'], $rutaImg)) {
		// 		$datos['banner'] = $rutaImg;
		// 	}
		// }

		// Procesar el video promocional si existe
		if (isset($datos['video']) && !empty($datos['video']['name']) && $datos['video']['error'] == 0) {
			$directorioVideo = "videosPromos/";
			if (!file_exists($directorioVideo)) mkdir($directorioVideo, 0777, true);
			$nombreVideo = uniqid() . "_" . $datos['video']['name'];
			$rutaVideo = $directorioVideo . $nombreVideo;
			if (move_uploaded_file($datos['video']['tmp_name'], $rutaVideo)) {
				$datos['promo_video'] = $rutaVideo;
			}
		}

		// Eliminar las variables imagen y video para no enviarlas al modelo
		unset($datos['imagen']);
		unset($datos['video']);

		$tabla = "curso";
		$respuesta = ModeloCursos::mdlCrearCurso($tabla, $datos);
		return $respuesta;
	}
}
