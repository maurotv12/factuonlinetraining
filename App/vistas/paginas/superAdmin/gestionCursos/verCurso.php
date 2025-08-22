<?php
// Iniciar sesión una sola vez al principio
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar acceso (administradores o profesores)
if (!ControladorGeneral::ctrUsuarioTieneAlgunRol(['admin', 'superadmin', 'profesor'])) {
    echo '<div class="alert alert-danger">No tienes permisos para acceder a esta página.</div>';
    return;
}

require_once "controladores/cursos.controlador.php";

// Obtener el identificador del curso de la URL (puede ser ID o URL amigable)
$identificadorCurso = isset($_GET['identificador']) ? $_GET['identificador'] : (isset($_GET['id']) ? $_GET['id'] : null);

if (!$identificadorCurso) {
    echo '<div class="alert alert-danger">Curso no encontrado.</div>';
    return;
}

// Usar el controlador para cargar todos los datos necesarios
$datosVisualizacion = ControladorCursos::ctrCargarEdicionCurso($identificadorCurso);

// Verificar si hubo error
if ($datosVisualizacion['error']) {
    echo '<div class="alert alert-danger">' . $datosVisualizacion['mensaje'] . '</div>';
    return;
}

// Extraer los datos para la vista
$curso = $datosVisualizacion['curso'];
$categorias = $datosVisualizacion['categorias'];
$profesores = $datosVisualizacion['profesores'];
$secciones = $datosVisualizacion['secciones'];
$contenidoSecciones = $datosVisualizacion['contenidoSecciones'];

// Verificar permisos de profesor (solo puede ver sus propios cursos)
$esProfesor = ControladorGeneral::ctrUsuarioTieneAlgunRol(['profesor']);
$esAdmin = ControladorGeneral::ctrUsuarioTieneAlgunRol(['admin', 'superadmin']);

if ($esProfesor && !$esAdmin && $curso['id_persona'] != $_SESSION['idU']) {
    echo '<div class="alert alert-danger">No tienes permisos para ver este curso.</div>';
    return;
}

// Obtener datos del profesor y categoría
$profesor = null;
$categoria = null;

foreach ($profesores as $prof) {
    if ($prof['id'] == $curso['id_persona']) {
        $profesor = $prof;
        break;
    }
}

foreach ($categorias as $cat) {
    if ($cat['id'] == $curso['id_categoria']) {
        $categoria = $cat;
        break;
    }
}

// Incluir CSS para la página
echo '<link rel="stylesheet" href="/cursosApp/App/vistas/assets/css/pages/verCurso.css?v=' . time() . '">';
echo '<link rel="stylesheet" href="/cursosApp/App/vistas/assets/css/pages/editarCursoAdmin.css?v=' . time() . '">';
?>

<!-- Input oculto con el ID del curso para JavaScript -->
<input type="hidden" id="idCurso" value="<?= $curso['id'] ?? '' ?>">

