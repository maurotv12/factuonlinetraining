<?php
// Iniciar sesión al comienzo si no está ya iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Incluir PHPMailer
require_once $_SERVER['DOCUMENT_ROOT'] . "/factuonlinetraining/App/extensiones/vendor/autoload.php";

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;


require_once $_SERVER['DOCUMENT_ROOT'] . "/factuonlinetraining/App/modelos/usuarios.modelo.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/factuonlinetraining/App/controladores/usuarios.controlador.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/factuonlinetraining/App/controladores/general.controlador.php";

class ControladorAutenticacion
{

    /*--=====================================
	Registro de Usuarios
======================================--*/
    public static function ctrRegistroUsuario()
    {
        if (isset($_POST["nombreRegistro"])) {
            $ruta = ControladorGeneral::ctrRutaApp();
            $tabla = "persona";
            $item = "email";
            $valor = $_POST["emailRegistro"];

            $respuesta = ModeloUsuarios::mdlMostrarUsuarios($tabla, $item, $valor);

            // Verificar si el usuario ya existe
            if ($respuesta && isset($respuesta["email"]) && $respuesta["email"] == $_POST["emailRegistro"]) {
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
                // Validar formato de contraseña
                $password = $_POST['passRegistro'];
                $erroresPassword = [];

                // Verificar longitud mínima
                if (strlen($password) < 8) {
                    $erroresPassword[] = "mínimo 8 caracteres";
                }

                // Verificar letra mayúscula
                if (!preg_match('/[A-Z]/', $password)) {
                    $erroresPassword[] = "una letra mayúscula";
                }

                // Verificar letra minúscula
                if (!preg_match('/[a-z]/', $password)) {
                    $erroresPassword[] = "una letra minúscula";
                }

                // Verificar número
                if (!preg_match('/[0-9]/', $password)) {
                    $erroresPassword[] = "un número";
                }

                // Si hay errores de contraseña, mostrarlos
                if (!empty($erroresPassword)) {
                    echo '<script>
                        swal({
                            type:"error",
                            title: "¡CONTRASEÑA INVÁLIDA!",
                            text: "La contraseña debe contener: ' . implode(', ', $erroresPassword) . '",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                            }).then(function(result){
                            if(result.value){
                                history.back();
                            }
                        });
                    </script>';
                    return;
                }

                // Validar nombre
                $nombre = trim($_POST["nombreRegistro"]);
                if (strlen($nombre) < 2) {
                    echo '<script>
                        swal({
                            type:"error",
                            title: "¡NOMBRE INVÁLIDO!",
                            text: "El nombre debe tener al menos 2 caracteres",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                            }).then(function(result){
                            if(result.value){
                                history.back();
                            }
                        });
                    </script>';
                    return;
                }

                if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $nombre)) {
                    echo '<script>
                        swal({
                            type:"error",
                            title: "¡NOMBRE INVÁLIDO!",
                            text: "El nombre solo puede contener letras y espacios",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                            }).then(function(result){
                            if(result.value){
                                history.back();
                            }
                        });
                    </script>';
                    return;
                }

                // Validar que las políticas de privacidad estén aceptadas
                if (!isset($_POST["politicas"]) || $_POST["politicas"] != "on") {
                    echo '<script>
                        swal({
                            type:"error",
                            title: "¡POLÍTICAS REQUERIDAS!",
                            text: "Debe aceptar las políticas de privacidad para continuar con el registro",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                            }).then(function(result){
                            if(result.value){
                                history.back();
                            }
                        });
                    </script>';
                    return;
                }

                $hashPassword = password_hash($password, PASSWORD_DEFAULT);
                $tabla = "persona";
                $datos = array(
                    "usuario" => $_POST["usuarioRegistro"],
                    "nombre" => $_POST["nombreRegistro"],
                    "email" => $_POST["emailRegistro"],
                    "password" => $hashPassword,
                    "politicas" => $_POST["politicas"]
                );
                $respuesta2 = ModeloUsuarios::mdlRegistroUsuario($tabla, $datos);
                if ($respuesta2 == "ok") {
                    $respuesta = ModeloUsuarios::mdlMostrarUsuarios($tabla, $item, $valor);

                    // Asignar rol de "estudiante" por defecto al nuevo usuario
                    $idUsuario = $respuesta["id"];
                    $rolEstudiante = ModeloUsuarios::mdlActualizarRol($idUsuario, "estudiante");

                    $_SESSION["nombre"] = $respuesta["nombre"];
                    $_SESSION["id"] = $respuesta["id"];

                    // Obtener roles del usuario recién registrado para la sesión
                    $rolesUsuario = ModeloUsuarios::mdlObtenerRolesPorUsuario($idUsuario);
                    $_SESSION["rolesU"] = $rolesUsuario;

                    // Obtener la ruta de la aplicación
                    $ruta = ControladorGeneral::ctrRutaApp();

                    echo '<script>
                        if (typeof Swal !== "undefined") {
                            Swal.fire({
                                icon: "success",
                                title: "¡Registro Exitoso!",
                                text: "Su cuenta ha sido creada y verificada correctamente",
                                timer: 2000,
                                showConfirmButton: false
                            }).then(function() {
                                window.location = "' . $ruta . 'inicio";
                            });
                        } else {
                            window.location = "' . $ruta . 'inicio";
                        }
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

            if ($respuesta && isset($respuesta["id"]) && isset($respuesta["email"]) && isset($respuesta["password"])) {
                $rolesUsuario = ModeloUsuarios::mdlObtenerRolesPorUsuario($respuesta["id"]);
                if ($respuesta["email"] == $_POST["emailIngreso"] && password_verify($_POST['passIngreso'], $respuesta["password"])) {
                    // La sesión ya está iniciada al comienzo del archivo
                    $_SESSION["validarSesion"] = "ok";
                    $_SESSION["idU"] = $respuesta["id"];
                    $_SESSION["nombreU"] = $respuesta["nombre"];
                    $_SESSION["emailU"] = $respuesta["email"];
                    $_SESSION["rolesU"] = $rolesUsuario;


                    $idU =     $respuesta["id"];
                    $navU = $_POST["navegadorU"];
                    $ipU =     $_POST["ipU"];
                    $res = ModeloUsuarios::mdlRegistroIngresoUsuarios($idU, $navU, $ipU);

                    $ruta = ControladorGeneral::ctrRutaApp();
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
            } else {
                echo '<script>
                        swal({
                            type:"error",
                            title: "¡ERROR!",
                            text: "¡Usuario no encontrado!",
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
            // Validar formato de email usando filter_var (más robusto)
            if (filter_var($_POST["emailRecuperarPassword"], FILTER_VALIDATE_EMAIL)) {
                /*=============================================
                GENERAR CONTRASEÑA ALEATORIA
                =============================================*/
                function generarPassword($longitud)
                {
                    // Incluir caracteres especiales para mayor seguridad
                    $mayusculas = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
                    $minusculas = "abcdefghijklmnopqrstuvwxyz";
                    $numeros = "1234567890";
                    $especiales = "!@#$%&*";

                    $password = "";

                    // Asegurar al menos un carácter de cada tipo
                    $password .= $mayusculas[rand(0, strlen($mayusculas) - 1)];
                    $password .= $minusculas[rand(0, strlen($minusculas) - 1)];
                    $password .= $numeros[rand(0, strlen($numeros) - 1)];
                    $password .= $especiales[rand(0, strlen($especiales) - 1)];

                    // Completar con caracteres aleatorios
                    $todosCaracteres = $mayusculas . $minusculas . $numeros . $especiales;
                    for ($i = 4; $i < $longitud; $i++) {
                        $password .= $todosCaracteres[rand(0, strlen($todosCaracteres) - 1)];
                    }

                    // Mezclar la contraseña
                    return str_shuffle($password);
                }

                $nuevoPassword = generarPassword(12); // Contraseña más larga y segura
                $hashPassword = password_hash($nuevoPassword, PASSWORD_DEFAULT); // Usar password_hash en lugar de crypt

                // Usar la tabla correcta de tu proyecto
                $tabla = "persona";
                $item = "email";
                $valor = $_POST["emailRecuperarPassword"];

                $traerUsuario = ModeloUsuarios::mdlMostrarUsuarios($tabla, $item, $valor);

                if ($traerUsuario) {
                    // Actualizar contraseña usando la estructura correcta
                    $datosActualizar = array(
                        "id" => $traerUsuario["id"], // Usar "id" en lugar de "id_usuario"
                        "password" => $hashPassword
                    );

                    // Usar método de actualización apropiado
                    $actualizarPassword = ModeloUsuarios::mdlActualizarPassword($datosActualizar);

                    if ($actualizarPassword == "ok") {
                        /*=============================================
                        Envío de correo electrónico
                        =============================================*/
                        $ruta = ControladorGeneral::ctrRutaApp();
                        date_default_timezone_set("America/Bogota");

                        $mail = new PHPMailer(true);
                        $mail->CharSet = "UTF-8";

                        try {
                            // Configuración SMTP
                            $mail->isSMTP();
                            $mail->Host = 'sandbox.smtp.mailtrap.io';
                            $mail->SMTPAuth = true;
                            $mail->Username = 'd26b5cb1025efd';
                            $mail->Password = 'dfaaeca0ba5f82';
                            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                            $mail->Port = 465;

                            $mail->setFrom("hello@demomailtrap.co", "Cursos App");
                            $mail->addReplyTo("hello@demomailtrap.co", "Soporte Cursos App");
                            $mail->Subject = "Recuperación de contraseña - Cursos App";
                            $mail->addAddress($traerUsuario["email"]);

                            $mail->isHTML(true);
                            $mail->Body = '
                            <div style="width:100%; background:#f4f4f4; position:relative; font-family:Arial, sans-serif; padding:40px 0">
                                <div style="position:relative; margin:auto; max-width:600px; background:white; padding:40px; border-radius:10px; box-shadow:0 4px 10px rgba(0,0,0,0.1)">		
                                    <div style="text-align:center; margin-bottom:30px">
                                        <h1 style="color:#333; font-weight:300; margin:0">Recuperación de Contraseña</h1>
                                        <hr style="border:none; height:2px; background:#007bff; width:100px; margin:20px auto">
                                    </div>
                                    
                                    <div style="background:#f8f9fa; padding:20px; border-radius:8px; margin:20px 0">
                                        <p style="color:#666; font-size:16px; line-height:1.6; margin:0 0 15px 0">
                                            Hola <strong>' . htmlspecialchars($traerUsuario["nombre"]) . '</strong>,
                                        </p>
                                        <p style="color:#666; font-size:16px; line-height:1.6; margin:0 0 15px 0">
                                            Has solicitado recuperar tu contraseña. Tu nueva contraseña temporal es:
                                        </p>
                                        <div style="text-align:center; background:#007bff; color:white; padding:15px; border-radius:5px; font-size:18px; font-weight:bold; letter-spacing:2px; margin:20px 0">
                                            ' . $nuevoPassword . '
                                        </div>
                                    </div>
                                    
                                    <div style="text-align:center; margin:30px 0">
                                        <a href="' . $ruta . 'login" 
                                           style="background:#007bff; color:white; padding:15px 30px; text-decoration:none; border-radius:5px; font-weight:bold; display:inline-block">
                                            Iniciar Sesión
                                        </a>
                                    </div>
                                    
                                    <div style="background:#fff3cd; border:1px solid #ffeaa7; padding:15px; border-radius:5px; margin:20px 0">
                                        <p style="color:#856404; font-size:14px; margin:0; text-align:center">
                                            <strong>Importante:</strong> Por tu seguridad, te recomendamos cambiar esta contraseña después de iniciar sesión.
                                        </p>
                                    </div>
                                    
                                    <hr style="border:none; height:1px; background:#eee; margin:30px 0">
                                    
                                    <p style="color:#999; font-size:12px; text-align:center; margin:0">
                                        Si no solicitaste este cambio, puedes ignorar este correo. Tu cuenta permanece segura.
                                    </p>
                                </div>
                            </div>';

                            $envio = $mail->send();

                            echo '<script>
                                if (typeof Swal !== "undefined") {
                                    Swal.fire({
                                        icon: "success",
                                        title: "¡Contraseña Enviada!",
                                        text: "Se ha enviado una nueva contraseña a tu correo electrónico. Revisa tu bandeja de entrada y la carpeta de spam.",
                                        showConfirmButton: true,
                                        confirmButtonText: "Ir a Iniciar Sesión"
                                    }).then(function(result){
                                        if(result.value){
                                            window.location = "' . $ruta . 'login";
                                        }
                                    });
                                } else {
                                    swal({
                                        type:"success",
                                        title: "¡Contraseña Enviada!",
                                        text: "Se ha enviado una nueva contraseña a tu correo electrónico. Revisa tu bandeja de entrada y la carpeta de spam.",
                                        showConfirmButton: true,
                                        confirmButtonText: "Ir a Iniciar Sesión"
                                    }).then(function(result){
                                        if(result.value){
                                            window.location = "' . $ruta . 'login";
                                        }
                                    });
                                }
                            </script>';
                        } catch (Exception $e) {
                            echo '<script>
                                if (typeof Swal !== "undefined") {
                                    Swal.fire({
                                        icon: "error",
                                        title: "Error de Envío",
                                        text: "Ha ocurrido un problema al enviar el correo. Por favor, inténtalo nuevamente.",
                                        showConfirmButton: true,
                                        confirmButtonText: "Cerrar"
                                    }).then(function(result){
                                        if(result.value){
                                            history.back();
                                        }
                                    });
                                } else {
                                    swal({
                                        type:"error",
                                        title: "Error de Envío",
                                        text: "Ha ocurrido un problema al enviar el correo. Por favor, inténtalo nuevamente.",
                                        showConfirmButton: true,
                                        confirmButtonText: "Cerrar"
                                    }).then(function(result){
                                        if(result.value){
                                            history.back();
                                        }
                                    });
                                }
                            </script>';
                        }
                    } else {
                        echo '<script>
                            if (typeof Swal !== "undefined") {
                                Swal.fire({
                                    icon: "error",
                                    title: "Error",
                                    text: "Ha ocurrido un error al actualizar la contraseña. Inténtalo nuevamente.",
                                    showConfirmButton: true,
                                    confirmButtonText: "Cerrar"
                                }).then(function(result){
                                    if(result.value){
                                        history.back();
                                    }
                                });
                            } else {
                                swal({
                                    type:"error",
                                    title: "Error",
                                    text: "Ha ocurrido un error al actualizar la contraseña. Inténtalo nuevamente.",
                                    showConfirmButton: true,
                                    confirmButtonText: "Cerrar"
                                }).then(function(result){
                                    if(result.value){
                                        history.back();
                                    }
                                });
                            }
                        </script>';
                    }
                } else {
                    echo '<script>
                        if (typeof Swal !== "undefined") {
                            Swal.fire({
                                icon: "error",
                                title: "Usuario No Encontrado",
                                text: "El correo electrónico no está registrado en el sistema.",
                                showConfirmButton: true,
                                confirmButtonText: "Cerrar"
                            }).then(function(result){
                                if(result.value){
                                    history.back();
                                }
                            });
                        } else {
                            swal({
                                type:"error",
                                title: "Usuario No Encontrado",
                                text: "El correo electrónico no está registrado en el sistema.",
                                showConfirmButton: true,
                                confirmButtonText: "Cerrar"
                            }).then(function(result){
                                if(result.value){
                                    history.back();
                                }
                            });
                        }
                    </script>';
                }
            } else {
                echo '<script>
                    if (typeof Swal !== "undefined") {
                        Swal.fire({
                            icon: "error",
                            title: "Email Inválido",
                            text: "Por favor, ingresa un correo electrónico válido.",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result){
                            if(result.value){
                                history.back();
                            }
                        });
                    } else {
                        swal({
                            type:"error",
                            title: "Email Inválido",
                            text: "Por favor, ingresa un correo electrónico válido.",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result){
                            if(result.value){
                                history.back();
                            }
                        });
                    }
                </script>';
            }
        }
    }


    /*=============================================
Cambiar contraseña
=============================================*/
    public function ctrCambiarPassword()
    {
        if (isset($_POST["idClientePass"])) {
            // Verificar que vengan todos los campos necesarios
            $camposRequeridos = [];

            // Detectar qué formulario se está usando
            if (isset($_POST["passwordActual"]) && isset($_POST["passwordNuevo"]) && isset($_POST["passwordConfirmar"])) {
                // Formulario del perfil (con validación de contraseña actual)
                $camposRequeridos = ["passwordActual", "passwordNuevo", "passwordConfirmar"];
                $passwordActual = $_POST["passwordActual"];
                $nuevaPassword = $_POST["passwordNuevo"];
                $confirmarPassword = $_POST["passwordConfirmar"];

                // Verificar contraseña actual
                $tabla = "persona";
                $item = "id";
                $valor = $_POST["idClientePass"];
                $usuario = ModeloUsuarios::mdlMostrarUsuarios($tabla, $item, $valor);

                if (!$usuario || !password_verify($passwordActual, $usuario["password"])) {
                    echo '<script>
                        document.addEventListener("DOMContentLoaded", function() {
                            if (typeof Swal !== "undefined") {
                                Swal.fire({
                                    icon: "error",
                                    title: "Error",
                                    text: "La contraseña actual no es correcta.",
                                    showConfirmButton: true,
                                    confirmButtonText: "Cerrar"
                                });
                            } else {
                                alert("La contraseña actual no es correcta.");
                            }
                        });
                    </script>';
                    return;
                }
            } else if (isset($_POST["nuevaPassword"]) && isset($_POST["nuevaPassword2"])) {
                // Formulario del modal (sin validación de contraseña actual)
                $camposRequeridos = ["nuevaPassword", "nuevaPassword2"];
                $nuevaPassword = $_POST["nuevaPassword"];
                $confirmarPassword = $_POST["nuevaPassword2"];
            } else {
                echo '<script>
                    document.addEventListener("DOMContentLoaded", function() {
                        if (typeof Swal !== "undefined") {
                            Swal.fire({
                                icon: "error",
                                title: "¡ERROR!",
                                text: "¡Faltan datos requeridos!",
                                showConfirmButton: true,
                                confirmButtonText: "Cerrar"
                            });
                        } else {
                            alert("Faltan datos requeridos.");
                        }
                    });
                </script>';
                return;
            }

            // Verificar que las contraseñas coincidan
            if ($nuevaPassword == $confirmarPassword) {
                $rutaApp = ControladorGeneral::ctrRutaApp();
                $hashPassword = password_hash($nuevaPassword, PASSWORD_DEFAULT);
                $tabla = "persona";
                $id = $_POST["idClientePass"];
                $item = "password";
                $valor = $hashPassword;
                $respuesta = ModeloUsuarios::mdlActualizarUsuario($tabla, $id, $item, $valor);

                if ($respuesta == "ok") {
                    echo '<script>
                        document.addEventListener("DOMContentLoaded", function() {
                            if (typeof Swal !== "undefined") {
                                Swal.fire({
                                    icon: "success",
                                    title: "¡Éxito!",
                                    text: "Contraseña cambiada correctamente.",
                                    showConfirmButton: true,
                                    confirmButtonText: "OK",
                                    timer: 3000
                                }).then(function(result){
                                    window.location = "' . $rutaApp . '";
                                });
                            } else {
                                alert("Contraseña cambiada correctamente.");
                                window.location = "' . $rutaApp . '";
                            }
                        });
                    </script>';
                } else {
                    echo '<script>
                        document.addEventListener("DOMContentLoaded", function() {
                            if (typeof Swal !== "undefined") {
                                Swal.fire({
                                    icon: "error",
                                    title: "Error",
                                    text: "Ha ocurrido un error al cambiar la contraseña. Inténtalo nuevamente.",
                                    showConfirmButton: true,
                                    confirmButtonText: "Cerrar"
                                });
                            } else {
                                alert("Error al cambiar la contraseña.");
                            }
                        });
                    </script>';
                }
            } else {
                echo '<script>
                    document.addEventListener("DOMContentLoaded", function() {
                        if (typeof Swal !== "undefined") {
                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: "Las contraseñas no coinciden.",
                                showConfirmButton: true,
                                confirmButtonText: "Cerrar"
                            });
                        } else {
                            alert("Las contraseñas no coinciden.");
                        }
                    });
                </script>';
                return;
            }
        }
    }
}
