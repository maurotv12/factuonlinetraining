<?php

require_once "modelos/inscripciones.modelo.php";


use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class ControladorInscripciones
{
	/*======================================================
	Mostrar Inscripciones pendientes de cada Usuario
========================================================*/
	public static function ctrMostrarInscripcionesPendientessCU($item, $valor)
	{
		$tabla = "inscripciones";
		$respuesta = ModeloInscripciones::mdlMostrarInscripcionesPendientessCU($tabla, $item, $valor);
		return $respuesta;
	}


	/*=============================================
	Mostrar Cursos
=============================================*/
	public static function ctrMostrarInscripciones($item, $valor)
	{
		$tabla = "";
		$respuesta = ModeloInscripciones::mdlMostrarInscripciones($tabla, $item, $valor);
		return $respuesta;
	}
}
