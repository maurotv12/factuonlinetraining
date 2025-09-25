<?php
// Verificar acceso (solo profesores)
if (!ControladorGeneral::ctrUsuarioTieneAlgunRol(['profesor'])) {
    $editarUrl = !empty($curso["url_amiga"])
        ? "/factuonlinetraining/App/verCursoProfe/" . $curso["url_amiga"]
        : "/factuonlinetraining/App/verCursoProfe?id=" . $curso["id"];
    echo '<div class="alert alert-danger">No tienes permisos para acceder a esta página.</div>';
    return;
}

require_once "controladores/cursos.controlador.php";

// Cargar datos para el listado del profesor logueado
$cursos = ControladorCursos::ctrCargarListadoCursosProfesor();

// Debug temporal - solo mostrar si hay problema
if (empty($cursos)) {
    echo '<div class="alert alert-info">
        <i class="bi bi-info-circle"></i> 
        Aún no has creado ningún curso. <a href="/factuonlinetraining/App/crearCursoProfe" class="alert-link">¡Crea tu primer curso aquí!</a>
    </div>';
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
                        <i class="bi bi-person-workspace"></i>
                        Mis Cursos
                    </h4>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-primary me-2">
                            <?= count($cursos) ?> curso<?= count($cursos) !== 1 ? 's' : '' ?>
                        </span>
                        <a href="/factuonlinetraining/App/crearCursoProfe" class="btn btn-success btn-sm">
                            <i class="bi bi-plus-circle"></i> Crear Nuevo Curso
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($cursos)) : ?>
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
                                        <th>Secciones</th>
                                        <th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cursos as $index => $curso) : ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td>
                                                <?php if ($curso["banner"]) : ?>
                                                    <img src="<?= $curso["banner"] ?>" class="banner-mini" alt="Banner" style="width: 50px; height: 50px; object-fit: cover;">
                                                <?php else : ?>
                                                    <div class="bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 4px;">
                                                        <i class="bi bi-image text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?= htmlspecialchars($curso["nombre"]) ?></strong>
                                                <?php if (!empty($curso["descripcion"])) : ?>
                                                    <br><small class="text-muted"><?= htmlspecialchars(substr($curso["descripcion"], 0, 50)) ?>...</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-info"><?= htmlspecialchars($curso["categoria"]) ?></span>
                                            </td>
                                            <td>
                                                <?php if (isset($curso["valor"]) && $curso["valor"] > 0): ?>
                                                    <strong>$<?= number_format($curso["valor"], 0, ',', '.') ?></strong>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Gratis</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= $curso["estado"] == 'activo' ? 'success' : 'secondary' ?>">
                                                    <?= htmlspecialchars($curso["estado"]) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-section bg-outline-primary">
                                                    <i class="bi bi-collection"></i> <?= $curso["total_secciones"] ?? 0 ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small><?= $curso["fecha_formateada"] ?></small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <?php
                                                    // Usar URL amigable si está disponible, sino usar ID para ver
                                                    $urlVer = "/factuonlinetraining/App/verCursoProfe/" . $curso["url_amiga"];
                                                    ?>
                                                    <a href="<?= $urlVer ?>" class="btn btn-sm btn-info" title="Ver curso y editarlo">
                                                        <i class="bi bi-eye"></i> Ver y editar
                                                    </a>

                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else : ?>
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="bi bi-book display-1 text-muted"></i>
                            </div>
                            <h4 class="text-muted mb-3">¡Crea tu primer curso!</h4>
                            <p class="text-muted mb-4">
                                Aún no tienes cursos creados. Comienza compartiendo tu conocimiento con el mundo.
                            </p>
                            <a href="/factuonlinetraining/App/crearCursoProfe" class="btn btn-primary btn-lg">
                                <i class="bi bi-plus-circle"></i> Crear Mi Primer Curso
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Incluir el archivo JavaScript para la página -->
<script src="/factuonlinetraining/App/vistas/assets/js/pages/listadoCursosProfe.js"></script>

<script>
    // Función para gestionar contenido del curso
    function gestionarContenido(identificador, esUrlAmiga) {
        if (esUrlAmiga) {
            window.location.href = `/factuonlinetraining/App/verCursoProfe/${identificador}`;
        } else {
            window.location.href = `/factuonlinetraining/App/verCursoProfe?id=${identificador}`;
        }
    }
</script>