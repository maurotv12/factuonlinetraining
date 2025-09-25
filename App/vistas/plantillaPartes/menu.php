<?php
$roles = $_SESSION["rolesU"] ?? [];
$idsRoles = array_column($roles, 'id'); // extrae solo los IDs
?>

<!-- Bootstrap Offcanvas Menu -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
    <!-- Offcanvas Header -->
    <div class="offcanvas-header justify-content-center">
        <div style="font-family: 'Nunito', sans-serif;" class="d-flex align-items-center justify-content-center w-100">
            <?php
            $rutaLogo = $_SERVER['DOCUMENT_ROOT'] . "/factuonlinetraining/App/vistas/img/logo.png";
            $rutaWebLogo = "/factuonlinetraining/App/vistas/img/logo.png";
            ?>
            <img src="<?php echo $rutaWebLogo; ?>" alt="Logo" class="logo-offcanvas me-2" style="height: 60px;">
        </div>
        <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <!-- Offcanvas Body -->
    <div class="offcanvas-body p-0">
        <div class="sidebar-menu">
            <!-- Acordeón Bootstrap para los roles -->
            <div class="accordion accordion-flush" id="rolesAccordion">
                <?php
                // Verificar el rol del usuario y mostrar el menú correspondiente
                if (in_array(1, $idsRoles)) { ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingAdmin">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAdmin" aria-expanded="true" aria-controls="collapseAdmin">
                                <i class="bi bi-shield-check me-2"></i>
                                Administrador
                            </button>
                        </h2>
                        <div id="collapseAdmin" class="accordion-collapse collapse show" aria-labelledby="headingAdmin" data-bs-parent="#rolesAccordion">
                            <div class="accordion-body p-0">
                                <?php include 'menuAdmin.php'; ?>
                            </div>
                        </div>
                    </div>
                <?php }

                if (in_array(2, $idsRoles)) { ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingProfesor">
                            <button class="accordion-button <?php echo in_array(1, $idsRoles) ? 'collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapseProfesor" aria-expanded="<?php echo !in_array(1, $idsRoles) ? 'true' : 'false'; ?>" aria-controls="collapseProfesor">
                                <i class="bi bi-motherboard me-2"></i>
                                Profesor
                            </button>
                        </h2>
                        <div id="collapseProfesor" class="accordion-collapse collapse <?php echo !in_array(1, $idsRoles) ? 'show' : ''; ?>" aria-labelledby="headingProfesor" data-bs-parent="#rolesAccordion">
                            <div class="accordion-body p-0">
                                <?php include 'menuProfesor.php'; ?>
                            </div>
                        </div>
                    </div>
                <?php }

                if (in_array(3, $idsRoles)) { ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingEstudiante">
                            <button class="accordion-button <?php echo (in_array(1, $idsRoles) || in_array(2, $idsRoles)) ? 'collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEstudiante" aria-expanded="<?php echo (!in_array(1, $idsRoles) && !in_array(2, $idsRoles)) ? 'true' : 'false'; ?>" aria-controls="collapseEstudiante">
                                <i class="bi bi-mortarboard me-2"></i>
                                Estudiante
                            </button>
                        </h2>
                        <div id="collapseEstudiante" class="accordion-collapse collapse <?php echo (!in_array(1, $idsRoles) && !in_array(2, $idsRoles)) ? 'show' : ''; ?>" aria-labelledby="headingEstudiante" data-bs-parent="#rolesAccordion">
                            <div class="accordion-body p-0">
                                <?php include 'menuEstudiante.php'; ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <!-- Espaciador para empujar el botón General hacia abajo -->
            <div class="flex-grow-1"></div>

            <!-- Botón General Colapsable (en la parte inferior) -->
            <div class="general-section-bottom">
                <div class="collapse" id="collapseGeneral">
                    <div class="general-content">
                        <ul class="menu">
                            <li class="sidebar-title">Contenido y Soporte</li>

                            <?php if (in_array(1, $idsRoles)) { ?>
                                <li class="sidebar-item">
                                    <a href="/factuonlinetraining/App/paginas" class='sidebar-link'>
                                        <i class="bi bi-file-earmark-text"></i>
                                        <span>Páginas</span>
                                    </a>
                                </li>
                            <?php } ?>

                            <li class="sidebar-item">
                                <a href="/factuonlinetraining/App/faq" class='sidebar-link'>
                                    <i class="bi bi-question-circle"></i>
                                    <span>FAQs</span>
                                </a>
                            </li>

                            <li class="sidebar-item">
                                <a href="/factuonlinetraining/App/soporte" class='sidebar-link'>
                                    <i class="bi bi-envelope-paper"></i>
                                    <span>Soporte</span>
                                </a>
                            </li>
                        </ul>

                        <ul class="menu">
                            <li class="sidebar-title">Configuración</li>

                            <li class="sidebar-item">
                                <a href="/factuonlinetraining/App/configuracion" class='sidebar-link'>
                                    <i class="bi bi-gear-fill"></i>
                                    <span>Configuración General</span>
                                </a>
                            </li>

                            <li class="sidebar-item">
                                <a href="/factuonlinetraining/App/perfil" class='sidebar-link'>
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

                <button class="btn btn-general-toggle w-100" type="button" data-bs-toggle="collapse" data-bs-target="#collapseGeneral" aria-expanded="false" aria-controls="collapseGeneral">
                    <i class="bi bi-chevron-up me-2"></i>
                    General
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function cerrarSesion() {
        if (confirm('¿Estás seguro de que quieres cerrar sesión?')) {
            window.location.href = '/factuonlinetraining/App/index.php?pagina=general/salir';
        }
    }

    // Script para manejar la rotación del icono del botón General
    document.addEventListener('DOMContentLoaded', function() {
        const generalButton = document.querySelector('.btn-general-toggle');
        const generalCollapse = document.getElementById('collapseGeneral');

        if (generalButton && generalCollapse) {
            generalCollapse.addEventListener('show.bs.collapse', function() {
                const icon = generalButton.querySelector('i');
                if (icon) {
                    icon.classList.remove('bi-chevron-up');
                    icon.classList.add('bi-chevron-down');
                }
            });

            generalCollapse.addEventListener('hide.bs.collapse', function() {
                const icon = generalButton.querySelector('i');
                if (icon) {
                    icon.classList.remove('bi-chevron-down');
                    icon.classList.add('bi-chevron-up');
                }
            });
        }
    });
</script>