<?php
// Verificar acceso (solo administradores)
if (!ControladorGeneral::ctrUsuarioTieneAlgunRol(['admin', 'superadmin'])) {
    echo '<div class="alert alert-danger">No tienes permisos para acceder a esta página.</div>';
    return;
}

require_once "modelos/conexion.php";
require_once "modelos/cursos.modelo.php";
require_once "controladores/cursos.controlador.php";

// Obtener todos los cursos
$cursos = ControladorCursos::ctrMostrarCursos(null, null);

// Conexión directa para obtener info de categorías y profesores
$conn = Conexion::conectar();
?>

<div class="container-fluid mt-4">
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
                                        <?php
                                        // Obtener categoría
                                        $stmtCategoria = $conn->prepare("SELECT nombre FROM categoria WHERE id = ?");
                                        $stmtCategoria->execute([$curso["id_categoria"]]);
                                        $categoria = $stmtCategoria->fetchColumn();

                                        // Obtener nombre del profesor
                                        $stmtProfesor = $conn->prepare("SELECT nombre FROM persona WHERE id = ?");
                                        $stmtProfesor->execute([$curso["id_instructor"]]);
                                        $profesor = $stmtProfesor->fetchColumn();
                                        ?>
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
                                            <td><?= htmlspecialchars($categoria ?: 'Sin categoría') ?></td>
                                            <td>$<?= number_format($curso["valor"], 0, ',', '.') ?></td>
                                            <td>
                                                <span class="badge bg-<?= $curso["estado"] == 'activo' ? 'success' : 'secondary' ?>">
                                                    <?= htmlspecialchars($curso["estado"]) ?>
                                                </span>
                                            </td>
                                            <td><?= htmlspecialchars($profesor ?: 'Desconocido') ?></td>
                                            <td><?= date("Y-m-d", strtotime($curso["fecha_registro"])) ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="superAdmin/gestionCursos/verCurso&id=<?= $curso['id'] ?>" class="btn btn-sm btn-info">
                                                        <i class="bi bi-eye"></i> Ver
                                                    </a>
                                                    <a href="superAdmin/gestionCursos/editarCurso&id=<?= $curso["id"] ?>" class="btn btn-sm btn-warning">
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