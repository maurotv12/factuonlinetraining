<?php
include $_SERVER['DOCUMENT_ROOT'] . "/factuonlinetraining/assets/plantilla/head.php";
include $_SERVER['DOCUMENT_ROOT'] . "/factuonlinetraining/assets/plantilla/menu.php";

require_once  $_SERVER['DOCUMENT_ROOT'] . "/factuonlinetraining/App/controladores/cursos.controlador.php";

// Obtener el identificador del curso de la URL (puede ser ID o URL amigable)
$identificadorCurso = isset($_GET['identificador']) ? $_GET['identificador'] : (isset($_GET['id']) ? $_GET['id'] : null);
$urlAmiga = $_GET['pagina'] ?? '';

// Si hay URL amigable, usarla como identificador principal
if (!empty($urlAmiga)) {
    $identificadorCurso = $urlAmiga;
}

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

// Obtener la URL del banner del curso
$bannerUrl = '';
if (!empty($curso['banner'])) {
    $bannerUrl = ControladorCursos::ctrValidarImagenCurso($curso['banner']);
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
echo '<link rel="stylesheet" href="/factuonlinetraining/assets/css/verCursoPublic.css?v=' . time() . '">';
?>

<!-- Vista del curso -->
<div class="ver-curso-container mt-5" data-curso-id="<?= $curso['id'] ?>">
    <!-- Header del curso -->
    <div class="curso-header">
        <div class="row align-items-center">
            <div class="col-12">
                <!-- Título del curso -->
                <h1 class="curso-titulo mb-3">
                    <?= htmlspecialchars($curso['nombre']) ?>
                </h1>

                <div class="curso-meta">
                    <!-- Categoría -->
                    <span class="badge badge-categoria me-2">
                        <?= htmlspecialchars($categoria['nombre'] ?? 'Sin categoría') ?>
                    </span>

                    <!-- Precio -->
                    <span class="curso-precio ms-2">
                        <?php if ($curso["valor"] == 0): ?>
                            Gratis
                        <?php else: ?>
                            $<?= number_format($curso["valor"], 0, ',', '.') ?> COL
                        <?php endif; ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Video principal y contenido -->
        <div class="col-lg-8">
            <!-- Video/Imagen principal -->
            <div id="video-container" class="video-container" data-promo-video="<?= !empty($curso['promo_video']) ? ControladorCursos::ctrObtenerUrlVideoPromo($curso['promo_video']) : '' ?>" data-banner="<?= $bannerUrl ?>">
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
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="profesor-tab" data-bs-toggle="tab"
                                data-bs-target="#profe" type="button" role="tab">
                                Profesor
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content" id="cursoTabsContent">
                        <!-- Descripción -->
                        <div class="tab-pane fade show active" id="descripcion" role="tabpanel">
                            <div class="content-section">
                                <h5>Acerca de este curso</h5>
                                <div class="text-wrap text-break">
                                    <?= nl2br(htmlspecialchars($curso['descripcion'])) ?>
                                </div>
                            </div>
                        </div>

                        <!-- Lo que aprenderás -->
                        <div class="tab-pane fade" id="aprendizaje" role="tabpanel">
                            <div class="content-section">
                                <h5>Al finalizar este curso serás capaz de:</h5>
                                <div class="text-wrap text-break">
                                    <?php if (!empty($curso['lo_que_aprenderas'])): ?>
                                        <?= nl2br(htmlspecialchars($curso['lo_que_aprenderas'])) ?>
                                    <?php else: ?>
                                        <p class="text-muted">No se han definido objetivos de aprendizaje.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Requisitos -->
                        <div class="tab-pane fade" id="requisitos" role="tabpanel">
                            <div class="content-section">
                                <h5>Requisitos y conocimientos previos</h5>
                                <div class="text-wrap text-break">
                                    <?php if (!empty($curso['requisitos'])): ?>
                                        <?= nl2br(htmlspecialchars($curso['requisitos'])) ?>
                                    <?php else: ?>
                                        <p class="text-muted">Este curso no requiere conocimientos previos específicos.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Para quién -->
                        <div class="tab-pane fade" id="para-quien" role="tabpanel">
                            <div class="content-section">
                                <h5>¿Para quién es este curso?</h5>
                                <div class="text-wrap text-break">
                                    <?php if (!empty($curso['para_quien'])): ?>
                                        <?= nl2br(htmlspecialchars($curso['para_quien'])) ?>
                                    <?php else: ?>
                                        <p class="text-muted">Información no disponible.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <!-- Profesor -->
                        <div class="tab-pane fade" id="profe" role="tabpanel">
                            <div class="content-section">
                                <h5>Profesor</h5>
                                <div class="profesor-info-container">
                                    <?php if ($profesor): ?>
                                        <div class="profesor-card">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="profesor-avatar">
                                                        <?php
                                                        $fotoProfesor = $profesor['foto'] ?? 'storage/public/usuarios/default.png';

                                                        // Limpiar ruta para evitar rutas duplicadas
                                                        $fotoProfesor = str_replace(['vistas/img/usuarios/default/', '/factuonlinetraining/'], '', $fotoProfesor);

                                                        // Si es la foto por defecto antigua, usar la nueva ruta
                                                        if (strpos($fotoProfesor, 'default.png') !== false) {
                                                            $fotoProfesor = 'storage/public/usuarios/default.png';
                                                        }

                                                        // Validar si el archivo existe, sino usar default
                                                        $rutaCompleta = $_SERVER['DOCUMENT_ROOT'] . '/factuonlinetraining/' . $fotoProfesor;
                                                        if (!file_exists($rutaCompleta)) {
                                                            $fotoProfesor = 'storage/public/usuarios/default.png';
                                                        }
                                                        ?>
                                                        <img src="/factuonlinetraining/<?= htmlspecialchars($fotoProfesor) ?>"
                                                            alt="<?= htmlspecialchars($profesor['nombre']) ?>"
                                                            class="profesor-photo"
                                                            onerror="this.src='/factuonlinetraining/storage/public/usuarios/default.png'">
                                                    </div>
                                                </div>
                                                <div class="col-md-9">
                                                    <div class="profesor-details">
                                                        <h4 class="profesor-nombre">
                                                            <?= htmlspecialchars($profesor['nombre']) ?>
                                                        </h4>

                                                        <?php if (!empty($profesor['profesion'])): ?>
                                                            <p class="profesor-profesion">
                                                                <i class="bi bi-briefcase"></i>
                                                                <?= htmlspecialchars($profesor['profesion']) ?>
                                                            </p>
                                                        <?php endif; ?>

                                                        <!-- Ubicación -->
                                                        <?php if (!empty($profesor['pais']) || !empty($profesor['ciudad'])): ?>
                                                            <p class="profesor-ubicacion">
                                                                <i class="bi bi-geo-alt"></i>
                                                                <?php
                                                                $ubicacion = [];
                                                                if (!empty($profesor['ciudad'])) $ubicacion[] = $profesor['ciudad'];
                                                                if (!empty($profesor['pais'])) $ubicacion[] = $profesor['pais'];
                                                                echo htmlspecialchars(implode(', ', $ubicacion));
                                                                ?>
                                                            </p>
                                                        <?php endif; ?>

                                                        <!-- Biografía -->
                                                        <?php if (!empty($profesor['biografia'])): ?>
                                                            <div class="profesor-biografia text-wrap text-break">
                                                                <h6><i class="bi bi-person-badge"></i> Acerca del profesor</h6>
                                                                <p><?= nl2br(htmlspecialchars($profesor['biografia'])) ?></p>
                                                            </div>
                                                        <?php endif; ?>
                                                        </small>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">
                                            <i class="bi bi-info-circle"></i>
                                            <strong>Información del profesor no disponible</strong><br>
                                            <small>Es posible que la información del profesor no esté configurada correctamente o que no tenga permisos suficientes para mostrar esta información.</small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar con contenido del curso -->
        <div class="col-lg-4 container-sidebar">
            <div class="curso-sidebar">
                <div class="sidebar-header-content">
                    <h4>Contenido del curso</h4>
                    <span class="text-muted">Explora las secciones y recursos disponibles</span>
                </div>

                <!-- Banner del curso en sidebar -->
                <?php if (!empty($bannerUrl)): ?>
                    <div class="banner-wrapper mb-3">
                        <img src="<?= $bannerUrl ?>"
                            alt="Banner del curso"
                            class="banner-image"
                            style="width: 600px; height: 400px; object-fit: contain; border-radius: 8px; max-width: 100%; border: 1px solid #dee2e6;">
                    </div>
                <?php endif; ?>

                <!-- Botón de inscripción centrado y responsive -->
                <div class="inscripcion-container text-center p-3">
                    <div class="inscripcion-button">
                        <a href="/factuonlinetraining/login" class="btn btn-inscripcion btn-lg w-100">
                            <i class="bi bi-cart4 me-2"></i>
                            <span class="btn-text">Inicia sesión para inscribirte</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Script básico para reproducción de video -->
<script>
    // Funcionalidad básica de video container para vista pública
    document.addEventListener('DOMContentLoaded', function() {
        const videoContainer = document.getElementById('video-container');
        if (!videoContainer) return;

        const promoVideo = videoContainer.dataset.promoVideo;
        const banner = videoContainer.dataset.banner;

        // Renderizar contenido inicial
        if (promoVideo) {
            renderizarVideoPromo(promoVideo);
        } else if (banner) {
            renderizarBanner(banner);
        } else {
            renderizarPlaceholder();
        }

        function renderizarVideoPromo(videoUrl) {
            videoContainer.innerHTML = `
            <div class="video-wrapper">
                <video controls class="main-video w-100">
                    <source src="${videoUrl}" type="video/mp4">
                    Tu navegador no soporta videos.
                </video>
            </div>
            <div class="video-info mt-2">
                <h6 class="mb-1">Video promocional</h6>
                <small class="text-muted">Video de presentación del curso</small>
            </div>`;
        }

        function renderizarBanner(bannerUrl) {
            videoContainer.innerHTML = `
            <div class="banner-wrapper">
                <img src="${bannerUrl}" alt="Banner del curso" class="img-fluid w-100" style="border-radius: 8px;">
            </div>`;
        }

        function renderizarPlaceholder() {
            videoContainer.innerHTML = `
            <div class="placeholder-container text-center p-5" style="background: #f8f9fa; border-radius: 8px; border: 2px dashed #dee2e6;">
                <i class="bi bi-camera-video" style="font-size: 3rem; color: #6c757d;"></i>
                <h5 class="mt-3 text-muted">Contenido no disponible</h5>
                <p class="text-muted">Este curso aún no tiene video promocional o imagen de presentación.</p>
            </div>`;
        }
    });
</script>