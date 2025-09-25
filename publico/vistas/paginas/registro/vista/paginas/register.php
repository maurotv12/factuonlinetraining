<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/factuonlinetraining/App/controladores/usuarios.controlador.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/factuonlinetraining/App/controladores/autenticacion.controlador.php";

$registro = new ControladorAutenticacion();
$registro->ctrRegistroUsuario();

$rutaLogin = ControladorRuta::ctrRutaLogin();
?>

<link rel="stylesheet" href="/factuonlinetraining/assets/css/validacionRegistro.css">
<div id="auth">
    <div class="row h-100 pt-5">
        <div class="col-lg-4 col-12">
        </div>
        <div class="col-lg-4 col-12">
            <div id="auth-left">
                <br>
                <h1 class="auth-title">Crea una cuenta</h1>
                <form method="post" onsubmit="return validarPoliticas()">
                    <input type="hidden" value="clienteRegistro" name="usuarioRegistro">
                    <div class="form-group position-relative has-icon-left mb-2">
                        <input type="email" class="form-control form-control-xl" name="emailRegistro" placeholder="e-Mail" required="">
                        <div class="form-control-icon">
                            <i class="bi bi-envelope"></i>
                        </div>
                    </div>
                    <div class="form-group position-relative has-icon-left mb-2">
                        <input type="text" class="form-control form-control-xl" name="nombreRegistro" placeholder="Nombre" required="">
                        <div class="form-control-icon">
                            <i class="bi bi-person"></i>
                        </div>
                    </div>
                    <div class="form-group position-relative has-icon-left mb-2">
                        <input type="password" class="form-control form-control-xl" name="passRegistro" placeholder="Contraseña" required="">
                        <div class="form-control-icon">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                    </div>

                    <!-- Checkbox de políticas de privacidad -->
                    <div class="politicas-container" id="politicasContainer">
                        <label class="custom-checkbox">
                            <input type="checkbox" id="politicas" name="politicas" required>
                            <span class="checkmark"></span>
                            <span class="politicas-text">
                                Acepto las <a href="#" data-bs-toggle="modal" data-bs-target="#modalPoliticas">políticas de privacidad</a> y términos de servicio
                                <small class="d-block text-muted mt-1">
                                    <i class="fas fa-info-circle"></i>
                                    Al aceptar, su cuenta será verificada automáticamente
                                </small>
                            </span>
                        </label>
                        <div class="politicas-error" id="politicasError">
                            <i class="fas fa-exclamation-triangle"></i>
                            Debe aceptar las políticas de privacidad para continuar
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <input type="submit" id="submit" class="btn btn-primary btn-lg shadow-lg mt-3 auth-colorBtn" value="Crear Cuenta">
                    </div>
                </form>
                <div class="text-center mt-3 text-lg fs-5">
                    <p class='text-gray-600'>¿Ya tienes una cuenta? <a href="<?php echo $rutaLogin; ?>"
                            class="font-bold">Entrar</a>.</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-12">
        </div>
    </div>
</div>

<!-- Modal de Políticas de Privacidad -->
<div class="modal fade" id="modalPoliticas" tabindex="-1" aria-labelledby="modalPoliticasLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPoliticasLabel">
                    <i class="fas fa-shield-alt text-primary"></i>
                    Políticas de Privacidad y Términos de Servicio
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Importante:</strong> Al aceptar estas políticas, su cuenta será verificada automáticamente y podrá acceder a todos nuestros servicios.
                </div>
                <div class="accordion" id="accordionPoliticas">
                    <!-- Políticas de Privacidad -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingPrivacidad">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePrivacidad" aria-expanded="true" aria-controls="collapsePrivacidad">
                                <i class="fas fa-user-shield me-2"></i>
                                Políticas de Privacidad
                            </button>
                        </h2>
                        <div id="collapsePrivacidad" class="accordion-collapse collapse show" aria-labelledby="headingPrivacidad" data-bs-parent="#accordionPoliticas">
                            <div class="accordion-body">
                                <h6>Recopilación de Información</h6>
                                <p>Recopilamos la información que usted nos proporciona directamente cuando se registra en nuestra plataforma, incluyendo nombre, correo electrónico y otros datos de perfil.</p>

                                <h6>Uso de la Información</h6>
                                <p>Utilizamos su información para:</p>
                                <ul>
                                    <li>Proporcionarle acceso a nuestros cursos y servicios</li>
                                    <li>Comunicarnos con usted sobre actualizaciones y nuevos contenidos</li>
                                    <li>Mejorar nuestros servicios y experiencia de usuario</li>
                                    <li>Cumplir con obligaciones legales</li>
                                </ul>

                                <h6>Protección de Datos</h6>
                                <p>Implementamos medidas de seguridad técnicas y organizacionales para proteger su información personal contra acceso no autorizado, alteración, divulgación o destrucción.</p>

                                <h6>Sus Derechos</h6>
                                <p>Usted tiene derecho a acceder, rectificar, eliminar o portar sus datos personales. Puede ejercer estos derechos contactándonos.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Términos de Servicio -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTerminos">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTerminos" aria-expanded="false" aria-controls="collapseTerminos">
                                <i class="fas fa-file-contract me-2"></i>
                                Términos de Servicio
                            </button>
                        </h2>
                        <div id="collapseTerminos" class="accordion-collapse collapse" aria-labelledby="headingTerminos" data-bs-parent="#accordionPoliticas">
                            <div class="accordion-body">
                                <h6>Aceptación de Términos</h6>
                                <p>Al registrarse y usar nuestros servicios, usted acepta cumplir con estos términos y condiciones.</p>

                                <h6>Uso Permitido</h6>
                                <p>Nuestros servicios están destinados para uso educativo y profesional. No está permitido:</p>
                                <ul>
                                    <li>Compartir credenciales de acceso</li>
                                    <li>Redistribuir contenido sin autorización</li>
                                    <li>Usar la plataforma para actividades ilegales</li>
                                    <li>Intentar vulnerar la seguridad del sistema</li>
                                </ul>

                                <h6>Propiedad Intelectual</h6>
                                <p>Todo el contenido de los cursos, incluyendo videos, textos, ejercicios y materiales, es propiedad de Factu Online Training y está protegido por derechos de autor.</p>

                                <h6>Modificaciones</h6>
                                <p>Nos reservamos el derecho de modificar estos términos en cualquier momento. Le notificaremos sobre cambios significativos.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cerrar
                </button>
                <button type="button" class="btn btn-success" id="aceptarPoliticas">
                    <i class="fas fa-check"></i> Acepto las Políticas
                </button>
            </div>
        </div>
    </div>
</div>