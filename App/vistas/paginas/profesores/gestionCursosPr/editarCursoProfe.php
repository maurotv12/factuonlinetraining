<?php
// Iniciar sesión una sola vez al principio
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar acceso (solo profesores)
if (!ControladorGeneral::ctrUsuarioTieneAlgunRol(['profesor'])) {
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

// Verificar que el profesor puede editar este curso (solo sus propios cursos)
if ($curso['id_persona'] != $_SESSION['idU']) {
    echo '<div class="alert alert-danger">No tienes permisos para editar este curso.</div>';
    return;
}

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
        'id_persona' => $_SESSION['idU'], // Mantener el profesor actual
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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-pencil-square"></i> Editar Curso
                        <?php if (!empty($curso['nombre'])): ?>
                            - <?= htmlspecialchars($curso['nombre']) ?>
                        <?php endif; ?>
                    </h4>
                    <div>
                        <a href="/cursosApp/App/listadoCursosProfe" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Volver al listado
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Información básica del curso -->
                    <form method="POST" class="mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre del curso <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nombre" name="nombre"
                                        value="<?= htmlspecialchars($curso['nombre'] ?? '') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="valor" class="form-label">Precio</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="valor" name="valor"
                                            value="<?= htmlspecialchars($curso['valor'] ?? 0) ?>" min="0" step="0.01">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="id_categoria" class="form-label">Categoría</label>
                                    <select class="form-select" id="id_categoria" name="id_categoria" required>
                                        <option value="">Seleccionar categoría</option>
                                        <?php foreach ($categorias as $categoria): ?>
                                            <option value="<?= $categoria['id'] ?>"
                                                <?= ($categoria['id'] == ($curso['id_categoria'] ?? '')) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($categoria['nombre']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required><?= htmlspecialchars($curso['descripcion'] ?? '') ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="lo_que_aprenderas" class="form-label">Lo que aprenderás</label>
                                    <textarea class="form-control" id="lo_que_aprenderas" name="lo_que_aprenderas" rows="5"
                                        placeholder="Escribe cada punto en una línea nueva"><?= htmlspecialchars($curso['lo_que_aprenderas'] ?? '') ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="requisitos" class="form-label">Requisitos</label>
                                    <textarea class="form-control" id="requisitos" name="requisitos" rows="5"
                                        placeholder="Escribe cada requisito en una línea nueva"><?= htmlspecialchars($curso['requisitos'] ?? '') ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="para_quien" class="form-label">Para quién es este curso</label>
                                    <textarea class="form-control" id="para_quien" name="para_quien" rows="5"
                                        placeholder="Escribe cada punto en una línea nueva"><?= htmlspecialchars($curso['para_quien'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="estado" class="form-label">Estado</label>
                            <select class="form-select" id="estado" name="estado">
                                <option value="activo" <?= ($curso['estado'] ?? '') === 'activo' ? 'selected' : '' ?>>Activo</option>
                                <option value="inactivo" <?= ($curso['estado'] ?? '') === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                                <option value="borrador" <?= ($curso['estado'] ?? '') === 'borrador' ? 'selected' : '' ?>>Borrador</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" name="actualizarCurso" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i> Actualizar Curso
                            </button>
                        </div>
                    </form>

                    <hr>

                    <!-- Gestión de Secciones y Contenido -->
                    <div class="mt-4">
                        <h5><i class="bi bi-collection"></i> Secciones del Curso</h5>
                        <p class="text-muted">Organiza el contenido de tu curso en secciones.</p>

                        <div id="secciones-container">
                            <?php if (!empty($secciones)): ?>
                                <?php foreach ($secciones as $seccion): ?>
                                    <div class="card mb-3">
                                        <div class="card-header">
                                            <h6><?= htmlspecialchars($seccion['titulo']) ?></h6>
                                            <small class="text-muted"><?= htmlspecialchars($seccion['descripcion']) ?></small>
                                        </div>
                                        <div class="card-body">
                                            <?php if (isset($contenidoSecciones[$seccion['id']]) && !empty($contenidoSecciones[$seccion['id']])): ?>
                                                <ul class="list-group">
                                                    <?php foreach ($contenidoSecciones[$seccion['id']] as $contenido): ?>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <strong><?= htmlspecialchars($contenido['titulo']) ?></strong>
                                                                <br>
                                                                <small class="text-muted">
                                                                    Tipo: <?= htmlspecialchars($contenido['tipo']) ?>
                                                                    <?php if ($contenido['duracion']): ?>
                                                                        | Duración: <?= htmlspecialchars($contenido['duracion']) ?>
                                                                    <?php endif; ?>
                                                                </small>
                                                            </div>
                                                            <span class="badge bg-<?= $contenido['estado'] === 'activo' ? 'success' : 'secondary' ?>">
                                                                <?= htmlspecialchars($contenido['estado']) ?>
                                                            </span>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php else: ?>
                                                <p class="text-muted">No hay contenido en esta sección.</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> Este curso aún no tiene secciones.
                                    <strong>Nota:</strong> La gestión completa de secciones estará disponible próximamente.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 para notificaciones -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>