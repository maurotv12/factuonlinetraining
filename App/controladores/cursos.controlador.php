<?php

/**
@grcarvajal grcarvajal@gmail.com **Gildardo Restrepo Carvajal**
12/06/2022 Plataforma Calibelula mostrar Cursos
Controlador de cursos registro
 */

// Incluir modelo de cursos
require_once "modelos/cursos.modelo.php";

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

			// Directorio según el tipo - usar la estructura existente
			if ($tipo == 'video') {
				$directorio = 'subidas/videos/';
			} else {
				$directorio = 'subidas/documentos/';
			}

			// Crear directorio si no existe
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

		// Validar campos de viñetas
		$validacionViñetas = self::ctrValidarCamposViñetas($datos);
		if ($validacionViñetas['error']) {
			return $validacionViñetas;
		}

		// Generar URL amigable
		$datos['url_amiga'] = self::generarUrlAmigable($datos['nombre']);

		// Usar el método existente para crear curso que ya maneja archivos
		$respuesta = self::ctrCrearCurso($datos);

		if ($respuesta === "ok") {
			return [
				'error' => false,
				'mensaje' => 'Curso creado exitosamente.'
			];
		} elseif ($respuesta === "error_dimensiones") {
			return [
				'error' => true,
				'mensaje' => 'La imagen debe tener dimensiones exactas de 600x400 píxeles.'
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
	Procesar formulario de creación desde POST
	=============================================*/
	public static function ctrProcesarFormularioCreacion()
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			return null;
		}

		// Recopilar datos del formulario
		$datos = [
			"nombre" => $_POST['nombre'] ?? '',
			"descripcion" => $_POST['descripcion'] ?? '',
			"lo_que_aprenderas" => $_POST['lo_que_aprenderas'] ?? '',
			"requisitos" => $_POST['requisitos'] ?? '',
			"para_quien" => $_POST['para_quien'] ?? '',
			"imagen" => $_FILES['imagen'] ?? null,
			"video" => $_FILES['video'] ?? null,
			"valor" => $_POST['precio'] ?? 0,
			"id_categoria" => $_POST['categoria'] ?? '',
			"id_persona" => $_POST['profesor'] ?? $_SESSION['idU'] ?? '',
			"estado" => "activo"
		];

		// Procesar la creación
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

		$respuesta = ModeloCursos::mdlActualizarCurso($datos);

		if ($respuesta == "ok") {
			return [
				'error' => false,
				'mensaje' => 'Los datos del curso se han actualizado correctamente.'
			];
		} else {
			return [
				'error' => true,
				'mensaje' => 'Error al actualizar el curso.'
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
	Validar imagen del curso y devolver imagen por defecto si no existe
	=============================================*/
	public static function ctrValidarImagenCurso($rutaImagen)
	{
		// Si no hay imagen asignada, devolver imagen por defecto
		if (empty($rutaImagen) || $rutaImagen === null) {
			return 'vistas/img/cursos/default/defaultCurso.png';
		}

		// Construir la ruta completa del archivo
		$rutaCompleta = $_SERVER['DOCUMENT_ROOT'] . '/cursosApp/App/' . $rutaImagen;

		// Verificar si el archivo existe
		if (file_exists($rutaCompleta) && is_file($rutaCompleta)) {
			// Verificar que sea una imagen válida
			$infoImagen = @getimagesize($rutaCompleta);
			if ($infoImagen !== false) {
				return $rutaImagen; // La imagen existe y es válida
			}
		}

		// Si llegamos aquí, la imagen no existe o no es válida
		return 'vistas/img/cursos/default/defaultCurso.png';
	}
}
