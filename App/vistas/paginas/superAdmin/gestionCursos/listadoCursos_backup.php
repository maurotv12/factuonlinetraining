<?php
// session_start();
require_once "modelos/conexion.php";
require_once "modelos/cursos.modelo.php";
require_once "controladores/cursos.controlador.php";
require_once "controladores/general.controlador.php";

// Obtener todos los cursos
$cursos = ControladorCursos::ctrMostrarCursos(null, null);

// Conexión directa para obtener info de categorías y profesores
$conn = Conexion::conectar();
?>

<!DOCTYPE html>
<html lang="es">



<body>

    <div class="container mt-5">
        <h2 class="mb-4">Listado de Cursos</h2>

        <table class="table table-bordered table-hover">
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
                        $stmtProfesor->execute([$curso["id_persona"]]);
                        $profesor = $stmtProfesor->fetchColumn();
                        ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td>
                                <?php if ($curso["banner"]) : ?>
                                    <img src="<?= $curso["banner"] ?>" class="banner-mini" alt="Banner">
                                <?php else : ?>
                                    <span class="text-muted">Sin imagen</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $curso["nombre"] ?></td>
                            <td><?= $categoria ?: 'Sin categoría' ?></td>
                            <td>$<?= number_format($curso["valor"], 0, ',', '.') ?></td>
                            <td><span class="badge bg-<?= $curso["estado"] == 'activo' ? 'success' : 'secondary' ?>"><?= $curso["estado"] ?></span></td>
                            <td><?= $profesor ?: 'Desconocido' ?></td>
                            <td><?= date("Y-m-d", strtotime($curso["fecha_registro"])) ?></td>
                            <td>
                                <a href="index.php?pagina=superAdmin/gestionCursos/verCurso&id=<?= $curso['id'] ?>" class="btn btn-sm btn-info">Ver</a>

                                <a href="editarCurso.php?id=<?= $curso["id"] ?>" class="btn btn-sm btn-warning">Editar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="9" class="text-center">No hay cursos registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>

</html>