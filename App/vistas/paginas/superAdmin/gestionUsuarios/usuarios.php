<!-- vistas/paginas/usuarios.php -->

<?php
// Verificar acceso (solo administradores)
if (!ControladorGeneral::ctrUsuarioTieneAlgunRol(['admin', 'superadmin'])) {
    echo '<div class="alert alert-danger">No tienes permisos para acceder a esta página.</div>';
    return;
}

require_once "controladores/usuarios.controlador.php";

// Cargar todos los datos necesarios usando el controlador
$datosUsuarios = ControladorUsuarios::ctrCargarDatosUsuariosAdmin();
$usuarios = $datosUsuarios['usuarios'];
$cursosPorProfesor = $datosUsuarios['cursosPorProfesor'];
$roles = $datosUsuarios['roles'];
$rolesPorUsuario = $datosUsuarios['rolesPorUsuario'];
?>

<!-- Incluir CSS específico para esta página -->
<link rel="stylesheet" href="vistas/assets/css/pages/usuariosAdmin.css">

<section class="content">
    <div class="usuarios-admin-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="fas fa-users"></i>
                            Gestión de Usuarios
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table_id" class="table table-bordered dt-responsive table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Foto</th>
                                        <th>Usuario</th>
                                        <th>Nombre</th>
                                        <th>e-Mail</th>
                                        <th>Identificación</th>
                                        <th>Profesión</th>
                                        <th>Teléfono</th>
                                        <th>Dirección</th>
                                        <th>Biografía</th>
                                        <th>País</th>
                                        <th>Ciudad</th>
                                        <th>Cursos Asignados</th>
                                        <th>Roles</th>
                                        <th>Estado</th>
                                        <th>Fecha registro</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($usuarios as $key => $value): ?>
                                        <tr>
                                            <td><?php echo ($key + 1); ?></td>
                                            <td><img src="<?php echo ControladorUsuarios::ctrValidarFotoUsuario($value["foto"]) ?>" class="img-fluid avatar avatar-xl me-3" width="30px"></td>
                                            <td><?php echo $value["usuario_link"] ?></td>
                                            <td><?php echo $value["nombre"] ?></td>
                                            <td><?php echo $value["email"] ?></td>
                                            <td><?php echo $value["numero_identificacion"] ?></td>
                                            <td><?php echo $value["profesion"] ?></td>
                                            <td><?php echo $value["telefono"] ?></td>
                                            <td><?php echo $value["direccion"] ?></td>
                                            <td>
                                                <div class="biografia-container">
                                                    <?php
                                                    // Usar el controlador de usuarios para procesar la biografía
                                                    $biografia = $value["biografia"] ?? '';
                                                    $bioData = ControladorUsuarios::ctrProcesarBiografiaUsuario($biografia, 15, 100);
                                                    $biografiaId = "bio-" . $value["id"];

                                                    // Mostrar versión corta con "Ver más" si es necesario
                                                    echo '<div id="bio-short-' . $biografiaId . '" style="display:block;">';
                                                    echo nl2br(htmlspecialchars($bioData['bioShort']));

                                                    if ($bioData['showVerMas']) {
                                                        echo '... <a href="#" class="text-primary" 
                                                            onclick="document.getElementById(\'bio-short-' . $biografiaId . '\').style.display=\'none\';
                                                                     document.getElementById(\'bio-full-' . $biografiaId . '\').style.display=\'block\';
                                                                     return false;">Ver más</a>';
                                                    }

                                                    echo '</div>';

                                                    // Versión completa oculta inicialmente
                                                    if ($bioData['showVerMas']) {
                                                        echo '<div id="bio-full-' . $biografiaId . '" style="display:none;">';
                                                        echo nl2br(htmlspecialchars($bioData['bioFull']));
                                                        echo ' <a href="#" class="text-primary" 
                                                            onclick="document.getElementById(\'bio-full-' . $biografiaId . '\').style.display=\'none\';
                                                                     document.getElementById(\'bio-short-' . $biografiaId . '\').style.display=\'block\';
                                                                     return false;">Ver menos</a>';
                                                        echo '</div>';
                                                    }
                                                    ?>
                                                </div>
                                            </td>
                                            <td><?php echo $value["pais"] ?></td>
                                            <td><?php echo $value["ciudad"] ?></td>
                                            <td>
                                                <div class="cursos-asignados">
                                                    <?php
                                                    if (isset($cursosPorProfesor[$value["id"]]) && !empty($cursosPorProfesor[$value["id"]])) {
                                                        $totalCursos = count($cursosPorProfesor[$value["id"]]);
                                                        echo '<button type="button" class="btn btn-sm btn-outline-primary ver-cursos" 
                                                              data-bs-toggle="modal" 
                                                              data-bs-target="#modalCursos" 
                                                              data-usuario-id="' . $value["id"] . '"
                                                              data-usuario-nombre="' . htmlspecialchars($value["nombre"]) . '">';
                                                        echo '<i class="fas fa-book"></i> Ver Cursos (' . $totalCursos . ')';
                                                        echo '</button>';
                                                    } else {
                                                        echo '<span class="text-muted">Sin cursos asignados</span>';
                                                    }
                                                    ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="roles-container">
                                                    <?php
                                                    // Mostrar los roles actuales del usuario
                                                    if (isset($rolesPorUsuario[$value["id"]]) && !empty($rolesPorUsuario[$value["id"]])) {
                                                        foreach ($rolesPorUsuario[$value["id"]] as $rol) {
                                                            echo '<span class="badge bg-info me-1 mb-1">' . htmlspecialchars($rol["nombre"]) . '</span>';
                                                        }
                                                    } else {
                                                        echo '<span class="text-muted">Sin roles asignados</span>';
                                                    }
                                                    ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="estado-badge <?php echo $value["estado"] == 'activo' ? 'estado-activo' : 'estado-inactivo'; ?>">
                                                    <?php echo $value["estado"] ?>
                                                </span>
                                            </td>
                                            <td><?php echo $value["fecha_registro"] ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary cambiar-roles btn-cambiar-roles"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalRoles"
                                                    data-id="<?php echo $value["id"]; ?>"
                                                    data-nombre="<?php echo htmlspecialchars($value["nombre"]); ?>">
                                                    <i class="fas fa-user-tag"></i> Cambiar Roles
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para cambiar roles -->
    <div class="modal fade" id="modalRoles" tabindex="-1" aria-labelledby="modalRolesLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalRolesLabel">Cambiar Roles para <span id="nombreUsuarioModal"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formCambiarRoles" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="idUsuario" id="idUsuario">

                        <div class="mb-3">
                            <label class="form-label">Seleccione los roles:</label>
                            <div class="roles-checkboxes">
                                <?php foreach ($roles as $rol): ?>
                                    <div class="form-check">
                                        <input class="form-check-input role-checkbox" type="checkbox"
                                            name="roles[]" value="<?php echo $rol['id']; ?>"
                                            id="rol<?php echo $rol['id']; ?>">
                                        <label class="form-check-label" for="rol<?php echo $rol['id']; ?>">
                                            <?php echo htmlspecialchars($rol['nombre']); ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="guardarRoles" class="btn btn-primary">Guardar Cambios</button>
                    </div>

                    <?php
                    $cambiarRoles = new ControladorUsuarios();
                    $respuesta = $cambiarRoles->ctrActualizarRolesUsuario();

                    if ($respuesta == "ok") {
                        echo '<script>
                        if(window.history.replaceState) {
                            window.history.replaceState(null, null, window.location.href);
                        }
                        
                        Swal.fire({
                            icon: "success",
                            title: "¡Roles actualizados correctamente!",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result) {
                            if(result.value) {
                                window.location = window.location.href;
                            }
                        });
                    </script>';
                    }
                    ?>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para mostrar cursos asignados -->
    <div class="modal fade" id="modalCursos" tabindex="-1" aria-labelledby="modalCursosLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCursosLabel">
                        <i class="fas fa-book"></i>
                        Cursos asignados a <span id="nombreUsuarioCursos"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="listaCursos">
                        <!-- Los cursos se cargarán dinámicamente aquí -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Pasar datos a JavaScript -->
<script>
    window.rolesPorUsuario = <?php echo json_encode($rolesPorUsuario); ?>;
    window.cursosPorProfesor = <?php echo json_encode($cursosPorProfesor); ?>;
</script>

<!-- Incluir el archivo JavaScript para la página -->
<script src="vistas/assets/js/pages/usuariosAdmin.js"></script>