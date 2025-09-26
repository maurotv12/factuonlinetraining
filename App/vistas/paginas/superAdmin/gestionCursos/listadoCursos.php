<?php
// Verificar acceso (solo administradores)
if (!ControladorGeneral::ctrUsuarioTieneAlgunRol(['admin', 'superadmin'])) {
    echo '<div class="alert alert-danger">No tienes permisos para acceder a esta página.</div>';
    return;
}

require_once "controladores/cursos.controlador.php";

// Procesar cambio de estado del curso
if (isset($_POST['guardarEstadoCurso'])) {
    $cambiarEstadoCurso = new ControladorCursos();
    $respuesta = $cambiarEstadoCurso->ctrCambiarEstadoCurso($_POST['idCurso'], $_POST['nuevoEstado']);

    if ($respuesta['error'] === false) {
        echo '<script>
            Swal.fire({
                icon: "success",
                title: "¡Estado actualizado!",
                text: "' . $respuesta['mensaje'] . '",
                showConfirmButton: true,
                confirmButtonText: "Cerrar"
            }).then(function(result) {
                if(result.value) {
                    window.location = window.location.href;
                }
            });
        </script>';
    } else {
        echo '<script>
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "' . $respuesta['mensaje'] . '",
                confirmButtonText: "Cerrar"
            });
        </script>';
    }
}

// Cargar datos para el listado
$cursos = ControladorCursos::ctrCargarListadoCursos();

// Debug temporal - solo mostrar si hay problema
if (empty($cursos) || !isset($cursos[0]['valor'])) {
    echo '<div class="alert alert-info">Debug: ';
    if (empty($cursos)) {
        echo 'No hay cursos cargados.';
    } else {
        echo 'Primer curso - campos disponibles: ' . implode(', ', array_keys($cursos[0]));
        // Verificar si url_amiga está disponible
        if (isset($cursos[0]['url_amiga'])) {
            echo ' | URL amiga encontrada: ' . $cursos[0]['url_amiga'];
        } else {
            echo ' | URL amiga NO encontrada';
        }
    }
    echo '</div>';
}
?>

<!-- Incluir CSS específico para esta página -->
<link rel="stylesheet" href="/factuonlinetraining/App/vistas/assets/css/pages/listadoCursos.css">

<div class="listado-cursos-container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="bi bi-list-ul"></i>
                        Listado de Cursos
                    </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="table_id">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Imagen</th>
                                    <th>Nombre</th>
                                    <th>Categoría</th>
                                    <th>Valor</th>
                                    <th>Estado</th>
                                    <th>Profesor</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($cursos)) : ?>
                                    <?php foreach ($cursos as $index => $curso) : ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td>
                                                <?php if ($curso["banner"]) : ?>
                                                    <img src="<?= $curso["banner"] ?>" class="banner-mini" alt="Banner" style="width: 50px; height: 50px; object-fit: cover;">
                                                <?php else : ?>
                                                    <span class="text-muted">Sin imagen</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($curso["nombre"]) ?></td>
                                            <td><?= htmlspecialchars($curso["categoria"]) ?></td>
                                            <td>
                                                <?php if (isset($curso["valor"]) && $curso["valor"] !== null): ?>
                                                    $<?= number_format($curso["valor"], 0, ',', '.') ?>
                                                <?php else: ?>
                                                    <span class="text-muted">Sin valor</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-<?= $curso["estado"] == 'activo' ? 'success' : ($curso["estado"] == 'borrador' ? 'warning' : 'secondary') ?> cambiar-estado-curso"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalEstadoCurso"
                                                    data-id="<?= $curso["id"] ?>"
                                                    data-nombre="<?= htmlspecialchars($curso["nombre"]) ?>"
                                                    data-estado-actual="<?= $curso["estado"] ?>">
                                                    <i class="bi bi-<?= $curso["estado"] == 'activo' ? 'check-circle' : ($curso["estado"] == 'borrador' ? 'pencil-square' : 'x-circle') ?>"></i>
                                                    <?= htmlspecialchars(ucfirst($curso["estado"])) ?>
                                                </button>
                                            </td>
                                            <td><?= htmlspecialchars($curso["profesor"]) ?></td>
                                            <td><?= $curso["fecha_formateada"] ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <?php
                                                    // Usar URL amigable si está disponible, sino usar ID para ver
                                                    $urlVer = "/factuonlinetraining/App/verCursoProfe/" . $curso["url_amiga"];
                                                    ?>
                                                    <a href="<?= $urlVer ?>" class="btn btn-sm btn-info">
                                                        <i class="bi bi-eye"></i> Ver
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">
                                            <i class="bi bi-inbox"></i>
                                            No hay cursos registrados.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para cambiar estado del curso -->
<div class="modal fade" id="modalEstadoCurso" tabindex="-1" aria-labelledby="modalEstadoCursoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEstadoCursoLabel">
                    <i class="bi bi-gear-fill"></i>
                    Cambiar Estado del Curso
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formCambiarEstadoCurso" method="post">
                    <input type="hidden" name="idCurso" id="idCursoEstado">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Curso:</label>
                        <p class="text-muted mb-3" id="nombreCursoModal">-</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Seleccionar nuevo estado:</label>
                        <div class="estado-options">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="nuevoEstado" value="activo" id="estadoActivo">
                                <label class="form-check-label" for="estadoActivo">
                                    <i class="bi bi-check-circle text-success"></i>
                                    <strong>ACTIVO</strong> - El curso está disponible para los estudiantes
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="nuevoEstado" value="borrador" id="estadoBorrador">
                                <label class="form-check-label" for="estadoBorrador">
                                    <i class="bi bi-pencil-square text-warning"></i>
                                    <strong>BORRADOR</strong> - El curso está en desarrollo
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="nuevoEstado" value="inactivo" id="estadoInactivo">
                                <label class="form-check-label" for="estadoInactivo">
                                    <i class="bi bi-x-circle text-secondary"></i>
                                    <strong>INACTIVO</strong> - El curso no está disponible
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <small>El cambio se aplicará inmediatamente y afectará la visibilidad del curso para los estudiantes.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg"></i> Cancelar
                </button>
                <button type="submit" form="formCambiarEstadoCurso" name="guardarEstadoCurso" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Incluir el archivo JavaScript para la página -->
<script src="/factuonlinetraining/App/vistas/assets/js/pages/listadoCursos.js"></script>