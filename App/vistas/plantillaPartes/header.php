            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>

            <script>
                function cerrarSesion() {
                    if (confirm('¿Estás seguro de que quieres cerrar sesión?')) {
                        window.location.href = '/cursosApp/App/index.php?pagina=general/salir';
                    }
                }
            </script>

            <div class="page-heading">
                <div class="page-title">
                    <div class="row">
                        <div class="col-12 col-md-6 order-md-1 order-last">
                            <p class="text-subtitle text-muted"><?php echo $usuario["nombre"]; ?></p>
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