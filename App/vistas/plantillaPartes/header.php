
<div class="page-heading">
    <div class="page-title">
        <div class="d-flex justify-content-between align-items-center w-100">
            <!-- Sección izquierda: Botón menú y logo -->
            <div class="d-flex align-items-center">
                <button class="btn btn1 me-3 d-none d-lg-flex align-items-center" 
                        type="button" 
                        data-bs-toggle="offcanvas" 
                        data-bs-target="#offcanvasSidebar" 
                        aria-controls="offcanvasSidebar">
                    <i class="bi bi-list"></i> 
                    <span class="ms-2">Menú</span>
                </button>
                <button class="btn btn1 me-3 d-lg-none" 
                        type="button" 
                        data-bs-toggle="offcanvas" 
                        data-bs-target="#offcanvasSidebar" 
                        aria-controls="offcanvasSidebar">
                    <i class="bi bi-list"></i>
                </button>
                <?php
                // Obtener roles del usuario para determinar la redirección del logo
                $rolesUsuario = [];
                if (isset($_SESSION['rolesU']) && !empty($_SESSION['rolesU'])) {
                    $rolesUsuario = $_SESSION['rolesU'];
                } else {
                    // Si no están en sesión, obtenerlos de la base de datos
                    require_once "modelos/usuarios.modelo.php";
                    $rolesUsuario = ModeloUsuarios::mdlObtenerRolesPorUsuario($_SESSION['idU']);
                    $_SESSION['rolesU'] = $rolesUsuario;
                }
                
                // Extraer nombres de roles
                $nombresRoles = array_column($rolesUsuario, 'nombre');
                
                // Determinar URL de redirección según prioridad de roles
                $urlRedirecccion = '/factuonlinetraining/App/inicioEstudiante'; // Por defecto
                
                if (in_array('admin', $nombresRoles) || in_array('superadmin', $nombresRoles)) {
                    $urlRedirecccion = '/factuonlinetraining/App/usuarios';
                } elseif (in_array('profesor', $nombresRoles)) {
                    $urlRedirecccion = '/factuonlinetraining/App/listadoCursosProfe';
                } elseif (in_array('estudiante', $nombresRoles)) {
                    $urlRedirecccion = '/factuonlinetraining/App/inicioEstudiante';
                }
                ?>
                <a class="navbar-brand d-flex align-items-center" href="<?php echo $urlRedirecccion; ?>">
                    <?php
                    $rutaLogo = $_SERVER['DOCUMENT_ROOT'] . "/factuonlinetraining/App/vistas/img/logo.png";
                    $rutaWebLogo = "/factuonlinetraining/App/vistas/img/logo.png";
                    ?>
                    <img src="<?php echo $rutaWebLogo; ?>" 
                         alt="Logo" 
                         class="logo-offcanvas me-2" 
                         style="height: 40px;">
                </a>
            </div>
            <div class="d-flex align-items-center">
                <p class="mb-0 me-3" style="font-size: 1.1rem; font-weight: 500;">
                    Hola de nuevo, <?php echo $usuario["nombre"]; ?>
                </p>
                
            </div>
        </div>
    </div>
</div>