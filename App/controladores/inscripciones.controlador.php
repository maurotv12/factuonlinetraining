<?php
/**
@grcarvajal grcarvajal@gmail.com **Gildardo Restrepo Carvajal**
12/06/2022 Plataforma Calibelula mostrar Cursos
Controlador de cursos gestion cursos.
 */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class ControladorInscripciones
{

/*======================================================
	Mostrar Inscripciones pendientes de cada Usuario
========================================================*/
	static public function ctrMostrarInscripcionesPendientessCU($item, $valor)
	{
		$tabla = "inscripciones";
        $respuesta = ModeloInscripciones::mdlMostrarInscripcionesPendientessCU($tabla, $item, $valor);
        return $respuesta;
    }


/*=============================================
	Mostrar Cursos
=============================================*/
	static public function ctrMostrarInscripciones($item, $valor)
	{
		$tabla = "";
		$respuesta = ModeloInscripciones::mdlMostrarInscripciones($tabla, $item, $valor);
		return $respuesta;
	}



         	


}