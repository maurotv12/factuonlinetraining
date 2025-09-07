<?php

/**
@grcarvajal grcarvajal@gmail.com **Gildardo Restrepo Carvajal**
12/06/2022 Plataforma Calibelula mostrar Cursos
Controlador de cursos registro
 */

// Incluir modelo de cursos - Ruta adaptativa para AJAX
if (file_exists("modelos/cursos.modelo.php")) {
	require_once "modelos/cursos.modelo.php";
} else {
	require_once "../modelos/cursos.modelo.php";
}

class ControladorCursos
{

	/*=============================================
	Mostrar Cursos
=============================================*/
	/**
	 * Mostrar Cursos con normalizacion de resultados
	 * @param string|null $item Campo para filtrar
	 * @param mixed|null $valor Valor para el filtro
	 * @return array|null Devuelve array de cursos o null
	 */
	public static function ctrMostrarCursos($item, $valor)
	{
		$tabla = "curso";
		// $item = null;
		// $valor = null;
		$rutaInicio = ControladorGeneral::ctrRuta();
		$respuesta = ModeloCursos::mdlMostrarCursos($tabla, $item, $valor);

		// Normalizar resultado para garantizar formato consistente
		if ($respuesta === false || $respuesta === null) {
			return null;
		}

		// Si es un único registro (array asociativo sin índice numérico en primer nivel)
		if (is_array($respuesta) && !isset($respuesta[0]) && !empty($respuesta)) {
			// Verificar si tiene índices duplicados (asociativos y numéricos)
			// Si es así, filtrar solo las claves asociativas
			$resultado = [];
			foreach ($respuesta as $key => $value) {
				if (!is_numeric($key)) {
					$resultado[$key] = $value;
				}
			}
			return [$resultado]; // Devolver como array de elementos para consistencia
		}

		return $respuesta; // Ya es un array de elementos
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

	/*=============================================
	Crear directorio storage si no existe
	=============================================*/
	private static function crearDirectorioStorage($subdirectorio)
	{
		// Determinar la ruta base del proyecto
		$documentRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : 'C:\\xampp\\htdocs';

		$rutaCompleta = $documentRoot . "/cursosApp/storage/public/" . $subdirectorio;

		// Convertir barras para Windows si es necesario
		$rutaCompleta = str_replace('/', DIRECTORY_SEPARATOR, $rutaCompleta);

		if (!file_exists($rutaCompleta)) {
			mkdir($rutaCompleta, 0755, true);
		}

		return $rutaCompleta;
	}

	/*=============================================
	Validar y procesar imagen del banner - SOLO STORAGE
	=============================================*/
	private static function procesarImagenBanner($imagen)
	{
		if (!isset($imagen) || $imagen['error'] != 0) {
			return null;
		}

		// Validar dimensiones
		$dimensiones = getimagesize($imagen['tmp_name']);
		$ancho = $dimensiones[0];
		$alto = $dimensiones[1];

		if ($ancho != 600 || $alto != 400) {
			return "error_dimensiones";
		}

		// Crear directorio de banners en storage
		$directorioCompleto = self::crearDirectorioStorage("banners");

		// Generar nombre único para el archivo
		$extension = strtolower(pathinfo($imagen['name'], PATHINFO_EXTENSION));
		$nombreImg = uniqid() . "_" . time() . "." . $extension;
		$rutaCompleta = $directorioCompleto . DIRECTORY_SEPARATOR . $nombreImg;

		// Mover archivo
		if (move_uploaded_file($imagen['tmp_name'], $rutaCompleta)) {
			// Devolver SOLO ruta relativa para storage
			return "storage/public/banners/" . $nombreImg;
		}

		return null;
	}

	/*=============================================
	Validar y procesar video promocional - SOLO STORAGE
	=============================================*/
	private static function procesarVideoPromo($video)
	{
		if (!isset($video) || empty($video['name']) || $video['error'] != 0) {
			return null;
		}

		// Validar extensión de video
		$extension = strtolower(pathinfo($video['name'], PATHINFO_EXTENSION));
		$extensionesPermitidas = ['mp4'];

		if (!in_array($extension, $extensionesPermitidas)) {
			return "formato_invalido";
		}

		// Validar tamaño (máximo 15MB para videos HD/FHD)
		if ($video['size'] > 15 * 1024 * 1024) {
			return "archivo_grande";
		}

		// Crear directorio de videos promocionales en storage
		$directorioCompleto = self::crearDirectorioStorage("promoVideos");

		// Generar nombre único para el archivo
		$nombreVideo = uniqid() . "_" . time() . "." . $extension;
		$rutaCompleta = $directorioCompleto . DIRECTORY_SEPARATOR . $nombreVideo;

		// Mover archivo temporalmente para validaciones
		if (!move_uploaded_file($video['tmp_name'], $rutaCompleta)) {
			return null;
		}

		// Validar propiedades del video usando getID3 o métodos nativos
		$validacionVideo = self::validarPropiedadesVideo($rutaCompleta);

		if ($validacionVideo !== true) {
			// Eliminar archivo temporal si no pasa validaciones
			if (file_exists($rutaCompleta)) {
				unlink($rutaCompleta);
			}
			return $validacionVideo;
		}

		// Si llegamos aquí, el video es válido
		// Devolver ruta relativa para guardar en BD
		return "storage/public/promoVideos/" . $nombreVideo;
	}

	/*=============================================
	Validar propiedades del video (resolución y duración)
	=============================================*/
	private static function validarPropiedadesVideo($rutaArchivo)
	{
		// Verificar si ffprobe está disponible (recomendado para producción)
		$ffprobeOutput = @shell_exec("ffprobe -v quiet -print_format json -show_format -show_streams \"$rutaArchivo\" 2>&1");

		if ($ffprobeOutput && $videoInfo = json_decode($ffprobeOutput, true)) {
			// Usar ffprobe para validaciones precisas
			foreach ($videoInfo['streams'] as $stream) {
				if ($stream['codec_type'] === 'video') {
					$width = $stream['width'] ?? 0;
					$height = $stream['height'] ?? 0;
					$duration = floatval($videoInfo['format']['duration'] ?? 0);

					// Validar resolución (HD: 1280x720, y algunas variaciones comunes)
					$resolucionValida = self::esResolucionValida($width, $height);
					if (!$resolucionValida) {
						return "resolucion_invalida";
					}

					// Validar duración (máximo 5 minutos = 300 segundos)
					if ($duration > 300) {
						return "duracion_excedida";
					}

					return true;
				}
			}
		}

		// Fallback usando getimagesize para información básica (limitado)
		try {
			$videoData = @getimagesize($rutaArchivo);
			if ($videoData !== false && isset($videoData[0], $videoData[1])) {
				$width = $videoData[0];
				$height = $videoData[1];

				if (!self::esResolucionValida($width, $height)) {
					return "resolucion_invalida";
				}

				// Sin ffprobe no podemos validar duración exacta, asumir válida
				return true;
			}
		} catch (Exception $e) {
			// Error al leer el archivo
			return "archivo_corrupto";
		}

		// Si no podemos validar, permitir pero advertir
		return true;
	}

	/*=============================================
	Verificar si la resolución es HD o FHD válida
	=============================================*/
	private static function esResolucionValida($width, $height)
	{
		// Resoluciones HD y FHD aceptadas
		$resolucionesValidas = [
			// HD (720p)
			[1280, 720],   // HD estándar
			[1366, 768],   // HD común en laptops
			[1440, 900],   // HD+ widescreen
			// Orientaciones verticales (para contenido móvil)
			[720, 1280],   // HD vertical
		];

		foreach ($resolucionesValidas as $resolucion) {
			if ($width == $resolucion[0] && $height == $resolucion[1]) {
				return true;
			}
		}

		return false;
	}

	/*=============================================
	Eliminar archivo anterior cuando se actualiza
	=============================================*/
	private static function eliminarArchivoAnterior($rutaArchivo)
	{
		if (empty($rutaArchivo)) {
			return false;
		}

		// Determinar la ruta base del proyecto
		$documentRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : 'C:\\xampp\\htdocs';

		// Construir ruta completa
		$rutaCompleta = $documentRoot . "/cursosApp/" . $rutaArchivo;

		// Convertir barras para Windows si es necesario
		$rutaCompleta = str_replace('/', DIRECTORY_SEPARATOR, $rutaCompleta);

		// Verificar que el archivo existe y eliminarlo
		if (file_exists($rutaCompleta) && is_file($rutaCompleta)) {
			// No eliminar la imagen por defecto
			if (strpos($rutaArchivo, 'default') === false) {
				unlink($rutaCompleta);
				return true;
			}
		}

		return false;
	}

	public static function ctrCrearCurso($datos)
	{
		// Inicializar valores predeterminados para banner y video
		$datos['banner'] = null;
		$datos['promo_video'] = null;

		// Procesar la imagen del banner
		if (isset($datos['imagen']) && $datos['imagen']['error'] == 0) {
			$resultadoImagen = self::procesarImagenBanner($datos['imagen']);

			if ($resultadoImagen === "error_dimensiones") {
				return "error_dimensiones";
			} elseif ($resultadoImagen) {
				$datos['banner'] = $resultadoImagen;
			}
		}

		// Procesar el video promocional
		if (isset($datos['video']) && !empty($datos['video']['name']) && $datos['video']['error'] == 0) {
			$resultadoVideo = self::procesarVideoPromo($datos['video']);

			if ($resultadoVideo === "formato_invalido") {
				return "formato_invalido";
			} elseif ($resultadoVideo === "archivo_grande") {
				return "archivo_grande";
			} elseif ($resultadoVideo === "resolucion_invalida") {
				return "resolucion_invalida";
			} elseif ($resultadoVideo === "duracion_excedida") {
				return "duracion_excedida";
			} elseif ($resultadoVideo === "archivo_corrupto") {
				return "archivo_corrupto";
			} elseif ($resultadoVideo) {
				$datos['promo_video'] = $resultadoVideo;
			}
		}

		// Eliminar las variables imagen y video para no enviarlas al modelo
		unset($datos['imagen']);
		unset($datos['video']);

		$tabla = "curso";
		$respuesta = ModeloCursos::mdlCrearCurso($tabla, $datos);
		return $respuesta;
	}

	/*=============================================
	Actualizar curso con manejo de archivos
	=============================================*/
	public static function ctrActualizarCurso($datos)
	{
		$datosActualizacion = $datos;

		// Procesar nueva imagen si se proporcionó
		if (isset($datos['imagen']) && $datos['imagen']['error'] == 0) {
			$resultadoImagen = self::procesarImagenBanner($datos['imagen']);

			if ($resultadoImagen === "error_dimensiones") {
				return "error_dimensiones";
			} elseif ($resultadoImagen) {
				// Eliminar imagen anterior si existe
				if (!empty($datos['banner_anterior'])) {
					self::eliminarArchivoAnterior($datos['banner_anterior']);
				}
				$datosActualizacion['banner'] = $resultadoImagen;
			}
		}

		// Procesar nuevo video si se proporcionó
		if (isset($datos['video']) && !empty($datos['video']['name']) && $datos['video']['error'] == 0) {
			$resultadoVideo = self::procesarVideoPromo($datos['video']);

			if ($resultadoVideo === "formato_invalido") {
				return "formato_invalido";
			} elseif ($resultadoVideo === "archivo_grande") {
				return "archivo_grande";
			} elseif ($resultadoVideo === "resolucion_invalida") {
				return "resolucion_invalida";
			} elseif ($resultadoVideo === "duracion_excedida") {
				return "duracion_excedida";
			} elseif ($resultadoVideo === "archivo_corrupto") {
				return "archivo_corrupto";
			} elseif ($resultadoVideo) {
				// Eliminar video anterior si existe
				if (!empty($datos['promo_video_anterior'])) {
					self::eliminarArchivoAnterior($datos['promo_video_anterior']);
				}
				$datosActualizacion['promo_video'] = $resultadoVideo;
			}
		}

		// Limpiar datos antes de enviar al modelo
		unset($datosActualizacion['imagen']);
		unset($datosActualizacion['video']);
		unset($datosActualizacion['banner_anterior']);
		unset($datosActualizacion['promo_video_anterior']);

		$respuesta = ModeloCursos::mdlActualizarCurso($datosActualizacion);
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

	public static function ctrObtenerSecciones($idCurso)
	{
		$respuesta = ModeloCursos::mdlObtenerSecciones($idCurso);
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
	Obtener contenido de una sección con assets
	============================================--*/
	public static function ctrObtenerContenidoSeccionConAssets($idSeccion)
	{
		try {
			$contenidoRaw = ModeloCursos::mdlObtenerContenidoSeccionConAssets($idSeccion);

			if (!$contenidoRaw) {
				return [
					'success' => true,
					'contenido' => [],
					'mensaje' => 'No hay contenido en esta sección'
				];
			}

			// Organizar los datos agrupando contenido con sus assets
			$contenidoOrganizado = [];

			foreach ($contenidoRaw as $row) {
				$contenidoId = $row['id'];

				// Si el contenido no existe en el array, crearlo
				if (!isset($contenidoOrganizado[$contenidoId])) {
					$contenidoOrganizado[$contenidoId] = [
						'id' => $row['id'],
						'titulo' => $row['titulo'],
						'descripcion' => $row['descripcion'],
						'tipo' => $row['tipo'],
						'duracion' => $row['duracion'],
						'orden' => $row['orden'],
						'estado' => $row['estado'],
						'id_seccion' => $row['id_seccion'],
						'assets' => []
					];
				}

				// Si hay asset, agregarlo
				if ($row['asset_id']) {
					$contenidoOrganizado[$contenidoId]['assets'][] = [
						'id' => $row['asset_id'],
						'asset_tipo' => $row['asset_tipo'],
						'nombre_original' => $row['nombre_original'],
						'nombre_archivo' => $row['nombre_archivo'],
						'storage_path' => $row['storage_path'],
						'public_url' => $row['public_url'],
						'tamano_bytes' => $row['tamano_bytes'],
						'duracion_segundos' => $row['duracion_segundos'],
						'fecha_subida' => $row['fecha_subida']
					];
				}
			}

			return [
				'success' => true,
				'contenido' => array_values($contenidoOrganizado),
				'mensaje' => 'Contenido obtenido exitosamente'
			];
		} catch (Exception $e) {
			return [
				'success' => false,
				'mensaje' => 'Error al obtener el contenido: ' . $e->getMessage()
			];
		}
	}


	/*--==========================================
	Controladores para gestión de assets de contenido
	============================================--*/

	/**
	 * Guardar asset de contenido con validaciones
	 */
	public static function ctrGuardarContenidoAsset($datos)
	{
		$respuesta = ModeloCursos::mdlGuardarContenidoAsset($datos);
		return $respuesta;
	}

	/**
	 * Actualizar asset de contenido
	 */
	public static function ctrActualizarContenidoAsset($datos)
	{
		$respuesta = ModeloCursos::mdlActualizarContenidoAsset($datos);
		return $respuesta;
	}

	/**
	 * Eliminar asset de contenido
	 */
	public static function ctrEliminarContenidoAsset($id)
	{
		$respuesta = ModeloCursos::mdlEliminarContenidoAsset($id);
		return $respuesta;
	}

	/**
	 * Obtener assets de un contenido
	 */
	public static function  ctrObtenerAssetsContenido($idContenido)
	{
		$respuesta = ModeloCursos::mdlObtenerAssetsContenido($idContenido);
		return $respuesta;
	}

	/**
	 * Calcular duración total de un contenido
	 */
	public static function ctrCalcularDuracionTotalContenido($idContenido)
	{
		$respuesta = ModeloCursos::mdlCalcularDuracionTotalContenido($idContenido);
		return $respuesta;
	}

	/**
	 * Validar archivo MP4 con restricciones específicas
	 */
	public static function ctrValidarVideoMP4($archivo)
	{
		// Validaciones básicas
		if (!isset($archivo) || $archivo['error'] !== 0) {
			return [
				'success' => false,
				'mensaje' => 'Error en la subida del archivo'
			];
		}

		// Validar extensión
		$extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
		if ($extension !== 'mp4') {
			return [
				'success' => false,
				'mensaje' => 'Solo se permiten archivos MP4'
			];
		}

		// Validar tipo MIME
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mimeType = finfo_file($finfo, $archivo['tmp_name']);
		finfo_close($finfo);

		if ($mimeType !== 'video/mp4') {
			return [
				'success' => false,
				'mensaje' => 'El archivo no es un video MP4 válido'
			];
		}

		// Validar tamaño (máximo 40MB - aumentado desde 40MB)
		$tamanoMaximo = 40 * 1024 * 1024; // 40MB en bytes
		if ($archivo['size'] > $tamanoMaximo) {
			return [
				'success' => false,
				'mensaje' => 'El video no puede superar los 40MB'
			];
		}

		// Validar duración y resolución con ffprobe si está disponible
		$duracionValidacion = self::ctrValidarDuracionYResolucionVideo($archivo['tmp_name']);

		// Siempre retornar éxito, incluso si ffprobe no está disponible
		return [
			'success' => true,
			'duracion_segundos' => $duracionValidacion['duracion_segundos'] ?? 0,
			'resolucion' => $duracionValidacion['resolucion'] ?? 'unknown',
			'mensaje' => $duracionValidacion['mensaje'] ?? 'Video válido'
		];
	}

	/**
	 * Validar archivo PDF
	 */
	public static function ctrValidarPDF($archivo)
	{
		// Validaciones básicas
		if (!isset($archivo) || $archivo['error'] !== 0) {
			return [
				'success' => false,
				'mensaje' => 'Error en la subida del archivo'
			];
		}

		// Validar extensión
		$extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
		if ($extension !== 'pdf') {
			return [
				'success' => false,
				'mensaje' => 'Solo se permiten archivos PDF'
			];
		}

		// Validar tipo MIME
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mimeType = finfo_file($finfo, $archivo['tmp_name']);
		finfo_close($finfo);

		if ($mimeType !== 'application/pdf') {
			return [
				'success' => false,
				'mensaje' => 'El archivo no es un PDF válido'
			];
		}

		// Validar tamaño (máximo 10MB)
		$tamanoMaximo = 10 * 1024 * 1024; // 10MB en bytes
		if ($archivo['size'] > $tamanoMaximo) {
			return [
				'success' => false,
				'mensaje' => 'El PDF no puede superar los 10MB'
			];
		}

		return [
			'success' => true,
			'tamano_bytes' => $archivo['size'],
			'mensaje' => 'PDF válido'
		];
	}

	/**
	 * Validar duración y resolución de video usando ffprobe
	 */
	private static function ctrValidarDuracionYResolucionVideo($rutaArchivo)
	{
		// Verificar si ffprobe está disponible
		$ffprobePath = 'ffprobe'; // Ajustar según la instalación
		$command = "$ffprobePath -v quiet -print_format json -show_format -show_streams \"$rutaArchivo\" 2>&1";

		$output = shell_exec($command);

		// Debug: Log del comando y output para debugging
		error_log("FFprobe Command: " . $command);
		error_log("FFprobe Output: " . ($output ?? 'NULL'));

		if ($output === null || empty(trim($output))) {
			// Si ffprobe no está disponible, validar solo el tamaño básico
			$fileSize = filesize($rutaArchivo);
			if ($fileSize > 100 * 1024 * 1024) { // 100MB
				return [
					'success' => false,
					'mensaje' => 'El archivo es demasiado grande (máximo 100MB)'
				];
			}

			return [
				'success' => true,
				'duracion_segundos' => 0,
				'resolucion' => 'unknown',
				'mensaje' => 'Archivo validado (ffprobe no disponible - validación básica aplicada)'
			];
		}

		// Verificar si la salida contiene error
		if (strpos($output, 'not found') !== false || strpos($output, 'not recognized') !== false) {
			return [
				'success' => true,
				'duracion_segundos' => 0,
				'resolucion' => 'unknown',
				'mensaje' => 'ffprobe no disponible - validación básica aplicada'
			];
		}

		$data = json_decode($output, true);

		if (json_last_error() !== JSON_ERROR_NONE || !isset($data['format'])) {
			// Log adicional para debugging
			error_log("JSON Error: " . json_last_error_msg());
			error_log("Parsed data: " . print_r($data, true));

			// Fallback: validación básica sin ffprobe
			$fileSize = filesize($rutaArchivo);
			if ($fileSize > 100 * 1024 * 1024) { // 100MB
				return [
					'success' => false,
					'mensaje' => 'El archivo es demasiado grande (máximo 100MB)'
				];
			}

			return [
				'success' => true,
				'duracion_segundos' => 0,
				'resolucion' => 'unknown',
				'mensaje' => 'Video procesado con validación básica (no se pudo analizar con ffprobe)'
			];
		}

		// Obtener duración
		$duracion = floatval($data['format']['duration'] ?? 0);
		$duracionSegundos = intval($duracion);

		// Validar duración máxima (10 minutos = 600 segundos)
		if ($duracionSegundos > 600) {
			return [
				'success' => false,
				'mensaje' => 'El video no puede superar los 10 minutos de duración'
			];
		}

		// Obtener resolución del primer stream de video
		$resolucion = 'unknown';
		foreach ($data['streams'] as $stream) {
			if ($stream['codec_type'] === 'video') {
				$width = intval($stream['width'] ?? 0);
				$height = intval($stream['height'] ?? 0);

				// Validar resolución máxima HD (1280x720)
				if ($width > 1280 || $height > 720) {
					return [
						'success' => false,
						'mensaje' => 'La resolución máxima permitida es 1280x720 (HD)'
					];
				}

				$resolucion = "{$width}x{$height}";
				break;
			}
		}

		return [
			'success' => true,
			'duracion_segundos' => $duracionSegundos,
			'resolucion' => $resolucion,
			'mensaje' => 'Video validado correctamente'
		];
	}

	/**
	 * Crear estructura de directorios para assets
	 */
	public static function ctrCrearEstructuraDirectoriosAssets($idCurso, $idSeccion, $idContenido)
	{
		// Ruta base para assets de secciones
		$rutaBase = $_SERVER['DOCUMENT_ROOT'] . "/cursosApp/storage/public/section_assets";

		// Crear estructura: storage/public/section_assets/{curso}/{seccion}/{contenido}/
		$rutaCurso = $rutaBase . "/" . $idCurso;
		$rutaSeccion = $rutaCurso . "/" . $idSeccion;
		$rutaContenido = $rutaSeccion . "/" . $idContenido;
		$rutaVideo = $rutaContenido . "/video";
		$rutaPdf = $rutaContenido . "/pdf";

		// Crear directorios si no existen
		$directorios = [$rutaBase, $rutaCurso, $rutaSeccion, $rutaContenido, $rutaVideo, $rutaPdf];

		foreach ($directorios as $directorio) {
			if (!file_exists($directorio)) {
				if (!mkdir($directorio, 0755, true)) {
					return [
						'success' => false,
						'mensaje' => 'Error al crear directorio: ' . $directorio
					];
				}
			}
		}

		return [
			'success' => true,
			'ruta_video' => $rutaVideo,
			'ruta_pdf' => $rutaPdf,
			'mensaje' => 'Estructura de directorios creada'
		];
	}

	/**
	 * Procesar subida completa de asset (validación + guardado + registro en BD)
	 */
	public static function ctrProcesarSubidaAsset($archivo, $idContenido, $assetTipo, $idCurso, $idSeccion)
	{
		// Validar archivo según tipo
		if ($assetTipo === 'video') {
			$validacion = self::ctrValidarVideoMP4($archivo);
		} else if ($assetTipo === 'pdf') {
			$validacion = self::ctrValidarPDF($archivo);
		} else {
			return [
				'success' => false,
				'mensaje' => 'Tipo de asset no válido'
			];
		}

		if (!$validacion['success']) {
			return $validacion;
		}

		// Crear estructura de directorios
		$directorios = self::ctrCrearEstructuraDirectoriosAssets($idCurso, $idSeccion, $idContenido);
		if (!$directorios['success']) {
			return $directorios;
		}

		// Generar nombre único para el archivo
		$extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
		$nombreArchivo = uniqid() . '_' . time() . '.' . $extension;

		// Determinar ruta de destino
		$rutaDestino = ($assetTipo === 'video') ?
			$directorios['ruta_video'] . "/" . $nombreArchivo :
			$directorios['ruta_pdf'] . "/" . $nombreArchivo;

		// Mover archivo al destino
		if (!move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
			return [
				'success' => false,
				'mensaje' => 'Error al mover el archivo al directorio destino'
			];
		}

		// Crear URL pública relativa
		$publicUrl = "storage/public/section_assets/{$idCurso}/{$idSeccion}/{$idContenido}/" .
			($assetTipo === 'video' ? 'video' : 'pdf') . "/" . $nombreArchivo;

		// Preparar datos para guardar en BD
		$datosAsset = [
			'id_contenido' => $idContenido,
			'asset_tipo' => $assetTipo,
			'storage_path' => $rutaDestino,
			'public_url' => $publicUrl,
			'tamano_bytes' => $archivo['size'],
			'duracion_segundos' => $validacion['duracion_segundos'] ?? null
		];

		// Guardar en base de datos
		$resultado = self::ctrGuardarContenidoAsset($datosAsset);

		if ($resultado['success']) {
			// Actualizar duración total del contenido
			self::ctrActualizarDuracionContenido($idContenido);

			return [
				'success' => true,
				'asset_id' => $resultado['id'],
				'public_url' => $publicUrl,
				'mensaje' => 'Asset subido y registrado exitosamente'
			];
		}

		return $resultado;
	}

	/**
	 * Actualizar duración total de un contenido basado en sus assets
	 */
	public static function ctrActualizarDuracionContenido($idContenido)
	{
		$duracionData = self::ctrCalcularDuracionTotalContenido($idContenido);

		if ($duracionData['success']) {
			// Actualizar la duración en la tabla seccion_contenido
			$resultado = ModeloCursos::mdlActualizarDuracionContenido($idContenido, $duracionData['duracion_formateada']);

			if ($resultado['success']) {
				return [
					'success' => true,
					'duracion_formateada' => $duracionData['duracion_formateada'],
					'duracion_segundos' => $duracionData['duracion_segundos'],
					'mensaje' => 'Duración actualizada correctamente'
				];
			}

			return $resultado;
		}

		return $duracionData;
	}

	/*=============================================
	Cargar página de crear curso con datos necesarios
	=============================================*/
	public static function ctrCargarCreacionCurso()
	{
		// Obtener lista de profesores y categorías
		$profesores = self::ctrObtenerProfesores();
		$categorias = self::ctrObtenerCategorias();

		return [
			'profesores' => $profesores,
			'categorias' => $categorias
		];
	}

	/*=============================================
	Procesar creación de curso con validaciones mejoradas
	=============================================*/
	public static function ctrProcesarCreacionCurso($datos)
	{
		// Validaciones básicas
		if (empty($datos['nombre']) || empty($datos['descripcion'])) {
			return [
				'error' => true,
				'mensaje' => 'El nombre y descripción son obligatorios.'
			];
		}

		// Validar que el nombre del curso sea único
		if (!self::ctrValidarNombreUnico($datos['nombre'])) {
			return [
				'error' => true,
				'mensaje' => 'Ya existe un curso con este nombre. Por favor, elige un nombre diferente.',
				'campo' => 'nombre'
			];
		}

		// Validar campos de viñetas
		$validacionViñetas = self::ctrValidarCamposViñetas($datos);
		if ($validacionViñetas['error']) {
			return $validacionViñetas;
		}

		// Generar URL amigable única
		$datos['url_amiga'] = self::generarUrlAmigableUnica($datos['nombre']);

		// Usar el método existente para crear curso que ya maneja archivos
		$respuesta = self::ctrCrearCurso($datos);

		if ($respuesta === "ok") {
			// **NUEVA FUNCIONALIDAD:** Organizar archivos en carpetas por curso
			$conexion = Conexion::conectar();
			$idCurso = $conexion->lastInsertId();

			// Organizar archivos si existen
			if (!empty($datos['banner']) || !empty($datos['promo_video'])) {
				self::organizarArchivosPorCurso($idCurso, $datos['banner'] ?? '', $datos['promo_video'] ?? '');
			}

			return [
				'error' => false,
				'mensaje' => 'Curso creado exitosamente.'
			];
		} elseif ($respuesta === "error_dimensiones") {
			return [
				'error' => true,
				'mensaje' => 'La imagen debe tener dimensiones exactas de 600x400 píxeles.'
			];
		} elseif ($respuesta === "formato_invalido") {
			return [
				'error' => true,
				'mensaje' => 'Formato de video no válido. Formatos permitidos: MP4'
			];
		} elseif ($respuesta === "archivo_grande") {
			return [
				'error' => true,
				'mensaje' => 'El video es muy grande. Tamaño máximo permitido: 10MB.'
			];
		} elseif ($respuesta === "resolucion_invalida") {
			return [
				'error' => true,
				'mensaje' => 'Resolución de video no válida. Se requiere HD (1280x720).'
			];
		} elseif ($respuesta === "duracion_excedida") {
			return [
				'error' => true,
				'mensaje' => 'El video excede la duración máxima permitida de 5 minutos.'
			];
		} elseif ($respuesta === "archivo_corrupto") {
			return [
				'error' => true,
				'mensaje' => 'El archivo de video parece estar corrupto o no es válido.'
			];
		} else {
			return [
				'error' => true,
				'mensaje' => 'Error al crear el curso: ' . $respuesta
			];
		}
	}

	/*=============================================
	Validar campos de viñetas (líneas con máximo de caracteres)
	=============================================*/
	public static function ctrValidarCamposViñetas($datos)
	{
		$camposViñetas = [
			'lo_que_aprenderas' => 'Lo que aprenderás',
			'requisitos' => 'Requisitos',
			'para_quien' => 'Para quién es este curso'
		];

		$maxCaracteres = 100;

		foreach ($camposViñetas as $campo => $nombreAmigable) {
			if (!empty($datos[$campo])) {
				$lineas = explode("\n", $datos[$campo]);

				foreach ($lineas as $numeroLinea => $linea) {
					$linea = trim($linea);
					if (!empty($linea) && strlen($linea) > $maxCaracteres) {
						return [
							'error' => true,
							'mensaje' => "En el campo '{$nombreAmigable}', la línea " . ($numeroLinea + 1) . " excede el límite de {$maxCaracteres} caracteres."
						];
					}
				}
			}
		}

		return ['error' => false];
	}

	/*=============================================
	Procesar formulario de creación desde POST - UNIFICADO
	=============================================*/
	public static function ctrProcesarFormularioCreacion()
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			return null;
		}

		// DETECCIÓN DE LÍMITES PHP: Si POST y FILES están vacíos, probablemente excedió post_max_size
		if (empty($_POST) && empty($_FILES) && $_SERVER['CONTENT_LENGTH'] > 0) {
			$maxSize = ini_get('post_max_size');
			$uploadedSize = round($_SERVER['CONTENT_LENGTH'] / 1024 / 1024, 2); // MB

			return [
				'error' => true,
				'mensaje' => "El archivo excede el límite del servidor ({$maxSize}). Tamaño subido: {$uploadedSize}MB. Para videos promocionales, use archivos menores a 50MB.",
				'tipo' => 'php_limit_exceeded'
			]; //TODO
		}

		// Recopilar datos del formulario con mapeo de campos
		$datos = [
			"nombre" => $_POST['nombre'] ?? '',
			"descripcion" => $_POST['descripcion'] ?? '',
			"lo_que_aprenderas" => $_POST['lo_que_aprenderas'] ?? '',
			"requisitos" => $_POST['requisitos'] ?? '',
			"para_quien" => $_POST['para_quien'] ?? '',
			"imagen" => $_FILES['imagen'] ?? null,
			// Mapear tanto 'video' como 'video_promocional'
			"video" => $_FILES['video'] ?? $_FILES['video_promocional'] ?? null,
			// Mapear tanto 'precio' como 'valor'
			"valor" => $_POST['precio'] ?? $_POST['valor'] ?? 0,
			// Mapear tanto 'categoria' como 'id_categoria'  
			"id_categoria" => $_POST['categoria'] ?? $_POST['id_categoria'] ?? '',
			"estado" => $_POST['estado'] ?? "activo"
		];

		// **DIFERENCIA CLAVE:** Determinar el profesor
		if (isset($_POST['profesor']) && !empty($_POST['profesor'])) {
			// Caso Admin/SuperAdmin: pueden elegir profesor del dropdown
			$datos["id_persona"] = $_POST['profesor'];
		} elseif (isset($_POST['id_profesor']) && !empty($_POST['id_profesor'])) {
			// Caso Profesor: viene del campo hidden
			$datos["id_persona"] = $_POST['id_profesor'];
		} else {
			// Fallback: usar ID de sesión
			$datos["id_persona"] = $_SESSION['idU'] ?? $_SESSION['id'] ?? '';

			// Validar que el usuario logueado tenga permisos
			if (!ControladorGeneral::ctrUsuarioTieneAlgunRol(['profesor', 'admin', 'superadmin'])) {
				return [
					'error' => true,
					'mensaje' => 'No tienes permisos para crear cursos.'
				];
			}
		}

		// Procesar la creación usando el método unificado
		return self::ctrProcesarCreacionCurso($datos);
	}

	/*=============================================
	Generar URL amigable
	=============================================*/
	private static function generarUrlAmigable($texto)
	{
		return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $texto)));
	}

	/*=============================================
	Validar que el nombre del curso sea único
	=============================================*/
	public static function ctrValidarNombreUnico($nombre, $idCursoExcluir = null)
	{
		$conn = Conexion::conectar();

		// Si estamos editando un curso, excluir el curso actual de la validación
		if ($idCursoExcluir) {
			$stmt = $conn->prepare("SELECT COUNT(*) as total FROM curso WHERE nombre = ? AND id != ?");
			$stmt->execute([$nombre, $idCursoExcluir]);
		} else {
			$stmt = $conn->prepare("SELECT COUNT(*) as total FROM curso WHERE nombre = ?");
			$stmt->execute([$nombre]);
		}

		$resultado = $stmt->fetch(PDO::FETCH_ASSOC);
		return $resultado['total'] == 0; // Retorna true si no existe, false si ya existe
	}

	/*=============================================
	Validar que la URL amigable sea única
	=============================================*/
	public static function ctrValidarUrlAmigableUnica($urlAmiga, $idCursoExcluir = null)
	{
		$conn = Conexion::conectar();

		// Si estamos editando un curso, excluir el curso actual de la validación
		if ($idCursoExcluir) {
			$stmt = $conn->prepare("SELECT COUNT(*) as total FROM curso WHERE url_amiga = ? AND id != ?");
			$stmt->execute([$urlAmiga, $idCursoExcluir]);
		} else {
			$stmt = $conn->prepare("SELECT COUNT(*) as total FROM curso WHERE url_amiga = ?");
			$stmt->execute([$urlAmiga]);
		}

		$resultado = $stmt->fetch(PDO::FETCH_ASSOC);
		return $resultado['total'] == 0; // Retorna true si no existe, false si ya existe
	}

	/*=============================================
	Organizar archivos por curso después de la creación
	=============================================*/
	private static function organizarArchivosPorCurso($idCurso, $rutaBanner, $rutaVideo = '')
	{
		$documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? 'C:\\xampp\\htdocs';
		$basePath = $documentRoot . "/cursosApp/storage/public/";

		// Crear directorios específicos del curso
		$directorioBanner = $basePath . "banners/" . $idCurso . "/";
		$directorioVideo = $basePath . "promoVideos/" . $idCurso . "/";

		if (!file_exists($directorioBanner)) {
			mkdir($directorioBanner, 0755, true);
		}
		if (!file_exists($directorioVideo)) {
			mkdir($directorioVideo, 0755, true);
		}

		$rutaBannerFinal = $rutaBanner;
		$rutaVideoFinal = $rutaVideo;

		// Mover banner si existe
		if ($rutaBanner && file_exists($documentRoot . "/cursosApp/" . $rutaBanner)) {
			$nombreArchivo = basename($rutaBanner);
			$rutaOrigen = $documentRoot . "/cursosApp/" . $rutaBanner;
			$rutaDestino = $directorioBanner . $nombreArchivo;

			if (rename($rutaOrigen, $rutaDestino)) {
				$rutaBannerFinal = "storage/public/banners/" . $idCurso . "/" . $nombreArchivo;
			}
		}

		// Mover video si existe
		if ($rutaVideo && file_exists($documentRoot . "/cursosApp/" . $rutaVideo)) {
			$nombreArchivo = basename($rutaVideo);
			$rutaOrigen = $documentRoot . "/cursosApp/" . $rutaVideo;
			$rutaDestino = $directorioVideo . $nombreArchivo;

			if (rename($rutaOrigen, $rutaDestino)) {
				$rutaVideoFinal = "storage/public/promoVideos/" . $idCurso . "/" . $nombreArchivo;
			}
		}

		// Actualizar rutas en la base de datos si cambió algo
		if ($rutaBannerFinal !== $rutaBanner || $rutaVideoFinal !== $rutaVideo) {
			$datosActualizar = [
				'id' => $idCurso,
				'banner' => $rutaBannerFinal
			];

			if ($rutaVideoFinal) {
				$datosActualizar['promo_video'] = $rutaVideoFinal;
			}

			ModeloCursos::mdlActualizarRutasArchivos($datosActualizar);
		}

		return true;
	}

	/*=============================================
	Generar URL amigable única (con sufijo si es necesario)
	=============================================*/
	private static function generarUrlAmigableUnica($nombre, $idCursoExcluir = null)
	{
		$urlBase = self::generarUrlAmigable($nombre);
		$urlFinal = $urlBase;
		$contador = 1;

		// Verificar si la URL ya existe y generar una única
		while (!self::ctrValidarUrlAmigableUnica($urlFinal, $idCursoExcluir)) {
			$urlFinal = $urlBase . '-' . $contador;
			$contador++;
		}

		return $urlFinal;
	}

	/*=============================================
	Cargar página de editar curso con datos completos
	=============================================*/
	public static function ctrCargarEdicionCurso($identificador)
	{
		// Verificar que el identificador sea válido
		if (!$identificador) {
			return [
				'error' => true,
				'mensaje' => 'Identificador de curso no válido.'
			];
		}

		// Determinar si es un ID numérico o una URL amigable
		$esCursoId = is_numeric($identificador);
		$campo = $esCursoId ? "id" : "url_amiga";

		// Obtener los datos del curso
		$cursosArray = self::ctrMostrarCursos($campo, $identificador);

		// Como ctrMostrarCursos puede devolver un array de cursos, necesitamos obtener el primer elemento
		$curso = null;
		if (is_array($cursosArray) && !empty($cursosArray)) {
			// Si es un array indexado, tomar el primer elemento
			if (isset($cursosArray[0])) {
				$curso = $cursosArray[0];
			} else {
				// Si es un array asociativo directo, usarlo
				$curso = $cursosArray;
			}
		}

		if (!$curso) {
			return [
				'error' => true,
				'mensaje' => 'Curso no encontrado.'
			];
		}

		// Asegurar que tenemos el ID del curso para las consultas posteriores
		$idCurso = $curso['id'];

		// Obtener datos adicionales necesarios para la vista
		$categorias = self::ctrObtenerCategorias();
		$profesores = self::ctrObtenerProfesores();

		// Obtener conexión para las secciones
		$conn = Conexion::conectar();

		// Obtener secciones del curso
		$stmtSecciones = $conn->prepare("
			SELECT * FROM curso_secciones 
			WHERE id_curso = ? 
			ORDER BY orden ASC
		");
		$stmtSecciones->execute([$idCurso]);
		$secciones = $stmtSecciones->fetchAll(PDO::FETCH_ASSOC);

		// Obtener contenido de cada sección
		$contenidoSecciones = [];
		foreach ($secciones as $seccion) {
			$stmtContenido = $conn->prepare("
				SELECT * FROM seccion_contenido 
				WHERE id_seccion = ? 
				ORDER BY orden ASC
			");
			$stmtContenido->execute([$seccion['id']]);
			$contenidoSecciones[$seccion['id']] = $stmtContenido->fetchAll(PDO::FETCH_ASSOC);
		}

		// Retornar todos los datos necesarios para la vista
		return [
			'error' => false,
			'curso' => $curso,
			'categorias' => $categorias,
			'profesores' => $profesores,
			'secciones' => $secciones,
			'contenidoSecciones' => $contenidoSecciones
		];
	}

	/*=============================================
	Procesar actualización del curso
	=============================================*/
	public static function ctrActualizarDatosCurso($datos)
	{
		if (empty($datos['id'])) {
			return [
				'error' => true,
				'mensaje' => 'ID de curso no válido.'
			];
		}

		// Si se está actualizando el nombre, validar que sea único
		if (!empty($datos['nombre'])) {
			if (!self::ctrValidarNombreUnico($datos['nombre'], $datos['id'])) {
				return [
					'error' => true,
					'mensaje' => 'Ya existe otro curso con este nombre. Por favor, elige un nombre diferente.',
					'campo' => 'nombre'
				];
			}

			// Generar nueva URL amigable única si el nombre cambió
			$datos['url_amiga'] = self::generarUrlAmigableUnica($datos['nombre'], $datos['id']);
		}

		$respuesta = ModeloCursos::mdlActualizarCurso($datos);

		if ($respuesta == "ok") {
			return [
				'error' => false,
				'mensaje' => 'Los datos del curso se han actualizado correctamente.'
			];
		} elseif ($respuesta === "error_dimensiones") {
			return [
				'error' => true,
				'mensaje' => 'La imagen debe tener dimensiones exactas de 600x400 píxeles.',
				'campo' => 'imagen'
			];
		} elseif ($respuesta === "formato_invalido") {
			return [
				'error' => true,
				'mensaje' => 'Formato de video no válido. Formatos permitidos: MP4',
				'campo' => 'video'
			];
		} elseif ($respuesta === "archivo_grande") {
			return [
				'error' => true,
				'mensaje' => 'El video es muy grande. Tamaño máximo permitido: 15MB.',
				'campo' => 'video'
			];
		} elseif ($respuesta === "resolucion_invalida") {
			return [
				'error' => true,
				'mensaje' => 'Resolución de video no válida. Se requiere HD (1280x720).',
				'campo' => 'video'
			];
		} elseif ($respuesta === "duracion_excedida") {
			return [
				'error' => true,
				'mensaje' => 'El video excede la duración máxima permitida de 20 minutos.',
				'campo' => 'video'
			];
		} elseif ($respuesta === "archivo_corrupto") {
			return [
				'error' => true,
				'mensaje' => 'El archivo de video parece estar corrupto o no es válido.',
				'campo' => 'video'
			];
		} else {
			return [
				'error' => true,
				'mensaje' => 'Error al actualizar el curso: ' . $respuesta
			];
		}
	}

	/*=============================================
	CARGAR DATOS PARA LISTADO DE CURSOS
	=============================================*/
	public static function ctrCargarListadoCursos()
	{
		// Obtener todos los cursos
		$cursos = self::ctrMostrarCursos(null, null);
		if (!$cursos) {
			$cursos = [];
		}
		if (isset($cursos['id'])) {
			$cursos = [$cursos];
		}

		// Obtener todas las categorías y profesores
		$categorias = self::ctrObtenerCategorias();
		$profesores = self::ctrObtenerProfesores();

		// Enriquecer cada curso con información adicional
		foreach ($cursos as &$curso) {
			// Asegurar que el valor esté presente
			if (!isset($curso["valor"])) {
				$curso["valor"] = 0;
			}

			// Validar imagen del banner
			$curso["banner"] = self::ctrValidarImagenCurso($curso["banner"]);

			// Obtener categoría
			$categoria = array_filter($categorias, function ($cat) use ($curso) {
				return $cat['id'] == $curso['id_categoria'];
			});
			$curso["categoria"] = $categoria ? reset($categoria)['nombre'] : 'Sin categoría';

			// Obtener nombre del profesor
			$profesor = array_filter($profesores, function ($prof) use ($curso) {
				return $prof['id'] == $curso['id_persona'];
			});
			$curso["profesor"] = $profesor ? reset($profesor)['nombre'] : 'Desconocido';

			// Formatear fecha
			$curso["fecha_formateada"] = date("Y-m-d", strtotime($curso["fecha_registro"]));
		}

		return $cursos;
	}

	/*=============================================
	CARGAR DATOS PARA LISTADO DE CURSOS DEL PROFESOR LOGUEADO
	=============================================*/
	public static function ctrCargarListadoCursosProfesor($idProfesor = null)
	{
		// Si no se proporciona ID, usar el de la sesión
		if (!$idProfesor && isset($_SESSION['idU'])) {
			$idProfesor = $_SESSION['idU'];
		}

		if (!$idProfesor) {
			return [];
		}

		// Obtener cursos del profesor específico directamente desde la base de datos
		// para asegurar que obtenemos TODOS los cursos, no solo el primero
		$conn = Conexion::conectar();
		$stmt = $conn->prepare("SELECT * FROM curso WHERE id_persona = ? ORDER BY fecha_registro DESC");
		$stmt->execute([$idProfesor]);
		$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

		if (!$cursos) {
			return [];
		}

		// Obtener todas las categorías para mapear
		$categorias = self::ctrObtenerCategorias();

		// Enriquecer cada curso con información adicional
		foreach ($cursos as &$curso) {
			// Asegurar que el valor esté presente
			if (!isset($curso["valor"])) {
				$curso["valor"] = 0;
			}

			// Validar imagen del banner
			$curso["banner"] = self::ctrValidarImagenCurso($curso["banner"]);

			// Obtener categoría
			$categoria = array_filter($categorias, function ($cat) use ($curso) {
				return $cat['id'] == $curso['id_categoria'];
			});
			$curso["categoria"] = $categoria ? reset($categoria)['nombre'] : 'Sin categoría';

			// El profesor ya es conocido (es el logueado)
			$curso["profesor"] = $_SESSION['nombreU'] ?? 'Profesor';

			// Formatear fecha
			$curso["fecha_formateada"] = date("Y-m-d", strtotime($curso["fecha_registro"]));

			// Agregar contador de secciones si existe la tabla
			try {
				$stmtSecciones = $conn->prepare("SELECT COUNT(*) as total_secciones FROM curso_secciones WHERE id_curso = ?");
				$stmtSecciones->execute([$curso['id']]);
				$secciones = $stmtSecciones->fetch(PDO::FETCH_ASSOC);
				$curso["total_secciones"] = $secciones['total_secciones'] ?? 0;
			} catch (Exception $e) {
				$curso["total_secciones"] = 0;
			}
		}

		return $cursos;
	}

	/*=============================================
	Generar URLs amigables para cursos existentes sin URL amigable
	=============================================*/
	public static function ctrGenerarUrlsAmigablesFaltantes()
	{
		$conn = Conexion::conectar();

		// Buscar cursos sin URL amigable
		$stmt = $conn->prepare("SELECT id, nombre FROM curso WHERE url_amiga IS NULL OR url_amiga = ''");
		$stmt->execute();
		$cursosSinUrl = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$actualizados = 0;
		foreach ($cursosSinUrl as $curso) {
			$urlAmiga = self::generarUrlAmigable($curso['nombre']);

			// Actualizar el curso con la nueva URL amigable
			$stmtUpdate = $conn->prepare("UPDATE curso SET url_amiga = ? WHERE id = ?");
			if ($stmtUpdate->execute([$urlAmiga, $curso['id']])) {
				$actualizados++;
			}
		}

		return [
			'total_encontrados' => count($cursosSinUrl),
			'actualizados' => $actualizados
		];
	}

	/*=============================================
	Validar imagen del curso - SOLO STORAGE
	=============================================*/
	public static function ctrValidarImagenCurso($rutaImagen)
	{
		// Si no hay imagen asignada, devolver imagen por defecto de storage
		if (empty($rutaImagen) || $rutaImagen === null) {
			return '/cursosApp/storage/public/banners/default/defaultCurso.png';
		}

		// SOLO aceptar rutas de storage
		if (strpos($rutaImagen, 'storage/public/banners/') !== 0) {
			// Si no es una ruta de storage, devolver imagen por defecto
			return '/cursosApp/storage/public/banners/default/defaultCurso.png';
		}

		// Determinar la ruta base del proyecto
		$documentRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : 'C:\\xampp\\htdocs';

		// Construir la ruta completa del archivo
		$rutaCompleta = $documentRoot . '/cursosApp/' . $rutaImagen;
		$rutaPublica = '/cursosApp/' . $rutaImagen;

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
		return '/cursosApp/storage/public/banners/default/defaultCurso.png';
	}

	/*=============================================
	Obtener URL pública para video promocional - SOLO STORAGE
	=============================================*/
	public static function ctrObtenerUrlVideoPromo($rutaVideo)
	{
		if (empty($rutaVideo)) {
			return null;
		}

		// SOLO aceptar rutas de storage
		if (strpos($rutaVideo, 'storage/public/promoVideos/') !== 0) {
			// Si no es una ruta de storage, devolver null
			return null;
		}

		// Devolver URL pública para storage
		return '/cursosApp/' . $rutaVideo;
	}

	/*=============================================
	Limpiar rutas antiguas de la base de datos - SOLO STORAGE
	=============================================*/
	public static function ctrLimpiarRutasAntiguas()
	{
		$conn = Conexion::conectar();
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		try {
			// Limpiar banners que no son de storage
			$stmt = $conn->prepare("UPDATE curso SET banner = NULL WHERE banner IS NOT NULL AND banner NOT LIKE 'storage/public/banners/%'");
			$bannersLimpiados = $stmt->execute();

			// Limpiar videos que no son de storage
			$stmt = $conn->prepare("UPDATE curso SET promo_video = NULL WHERE promo_video IS NOT NULL AND promo_video NOT LIKE 'storage/public/promoVideos/%'");
			$videosLimpiados = $stmt->execute();

			return [
				'success' => true,
				'banners_limpiados' => $bannersLimpiados,
				'videos_limpiados' => $videosLimpiados,
				'message' => 'Rutas antiguas limpiadas correctamente'
			];
		} catch (Exception $e) {
			return [
				'success' => false,
				'error' => $e->getMessage()
			];
		}
	}

	/*=============================================
	Cambiar estado del curso (activo/borrador/inactivo)
	=============================================*/
	public static function ctrCambiarEstadoCurso($idCurso, $nuevoEstado)
	{
		// Validar parámetros
		if (empty($idCurso) || empty($nuevoEstado)) {
			return [
				'error' => true,
				'mensaje' => 'Parámetros inválidos.'
			];
		}

		// Validar estados permitidos
		$estadosPermitidos = ['activo', 'borrador', 'inactivo'];
		if (!in_array($nuevoEstado, $estadosPermitidos)) {
			return [
				'error' => true,
				'mensaje' => 'Estado no válido. Estados permitidos: ' . implode(', ', $estadosPermitidos)
			];
		}

		// Verificar que el curso existe
		$curso = self::ctrMostrarCursos('id', $idCurso);
		if (!$curso) {
			return [
				'error' => true,
				'mensaje' => 'El curso no existe.'
			];
		}

		// Actualizar estado
		$respuesta = ModeloCursos::mdlActualizarEstadoCurso($idCurso, $nuevoEstado);

		if ($respuesta == "ok") {
			return [
				'error' => false,
				'mensaje' => 'Estado del curso actualizado exitosamente.',
				'nuevo_estado' => $nuevoEstado
			];
		} else {
			return [
				'error' => true,
				'mensaje' => 'Error al actualizar el estado del curso.'
			];
		}
	}
}
