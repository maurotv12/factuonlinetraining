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
?>

<!-- Vista del curso para profesores -->
<div class="ver-curso-container">
    <!-- Header del curso -->
    <div class="curso-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="breadcrumb-custom">
                    <a href="/cursosApp/App/listadoCursosProfe" class="breadcrumb-link">
                        <i class="bi bi-arrow-left"></i> Volver a mis cursos
                    </a>
                </div>
                <h1 class="curso-titulo"><?= htmlspecialchars($curso['nombre']) ?></h1>
                <div class="curso-meta">
                    <span class="badge badge-categoria"><?= htmlspecialchars($categoria['nombre'] ?? 'Sin categoría') ?></span>
                    <span class="badge badge-estado badge-<?= $curso['estado'] ?>"><?= htmlspecialchars($curso['estado']) ?></span>
                    <span class="curso-precio">$<?= number_format($curso['valor'] ?? 0, 0, ',', '.') ?></span>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <a href="/cursosApp/App/editarCursoProfe/<?= $curso['url_amiga'] ?>" class="btn btn-primary">
                    <i class="bi bi-pencil"></i> Editar Curso
                </a>
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
                            <button class="nav-link" id="estadisticas-tab" data-bs-toggle="tab"
                                data-bs-target="#estadisticas" type="button" role="tab">
                                Estadísticas
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content" id="cursoTabsContent">
                        <div class="tab-pane fade show active" id="descripcion" role="tabpanel">
                            <div class="content-section">
                                <h5>Acerca de este curso</h5>
                                <p><?= nl2br(htmlspecialchars($curso['descripcion'])) ?></p>

                                <?php if (!empty($curso['para_quien'])): ?>
                                    <h6>¿Para quién es este curso?</h6>
                                    <ul class="lista-puntos">
                                        <?php foreach (explode("\n", $curso['para_quien']) as $punto): ?>
                                            <?php if (trim($punto)): ?>
                                                <li><?= htmlspecialchars(trim($punto)) ?></li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="aprendizaje" role="tabpanel">
                            <div class="content-section">
                                <h5>Al finalizar este curso serás capaz de:</h5>
                                <?php if (!empty($curso['lo_que_aprenderas'])): ?>
                                    <ul class="lista-aprendizaje">
                                        <?php foreach (explode("\n", $curso['lo_que_aprenderas']) as $punto): ?>
                                            <?php if (trim($punto)): ?>
                                                <li>
                                                    <i class="bi bi-check-circle"></i>
                                                    <?= htmlspecialchars(trim($punto)) ?>
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p class="text-muted">No se han definido objetivos de aprendizaje.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="requisitos" role="tabpanel">
                            <div class="content-section">
                                <h5>Requisitos y conocimientos previos</h5>
                                <?php if (!empty($curso['requisitos'])): ?>
                                    <ul class="lista-requisitos">
                                        <?php foreach (explode("\n", $curso['requisitos']) as $requisito): ?>
                                            <?php if (trim($requisito)): ?>
                                                <li>
                                                    <i class="bi bi-arrow-right"></i>
                                                    <?= htmlspecialchars(trim($requisito)) ?>
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p class="text-muted">Este curso no requiere conocimientos previos específicos.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="estadisticas" role="tabpanel">
                            <div class="content-section">
                                <h5>Estadísticas del curso</h5>
                                <div class="stats-grid">
                                    <div class="stat-card">
                                        <i class="bi bi-people"></i>
                                        <div class="stat-number">0</div>
                                        <div class="stat-label">Estudiantes inscritos</div>
                                    </div>
                                    <div class="stat-card">
                                        <i class="bi bi-star"></i>
                                        <div class="stat-number">0</div>
                                        <div class="stat-label">Calificación promedio</div>
                                    </div>
                                    <div class="stat-card">
                                        <i class="bi bi-clock"></i>
                                        <div class="stat-number"><?= count($secciones) ?></div>
                                        <div class="stat-label">Secciones</div>
                                    </div>
                                    <div class="stat-card">
                                        <i class="bi bi-calendar"></i>
                                        <div class="stat-number"><?= date('d/m/Y', strtotime($curso['fecha_registro'])) ?></div>
                                        <div class="stat-label">Fecha de creación</div>
                                    </div>
                                </div>
                                <p class="text-muted mt-3">
                                    <small>Las estadísticas se actualizan diariamente.</small>
                                </p>
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
                            <a href="/cursosApp/App/editarCursoProfe/<?= $curso['url_amiga'] ?>" class="btn btn-primary btn-sm mt-2">
                                <i class="bi bi-plus"></i> Agregar contenido
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSS adicional para estadísticas -->
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .stat-card {
        background: linear-gradient(135deg, var(--primary-color), var(--dark-color));
        color: white;
        padding: 1.5rem;
        border-radius: 12px;
        text-align: center;
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-card i {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        color: var(--accent-color);
    }

    .stat-number {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        font-size: 0.85rem;
        opacity: 0.9;
    }
</style>

<!-- Incluir JavaScript para la página -->
<script src="/cursosApp/App/vistas/assets/js/pages/verCurso.js?v=<?= time() ?>"></script>