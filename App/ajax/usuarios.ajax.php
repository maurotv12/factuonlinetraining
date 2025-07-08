<?php

require_once "../controladores/general.controlador.php";
require_once "../controladores/usuarios.controlador.php";
require_once "../modelos/usuarios.modelo.php";

class AjaxUsuarios{

	/*=============================================
	Validar email existente
	=============================================*/
	public $validarEmail;
	public function ajaxValidarEmail(){
		$item = "email";
		$valor = $this->validarEmail;
		$respuesta = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);
		echo json_encode($respuesta);
	}

	/*=============================================
	Validar email existente
	=============================================*/
	public $validarUsuario;
	public function ajaxValidarUsuario(){
		$item = "usuarioLink";
		$valor = $this->validarUsuario;
		$respuesta = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);
		echo json_encode($respuesta);
	}
}

/*=============================================
Validar email existente
=============================================*/
if(isset($_POST["validarEmail"])){
	$valEmail = new AjaxUsuarios();
	$valEmail -> validarEmail = $_POST["validarEmail"];
	$valEmail -> ajaxValidarEmail();
}

/*=============================================
Validar usuario existente
=============================================*/
if(isset($_POST["validarUsuario"])){
	$valUsuario = new AjaxUsuarios();
	$valUsuario -> validarUsuario = $_POST["validarUsuario"];
	$valUsuario -> ajaxValidarUsuario();
}