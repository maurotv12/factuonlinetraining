<!-- vistas/paginas/usuarios.php -->

<?php
// Requerir controladores y modelos necesarios
require_once $_SERVER['DOCUMENT_ROOT'] . "/cursosApp/App/controladores/cursos.controlador.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/cursosApp/App/modelos/cursos.modelo.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/cursosApp/App/controladores/usuarios.controlador.php";

// if ($usuario["rol"] != "admin") {
//     echo '<script>
//   window.location = "' . $ruta . 'eKlycsApp/inicio";
//   </script>';
//     return;
// }
// $item = null;
// $valor = null;
// $usuarios = ControladorUsuarios::ctrMostrarusuarios($item, $valor);
// 
?>
<?php
$item = null;
$valor = null;
$usuarios = ControladorUsuarios::ctrMostrarusuarios($item, $valor);

// Obtener cursos para mostrar información adicional
$cursos = ControladorCursos::ctrMostrarCursos($item, $valor);
$categorias = ControladorCursos::ctrObtenerCategorias();

// Crear un array asociativo para facilitar el acceso a los cursos por profesor
$cursosPorProfesor = [];
if ($cursos) {
    foreach ($cursos as $curso) {
        if (!isset($cursosPorProfesor[$curso['id_persona']])) {
            $cursosPorProfesor[$curso['id_persona']] = [];
        }
        $cursosPorProfesor[$curso['id_persona']][] = $curso;
    }
}

// Obtener todos los roles disponibles
$roles = ModeloUsuarios::mdlObtenerRoles();

// Obtener los roles por usuario
$rolesPorUsuario = [];
foreach ($usuarios as $usuario) {
    $rolesPorUsuario[$usuario["id"]] = ModeloUsuarios::mdlObtenerRolesPorUsuario($usuario["id"]);
}
?>
<section class="content">
    <div class="row">
        <div class="col-12 col-lg-12">
            <div class="card sobraCrearLink">
                <div class="card-body">
                    <table id="table_id" class="table table-bordered dt-responsive table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Foto</th>
                                <th>Usuario</th>
                                <th>Nombre</th>
                                <th>e-Mail</th>
                                <th>Número de Identificación</th>
                                <th>Profesión</th>
                                <th>Teléfono</th>
                                <th>Dirección</th>
                                <th>Biografia</th>
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
                                    <td><img src="<?php echo $value["foto"] ?>" class="img-fluid avatar avatar-xl me-3" width="30px"></td>
                                    <td><?php echo $value["usuario_link"] ?></td>
                                    <td><?php echo $value["nombre"] ?></td>
                                    <td><?php echo $value["email"] ?></td>
                                    <td><?php echo $value["nro_identificacion"] ?></td>
                                    <td><?php echo $value["profesion"] ?></td>
                                    <td><?php echo $value["telefono"] ?></td>
                                    <td><?php echo $value["direccion"] ?></td>
                                    <td>
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
                                    </td>
                                    <td><?php echo $value["pais"] ?></td>
                                    <td><?php echo $value["ciudad"] ?></td>
                                    <td>
                                        <?php
                                        if (isset($cursosPorProfesor[$value["id"]]) && !empty($cursosPorProfesor[$value["id"]])) {
                                            echo '<div class="cursos-asignados text-break">';
                                            foreach ($cursosPorProfesor[$value["id"]] as $curso) {
                                                echo '<span class="badge bg-primary me-1 mb-1 text-break">' . htmlspecialchars($curso["nombre"]) . '</span>';
                                            }
                                            echo '</div>';
                                        } else {
                                            echo '<span class="text-muted">Sin cursos asignados</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
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
                                    </td>
                                    <td><?php echo $value["estado"] ?></td>
                                    <td><?php echo $value["fecha_registro"] ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary cambiar-roles"
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

    <script>
        // Script para manejar la selección de roles en el modal
        document.addEventListener('DOMContentLoaded', function() {
            // Obtener referencias a los elementos del DOM
            const modalRoles = document.getElementById('modalRoles');
            const btnsCambiarRoles = document.querySelectorAll('.cambiar-roles');

            // Roles por usuario (para marcar los checkboxes correctamente)
            const rolesPorUsuario = <?php echo json_encode($rolesPorUsuario); ?>;

            // Agregar evento a los botones de cambiar roles
            btnsCambiarRoles.forEach(btn => {
                btn.addEventListener('click', function() {
                    // Obtener datos del usuario
                    const idUsuario = this.getAttribute('data-id');
                    const nombreUsuario = this.getAttribute('data-nombre');

                    // Actualizar el modal con los datos del usuario
                    document.getElementById('idUsuario').value = idUsuario;
                    document.getElementById('nombreUsuarioModal').textContent = nombreUsuario;

                    // Desmarcar todos los checkboxes primero
                    document.querySelectorAll('.role-checkbox').forEach(checkbox => {
                        checkbox.checked = false;
                    });

                    // Marcar los roles actuales del usuario
                    if (rolesPorUsuario[idUsuario]) {
                        rolesPorUsuario[idUsuario].forEach(rol => {
                            const checkbox = document.getElementById('rol' + rol.id);
                            if (checkbox) {
                                checkbox.checked = true;
                            }
                        });
                    }
                });
            });
        });
    </script>