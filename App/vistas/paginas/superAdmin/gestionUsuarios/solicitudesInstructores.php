<?php
// Obtener las solicitudes pendientes
$solicitudes = ControladorInstructores::ctrMostrarSolicitudes("pendiente");

// Procesar formulario si se envió una acción
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"])) {
    $idSolicitud = $_POST["idSolicitud"];
    $estado = $_POST["accion"] == "aprobar" ? "aprobada" : "rechazada";

    ControladorInstructores::ctrCambiarEstadoSolicitud($idSolicitud, $estado);

    echo "<script>location.reload();</script>";
}
?>

<div class="page-heading">
    <h3>Solicitudes de Instructores</h3>
</div>

<div class="page-content">
    <section class="section">
        <div class="card">
            <div class="card-header">
                Solicitudes Pendientes
            </div>
            <div class="card-body">
                <table id="tablaSolicitudes" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Fecha de Solicitud</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($solicitudes as $s): ?>
                            <tr>
                                <td><?= $s['nombre'] ?></td>
                                <td><?= $s['email'] ?></td>
                                <td><?= $s['fecha_solicitud'] ?></td>
                                <td><span class="badge bg-warning text-dark"><?= ucfirst($s['estado']) ?></span></td>
                                <td>
                                    <form method="post" style="display:inline-block;">
                                        <input type="hidden" name="idSolicitud" value="<?= $s['id'] ?>">
                                        <button type="submit" name="accion" value="aprobar" class="btn btn-success btn-sm">Aprobar</button>
                                        <button type="submit" name="accion" value="rechazar" class="btn btn-danger btn-sm">Rechazar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<script>
    $(document).ready(function() {
        $('#tablaSolicitudes').DataTable({
            language: {
                lengthMenu: "Mostrar _MENU_ registros por página",
                zeroRecords: "No se encontraron resultados",
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                infoEmpty: "No hay registros disponibles",
                infoFiltered: "(filtrado de _MAX_ registros totales)",
                search: "Buscar:",
                paginate: {
                    first: "Primero",
                    last: "Último",
                    next: "Siguiente",
                    previous: "Anterior"
                }
            }
        });
    });
</script>