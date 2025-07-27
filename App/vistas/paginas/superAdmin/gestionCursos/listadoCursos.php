<?php
// Verificar acceso (solo administradores)
if (!ControladorGeneral::ctrUsuarioTieneAlgunRol(['admin', 'superadmin'])) {
    echo '<div class="alert alert-danger">No tienes permisos para acceder a esta página.</div>';
    return;
}

require_once "controladores/cursos.controlador.php";

// Cargar datos para el listado
$cursos = ControladorCursos::ctrCargarListadoCursos();

// Debug temporal - solo mostrar si hay problema
if (empty($cursos) || !isset($cursos[0]['valor'])) {
    echo '<div class="alert alert-info">Debug: ';
    if (empty($cursos)) {
        echo 'No hay cursos cargados.';
    } else {
        echo 'Primer curso - campos disponibles: ' . implode(', ', array_keys($cursos[0]));
    }
    echo '</div>';
}
?>

<!-- Incluir CSS específico para esta página -->
<link rel="stylesheet" href="vistas/assets/css/pages/listadoCursos.css">

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
                                                    <?= number_format($curso["valor"], 0, ',', '.') ?>
                                                <?php else: ?>
                                                    <span class="text-muted">Sin valor</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= $curso["estado"] == 'activo' ? 'success' : 'secondary' ?>">
                                                    <?= htmlspecialchars($curso["estado"]) ?>
                                                </span>
                                            </td>
                                            <td><?= htmlspecialchars($curso["profesor"]) ?></td>
                                            <td><?= $curso["fecha_formateada"] ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="superAdmin/gestionCursos/verCurso=<?= $curso['id'] ?>" class="btn btn-sm btn-info">
                                                        <i class="bi bi-eye"></i> Ver
                                                    </a>
                                                    <a href="<?= $curso["url_amiga"] ?>" class="btn btn-sm btn-warning">
                                                        <i class="bi bi-pencil"></i> Editar
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

<!-- Incluir el archivo JavaScript para la página -->
<script src="vistas/assets/js/pages/listadoCursos.js"></script>