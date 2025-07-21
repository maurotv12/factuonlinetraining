<?php

/**
@grcarvajal grcarvajal@gmail.com **Gildardo Restrepo Carvajal**
@grcarvajal grcarvajal@gmail.com **Gildardo Restrepo Carvajal**
12/06/2022 Plataforma Cursos
 */

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;


require_once $_SERVER['DOCUMENT_ROOT'] . "/cursosApp/App/modelos/usuarios.modelo.php";

class ControladorUsuarios
{

	/*--=====================================
	Registro de Usuarios
======================================--*/
	public static function ctrRegistroUsuario()
	{
		if (isset($_POST["nombreRegistro"])) {
			$ruta = ControladorRuta::ctrRutaApp();
			$tabla = "persona";
			$item = "email";
			$valor = $_POST["emailRegistro"];

			$respuesta = ModeloUsuarios::mdlMostrarUsuarios($tabla, $item, $valor);
			if ($respuesta["email"] == $_POST["emailRegistro"]) {
				echo '<script>
					swal({
						type:"error",
						title: "¡CORREGIR!",
						text: "¡Su e-Mail ya esta registrado!....  ' . $respuesta["email"] . '",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"
						}).then(function(result){
						if(result.value){
							history.back();
						}
					});
				</script>';
			} else {
				$hashPassword = password_hash($_POST['passRegistro'], PASSWORD_DEFAULT);
				$tabla = "persona";
				$datos = array(
					"usuario" => $_POST["usuarioRegistro"],
					"nombre" => $_POST["nombreRegistro"],
					"email" => $_POST["emailRegistro"],
					"password" => $hashPassword,
					"verificacion" => 0
				);
				$respuesta2 = ModeloUsuarios::mdlRegistroUsuario($tabla, $datos);
				if ($respuesta2 == "ok") {
					session_start();
					$respuesta = ModeloUsuarios::mdlMostrarUsuarios($tabla, $item, $valor);
					$_SESSION["nombre"] = $respuesta["nombre"];
					$_SESSION["id"] = $respuesta["id"];
					echo '<script>
							window.location = "' . $ruta . 'paraQuien";
						</script>';
				}
			}
		}
	}

	/*=============================================
	Ingreso Usuario
=============================================*/
	public function ctrIngresoUsuario()
	{
		if (isset($_POST["emailIngreso"])) {
			$tabla = "persona";
			$item = "email";
			$valor = $_POST["emailIngreso"];
			$respuesta = ModeloUsuarios::mdlMostrarUsuarios($tabla, $item, $valor);
			$rolesUsuario = ModeloUsuarios::mdlObtenerRolesPorUsuario($respuesta["id"]);
			if ($respuesta["email"] == $_POST["emailIngreso"]  && password_verify($_POST['passIngreso'], $respuesta["password"])) {
				session_start();
				$_SESSION["validarSesion"] = "ok";
				$_SESSION["idU"] = $respuesta["id"];
				$_SESSION["nombreU"] = $respuesta["nombre"];
				$_SESSION["emailU"] = $respuesta["email"];
				$_SESSION["rolesU"] = $rolesUsuario;


				$idU = 	$respuesta["id"];
				$navU = $_POST["navegadorU"];
				$ipU = 	$_POST["ipU"];
				$res = ModeloUsuarios::mdlRegistroIngresoUsuarios($idU, $navU, $ipU);

				$ruta = ControladorRuta::ctrRutaApp();
				echo '<script>
			 		window.location = "' . $ruta . '";
			 		</script>';
			} else {
				echo '<script>
							swal({
								type:"error",
								title: "¡ERROR!",
								text: "¡El e-Mail o contraseña no son correctas, inténtalo de nuevo o recupera tu contraseña!",
								showConfirmButton: true,
								confirmButtonText: "Cerrar"
								}).then(function(result){
								if(result.value){
									history.back();
								}
								});	
							</script>';
			}
		}
	}

	/*=============================================
	Validar correo electrónico
	=============================================*/

	public static function ctrVerificarCorreoUsuario()
	{

		$item = "email_encriptado";

		$valor = $_GET["pagina"];

		$validarCorreo = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);

