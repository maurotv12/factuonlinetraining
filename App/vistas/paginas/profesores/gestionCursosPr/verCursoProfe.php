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
echo '<link rel="stylesheet" href="/cursosApp/App/vistas/assets/css/pages/verCursoProfe.css?v=' . time() . '">';
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
            <div class="col-md-4 text-end">
                <a href="/cursosApp/App/verCursoProfe/<?= $curso['url_amiga'] ?>" class="btn btn-primary">
                    <i class="bi bi-pencil"></i> Editor Avanzado
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Video principal y contenido -->
        <div class="col-lg-8">
            <!-- Video/Imagen principal -->
            <div class="video-container">
                <?php
                $videoUrl = null;
                if (!empty($curso['promo_video'])) {
                    $videoUrl = ControladorCursos::ctrObtenerUrlVideoPromo($curso['promo_video']);
                }
                ?>
                <?php if ($videoUrl): ?>
                    <div class="video-wrapper">
                        <video id="videoPlayer" controls class="main-video">
                            <source src="<?= $videoUrl ?>" type="video/mp4">
                            Tu navegador no soporta videos.
                        </video>
                        <div class="video-overlay">
                            <div class="video-title">Video promocional</div>
                            <button class="btn btn-sm btn-outline-light edit-video-btn" id="btn-subir-promo">
                                <i class="bi bi-camera-video"></i> Cambiar video
                            </button>
                        </div>
                    </div>
                <?php elseif (!empty($curso['banner'])): ?>
                    <div class="image-wrapper">
                        <img src="<?= ControladorCursos::ctrValidarImagenCurso($curso['banner']) ?>" alt="Banner del curso" class="main-image">
                        <div class="image-overlay">
                            <div class="image-title">Vista previa del curso</div>
                            <button class="btn btn-sm btn-outline-light edit-image-btn">
                                <i class="bi bi-image"></i> Cambiar imagen
                            </button>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="placeholder-wrapper">
                        <div class="placeholder-content">
                            <i class="bi bi-plus-circle"></i>
                            <p>Agregar contenido multimedia</p>
                            <div class="upload-buttons">
                                <button class="btn btn-primary me-2 add-video-btn" id="btn-subir-promo">
                                    <i class="bi bi-camera-video"></i> Agregar Video
                                </button>
                                <button class="btn btn-secondary add-image-btn">
                                    <i class="bi bi-image"></i> Agregar Imagen
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
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
                        <div class="accordion" id="seccionesAccordion">
                            <?php foreach ($secciones as $index => $seccion): ?>
                                <div class="accordion-item seccion-item" data-seccion-id="<?= $seccion['id'] ?>">
                                    <h2 class="accordion-header" id="heading<?= $seccion['id'] ?>">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#collapse<?= $seccion['id'] ?>"
                                            aria-expanded="false">
                                            <div class="w-100 d-flex justify-content-between align-items-center">
                                                <span><?= htmlspecialchars($seccion['titulo']) ?></span>
                                                <div class="seccion-actions" onclick="event.stopPropagation();">
                                                    <button class="btn btn-sm btn-outline-primary btn-editar-seccion me-1"
                                                        title="Editar sección">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger btn-eliminar-seccion"
                                                        title="Eliminar sección">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapse<?= $seccion['id'] ?>"
                                        class="accordion-collapse collapse"
                                        data-bs-parent="#seccionesAccordion">
                                        <div class="accordion-body">
                                            <?php if (!empty($seccion['descripcion'])): ?>
                                                <p class="text-muted mb-3"><?= htmlspecialchars($seccion['descripcion']) ?></p>
                                            <?php endif; ?>

                                            <!-- Contenido de la sección -->
                                            <div class="seccion-contenido" id="contenido-seccion-<?= $seccion['id'] ?>">
                                                <?php
                                                $contenidoSeccion = $contenidoSecciones[$seccion['id']] ?? [];
                                                if (!empty($contenidoSeccion)):
                                                ?>
                                                    <div class="contenido-lista">
                                                        <?php foreach ($contenidoSeccion as $contenido): ?>
                                                            <div class="contenido-item">
                                                                <i class="bi bi-<?= $contenido['tipo'] === 'video' ? 'camera-video' : 'file-pdf' ?>"></i>
                                                                <span><?= htmlspecialchars($contenido['titulo']) ?></span>
                                                                <?php if ($contenido['duracion']): ?>
                                                                    <small class="text-muted">(<?= $contenido['duracion'] ?>)</small>
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>

                                            <!-- Área de subida de archivos -->
                                            <div class="upload-area mt-3">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="drop-area"
                                                            data-tipo="video"
                                                            data-seccion-id="<?= $seccion['id'] ?>">
                                                            <i class="bi bi-camera-video"></i>
                                                            <p>Subir Videos</p>
                                                            <small>MP4, máx 10min, HD</small>
                                                            <input type="file"
                                                                class="file-input"
                                                                accept="video/mp4,video/avi,video/mov"
                                                                multiple>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="drop-area"
                                                            data-tipo="pdf"
                                                            data-seccion-id="<?= $seccion['id'] ?>">
                                                            <i class="bi bi-file-pdf"></i>
                                                            <p>Subir PDFs</p>
                                                            <small>Máx 10MB</small>
                                                            <input type="file"
                                                                class="file-input"
                                                                accept=".pdf"
                                                                multiple>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
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