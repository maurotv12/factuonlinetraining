<?php

/**
 * Ejemplo de uso del sistema de control de acceso por roles
 * Incluye este archivo en tus páginas para implementar control granular de contenido
 */

// Ejemplo 1: Verificar si el usuario puede ver todo el contenido de la página
$paginaActual = isset($_GET['pagina']) ? $_GET['pagina'] : 'inicio';
$tieneAcceso = ControladorGeneral::ctrVerificarAccesoContenido($paginaActual);

if (!$tieneAcceso) {
    echo '<div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            No tienes permisos para ver este contenido.
          </div>';
    return;
}

// Ejemplo 2: Mostrar contenido específico según el rol del usuario
$rolesUsuario = ControladorGeneral::ctrObtenerRolesUsuarioActual();
$esAdmin = false;
$esProfesor = false;
$esEstudiante = false;

foreach ($rolesUsuario as $rol) {
    switch ($rol['nombre']) {
        case 'admin':
        case 'superadmin':
            $esAdmin = true;
            break;
        case 'profesor':
            $esProfesor = true;
            break;
        case 'estudiante':
            $esEstudiante = true;
            break;
    }
}
?>

<!-- Ejemplo de contenido condicional -->
<div class="container-fluid">
    <?php if ($esAdmin): ?>
        <!-- Contenido solo para administradores -->
        <div class="row">
            <div class="col-12">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h5>Panel de Administrador</h5>
                        <p>Este contenido solo es visible para administradores.</p>
                        <a href="superAdmin/usuarios" class="btn btn-light">Gestionar Usuarios</a>
                        <a href="superAdmin/reportes" class="btn btn-light">Ver Reportes</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($esProfesor || $esAdmin): ?>
        <!-- Contenido para profesores y administradores -->
        <div class="row">
            <div class="col-12">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5>Panel de Profesor</h5>
                        <p>Gestiona tus cursos y estudiantes.</p>
                        <a href="cursos" class="btn btn-light">Mis Cursos</a>
                        <a href="profesores" class="btn btn-light">Otros Profesores</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($esEstudiante || $esProfesor || $esAdmin): ?>
        <!-- Contenido para todos los usuarios autenticados -->
        <div class="row">
            <div class="col-12">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5>Panel de Estudiante</h5>
                        <p>Accede a tus cursos y materiales de estudio.</p>
                        <a href="misCursos" class="btn btn-light">Mis Cursos</a>
                        <a href="inscripciones" class="btn btn-light">Inscripciones</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Contenido común para todos los usuarios -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5>Información General</h5>
                    <p>Este contenido es visible para todos los usuarios autenticados.</p>

                    <h6>Tus roles actuales:</h6>
                    <ul>
                        <?php foreach ($rolesUsuario as $rol): ?>
                            <li><span class="badge badge-primary"><?php echo ucfirst($rol['nombre']); ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
/**
 * Funciones auxiliares para usar en las vistas
 */

// Función para verificar un rol específico
function tieneRol($nombreRol)
{
    $rolesUsuario = ControladorGeneral::ctrObtenerRolesUsuarioActual();
    foreach ($rolesUsuario as $rol) {
        if ($rol['nombre'] === $nombreRol) {
            return true;
        }
    }
    return false;
}

// Función para verificar múltiples roles
function tieneAlgunRol($rolesPermitidos)
{
    if (!isset($_SESSION['id'])) {
        return false;
    }
    return ControladorGeneral::ctrVerificarRolUsuario($_SESSION['id'], $rolesPermitidos);
}
?>

<!-- Ejemplos de uso de las funciones auxiliares -->
<?php if (tieneRol('admin')): ?>
    <div class="alert alert-info">Eres administrador</div>
<?php endif; ?>

<?php if (tieneAlgunRol(['profesor', 'admin'])): ?>
    <div class="alert alert-success">Tienes permisos de profesor o superior</div>
<?php endif; ?>