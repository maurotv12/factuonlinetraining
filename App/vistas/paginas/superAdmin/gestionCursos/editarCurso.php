<?php
// Iniciar sesión una sola vez al principio
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar acceso (solo administradores)
if (!ControladorGeneral::ctrUsuarioTieneAlgunRol(['admin', 'superadmin'])) {
    echo '<div class="alert alert-danger">No tienes permisos para acceder a esta página.</div>';
    return;
}

// Importaciones necesarias
require_once "controladores/cursos.controlador.php";

// Obtener el identificador del curso de la URL (puede ser ID o URL amigable)
$identificadorCurso = isset($_GET['identificador']) ? $_GET['identificador'] : (isset($_GET['id']) ? $_GET['id'] : null);

// Usar el controlador para cargar todos los datos necesarios
$datosEdicion = ControladorCursos::ctrCargarEdicionCurso($identificadorCurso);

// Verificar si hubo error
if ($datosEdicion['error']) {
    echo '<div class="alert alert-danger">' . $datosEdicion['mensaje'] . '</div>';
    return;
}

// Extraer los datos para la vista
$curso = $datosEdicion['curso'];
$categorias = $datosEdicion['categorias'];
$profesores = $datosEdicion['profesores'];
$secciones = $datosEdicion['secciones'];
$contenidoSecciones = $datosEdicion['contenidoSecciones'];

// Debug: Verificar que se está cargando la información del curso
if (empty($curso)) {
    echo '<div class="alert alert-warning">
        <strong>Aviso:</strong> No se pudo cargar la información del curso. 
        Identificador recibido: ' . htmlspecialchars($identificadorCurso ?? 'NULL') . '
    </div>';
}

// Procesar actualización del curso básico
if (isset($_POST['actualizarCurso'])) {
    $datosActualizar = [
        'id' => $curso['id'], // Usar el ID real del curso obtenido
        'nombre' => $_POST['nombre'],
        'descripcion' => $_POST['descripcion'],
        'lo_que_aprenderas' => $_POST['lo_que_aprenderas'],
        'requisitos' => $_POST['requisitos'],
        'para_quien' => $_POST['para_quien'],
        'valor' => $_POST['valor'],
        'id_categoria' => $_POST['id_categoria'],
        'id_persona' => $_POST['id_persona'],
        'estado' => $_POST['estado']
    ];

    $resultadoActualizacion = ControladorCursos::ctrActualizarDatosCurso($datosActualizar);

    // Usar sesión para mostrar mensaje después de la redirección (sesión ya iniciada)
    if (!$resultadoActualizacion['error']) {
        $_SESSION['mensaje_exito'] = 'Los datos del curso se han actualizado correctamente.';
    } else {
        $_SESSION['mensaje_error'] = $resultadoActualizacion['mensaje'];
    }

    // Redireccionar para evitar reenvío del formulario
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

// Mostrar mensajes de la sesión si existen (sesión ya iniciada)
if (isset($_SESSION['mensaje_exito'])) {
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                icon: "success",
                title: "¡Curso actualizado!",
                text: "' . $_SESSION['mensaje_exito'] . '",
                confirmButtonText: "Aceptar"
            });
        });
    </script>';
    unset($_SESSION['mensaje_exito']);
}

if (isset($_SESSION['mensaje_error'])) {
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "' . $_SESSION['mensaje_error'] . '",
                confirmButtonText: "Aceptar"
            });
        });
    </script>';
    unset($_SESSION['mensaje_error']);
}


?>

<!-- Input oculto con el ID del curso para JavaScript -->
<input type="hidden" id="idCurso" value="<?= $curso['id'] ?? '' ?>">

