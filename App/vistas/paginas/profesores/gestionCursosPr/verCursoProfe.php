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

// Verificar que el profesor puede ver este curso (solo sus propios cursos)
if ($curso['id_persona'] != $_SESSION['idU']) {
    echo '<div class="alert alert-danger">No tienes permisos para ver este curso.</div>';
    return;
}

// Procesar actualización del curso básico (migrado desde editarCursoProfe.php)
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

    // Usar sesión para mostrar mensaje después de la redirección
    if (!$resultadoActualizacion['error']) {
        $_SESSION['mensaje_exito'] = 'Los datos del curso se han actualizado correctamente.';
    } else {
        $_SESSION['mensaje_error'] = $resultadoActualizacion['mensaje'];
    }

    // Redireccionar para evitar reenvío del formulario
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
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
echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';

// Mostrar mensajes de la sesión si existen (migrado desde editarCursoProfe.php)
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

<!-- Vista del curso para profesores -->
<div class="ver-curso-container" data-curso-id="<?= $curso['id'] ?>">
    <!-- Header del curso -->
    <div class="curso-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="breadcrumb-custom">
                    <a href="/cursosApp/App/listadoCursosProfe" class="breadcrumb-link">
                        <i class="bi bi-arrow-left"></i> Volver a mis cursos
                    </a>
                </div>

                <!-- Título editable -->
                <div class="editable-field">
                    <h1 class="curso-titulo"
                        id="nombre-display"
                        data-valor-original="<?= htmlspecialchars($curso['nombre']) ?>">
                        <?= htmlspecialchars($curso['nombre']) ?>
                    </h1>
                    <button class="btn btn-sm btn-outline-primary edit-btn"
                        id="btn-editar-nombre"
                        title="Editar título">
                        <i class="bi bi-pencil"></i>
                    </button>
                </div>

                <div class="curso-meta">
                    <!-- Categoría editable -->
                    <div class="editable-field d-inline-block me-2">
                        <span class="badge badge-categoria"
                            id="id_categoria-display"
                            data-valor-original="<?= htmlspecialchars($categoria['nombre'] ?? 'Sin categoría') ?>"
                            data-categoria-id="<?= $curso['id_categoria'] ?>">
                            <?= htmlspecialchars($categoria['nombre'] ?? 'Sin categoría') ?>
                        </span>
                        <button class="btn btn-sm btn-outline-primary edit-btn"
                            id="btn-editar-id_categoria"
                            title="Cambiar categoría">
                            <i class="bi bi-pencil"></i>
                        </button>
                    </div>

                    <span class="badge badge-estado badge-<?= $curso['estado'] ?>"><?= htmlspecialchars($curso['estado']) ?></span>

                    <!-- Precio editable -->
                    <div class="editable-field d-inline-block ms-2">
                        <span class="curso-precio"
                            id="valor-display"
                            data-valor-original="$<?= number_format($curso['valor'] ?? 0, 0, ',', '.') ?>">
                            $<?= number_format($curso['valor'] ?? 0, 0, ',', '.') ?>
                        </span>
                        <button class="btn btn-sm btn-outline-primary edit-btn"
                            id="btn-editar-valor"
                            title="Editar precio">
                            <i class="bi bi-pencil"></i>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="row">
        <!-- Video principal y contenido -->
        <div class="col-lg-8">
            <!-- Video/Imagen principal - Contenedor dinámico manejado por JavaScript -->
            <div id="video-container" class="video-container" data-promo-video="<?= !empty($curso['promo_video']) ? ControladorCursos::ctrObtenerUrlVideoPromo($curso['promo_video']) : '' ?>" data-banner="<?= !empty($curso['banner']) ? ControladorCursos::ctrValidarImagenCurso($curso['banner']) : '' ?>">
                <!-- El contenido se renderiza dinámicamente con JavaScript -->
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
                            <button class="nav-link" id="para-quien-tab" data-bs-toggle="tab"
                                data-bs-target="#para-quien" type="button" role="tab">
                                Para quién
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content" id="cursoTabsContent">
                        <!-- Descripción editable -->
                        <div class="tab-pane fade show active" id="descripcion" role="tabpanel">
                            <div class="content-section">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>Acerca de este curso</h5>
                                    <button class="btn btn-sm btn-outline-primary" id="btn-editar-descripcion">
                                        <i class="bi bi-pencil"></i> Editar
                                    </button>
                                </div>
                                <div id="descripcion-display"
                                    data-valor-original="<?= htmlspecialchars($curso['descripcion']) ?>">
                                    <?= nl2br(htmlspecialchars($curso['descripcion'])) ?>
                                </div>
                            </div>
                        </div>

                        <!-- Lo que aprenderás editable -->
                        <div class="tab-pane fade" id="aprendizaje" role="tabpanel">
                            <div class="content-section">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>Al finalizar este curso serás capaz de:</h5>
                                    <button class="btn btn-sm btn-outline-primary" id="btn-editar-lo_que_aprenderas">
                                        <i class="bi bi-pencil"></i> Editar
                                    </button>
                                </div>
                                <div id="lo_que_aprenderas-display"
                                    data-valor-original="<?= htmlspecialchars($curso['lo_que_aprenderas']) ?>">
                                    <?php if (!empty($curso['lo_que_aprenderas'])): ?>
                                        <?= nl2br(htmlspecialchars($curso['lo_que_aprenderas'])) ?>
                                    <?php else: ?>
                                        <p class="text-muted">No se han definido objetivos de aprendizaje.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Requisitos editable -->
                        <div class="tab-pane fade" id="requisitos" role="tabpanel">
                            <div class="content-section">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>Requisitos y conocimientos previos</h5>
                                    <button class="btn btn-sm btn-outline-primary" id="btn-editar-requisitos">
                                        <i class="bi bi-pencil"></i> Editar
                                    </button>
                                </div>
                                <div id="requisitos-display"
                                    data-valor-original="<?= htmlspecialchars($curso['requisitos']) ?>">
                                    <?php if (!empty($curso['requisitos'])): ?>
                                        <?= nl2br(htmlspecialchars($curso['requisitos'])) ?>
                                    <?php else: ?>
                                        <p class="text-muted">Este curso no requiere conocimientos previos específicos.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Para quién editable -->
                        <div class="tab-pane fade" id="para-quien" role="tabpanel">
                            <div class="content-section">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>¿Para quién es este curso?</h5>
                                    <button class="btn btn-sm btn-outline-primary" id="btn-editar-para_quien">
                                        <i class="bi bi-pencil"></i> Editar
                                    </button>
                                </div>
                                <div id="para_quien-display"
                                    data-valor-original="<?= htmlspecialchars($curso['para_quien']) ?>">
                                    <?php if (!empty($curso['para_quien'])): ?>
                                        <?= nl2br(htmlspecialchars($curso['para_quien'])) ?>
                                    <?php else: ?>
                                        <p class="text-muted">Información no disponible.</p>
                                    <?php endif; ?>
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
                    <div class="d-flex justify-content-between align-items-center">
                        <h4>Contenido del curso</h4>
                        <button class="btn btn-sm btn-primary" id="btn-agregar-seccion">
                            <i class="bi bi-plus"></i> Nueva Sección
                        </button>
                    </div>
                    <div class="curso-stats">
                        <span class="stat-item">
                            <i class="bi bi-collection"></i>
                            <?= count($secciones) ?> secciones
                        </span>
                        <span class="stat-item">
                            <i class="bi bi-clock"></i>
                            Duración variable
                        </span>
                    </div>
                </div>

                <div class="contenido-lista">
                    <?php if (!empty($secciones)): ?>
                        <div id="secciones-container">
                            <?php foreach ($secciones as $index => $seccion): ?>
                                <div class="seccion-container" data-seccion-id="<?= $seccion['id'] ?>">
                                    <div class="seccion-header" onclick="toggleSeccion(<?= $seccion['id'] ?>)">
                                        <h6 class="seccion-title"><?= htmlspecialchars($seccion['titulo']) ?></h6>
                                        <div class="seccion-actions" onclick="event.stopPropagation();">
                                            <button class="btn btn-sm btn-outline-light"
                                                onclick="editarSeccion(<?= $seccion['id'] ?>)"
                                                title="Editar sección">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-light"
                                                onclick="eliminarSeccion(<?= $seccion['id'] ?>)"
                                                title="Eliminar sección">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="seccion-content<?= $index == 0 ? ' show' : '' ?>" id="seccion-content-<?= $seccion['id'] ?>">
                                        <?php if (!empty($seccion['descripcion'])): ?>
                                            <p class="seccion-description"><?= htmlspecialchars($seccion['descripcion']) ?></p>
                                        <?php endif; ?>

                                        <!-- Contenido existente de la sección -->
                                        <div class="contenido-items" id="contenido-items-<?= $seccion['id'] ?>">
                                            <?php
                                            // Obtener contenido de la sección con assets
                                            $contenidoCompleto = ControladorCursos::ctrObtenerContenidoSeccionConAssets($seccion['id']);

                                            // Debug temporal - eliminar después
                                            echo "<!-- DEBUG SECCION " . $seccion['id'] . ": " . json_encode($contenidoCompleto) . " -->";

                                            if ($contenidoCompleto['success'] && !empty($contenidoCompleto['contenido'])):
                                                foreach ($contenidoCompleto['contenido'] as $contenido):
                                                    // Organizar assets por tipo
                                                    $videos = array_filter($contenido['assets'], function ($asset) {
                                                        return $asset['asset_tipo'] === 'video';
                                                    });
                                                    $pdfs = array_filter($contenido['assets'], function ($asset) {
                                                        return $asset['asset_tipo'] === 'pdf';
                                                    });

                                                    $tieneVideo = !empty($videos);
                                                    $tienePDFs = !empty($pdfs);
                                            ?>
                                                    <div class="contenido-item-completo mb-3 p-3" style="border: 1px solid #e0e0e0; border-radius: 8px; background: #f8f9fa;">
                                                        <!-- Header del contenido -->
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <div class="contenido-info">
                                                                <h6 class="mb-1"><?= htmlspecialchars($contenido['titulo']) ?></h6>
                                                                <small class="text-muted">Duración: <?= $contenido['duracion'] ?? '00:00:00' ?></small>
                                                            </div>
                                                            <div class="contenido-actions">
                                                                <button class="btn btn-sm btn-outline-primary"
                                                                    onclick="editarContenido(<?= $contenido['id'] ?>)"
                                                                    title="Editar contenido">
                                                                    <i class="bi bi-pencil"></i>
                                                                </button>
                                                                <button class="btn btn-sm btn-outline-danger"
                                                                    onclick="eliminarContenido(<?= $contenido['id'] ?>)"
                                                                    title="Eliminar contenido">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>

                                                        <!-- Assets del contenido -->
                                                        <div class="contenido-assets">
                                                            <!-- Videos -->
                                                            <?php if ($tieneVideo): ?>
                                                                <div class="asset-group mb-2 cursor-pointer" onclick="this.querySelector('button.reproducir-video').click();">
                                                                    <div class="d-flex align-items-center justify-content-between ">
                                                                        <div class="d-flex align-items-center">
                                                                            <i class="bi bi-camera-video text-primary me-2"></i>
                                                                            <span class="fw-bold">Video:</span>
                                                                            <?php foreach ($videos as $video): ?>
                                                                                <span class="ms-2">Video</span>
                                                                                <?php if ($video['duracion_segundos']): ?>
                                                                                    <small class="text-muted ms-1">(<?= gmdate("H:i:s", $video['duracion_segundos']) ?>)</small>
                                                                                <?php endif; ?>
                                                                            <?php endforeach; ?>
                                                                        </div>
                                                                        <div class="video-actions">
                                                                            <?php foreach ($videos as $video): ?>
                                                                                <button class="btn btn-sm btn-primary reproducir-video ms-2 d-none"
                                                                                    data-video-url="<?= $video['public_url'] ?>"
                                                                                    data-titulo="<?= htmlspecialchars($contenido['titulo']) ?>"
                                                                                    data-contenido-id="<?= $contenido['id'] ?>"
                                                                                    title="Reproducir video">
                                                                                    <i class="bi bi-play-fill"></i>
                                                                                </button>
                                                                            <?php endforeach; ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>

                                                            <!-- PDFs -->
                                                            <?php if ($tienePDFs): ?>
                                                                <div class="asset-group">
                                                                    <div class="d-flex align-items-center">
                                                                        <i class="bi bi-file-pdf text-danger me-2"></i>
                                                                        <span class="fw-bold">PDFs:</span>
                                                                        <button class="btn btn-sm btn-outline-secondary ms-2"
                                                                            type="button"
                                                                            data-bs-toggle="collapse"
                                                                            data-bs-target="#pdfs-<?= $contenido['id'] ?>"
                                                                            aria-expanded="false">
                                                                            <i class="bi bi-list"></i> Ver archivos (<?= count($pdfs) ?>)
                                                                        </button>
                                                                    </div>
                                                                    <div class="collapse mt-2" id="pdfs-<?= $contenido['id'] ?>">
                                                                        <div class="pdf-list ps-3">
                                                                            <?php foreach ($pdfs as $pdf): ?>
                                                                                <div class="pdf-item d-flex justify-content-between align-items-center py-1">
                                                                                    <span>
                                                                                        <button class="btn btn-link text-decoration-none p-0 text-start btn-descargar-pdf"
                                                                                            data-asset-id="<?= $pdf['id'] ?>"
                                                                                            data-curso-id="<?= $curso['id'] ?>"
                                                                                            data-nombre="<?= htmlspecialchars(basename($pdf['storage_path'])) ?>"
                                                                                            title="Descargar archivo PDF">
                                                                                            <i class="bi bi-file-pdf-fill text-danger me-1"></i>
                                                                                            <?= htmlspecialchars(basename($pdf['storage_path'])) ?>
                                                                                            <i class="bi bi-download ms-1 text-primary"></i>
                                                                                        </button>
                                                                                    </span>
                                                                                    <small class="text-muted">
                                                                                        <?php if ($pdf['tamano_bytes']): ?>
                                                                                            (<?= number_format($pdf['tamano_bytes'] / (1024 * 1024), 2) ?> MB)
                                                                                        <?php endif; ?>
                                                                                    </small>
                                                                                </div>
                                                                            <?php endforeach; ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>

                                                            <!-- Si no hay assets -->
                                                            <?php if (!$tieneVideo && !$tienePDFs): ?>
                                                                <div class="text-muted text-center py-2">
                                                                    <i class="bi bi-info-circle"></i>
                                                                    Sin archivos adjuntos
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                <?php endforeach;
                                            else: ?>
                                                <div class="text-center text-muted py-3">
                                                    <i class="bi bi-folder-x"></i>
                                                    <p class="mb-0">No hay contenido en esta sección</p>
                                                </div>
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
                            <p>Comienza creando tu primera sección de contenido.</p>
                            <button class="btn btn-primary btn-sm mt-2" id="btn-crear-primera-seccion">
                                <i class="bi bi-plus"></i> Crear Primera Sección
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Scripts necesarios -->
<script>
    // Pasar datos del curso a JavaScript
    window.cursoData = {
        id: <?= $curso['id'] ?>,
        nombre: <?= json_encode($curso['nombre']) ?>,
        estado: <?= json_encode($curso['estado']) ?>
    };
</script>

<!-- Incluir JavaScript para la página -->
<script src="/cursosApp/App/vistas/assets/js/pages/verCursoProfe.js?v=<?= time() ?>"></script>