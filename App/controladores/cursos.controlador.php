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

	public static function ctrCrearCurso($datos)
	{
		// Inicializar valores predeterminados para banner y video
		$datos['banner'] = null;
		$datos['promo_video'] = null;

		// Procesar la imagen del curso si existe
		if (isset($datos['imagen']) && $datos['imagen']['error'] == 0) {
			$directorio = "vistas/img/cursos/";
			if (!file_exists($directorio)) mkdir($directorio, 0777, true);
			$nombreImg = uniqid() . "_" . $datos['imagen']['name'];
			$rutaImg = $directorio . $nombreImg;
			if (move_uploaded_file($datos['imagen']['tmp_name'], $rutaImg)) {
				$datos['banner'] = $rutaImg;
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
}