<div class="course-editor">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="bi bi-pencil-square"></i>
                        Editar Curso: <?= htmlspecialchars($curso['nombre'] ?? 'Curso sin nombre') ?>
                    </h4>
                </div>
                <div class="card-body">
                    <div class="container-fluid">
                        <!-- Header del curso -->
                        <div class="course-header">
                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="d-flex align-items-center mb-3">
                                        <a href="/cursosApp/App/listadoCursos" class="btn btn-outline-secondary me-3">
                                            <i class="bi bi-arrow-left"></i> Volver al listado
                                        </a>
                                        <h2 class="mb-0"><?= htmlspecialchars($curso['nombre'] ?? 'Curso sin nombre') ?></h2>
                                    </div>

                                    <!-- Formulario de datos básicos del curso -->
                                    <form method="post" id="formCursoBasico">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Nombre del curso</label>
                                                    <input type="text" class="form-control" name="nombre"
                                                        value="<?= htmlspecialchars($curso['nombre'] ?? '') ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label">Valor</label>
                                                    <input type="number" class="form-control" name="valor"
                                                        value="<?= $curso['valor'] ?? 0 ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label">Estado</label>
                                                    <select class="form-control" name="estado" required>
                                                        <option value="activo" <?= ($curso['estado'] ?? '') == 'activo' ? 'selected' : '' ?>>Activo</option>
                                                        <option value="inactivo" <?= ($curso['estado'] ?? '') == 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Categoría</label>
                                                    <select class="form-control" name="id_categoria" required>
                                                        <?php foreach ($categorias as $categoria): ?>
                                                            <option value="<?= $categoria['id'] ?>"
                                                                <?= ($categoria['id'] == ($curso['id_categoria'] ?? '')) ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($categoria['nombre'] ?? '') ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Profesor</label>
                                                    <select class="form-control" name="id_persona" required>
                                                        <?php foreach ($profesores as $profesor): ?>
                                                            <option value="<?= $profesor['id'] ?>"
                                                                <?= ($profesor['id'] == ($curso['id_persona'] ?? '')) ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($profesor['nombre'] ?? '') ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Descripción</label>
                                            <textarea class="form-control" name="descripcion" rows="4"><?= htmlspecialchars($curso['descripcion'] ?? '') ?></textarea>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">Lo que aprenderás</label>
                                                    <textarea class="form-control" name="lo_que_aprenderas" rows="6"><?= htmlspecialchars($curso['lo_que_aprenderas'] ?? '') ?></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">Requisitos</label>
                                                    <textarea class="form-control" name="requisitos" rows="6"><?= htmlspecialchars($curso['requisitos'] ?? '') ?></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">Para quién es este curso</label>
                                                    <textarea class="form-control" name="para_quien" rows="6"><?= htmlspecialchars($curso['para_quien'] ?? '') ?></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <button type="submit" name="actualizarCurso" class="btn btn-primary" id="btnActualizar">
                                            <i class="bi bi-save"></i> Actualizar datos básicos
                                        </button>
                                    </form>
                                </div>

                                <div class="col-lg-4">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <?php if (isset($curso['banner']) && $curso['banner']): ?>
                                                <img src="<?= $curso['banner'] ?>" class="img-fluid rounded mb-3" alt="Banner del curso">
                                            <?php else: ?>
                                                <div class="bg-light p-4 rounded mb-3">
                                                    <i class="bi bi-image" style="font-size: 3rem; color: #ccc;"></i>
                                                    <p class="text-muted">Sin imagen</p>
                                                </div>
                                            <?php endif; ?>

                                            <div class="row text-center">
                                                <div class="col-4">
                                                    <h6><?= count($secciones ?? []) ?></h6>
                                                    <small class="text-muted">Secciones</small>
                                                </div>
                                                <div class="col-4">
                                                    <?php
                                                    $totalContenido = 0;
                                                    if (isset($contenidoSecciones) && is_array($contenidoSecciones)) {
                                                        foreach ($contenidoSecciones as $contenido) {
                                                            $totalContenido += count($contenido);
                                                        }
                                                    }
                                                    ?>
                                                    <h6><?= $totalContenido ?></h6>
                                                    <small class="text-muted">Elementos</small>
                                                </div>
                                                <div class="col-4">
                                                    <h6><?= ($curso['estado'] ?? '') == 'activo' ? 'Activo' : 'Inactivo' ?></h6>
                                                    <small class="text-muted">Estado</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Secciones del curso -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h4><i class="bi bi-collection"></i> Contenido del curso</h4>
                                    <button class="btn btn-add-section" onclick="agregarSeccion()">
                                        <i class="bi bi-plus-circle"></i> Agregar sección
                                    </button>
                                </div>

                                <div id="secciones-container">
                                    <?php if (isset($secciones) && is_array($secciones)): ?>
                                        <?php foreach ($secciones as $index => $seccion): ?>
                                            <div class="section-card" data-seccion-id="<?= $seccion['id'] ?? '' ?>">
                                                <div class="section-header" onclick="toggleSeccion(<?= $seccion['id'] ?? 0 ?>)">
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-grip-vertical drag-handle me-2"></i>
                                                        <strong><?= $index + 1 ?>. <?= htmlspecialchars($seccion['titulo'] ?? 'Sección sin título') ?></strong>
                                                    </div>
                                                    <div class="section-stats">
                                                        <?= count($contenidoSecciones[$seccion['id']] ?? []) ?> elementos
                                                        <i class="bi bi-chevron-down ms-2"></i>
                                                    </div>
                                                </div>

                                                <div class="section-content" id="seccion-<?= $seccion['id'] ?? '' ?>">
                                                    <div class="row mb-3">
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control"
                                                                value="<?= htmlspecialchars($seccion['titulo'] ?? '') ?>"
                                                                onchange="actualizarTituloSeccion(<?= $seccion['id'] ?? 0 ?>, this.value)"
                                                                placeholder="Título de la sección">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="btn-group w-100">
                                                                <button class="btn btn-outline-primary btn-sm"
                                                                    onclick="agregarContenido(<?= $seccion['id'] ?? 0 ?>, 'video')">
                                                                    <i class="bi bi-camera-video"></i> Video
                                                                </button>
                                                                <button class="btn btn-outline-success btn-sm"
                                                                    onclick="agregarContenido(<?= $seccion['id'] ?? 0 ?>, 'pdf')">
                                                                    <i class="bi bi-file-pdf"></i> PDF
                                                                </button>
                                                                <button class="btn btn-outline-danger btn-sm"
                                                                    onclick="eliminarSeccion(<?= $seccion['id'] ?? 0 ?>)">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="contenido-lista" id="contenido-<?= $seccion['id'] ?? '' ?>">
                                                        <?php if (isset($contenidoSecciones[$seccion['id']]) && is_array($contenidoSecciones[$seccion['id']])): ?>
                                                            <?php foreach ($contenidoSecciones[$seccion['id']] as $contenido): ?>
                                                                <div class="content-item <?= $contenido['tipo'] ?? '' ?>" data-contenido-id="<?= $contenido['id'] ?? '' ?>">
                                                                    <i class="bi bi-grip-vertical drag-handle"></i>
                                                                    <i class="bi bi-<?= ($contenido['tipo'] ?? '') == 'video' ? 'camera-video' : 'file-pdf' ?> me-2"></i>
                                                                    <span class="flex-grow-1"><?= htmlspecialchars($contenido['titulo'] ?? 'Sin título') ?></span>
                                                                    <small class="text-muted me-2"><?= $contenido['duracion'] ?? '00:00' ?></small>
                                                                    <div class="content-actions">
                                                                        <button class="btn btn-sm btn-outline-primary me-1"
                                                                            onclick="editarContenido(<?= $contenido['id'] ?? 0 ?>)">
                                                                            <i class="bi bi-pencil"></i>
                                                                        </button>
                                                                        <button class="btn btn-sm btn-outline-danger"
                                                                            onclick="eliminarContenido(<?= $contenido['id'] ?? 0 ?>)">
                                                                            <i class="bi bi-trash"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>

                                    <?php if (empty($secciones)): ?>
                                        <div class="text-center py-5">
                                            <i class="bi bi-collection" style="font-size: 3rem; color: #ccc;"></i>
                                            <h5 class="text-muted mt-3">No hay secciones creadas</h5>
                                            <p class="text-muted">Comienza agregando tu primera sección al curso</p>
                                            <button class="btn btn-primary" onclick="agregarSeccion()">
                                                <i class="bi bi-plus-circle"></i> Crear primera sección
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <!-- Modal para agregar/editar contenido -->
    <div class="modal fade" id="modalContenido" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalContenidoLabel">Agregar contenido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formContenido">
                        <input type="hidden" id="idSeccion" name="idSeccion">
                        <input type="hidden" id="idContenido" name="idContenido">
                        <input type="hidden" id="tipoContenido" name="tipo">

                        <div class="mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" class="form-control" id="tituloContenido" name="titulo" required>
                        </div>

                        <div class="mb-3" id="campoArchivo">
                            <label class="form-label">Archivo</label>
                            <input type="file" class="form-control" id="archivoContenido" name="archivo"
                                accept=".mp4,.avi,.mov,.pdf">
                        </div>

                        <div class="mb-3" id="campoDuracion" style="display: none;">
                            <label class="form-label">Duración (mm:ss)</label>
                            <input type="text" class="form-control" id="duracionContenido" name="duracion"
                                placeholder="05:30">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descripción (opcional)</label>
                            <textarea class="form-control" id="descripcionContenido" name="descripcion" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarContenido()">Guardar</button>
                </div>
            </div>
        </div>
    </div>


    <script>
        // Manejar el formulario de actualización de curso
        document.addEventListener('DOMContentLoaded', function() {
            const formCursoBasico = document.getElementById('formCursoBasico');
            const btnActualizar = document.getElementById('btnActualizar');

            if (formCursoBasico && btnActualizar) {
                formCursoBasico.addEventListener('submit', function() {
                    btnActualizar.disabled = true;
                    btnActualizar.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Actualizando...';
                });
            }
        });
    </script>