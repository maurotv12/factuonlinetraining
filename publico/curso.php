<?php
include $_SERVER['DOCUMENT_ROOT'] . "/cursosApp/assets/plantilla/head.php";
include $_SERVER['DOCUMENT_ROOT'] . "/cursosApp/assets/plantilla/menu.php";

require_once  $_SERVER['DOCUMENT_ROOT'] . "/cursosApp/App/controladores/cursos.controlador.php";
require_once  $_SERVER['DOCUMENT_ROOT'] . "/cursosApp/App/controladores/general.controlador.php";

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

// Verificar si el usuario está autenticado
$usuarioAutenticado = isset($_SESSION['idU']);
$usuarioId = $usuarioAutenticado ? $_SESSION['idU'] : null;

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


// Procesar actualización del curso básico (solo si el usuario está autenticado y es propietario)
if ($usuarioAutenticado && isset($_POST['actualizarCurso'])) {
    $datosActualizar = [
        'id' => $curso['id'], // Usar el ID real del curso obtenido
        'nombre' => $_POST['nombre'],
        'descripcion' => $_POST['descripcion'],
        'lo_que_aprenderas' => $_POST['lo_que_aprenderas'],
        'requisitos' => $_POST['requisitos'],
        'para_quien' => $_POST['para_quien'],
        'valor' => $_POST['valor'],
        'id_categoria' => $_POST['id_categoria'],
        'id_persona' => $usuarioId, // Mantener el profesor actual
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
echo '<link rel="stylesheet" href="/cursosApp/assets/css/verCursoPublic.css?v=' . time() . '">';
echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';

// Mostrar mensajes de la sesión si existen (solo para usuarios autenticados)
if ($usuarioAutenticado && isset($_SESSION['mensaje_exito'])) {
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

if ($usuarioAutenticado && isset($_SESSION['mensaje_error'])) {
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

<!-- Vista del curso -->
<div class="ver-curso-container  mt-5" data-curso-id="<?= $curso['id'] ?>">
    <!-- Header del curso -->
    <div class="curso-header">
        <div class="row align-items-center">
            <div class="col-md-10">
                <?php if ($usuarioAutenticado && $curso['id_persona'] == $usuarioId): ?>
                    <div class="breadcrumb-custom">
                        <a href="/cursosApp/App/listadoCursosProfe" class="breadcrumb-link">
                            <i class="bi bi-arrow-left"></i> Volver a mis cursos
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Título editable -->
                <div class="editable-field">
                    <h1 class="curso-titulo"
                        id="nombre-display"
                        data-valor-original="<?= htmlspecialchars($curso['nombre']) ?>">
                        <?= htmlspecialchars($curso['nombre']) ?>
                    </h1>
                    <?php if ($usuarioAutenticado && $curso['id_persona'] == $usuarioId): ?>
                        <button class="btn btn-sm btn-outline-primary edit-btn"
                            id="btn-editar-nombre"
                            title="Editar título">
                            <i class="bi bi-pencil"></i>
                        </button>
                    <?php endif; ?>
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
                        <?php if ($usuarioAutenticado && $curso['id_persona'] == $usuarioId): ?>
                            <button class="btn btn-sm btn-outline-primary edit-btn"
                                id="btn-editar-id_categoria"
                                title="Cambiar categoría">
                                <i class="bi bi-pencil"></i>
                            </button>
                        <?php endif; ?>
                    </div>

                    <!-- Estado del curso - Solo visible para admin y profesor -->
                    <?php if ($usuarioAutenticado && ControladorGeneral::ctrUsuarioTieneAlgunRol(['admin', 'profesor'])): ?>
                        <span class="badge badge-estado badge-<?= $curso['estado'] ?>"><?= htmlspecialchars($curso['estado']) ?></span>
                    <?php endif; ?>

                    <!-- Precio editable -->
                    <div class="editable-field d-inline-block ms-2">
                        <span class="curso-precio"
                            id="valor-display"
                            data-valor-original="$<?= number_format($curso['valor'] ?? 0, 0, ',', '.') ?>">
                            $<?= number_format($curso['valor'] ?? 0, 0, ',', '.') ?>
                        </span>
                        <?php if ($usuarioAutenticado && $curso['id_persona'] == $usuarioId): ?>
                            <button class="btn btn-sm btn-outline-primary edit-btn"
                                id="btn-editar-valor"
                                title="Editar precio">
                                <i class="bi bi-pencil"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-2 d-flex align-items-center">
                <div class="inscription-buttons w-100">
                    <a href="/cursosApp/login" class="btn btn-primary btn-login-responsive">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span class="btn-text">Inicia sesión para inscribirte</span>
                    </a>
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

            <!-- Botones para cambiar banner y video - Solo visible para el dueño del curso -->
            <?php if ($usuarioAutenticado && $curso['id_persona'] == $usuarioId): ?>
                <div class="banner-actions mt-2 mb-3">
                    <button class="btn btn-sm btn-outline-primary" id="btn-cambiar-banner">
                        <i class="bi bi-image"></i> Cambiar Imagen del Banner
                    </button>
                    <button class="btn btn-sm btn-primary ms-2" id="btn-subir-promo">
                        <i class="bi bi-camera-video"></i> Cambiar video promocional
                    </button>
                    <!-- Input oculto para subir imagen -->
                    <input type="file" id="input-banner" accept="image/jpeg,image/jpg,image/png" style="display: none;">
                </div>
            <?php endif; ?>

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
                        <!-- Descripción editable -->
                        <div class="tab-pane fade show active" id="descripcion" role="tabpanel">
                            <div class="content-section">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>Acerca de este curso</h5>
                                    <?php if ($usuarioAutenticado && $curso['id_persona'] == $usuarioId): ?>
                                        <button class="btn btn-sm btn-outline-primary" id="btn-editar-descripcion">
                                            <i class="bi bi-pencil"></i> Editar
                                        </button>
                                    <?php endif; ?>
                                </div>
                                <div id="descripcion-display" class="text-wrap text-break"
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
                                    <?php if ($usuarioAutenticado && $curso['id_persona'] == $usuarioId): ?>
                                        <button class="btn btn-sm btn-outline-primary" id="btn-editar-lo_que_aprenderas">
                                            <i class="bi bi-pencil"></i> Editar
                                        </button>
                                    <?php endif; ?>
                                </div>
                                <div id="lo_que_aprenderas-display" class="text-wrap text-break"
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
                                    <?php if ($usuarioAutenticado && $curso['id_persona'] == $usuarioId): ?>
                                        <button class="btn btn-sm btn-outline-primary" id="btn-editar-requisitos">
                                            <i class="bi bi-pencil"></i> Editar
                                        </button>
                                    <?php endif; ?>
                                </div>
                                <div id="requisitos-display" class="text-wrap text-break"
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
                                    <?php if ($usuarioAutenticado && $curso['id_persona'] == $usuarioId): ?>
                                        <button class="btn btn-sm btn-outline-primary" id="btn-editar-para_quien">
                                            <i class="bi bi-pencil"></i> Editar
                                        </button>
                                    <?php endif; ?>
                                </div>
                                <div id="para_quien-display" class="text-wrap text-break"
                                    data-valor-original="<?= htmlspecialchars($curso['para_quien']) ?>">
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
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>Profesor</h5>
                                </div>
                                <div id="profesor-display" class="profesor-info-container">
                                    <?php if ($profesor): ?>
                                        <div class="profesor-card">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="profesor-avatar">
                                                        <?php
                                                        $fotoProfesor = $profesor['foto'] ?? 'storage/public/usuarios/default.png';

                                                        // Limpiar ruta para evitar rutas duplicadas
                                                        $fotoProfesor = str_replace(['vistas/img/usuarios/default/', '/cursosApp/'], '', $fotoProfesor);

                                                        // Si es la foto por defecto antigua, usar la nueva ruta
                                                        if (strpos($fotoProfesor, 'default.png') !== false) {
                                                            $fotoProfesor = 'storage/public/usuarios/default.png';
                                                        }

                                                        // Validar si el archivo existe, sino usar default
                                                        $rutaCompleta = $_SERVER['DOCUMENT_ROOT'] . '/cursosApp/' . $fotoProfesor;
                                                        if (!file_exists($rutaCompleta)) {
                                                            $fotoProfesor = 'storage/public/usuarios/default.png';
                                                        }
                                                        ?>
                                                        <a href="/cursosApp/App/perfilProfesor?id=<?= $profesor['id'] ?>"
                                                            class="profesor-avatar-link"
                                                            title="Ver perfil de <?= htmlspecialchars($profesor['nombre']) ?>">
                                                            <img src="/cursosApp/<?= htmlspecialchars($fotoProfesor) ?>"
                                                                alt="<?= htmlspecialchars($profesor['nombre']) ?>"
                                                                class="profesor-photo"
                                                                onerror="this.src='/cursosApp/storage/public/usuarios/default.png'">
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="col-md-9">
                                                    <div class="profesor-details">
                                                        <h4 class="profesor-nombre">
                                                            <a href="/cursosApp/App/perfilProfesor?id=<?= $profesor['id'] ?>"
                                                                class="profesor-nombre-link"
                                                                title="Ver perfil de <?= htmlspecialchars($profesor['nombre']) ?>">
                                                                <?= htmlspecialchars($profesor['nombre']) ?>
                                                            </a>
                                                        </h4>

                                                        <?php if (!empty($profesor['profesion'])): ?>
                                                            <p class="profesor-profesion">
                                                                <i class="bi bi-briefcase"></i>
                                                                <?= htmlspecialchars($profesor['profesion']) ?>
                                                            </p>
                                                        <?php endif; ?>

                                                        <div class="profesor-contact-info">
                                                            <!-- Email (mostrar solo si está permitido) -->
                                                            <?php if ($profesor['mostrar_email'] == 1 && !empty($profesor['email'])): ?>
                                                                <p class="contact-item">
                                                                    <i class="bi bi-envelope"></i>
                                                                    <a href="mailto:<?= htmlspecialchars($profesor['email']) ?>">
                                                                        <?= htmlspecialchars($profesor['email']) ?>
                                                                    </a>
                                                                </p>
                                                            <?php endif; ?>

                                                            <!-- Teléfono (mostrar solo si está permitido) -->
                                                            <?php if ($profesor['mostrar_telefono'] == 1 && !empty($profesor['telefono'])): ?>
                                                                <p class="contact-item">
                                                                    <i class="bi bi-telephone"></i>
                                                                    <a href="tel:<?= htmlspecialchars($profesor['telefono']) ?>">
                                                                        <?= htmlspecialchars($profesor['telefono']) ?>
                                                                    </a>
                                                                </p>
                                                            <?php endif; ?>

                                                            <!-- Identificación (mostrar solo si está permitido) -->
                                                            <?php if ($profesor['mostrar_identificacion'] == 1 && !empty($profesor['numero_identificacion'])): ?>
                                                                <p class="contact-item">
                                                                    <i class="bi bi-card-text"></i>
                                                                    ID: <?= htmlspecialchars($profesor['numero_identificacion']) ?>
                                                                </p>
                                                            <?php endif; ?>
                                                        </div>

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

                                                        <!-- Fecha de registro -->
                                                        <p class="profesor-fecha-registro">
                                                            <small class="text-muted">
                                                                <i class="bi bi-calendar-check"></i>
                                                                Instructor desde <?php
                                                                                    // Formatear fecha en español
                                                                                    $meses = [
                                                                                        1 => 'Enero',
                                                                                        2 => 'Febrero',
                                                                                        3 => 'Marzo',
                                                                                        4 => 'Abril',
                                                                                        5 => 'Mayo',
                                                                                        6 => 'Junio',
                                                                                        7 => 'Julio',
                                                                                        8 => 'Agosto',
                                                                                        9 => 'Septiembre',
                                                                                        10 => 'Octubre',
                                                                                        11 => 'Noviembre',
                                                                                        12 => 'Diciembre'
                                                                                    ];
                                                                                    $fecha = strtotime($profesor['fecha_registro']);
                                                                                    $mes = $meses[date('n', $fecha)];
                                                                                    $año = date('Y', $fecha);
                                                                                    echo $mes . ' ' . $año;
                                                                                    ?>
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
        <div class="col-lg-4">
            <div class="curso-sidebar">
                <div class="sidebar-header-content">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4>Contenido del curso</h4>
                        <?php if ($usuarioAutenticado && $curso['id_persona'] == $usuarioId): ?>
                            <button class="btn btn-sm btn-primary" id="btn-agregar-seccion">
                                <i class="bi bi-plus"></i> Nueva Sección
                            </button>
                        <?php endif; ?>
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
                                        <?php if ($usuarioAutenticado && $curso['id_persona'] == $usuarioId): ?>
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
                                        <?php endif; ?>
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
                                                            <?php if ($usuarioAutenticado && $curso['id_persona'] == $usuarioId): ?>
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
                                                            <?php endif; ?>
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
                            <?php if ($usuarioAutenticado && $curso['id_persona'] == $usuarioId): ?>
                                <button class="btn btn-primary btn-sm mt-2" id="btn-crear-primera-seccion">
                                    <i class="bi bi-plus"></i> Crear Primera Sección
                                </button>
                            <?php endif; ?>
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
        estado: <?= json_encode($curso['estado']) ?>,
        id_persona: <?= $curso['id_persona'] ?>,
        usuario_actual_id: <?= $usuarioAutenticado ? $usuarioId : 'null' ?>
    };
</script>

<!-- Incluir JavaScript para la página -->
<script src="/cursosApp/App/assets/js/inscripcionesPublic.js?v=<?= time() ?>"></script>
<script src="/cursosApp/App/assets/js/verCursoPublic.js?v=<?= time() ?>"></script>