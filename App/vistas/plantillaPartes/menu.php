<?php
$roles = $_SESSION["rolesU"] ?? [];
$idsRoles = array_column($roles, 'id'); // extrae solo los IDs
?>

<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header">
            <div class="d-flex justify-content-between">
                <div style="font-family: 'Nunito', sans-serif;">
                    <?php
                    // Ruta dinámica que siempre apuntará al logo
                    $rutaLogo = $_SERVER['DOCUMENT_ROOT'] . "/cursosApp/App/vistas/img/logo.png";
                    $rutaWebLogo = "/cursosApp/App/vistas/img/logo.png";
                    ?>
                    <img src="<?php echo $rutaWebLogo; ?>" alt="Logo" class="Logo">

                </div>
                <div class="toggler">
                    <a href="#" class="sidebar-hide d-xl-none d-block">
                        <i class="bi bi-x bi-middle"></i>
                    </a>
                </div>
            </div>
        </div>

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
                        <a href="paginas" class='sidebar-link'>
                            <i class="bi bi-file-earmark-text"></i>
                            <span>Páginas</span>
                        </a>
                    </li>
                <?php } ?>


                <li class="sidebar-item">
                    <a href="faq" class='sidebar-link'>
                        <i class="bi bi-question-circle"></i>
                        <span>FAQs</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="soporte" class='sidebar-link'>
                        <i class="bi bi-envelope-paper"></i>
                        <span>Soporte</span>
                    </a>
                </li>
            </ul>

            <ul class="menu">
                <li class="sidebar-title">Configuración</li>

                <li class="sidebar-item">
                    <a href="configuracion" class='sidebar-link'>
                        <i class="bi bi-gear-fill"></i>
                        <span>Configuración General</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="perfil" class='sidebar-link'>
                        <i class="bi bi-person-circle"></i>
                        <span>Perfil</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="salir" class='sidebar-link text-danger'>
                        <i class="bi bi-door-closed"></i>
                        <span>Salir</span>
                    </a>
                </li>
            </ul>
        </div>

        <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
    </div>
</div>