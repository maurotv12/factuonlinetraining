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
	static public function ctrMostrarCursos($item, $valor){
		$tabla = "curso";
		$respuesta = ModeloCursos::mdlMostrarCursos($tabla, $item, $valor);
		return $respuesta;
	}


}