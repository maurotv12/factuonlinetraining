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
		$tabla = "curso";
		$respuesta = ModeloCursos::mdlCrearCurso($tabla, $datos);
		return $respuesta;
	}
}