		if ($validarCorreo["email_encriptado"] == $_GET["pagina"]) {
			$id = $validarCorreo["id_usuario"];
			$item2 = "verificacion";
			$valor2 = 1;

			$respuesta = ControladorUsuarios::ctrActualizarUsuario($id, $item2, $valor2);
			$ruta = ControladorRuta::ctrRuta();
			if ($respuesta == "ok") {
				echo '<script>
				swal({
							type:"success",
						  	title: "¡CORRECTO!",
						  	text: "¡Su cuenta ha sido verificada, ya puede ingresar a la aplicación!",
						  	showConfirmButton: true,
							confirmButtonText: "Cerrar"						  
					}).then(function(result){
							if(result.value){   
							    window.location = "' . $ruta . 'ingreso"
							  } 
					});
				</script>';
				return;
			}
		}
	}

	/*=============================================
	Recuperar contraseña
=============================================*/
	public static function ctrRecuperarPassword()
	{

		if (isset($_POST["emailRecuperarPassword"])) {
			if (preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/', $_POST["emailRecuperarPassword"])) {
				/*=============================================
GENERAR CONTRASEÑA ALEATORIA
=============================================*/
				function generarPassword($longitud)
				{
					$str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
					$password = "";
					//Reconstruimos la contraseña segun la longitud que se quiera
					for ($i = 0; $i < $longitud; $i++) {
						//obtenemos un caracter aleatorio escogido de la cadena de caracteres
						$password .= substr($str, rand(0, 62), 1);
					}
					return $password;
				}

				$nuevoPassword = generarPassword(8);
				$encriptar = crypt($nuevoPassword, '$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$');
				$tabla = "usuarios";
				$item = "email";
				$valor = $_POST["emailRecuperarPassword"];
				$traerUsuario = ModeloUsuarios::mdlMostrarUsuarios($tabla, $item, $valor);
				if ($traerUsuario) {
					$id = $traerUsuario["id_usuario"];
					$item = "password";
					$valor = $encriptar;
					$actualizarPassword = ModeloUsuarios::mdlActualizarUsuario($tabla, $id, $item, $valor);
					if ($actualizarPassword  == "ok") {
						/*=============================================
	Verificación Correo Electrónico
	=============================================*/
						$ruta = ControladorRuta::ctrRuta();
						date_default_timezone_set("America/Bogota");
						$mail = new PHPMailer;
						$mail->Charset = "UTF-8";

						$mail->isSMTP();
						$mail->Host = 'smtp.mi.com.co';
						$mail->SMTPAuth = true;
						$mail->Username = 'info@modelo-de-negocio-labs.com.co';
						$mail->Password = 'KwkModb93oz';
						$mail->SMTPSecure = 'ssl';
						$mail->Port = 465;

						$mail->setFrom("info@modelo-de-negocio-labs.com.co", "Modelo De Negocio Labs");
						$mail->addReplyTo("modelodenegocioapp@gmail.com", "Modelo De Negocio Labs");
						$mail->Subject  = "Solicitud de recuperar password, Modelod de Negocio Labs";
						$mail->addAddress($traerUsuario["email"]);

						$mail->addCC('grcarvajal@gmail.com', 'Solicitud de recuperar password, Modelo De Negocio Labs');


						$mail->msgHTML('<div style="width:100%; background:white; position:relative; font-family:sans-serif; padding-bottom:40px">
				
				<div style="position:relative; margin:auto; width:600px; background:#eee; padding:20px">		
					<center>
					<h1 style="font-weight:100; color:#999">SOLICITUD DE NUEVO PASSWORD</h1>
					<hr style="border:1px solid #ccc; width:80%">
					<h2 style="font-weight:100; color:#999; padding:0 20px"><strong>Su nuevo password es: </strong>' . $nuevoPassword . '</h2>
					<a href="' . $ruta . 'ingreso" target="_blank" style="text-decoration:none">
					<div style="line-height:30px; background:#FF7E09; width:60%; padding:20px; color:white">		
						Clic para ingresar
					</div>
					</a>
					<h4 style="font-weight:100; color:#999; padding:0 20px">Ingrese nuevamente al sitio con este password y recuerde cambiarlo en el panel de perfil de usuario</h4>
					<br>
					<hr style="border:1px solid #ccc; width:80%">
					<h5 style="font-weight:100; color:#999">Si no se inscribio en esta cuenta, puede ignorar este e-mail y la cuenta se eliminara.</h5>
					</center>
				</div>
				    <div style="background:#000000;">
            			<center>
            				<img src="https://modelo-de-negocio-labs.com.co/img/logo-medelo-de-negocio.png" alt="Modelo de Negocio Labs">
            			</center>
        			</div>
    		      <center>
                     <a style="list-style: none; text-decoration: none; color:#00AAAA;" href="https://www.facebook.com/ModeloDeNegocio/" target="_black"><img src="https://modelo-de-negocio-labs.com.co/RI/backoffice/vistas/img/inicio/iconFacebook.png" width="36" height="36"> Facebook</a>
                    <a style="list-style: none; text-decoration: none; color:#00AAAA;" href="https://www.instagram.com/modelodenegocioapp/" target="_black"><img src="https://modelo-de-negocio-labs.com.co/RI/backoffice/vistas/img/inicio/iconInstagram.png" width="36" height="36"> Instagram</a>
                     <a style="list-style: none; text-decoration: none; color:#00AAAA;" href="https://www.youtube.com/channel/UCBQCJlK4ON1b0PjbHO-ZOGw/featured" target="_black"><img src="https://modelo-de-negocio-labs.com.co/RI/backoffice/vistas/img/inicio/iconYoutube.png" width="36" height="36"> Youtube</a> 
                    </center>   
			</div>');
						$envio = $mail->Send();
						if (!$envio) {
							echo '<script>
			swal({
				type:"error",
				title: "¡ERROR!",
				text: "¡¡Ha ocurrido un problema enviando verificación de correo electrónico a ' . $traerUsuario["email"] . ' ' . $mail->ErrorInfo . ', por favor inténtelo nuevamente",
				showConfirmButton: true,
				confirmButtonText: "Cerrar"
				}).then(function(result){
				if(result.value){
					history.back();
				}
			});	
		</script>';
						} else {
							echo '<script>
					swal({
						type:"success",
						title: "¡SU NUEVA CONTRASEÑA HA SIDO ENVIADA!",
						text: "¡Por favor revise la bandeja de entrada o la carpeta SPAM de su correo electrónico para tomar la nueva contraseña!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"
						}).then(function(result){
						if(result.value){
							window.location = "' . $ruta . 'ingreso";
						}
					});	
				</script>';
						}
					}
				} else {
					echo '<script>
				swal({
					type:"error",
				  	title: "¡ERROR!",
				  	text: "¡El correo no existe en el sistema, puede registrase nuevamente con ese correo!",
				  	showConfirmButton: true,
					confirmButtonText: "Cerrar"				  
					}).then(function(result){
						if(result.value){   
						    history.back();
						  } 
				});
			</script>';
				}
			} else {
				echo '<script>
			swal({
				type:"error",
				title: "¡CORREGIR!",
				text: "¡Error al escribir el correo!",
				showConfirmButton: true,
				confirmButtonText: "Cerrar"
				}).then(function(result){
				if(result.value){
					history.back();
				}
			});	
		</script>';
			}
		}
	}


	/*=============================================
	Mostrar Usuarios
	=============================================*/

	public static function ctrMostrarUsuarios($item, $valor)
	{

		$tabla = "persona";
		$respuesta = ModeloUsuarios::mdlMostrarUsuarios($tabla, $item, $valor);
		return $respuesta;
	}


	/*=============================================
	Actualizar Usuario
	=============================================*/

	public static function ctrActualizarUsuario($id, $item, $valor)
	{

		$tabla = "usuarios";

		$respuesta = ModeloUsuarios::mdlActualizarUsuario($tabla, $id, $item, $valor);

		return $respuesta;
	}

	/*=============================================
	Cambiar foto perfil
=============================================*/
	public function ctrCambiarFoto()
	{
		if (isset($_POST["idClienteImagen"])) {
			$pagina = $_POST["pagina"]; // pagina de retorono
			if (isset($_FILES["nuevaImagen"]["tmp_name"]) && !empty($_FILES["nuevaImagen"]["tmp_name"])) {
				list($ancho, $alto) = getimagesize($_FILES["nuevaImagen"]["tmp_name"]);
				$nuevoAncho = 500;
				$nuevoAlto = 500;
				/*=============================================
CREAMOS EL DIRECTORIO DONDE VAMOS A GUARDAR LA FOTO DEL USUARIO
=============================================*/
				$directorio = "vistas/img/usuarios/" . $_POST["idClienteImagen"];
				/*=============================================
PRIMERO PREGUNTAMOS SI EXISTE OTRA IMAGEN EN LA BD Y EL CARPETA
=============================================*/
				// if($ruta != ""){
				// 	unlink($ruta);
				// }else{
				if (!file_exists($directorio)) {
					mkdir($directorio, 0755);
				}
				//}


				/*=============================================
DE ACUERDO AL TIPO DE IMAGEN APLICAMOS LAS FUNCIONES POR DEFECTO DE PHP
=============================================*/
				if ($_FILES["nuevaImagen"]["type"] == "image/jpeg") {
					$aleatorio = mt_rand(100, 999);
					$ruta = $directorio . "/" . $aleatorio . ".jpg";
					$origen = imagecreatefromjpeg($_FILES["nuevaImagen"]["tmp_name"]);
					$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);
					imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);
					imagejpeg($destino, $ruta);
				} else if ($_FILES["nuevaImagen"]["type"] == "image/png") {
					$aleatorio = mt_rand(100, 999);
					$ruta = $directorio . "/" . $aleatorio . ".png";
					$origen = imagecreatefrompng($_FILES["nuevaImagen"]["tmp_name"]);
					$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);
					imagealphablending($destino, FALSE);
					imagesavealpha($destino, TRUE);
					imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);
					imagepng($destino, $ruta);
				} else {
					echo '<div class="alert alert-danger">¡Solo formatos de imagen JPG y/o PNG!</div>';
					return;
				}
				// final condicion
				$rutaApp = ControladorGeneral::ctrRutaApp();
				$tabla = "usuarios";
				$id = $_POST["idClienteImagen"];
				$item = "foto";
				$valor = $ruta;
				$respuesta = ModeloUsuarios::mdlActualizarUsuario($tabla, $id, $item, $valor);
				if ($respuesta == "ok") {
					echo '<script>
						window.location = "' . $rutaApp . '' . $pagina . '";
					</script>';
				}
			}
		}
	}

	/*=============================================
Cambiar contraseña
=============================================*/
	public function ctrCambiarPassword()
	{

		if (isset($_POST["idClientePass"])) {
			if ($_POST['nuevaPassword'] == $_POST['nuevaPassword2']) {
				$rutaApp = ControladorGeneral::ctrRutaApp();
				$hashPassword = password_hash($_POST['nuevaPassword'], PASSWORD_DEFAULT);
				$tabla = "usuarios";
				$id = $_POST["idClientePass"];
				$item = "password";
				$valor = $hashPassword;
				$respuesta = ModeloUsuarios::mdlActualizarUsuario($tabla, $id, $item, $valor);
				if ($respuesta == "ok") {
					echo '<script>
						window.location = "' . $rutaApp . 'perfil";
					</script>';
				}
			} else {
				echo '<div class="alert alert-danger">¡Debe repetir el mismo password!</div>';
				return;
			}
		}
	}

	/*=============================================
	Actualizar Usuario completar datos perfil
=============================================*/
	public function ctrActualizarPerfilUsuario()
	{
		if (isset($_POST["nombreUsuario"])) {
			$rutaApp = ControladorGeneral::ctrRutaApp();
			$tabla = "usuarios";
			$datos = array(
				"id" => $_POST["idUsuario"],
				"nombre" => $_POST["nombreUsuario"],
				"email" => $_POST["email"],
				"pais" => $_POST["pais"],
				"ciudad" => $_POST["ciudad"],
				"contenido" => $_POST["contenido"]
			);
			$respuesta = ModeloUsuarios::mdlActualizarPerfilUsuario($tabla, $datos);
			if ($respuesta == "ok") {
				echo '<script>
						window.location = "' . $rutaApp . 'perfil";
					</script>';
			}
		}
	}
}
