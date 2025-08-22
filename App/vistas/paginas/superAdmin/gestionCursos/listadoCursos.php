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
<link rel="stylesheet" href="/cursosApp/App/vistas/assets/css/pages/listadoCursos.css">

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
                                                    <?php
                                                    // Usar URL amigable si está disponible, sino usar ID para ver
                                                    $urlVer = !empty($curso["url_amiga"])
                                                        ? "/cursosApp/App/verCurso/" . $curso["url_amiga"]
                                                        : "/cursosApp/App/superAdmin/gestionCursos/verCurso?id=" . $curso["id"];
                                                    ?>
                                                    <a href="<?= $urlVer ?>" class="btn btn-sm btn-info">
                                                        <i class="bi bi-eye"></i> Ver
                                                    </a>
                                                    <?php
                                                    // Ahora tanto ver como editar van a la misma vista (verCurso)
                                                    $urlEditar = !empty($curso["url_amiga"])
                                                        ? "/cursosApp/App/verCurso/" . $curso["url_amiga"]
                                                        : "/cursosApp/App/superAdmin/gestionCursos/verCurso?id=" . $curso["id"];
                                                    ?>
                                                    <a href="<?= $urlEditar ?>" class="btn btn-sm btn-warning"
                                                        title="URL: <?= htmlspecialchars($urlEditar) ?>">
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
<script src="/cursosApp/App/vistas/assets/js/pages/listadoCursos.js"></script>