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
		$item = null;
		$valor = null;
		$rutaInicio = ControladorGeneral::ctrRuta();
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

	/*--==========================================
	Consultar los datos de un curso específico
	============================================--*/
	public static function ctrConsultarUnCurso($item, $valor, $tabla)
	{
		$resul = ModeloCursos::mdlMostrarCursos($tabla, $item, $valor);
		return $resul;
	}

	/*--==========================================
	Obtener todos los datos del curso para la vista
	============================================--*/
	public static function ctrObtenerDatosCursoCompleto($item, $valor)
	{
		// Obtener datos del curso
		$curso = self::ctrMostrarCursos($item, $valor);

		if (!$curso) {
			return null; // Curso no encontrado
		}

		// Obtener datos de la categoría
		$categoria = self::ctrConsultarUnCurso("id", $curso["id_categoria"], "categoria");

		// Obtener datos del profesor
		$profesor = self::ctrConsultarUnCurso("id", $curso["id_persona"], "persona");

		// Procesar biografía del profesor
		$bioData = self::ctrProcesarBiografiaProfesor($profesor["biografia"] ?? '');

		// Obtener y procesar todas las categorías
		$todosCursos = self::ctrMostrarCursos(null, null);
		$categorias = self::procesarCategorias($todosCursos);

		// Procesar los campos de viñetas del curso
		$aprendizajes = self::procesarViñetas($curso["lo_que_aprenderas"] ?? '');
		$requisitos = self::procesarViñetas($curso["requisitos"] ?? '');
		$paraQuien = self::procesarViñetas($curso["para_quien"] ?? '');

		return [
			'curso' => $curso,
			'categoria' => $categoria,
			'profesor' => array_merge($profesor, ['bioData' => $bioData]),
			'categorias' => $categorias,
			'aprendizajes' => $aprendizajes,
			'requisitos' => $requisitos,
			'paraQuien' => $paraQuien
		];
	}

	/*--==========================================
	Procesar categorías únicas
	============================================--*/
	private static function procesarCategorias($todosCursos)
	{
		$categorias = [];
		$categoriasVistas = [];

		foreach ($todosCursos as $cursoTemp) {
			if (!in_array($cursoTemp["id_categoria"], $categoriasVistas)) {
				$categorias[] = self::ctrConsultarUnCurso("id", $cursoTemp["id_categoria"], "categoria");
				$categoriasVistas[] = $cursoTemp["id_categoria"];
			}
		}

		return $categorias;
	}

	/*--==========================================
	Procesar texto de viñetas (separado por \n)
	============================================--*/
	private static function procesarViñetas($texto)
	{
		if (empty($texto)) {
			return [];
		}

		$items = explode("\n", $texto);
		$viñetas = [];

		foreach ($items as $item) {
			$item = trim($item);
			if ($item !== '') {
				$viñetas[] = htmlspecialchars($item);
			}
		}

		return $viñetas;
	}

	/*--==========================================
	Procesar biografía del profesor para vista (mantiene compatibilidad)
	============================================--*/
	public static function ctrProcesarBiografiaProfesor($biografia, $maxWords = 40, $maxChars = 226)
	{
		// Verificar si existe el controlador de usuarios, si no, incluirlo
		if (!class_exists('ControladorUsuarios')) {
			require_once $_SERVER['DOCUMENT_ROOT'] . "/cursosApp/App/controladores/usuarios.controlador.php";
		}

		// Delegar al método del controlador de usuarios
		return ControladorUsuarios::ctrProcesarBiografiaUsuario($biografia, $maxWords, $maxChars);
	}

	/*--==========================================
	Gestión de secciones del curso
	============================================--*/
	public static function ctrCrearSeccion($datos)
	{
		$respuesta = ModeloCursos::mdlCrearSeccion($datos);
		return $respuesta;
	}

	public static function ctrActualizarSeccion($datos)
	{
		$respuesta = ModeloCursos::mdlActualizarSeccion($datos);
		return $respuesta;
	}

	public static function ctrEliminarSeccion($id)
	{
		$respuesta = ModeloCursos::mdlEliminarSeccion($id);
		return $respuesta;
	}

	/*--==========================================
	Gestión de contenido de secciones
	============================================--*/
	public static function ctrCrearContenido($datos)
	{
		$respuesta = ModeloCursos::mdlCrearContenido($datos);
		return $respuesta;
	}

	public static function ctrActualizarContenido($datos)
	{
		$respuesta = ModeloCursos::mdlActualizarContenido($datos);
		return $respuesta;
	}

	public static function ctrEliminarContenido($id)
	{
		$respuesta = ModeloCursos::mdlEliminarContenido($id);
		return $respuesta;
	}

	/*--==========================================
	Subir archivos para contenido
	============================================--*/
	public static function ctrSubirArchivoContenido($archivo, $tipo)
	{
		if (isset($archivo) && $archivo['error'] == 0) {
			$extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
			$nombreArchivo = uniqid() . '_' . time() . '.' . $extension;

			// Directorio según el tipo
			$directorio = $tipo == 'video' ? 'vistas/videos/' : 'vistas/documentos/';

			if (!file_exists($directorio)) {
				mkdir($directorio, 0777, true);
			}

			$rutaDestino = $directorio . $nombreArchivo;

			if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
				return $rutaDestino;
			}
		}

		return false;
	}
}
