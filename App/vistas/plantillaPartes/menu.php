<?php
$roles = $_SESSION["rolesU"] ?? [];
$idsRoles = array_column($roles, 'id'); // extrae solo los IDs
?>

<!-- Bootstrap Offcanvas Menu -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
    <!-- Offcanvas Header -->
    <div class="offcanvas-header">
        <div style="font-family: 'Nunito', sans-serif;" class="d-flex align-items-center">
            <?php
            // Ruta dinámica que siempre apuntará al logo
            $rutaLogo = $_SERVER['DOCUMENT_ROOT'] . "/cursosApp/App/vistas/img/logo.png";
            $rutaWebLogo = "/cursosApp/App/vistas/img/logo.png";
            ?>
            <img src="<?php echo $rutaWebLogo; ?>" alt="Logo" class="logo-offcanvas me-2" style="height: 40px;">
            <h5 class="offcanvas-title mb-0" id="offcanvasSidebarLabel">CursosApp</h5>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <!-- Offcanvas Body -->
    <div class="offcanvas-body p-0">
        <div class="sidebar-menu">
            <?php
            // Verificar el rol del usuario y mostrar el menú correspondiente
            if (in_array(1, $idsRoles)) {
                include 'menuAdmin.php';         // ID 1 → Admin
            }

            if (in_array(2, $idsRoles)) {
                include 'menuProfesor.php';      // ID 2 → Profesor/Instructor
            }

            if (in_array(3, $idsRoles)) {
                include 'menuEstudiante.php';    // ID 3 → Estudiante
            }
            ?>

            <h3 class="m-5">General</h3>
            <ul class="menu">
                <li class="sidebar-title">Contenido y Soporte</li>

                <?php if (in_array(1, $idsRoles)) { ?>
                    <li class="sidebar-item">
                        <a href="/cursosApp/App/paginas" class='sidebar-link'>
                            <i class="bi bi-file-earmark-text"></i>
                            <span>Páginas</span>
                        </a>
                    </li>
                <?php } ?>


                <li class="sidebar-item">
                    <a href="/cursosApp/App/faq" class='sidebar-link'>
                        <i class="bi bi-question-circle"></i>
                        <span>FAQs</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="/cursosApp/App/soporte" class='sidebar-link'>
                        <i class="bi bi-envelope-paper"></i>
                        <span>Soporte</span>
                    </a>
                </li>
            </ul>

            <ul class="menu">
                <li class="sidebar-title">Configuración</li>

                <li class="sidebar-item">
                    <a href="/cursosApp/App/configuracion" class='sidebar-link'>
                        <i class="bi bi-gear-fill"></i>
                        <span>Configuración General</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="/cursosApp/App/perfil" class='sidebar-link'>
                        <i class="bi bi-person-circle"></i>
                        <span>Perfil</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="#" onclick="cerrarSesion()" class='sidebar-link text-danger'>
                        <i class="bi bi-door-closed"></i>
                        <span>Salir</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<script>
    function cerrarSesion() {
        if (confirm('¿Estás seguro de que quieres cerrar sesión?')) {
            window.location.href = '/cursosApp/App/index.php?pagina=general/salir';
        }
    }
</script>