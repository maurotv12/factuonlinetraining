    <?php
    require_once $_SERVER['DOCUMENT_ROOT'] . "/cursosapp/publico/controladores/ruta.controlador.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/cursosapp/App/controladores/autenticacion.controlador.php";

    $rutaLogin = ControladorRuta::ctrRutaLogin();

    // Procesar recuperación de contraseña
    ControladorAutenticacion::ctrRecuperarPassword();
    ?>

    <div id="auth">
        <div class="row h-100 pt-5">
            <div class="col-lg-4 col-12">
            </div>
            <div class="col-lg-4 col-12">
                <div id="auth-left">
                    <br>
                    <h1 class="auth-title">Recuperar contraseña</h1>
                    <p class="auth-subtitle mb-5">Ingrese su correo electrónico y le enviaremos una nueva contraseña temporal.</p>

                    <form method="post">
                        <div class="form-group position-relative has-icon-left mb-2">
                            <input type="email"
                                name="emailRecuperarPassword"
                                class="form-control form-control-xl"
                                placeholder="Correo electrónico"
                                required>
                            <div class="form-control-icon">
                                <i class="bi bi-envelope"></i>
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-block btn-lg shadow-lg mt-3 auth-colorBtn">
                                Recuperar contraseña
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-3 text-lg fs-5">
                        <p class='text-gray-600'>¿Recuerdas tu cuenta?
                            <a href="<?php echo $rutaLogin; ?>" class="font-bold">Entrar</a>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-12">
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>