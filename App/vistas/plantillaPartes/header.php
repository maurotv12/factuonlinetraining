            <script>
                function cerrarSesion() {
                    if (confirm('¿Estás seguro de que quieres cerrar sesión?')) {
                        window.location.href = '/factuonlinetraining/App/index.php?pagina=general/salir';
                    }
                }
            </script>

            <div class="page-heading">
                <div class="page-title">
                    <div class="row">
                        <div class="col-12 col-md-6 order-md-1 order-last">
                            <p class="text-subtitle text-muted"><?php echo $usuario["nombre"]; ?></p>
                        </div>


                        <div class="d-flex align-items-center flex-shrink-0">
                            <button class="btn btn1 me-3 d-none d-lg-flex align-items-center" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar" aria-controls="offcanvasSidebar">
                                <i class="bi bi-list"></i> <span class="ms-2">Menú</span>
                            </button>
                            <button class="btn btn1 me-3 d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar" aria-controls="offcanvasSidebar">
                                <i class="bi bi-list"></i>
                            </button>
                            <a class="navbar-brand d-flex align-items-center" href="/factuonlinetraining/App/inicioEstudiante">
                                <?php
                                // Ruta dinámica que siempre apuntará al logo
                                $rutaLogo = $_SERVER['DOCUMENT_ROOT'] . "/factuonlinetraining/App/vistas/img/logo.png";
                                $rutaWebLogo = "/factuonlinetraining/App/vistas/img/logo.png";
                                ?>
                                <img src="<?php echo $rutaWebLogo; ?>" alt="Logo" class="logo-offcanvas me-2" style="height: 60px;">
                            </a>
                        </div>

                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active" aria-current="page">
                                        <h5><a href="#" onclick="cerrarSesion()"> Salir <i class="bi bi-door-closed"></i> </a></h5>
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>