<!-- Vista del curso con edición dinámica -->
<div class="ver-curso-container">
    <!-- Header del curso -->
    <div class="curso-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="breadcrumb-custom">
                    <?php if ($esAdmin): ?>
                        <a href="/cursosApp/App/listadoCursos" class="breadcrumb-link">
                            <i class="bi bi-arrow-left"></i> Volver al listado
                        </a>
                    <?php else: ?>
                        <a href="/cursosApp/App/profesores/gestionCursosPr/listadoCursosProfe" class="breadcrumb-link">
                            <i class="bi bi-arrow-left"></i> Volver a mis cursos
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Nombre del curso editable -->
                <div class="mb-3">
                    <input type="text" id="nombre" class="form-control-plaintext campo-editable curso-titulo"
                        value="<?= htmlspecialchars($curso['nombre']) ?>" readonly>
                </div>

                <div class="curso-meta">
                    <span class="badge badge-categoria"><?= htmlspecialchars($categoria['nombre'] ?? 'Sin categoría') ?></span>

                    <!-- Estado editable -->
                    <select id="estado" class="badge badge-estado select-editable" disabled>
                        <option value="activo" <?= ($curso['estado'] === 'activo') ? 'selected' : '' ?>>Activo</option>
                        <option value="inactivo" <?= ($curso['estado'] === 'inactivo') ? 'selected' : '' ?>>Inactivo</option>
                        <option value="borrador" <?= ($curso['estado'] === 'borrador') ? 'selected' : '' ?>>Borrador</option>
                    </select>

                    <!-- Precio editable -->
                    <div class="d-inline-block">
                        $<input type="number" id="valor" class="form-control-plaintext d-inline-block campo-editable"
                            style="width: 100px; display: inline;"
                            value="<?= $curso['valor'] ?? 0 ?>" readonly>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <?php if ($esAdmin || ($esProfesor && $curso['id_persona'] == $_SESSION['idU'])): ?>
                    <button id="btnToggleEdit" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> Editar Curso
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Controles de edición -->
        <div id="editorControls" class="row mt-3" style="display: none;">
            <div class="col-12">
                <div class="d-flex gap-2">
                    <button id="btnGuardarCambios" class="btn btn-success">
                        <i class="bi bi-check-lg"></i> Guardar cambios
                    </button>
                    <button id="btnCancelarCambios" class="btn btn-secondary">
                        <i class="bi bi-x-lg"></i> Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Video principal y contenido -->
        <div class="col-lg-8">
            <div class="video-container">
                <?php if (!empty($curso['promo_video'])): ?>
                    <div class="video-wrapper">
                        <video id="videoPlayer" controls class="main-video">
                            <source src="<?= $curso['promo_video'] ?>" type="video/mp4">
                            Tu navegador no soporta videos.
                        </video>
                        <div class="video-overlay">
                            <div class="video-title">Video promocional</div>
                        </div>
                    </div>
                <?php elseif (!empty($curso['banner'])): ?>
                    <div class="image-wrapper">
                        <img src="<?= $curso['banner'] ?>" alt="Banner del curso" class="main-image">
                        <div class="image-overlay">
                            <div class="image-title">Vista previa del curso</div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="placeholder-wrapper">
                        <div class="placeholder-content">
                            <i class="bi bi-play-circle"></i>
                            <p>Sin contenido multimedia</p>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Controles de archivo multimedia -->
                <div class="edit-only mt-3" style="display: none;">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Banner del curso</label>
                            <input type="file" id="banner" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Video promocional</label>
                            <input type="file" id="promo_video" class="form-control" accept="video/*">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información del curso -->
            <div class="curso-info">
                <div class="info-tabs">
                    <ul class="nav nav-tabs" id="cursoTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="descripcion-tab" data-bs-toggle="tab"
                                data-bs-target="#descripcion" type="button" role="tab">
                                Descripción
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="aprendizaje-tab" data-bs-toggle="tab"
                                data-bs-target="#aprendizaje" type="button" role="tab">
                                Lo que aprenderás
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="requisitos-tab" data-bs-toggle="tab"
                                data-bs-target="#requisitos" type="button" role="tab">
                                Requisitos
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="profesor-tab" data-bs-toggle="tab"
                                data-bs-target="#profesor" type="button" role="tab">
                                Instructor
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content" id="cursoTabsContent">
                        <div class="tab-pane fade show active" id="descripcion" role="tabpanel">
                            <div class="content-section">
                                <h5>Acerca de este curso</h5>
                                <textarea id="descripcion" class="form-control-plaintext campo-editable" rows="6" readonly><?= htmlspecialchars($curso['descripcion']) ?></textarea>

                                <h6 class="mt-4">¿Para quién es este curso?</h6>
                                <textarea id="para_quien" class="form-control-plaintext campo-editable" rows="4" readonly><?= htmlspecialchars($curso['para_quien']) ?></textarea>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="aprendizaje" role="tabpanel">
                            <div class="content-section">
                                <h5>Al finalizar este curso serás capaz de:</h5>
                                <textarea id="lo_que_aprenderas" class="form-control-plaintext campo-editable" rows="6" readonly><?= htmlspecialchars($curso['lo_que_aprenderas']) ?></textarea>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="requisitos" role="tabpanel">
                            <div class="content-section">
                                <h5>Requisitos y conocimientos previos</h5>
                                <textarea id="requisitos" class="form-control-plaintext campo-editable" rows="4" readonly><?= htmlspecialchars($curso['requisitos']) ?></textarea>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="profesor" role="tabpanel">
                            <div class="content-section">
                                <h5>Instructor del curso</h5>
                                <div class="edit-only" style="display: none;">
                                    <select id="id_persona" class="form-select select-editable" disabled>
                                        <?php foreach ($profesores as $prof): ?>
                                            <option value="<?= $prof['id'] ?>" <?= ($prof['id'] == ($curso['id_persona'] ?? '')) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($prof['nombre'] ?? '') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="profesor-info">
                                    <div class="profesor-avatar">
                                        <img src="<?= ControladorUsuarios::ctrValidarFotoUsuario($profesor['foto']) ?>"
                                            alt="Foto del profesor" class="avatar-img">
                                    </div>
                                    <div class="profesor-datos">
                                        <h5><?= htmlspecialchars($profesor['nombre'] ?? 'Instructor no especificado') ?></h5>
                                        <p class="profesor-email"><?= htmlspecialchars($profesor['email'] ?? '') ?></p>
                                    </div>
                                </div>

                                <!-- Categoría editable -->
                                <div class="edit-only mt-3" style="display: none;">
                                    <label class="form-label">Categoría del curso</label>
                                    <select id="id_categoria" class="form-select select-editable" disabled>
                                        <?php foreach ($categorias as $cat): ?>
                                            <option value="<?= $cat['id'] ?>" <?= ($cat['id'] == ($curso['id_categoria'] ?? '')) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cat['nombre'] ?? '') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar con contenido del curso -->
        <div class="col-lg-4">
            <div class="curso-sidebar">
                <div class="sidebar-header-content">
                    <h4>Contenido del curso</h4>
                    <div class="curso-stats">
                        <span class="stat-item">
                            <i class="bi bi-collection"></i>
                            <?= count($secciones) ?> secciones
                        </span>
                        <span class="stat-item">
                            <i class="bi bi-clock"></i>
                            Duración variable
                        </span>

                        <!-- Botón para agregar sección -->
                        <div class="edit-only" style="display: none;">
                            <button class="btn btn-sm btn-primary mt-2" onclick="agregarSeccion()">
                                <i class="bi bi-plus-circle"></i> Agregar sección
                            </button>
                        </div>
                    </div>
                </div>

                <div class="contenido-lista">
                    <?php if (!empty($secciones)): ?>
                        <div class="accordion" id="seccionesAccordion">
                            <?php foreach ($secciones as $index => $seccion): ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading<?= $seccion['id'] ?>">
                                        <button class="accordion-button <?= $index === 0 ? '' : 'collapsed' ?>"
                                            type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapse<?= $seccion['id'] ?>"
                                            aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>">
                                            <div class="seccion-header">
                                                <span class="seccion-titulo"><?= htmlspecialchars($seccion['titulo']) ?></span>
                                                <span class="seccion-count">
                                                    <?= count($contenidoSecciones[$seccion['id']] ?? []) ?> elementos
                                                </span>

                                                <!-- Controles de edición de sección -->
                                                <div class="edit-only ms-auto" style="display: none;">
                                                    <button class="btn btn-sm btn-outline-primary me-1"
                                                        onclick="agregarContenido(<?= $seccion['id'] ?>, 'video')">
                                                        <i class="bi bi-camera-video"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-success me-1"
                                                        onclick="agregarContenido(<?= $seccion['id'] ?>, 'pdf')">
                                                        <i class="bi bi-file-pdf"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger"
                                                        onclick="eliminarSeccion(<?= $seccion['id'] ?>)">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapse<?= $seccion['id'] ?>"
                                        class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>"
                                        data-bs-parent="#seccionesAccordion">
                                        <div class="accordion-body">
                                            <?php if (!empty($seccion['descripcion'])): ?>
                                                <p class="seccion-descripcion"><?= htmlspecialchars($seccion['descripcion']) ?></p>
                                            <?php endif; ?>

                                            <?php if (isset($contenidoSecciones[$seccion['id']]) && !empty($contenidoSecciones[$seccion['id']])): ?>
                                                <div class="contenido-items">
                                                    <?php foreach ($contenidoSecciones[$seccion['id']] as $contenido): ?>
                                                        <div class="contenido-item" data-tipo="<?= $contenido['tipo'] ?>"
                                                            data-url="<?= $contenido['archivo_url'] ?>">
                                                            <div class="item-icon">
                                                                <?php if ($contenido['tipo'] === 'video'): ?>
                                                                    <i class="bi bi-play-circle"></i>
                                                                <?php elseif ($contenido['tipo'] === 'documento'): ?>
                                                                    <i class="bi bi-file-earmark-text"></i>
                                                                <?php else: ?>
                                                                    <i class="bi bi-file"></i>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="item-info">
                                                                <span class="item-titulo"><?= htmlspecialchars($contenido['titulo']) ?></span>
                                                                <?php if ($contenido['duracion']): ?>
                                                                    <span class="item-duracion"><?= htmlspecialchars($contenido['duracion']) ?></span>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="item-action">
                                                                <button class="btn-preview" onclick="reproducirContenido('<?= $contenido['archivo_url'] ?>', '<?= $contenido['tipo'] ?>', '<?= htmlspecialchars($contenido['titulo']) ?>')">
                                                                    <i class="bi bi-eye"></i>
                                                                </button>

                                                                <!-- Controles de edición de contenido -->
                                                                <div class="edit-only" style="display: none;">
                                                                    <button class="btn btn-sm btn-outline-danger ms-1"
                                                                        onclick="eliminarContenido(<?= $contenido['id'] ?>)">
                                                                        <i class="bi bi-trash"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php else: ?>
                                                <p class="text-muted">Esta sección aún no tiene contenido.</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-content">
                            <i class="bi bi-collection"></i>
                            <h6>Sin contenido disponible</h6>
                            <p>Este curso aún no tiene secciones de contenido configuradas.</p>

                            <!-- Botón para primera sección -->
                            <div class="edit-only" style="display: none;">
                                <button class="btn btn-primary" onclick="agregarSeccion()">
                                    <i class="bi bi-plus-circle"></i> Crear primera sección
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

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
                        <input type="file" class="form-control" id="archivoContenido" name="archivo" accept=".mp4,.avi,.mov,.pdf">
                    </div>

                    <div class="mb-3" id="campoDuracion" style="display: none;">
                        <label class="form-label">Duración (mm:ss)</label>
                        <input type="text" class="form-control" id="duracionContenido" name="duracion" placeholder="05:30">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción (opcional)</label>
                        <textarea class="form-control" id="descripcionContenido" name="descripcion" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="guardarContenido">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Incluir el archivo JavaScript para la página -->
<script src="/cursosApp/App/vistas/assets/js/pages/editarCursoAdmin.js?v=<?= time() ?>"></script>