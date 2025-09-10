<?php
// Verificar si el usuario está logueado
if (!isset($_SESSION['idU'])) {
    echo '<script>window.location = "' . ControladorGeneral::ctrRuta() . '";</script>';
    exit;
}

// Determinar qué profesor mostrar
if (isset($_GET['id']) && !empty($_GET['id'])) {
    // Mostrar perfil de otro profesor
    $profesorId = $_GET['id'];
} else {
    // Mostrar perfil propio
    $profesorId = $_SESSION['idU'];
}

// Cargar información del profesor
$profesor = ControladorUsuarios::ctrMostrarUsuarios("id", $profesorId);

if (!$profesor) {
    echo '<script>window.location = "' . ControladorGeneral::ctrRutaApp() . 'error404";</script>';
    exit;
}


// Procesar biografía de forma simple y segura
$biografiaTexto = $profesor['biografia'] ?? '';
$biografiaProcesada = [
    'bioShort' => $biografiaTexto,
    'bioFull' => $biografiaTexto,
    'showVerMas' => strlen($biografiaTexto) > 500
];

// Si la biografía es muy larga, cortarla para la vista previa
if (strlen($biografiaTexto) > 2000) {
    $biografiaProcesada['bioShort'] = substr($biografiaTexto, 0, 2000) . '...';
}

// Verificar si es el propio profesor
$esProfesorLogueado = ($_SESSION['idU'] == $profesor['id']);

// Obtener cursos del profesor si está disponible
$cursosProfesore = [];
if (file_exists("controladores/cursos.controlador.php")) {
    require_once "controladores/cursos.controlador.php";
    try {
        $cursosData = ControladorCursos::ctrMostrarCursos("id_persona", $profesor['id']);
        if ($cursosData) {
            $cursosProfesore = isset($cursosData['id']) ? [$cursosData] : $cursosData;
        }
    } catch (Exception $e) {
        // Si hay error obteniendo cursos, mantener array vacío
        $cursosProfesore = [];
    }
}
?>

<link rel="stylesheet" href="vistas/assets/css/pages/perfilProfesor.css">

<div class="container-fluid">
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Perfil del Profesor</h3>
                    <p class="text-subtitle text-muted">Información pública del profesor</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Perfil del Profesor -->
    <div class="row">
        <div class="col-lg-4 col-md-5">
            <!-- Card de Información Personal -->
            <div class="card profile-card">
                <div class="card-body text-center">
                    <!-- Foto del Profesor -->
                    <div class="profile-photo-container">
                        <img src="<?php echo ControladorUsuarios::ctrValidarFotoUsuario($profesor['foto']); ?>"
                            alt="Foto del profesor"
                            class="profile-photo"
                            id="profilePhoto">
                    </div>

                    <!-- Información Básica -->
                    <div class="profile-info">
                        <h4 class="profile-name" id="displayNombre">
                            <?php echo htmlspecialchars($profesor['nombre'] ?? 'Nombre no disponible'); ?>
                        </h4>

                        <?php if ($esProfesorLogueado): ?>
                            <div class="edit-field" id="editNombre" style="display: none;">
                                <input type="text" class="form-control" id="inputNombre"
                                    value="<?php echo htmlspecialchars($profesor['nombre'] ?? ''); ?>">
                                <div class="edit-actions">
                                    <button class="btn btn-sm btn-success" onclick="saveField('nombre')">
                                        <i class="bi bi-check"></i>
                                    </button>
                                    <button class="btn btn-sm btn-secondary" onclick="cancelEdit('nombre')">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>

                        <p class="profile-profession" id="displayProfesion">
                            <?php echo htmlspecialchars($profesor['profesion'] ?? 'Profesión no especificada'); ?>
                        </p>

                        <?php if ($esProfesorLogueado): ?>
                            <div class="edit-field" id="editProfesion" style="display: none;">
                                <input type="text" class="form-control" id="inputProfesion"
                                    value="<?php echo htmlspecialchars($profesor['profesion'] ?? ''); ?>">
                                <div class="edit-actions">
                                    <button class="btn btn-sm btn-success" onclick="saveField('profesion')">
                                        <i class="bi bi-check"></i>
                                    </button>
                                    <button class="btn btn-sm btn-secondary" onclick="cancelEdit('profesion')">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Ubicación -->
                        <div class="profile-location">
                            <i class="bi bi-geo-alt"></i>
                            <span id="displayUbicacion">
                                <?php
                                $ubicacion = [];
                                if (!empty($profesor['ciudad'])) $ubicacion[] = $profesor['ciudad'];
                                if (!empty($profesor['pais'])) $ubicacion[] = $profesor['pais'];
                                echo !empty($ubicacion) ? implode(', ', $ubicacion) : 'Ubicación no especificada';
                                ?>
                            </span>
                        </div>

                        <!-- Fecha de registro -->
                        <div class="profile-since">
                            <i class="bi bi-calendar"></i>
                            <span>Miembro desde <?php echo date('M Y', strtotime($profesor['fecha_registro'])); ?></span>
                        </div>

                        <!-- Estado -->
                        <div class="profile-status">
                            <span class="badge badge-<?php echo $profesor['estado'] == 'activo' ? 'success' : 'warning'; ?>">
                                <?php echo ucfirst($profesor['estado']); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card de Estadísticas -->
            <div class="card stats-card">
                <div class="card-body">
                    <h5 class="card-title">Estadísticas</h5>
                    <div class="stats-grid d-flex justify-content-around">
                        <div class="stat-item">
                            <div class="stat-number"><?php echo count($cursosProfesore); ?></div>
                            <div class="stat-label">Cursos</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8 col-md-7">
            <!-- Tabs de Contenido -->
            <div class="card content-card">
                <div class="card-body">
                    <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="about-tab" data-bs-toggle="tab"
                                data-bs-target="#about" type="button" role="tab">
                                <i class="bi bi-person"></i> Acerca de
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="courses-tab" data-bs-toggle="tab"
                                data-bs-target="#courses" type="button" role="tab">
                                <i class="bi bi-book"></i> Cursos (<?php echo count($cursosProfesore); ?>)
                            </button>
                        </li>
                        <?php if ($esProfesorLogueado): ?>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="privacy-tab" data-bs-toggle="tab"
                                    data-bs-target="#privacy" type="button" role="tab">
                                    <i class="bi bi-shield-lock"></i> Privacidad
                                </button>
                            </li>
                        <?php endif; ?>
                    </ul>

                    <div class="tab-content" id="profileTabsContent">
                        <!-- Tab Acerca de -->
                        <div class="tab-pane fade show active" id="about" role="tabpanel">
                            <div class="about-section">
                                <h5>Biografía
                                    <?php if ($esProfesorLogueado): ?>
                                        <button class="btn btn-sm btn-outline-primary edit-btn" onclick="editField('biografia')">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    <?php endif; ?>
                                </h5>

                                <div id="displayBiografia">
                                    <?php if (!empty($profesor['biografia'])): ?>
                                        <p class="biografia-text">
                                            <span id="bioShort"><?php echo nl2br(htmlspecialchars($biografiaProcesada['bioShort'])); ?></span>
                                            <?php if ($biografiaProcesada['showVerMas']): ?>
                                                <span id="bioFull" style="display: none;"><?php echo nl2br(htmlspecialchars($biografiaProcesada['bioFull'])); ?></span>
                                                <a href="#" class="ver-mas" onclick="toggleBiografia(event)">Ver más</a>
                                            <?php endif; ?>
                                        </p>
                                    <?php else: ?>
                                        <p class="text-muted">No hay biografía disponible.</p>
                                    <?php endif; ?>
                                </div>

                                <?php if ($esProfesorLogueado): ?>
                                    <div class="edit-field" id="editBiografia" style="display: none;">
                                        <textarea class="form-control" id="inputBiografia" rows="5"><?php echo htmlspecialchars($profesor['biografia'] ?? ''); ?></textarea>
                                        <div class="edit-actions mt-2">
                                            <button class="btn btn-success" onclick="saveField('biografia')">
                                                <i class="bi bi-check"></i> Guardar
                                            </button>
                                            <button class="btn btn-secondary" onclick="cancelEdit('biografia')">
                                                <i class="bi bi-x"></i> Cancelar
                                            </button>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Información Adicional -->
                                <div class="info-grid mt-4">
                                    <div class="info-item">
                                        <strong>País:</strong>
                                        <span id="displayPais"><?php echo htmlspecialchars($profesor['pais'] ?? 'No especificado'); ?></span>
                                        <?php if ($esProfesorLogueado): ?>
                                            <button class="btn btn-sm btn-outline-primary edit-btn" onclick="editField('pais')">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <div class="edit-field" id="editPais" style="display: none;">
                                                <input type="text" class="form-control" id="inputPais"
                                                    value="<?php echo htmlspecialchars($profesor['pais'] ?? ''); ?>">
                                                <div class="edit-actions">
                                                    <button class="btn btn-sm btn-success" onclick="saveField('pais')">
                                                        <i class="bi bi-check"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-secondary" onclick="cancelEdit('pais')">
                                                        <i class="bi bi-x"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="info-item">
                                        <strong>Ciudad:</strong>
                                        <span id="displayCiudad"><?php echo htmlspecialchars($profesor['ciudad'] ?? 'No especificada'); ?></span>
                                        <?php if ($esProfesorLogueado): ?>
                                            <button class="btn btn-sm btn-outline-primary edit-btn" onclick="editField('ciudad')">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <div class="edit-field" id="editCiudad" style="display: none;">
                                                <input type="text" class="form-control" id="inputCiudad"
                                                    value="<?php echo htmlspecialchars($profesor['ciudad'] ?? ''); ?>">
                                                <div class="edit-actions">
                                                    <button class="btn btn-sm btn-success" onclick="saveField('ciudad')">
                                                        <i class="bi bi-check"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-secondary" onclick="cancelEdit('ciudad')">
                                                        <i class="bi bi-x"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab Cursos -->
                        <div class="tab-pane fade" id="courses" role="tabpanel">
                            <div class="courses-section">
                                <?php if (!empty($cursosProfesore)): ?>
                                    <div class="row">
                                        <?php foreach ($cursosProfesore as $curso): ?>
                                            <?php
                                            // Usar URL amigable 
                                            $urlVer = "/cursosApp/App/verCursoProfe/" . $curso["url_amiga"];
                                            ?>
                                            <div class="col-md-6 mb-4">
                                                <div class="course-card" role="group" data-url="<?php echo $urlVer; ?>" style="cursor: pointer;">
                                                    <div class="course-image">
                                                        <img src="<?php echo ControladorCursos::ctrValidarImagenCurso($curso['banner']); ?>"
                                                            alt="<?php echo htmlspecialchars($curso['nombre']); ?>">
                                                    </div>
                                                    <div class="course-content">
                                                        <h6><?php echo htmlspecialchars($curso['nombre']); ?></h6>
                                                        <p><?php echo htmlspecialchars(substr($curso['descripcion'] ?? '', 0, 100)) . '...'; ?></p>
                                                        <div class="course-meta">
                                                            <span class="price">$<?php echo number_format($curso['valor'] ?? 0); ?></span>
                                                            <!-- Estado del curso - Solo visible para admin y profesor -->
                                                            <?php if (ControladorGeneral::ctrUsuarioTieneAlgunRol(['admin', 'profesor'])): ?>
                                                                <span class="status badge badge-<?php echo $curso['estado'] == 'activo' ? 'success' : 'warning'; ?>">
                                                                    <?php echo ucfirst($curso['estado']); ?>
                                                                </span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-5">
                                        <i class="bi bi-book display-1 text-muted"></i>
                                        <h5 class="mt-3">No hay cursos disponibles</h5>
                                        <p class="text-muted">Este profesor aún no ha publicado cursos.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Tab Privacidad (solo para el profesor logueado) -->
                        <?php if ($esProfesorLogueado): ?>
                            <div class="tab-pane fade" id="privacy" role="tabpanel">
                                <div class="privacy-section">
                                    <h5>Configuración de Privacidad</h5>
                                    <p class="text-muted">Controla qué información personal quieres mostrar públicamente</p>

                                    <div class="privacy-options">
                                        <div class="privacy-item">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="showEmail"
                                                    <?php echo (isset($profesor['mostrar_email']) && $profesor['mostrar_email']) ? 'checked' : ''; ?>
                                                    onchange="updatePrivacySetting('mostrar_email', this.checked)">
                                                <label class="form-check-label" for="showEmail">
                                                    Mostrar Email
                                                </label>
                                            </div>
                                            <small class="text-muted">
                                                Email: <?php echo htmlspecialchars($profesor['email'] ?? 'No especificado'); ?>
                                            </small>
                                        </div>

                                        <div class="privacy-item">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="showPhone"
                                                    <?php echo (isset($profesor['mostrar_telefono']) && $profesor['mostrar_telefono']) ? 'checked' : ''; ?>
                                                    onchange="updatePrivacySetting('mostrar_telefono', this.checked)">
                                                <label class="form-check-label" for="showPhone">
                                                    Mostrar Teléfono
                                                </label>
                                            </div>
                                            <small class="text-muted">
                                                Teléfono: <?php echo htmlspecialchars($profesor['telefono'] ?? 'No especificado'); ?>
                                            </small>
                                        </div>

                                        <div class="privacy-item">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="showIdentification"
                                                    <?php echo (isset($profesor['mostrar_identificacion']) && $profesor['mostrar_identificacion']) ? 'checked' : ''; ?>
                                                    onchange="updatePrivacySetting('mostrar_identificacion', this.checked)">
                                                <label class="form-check-label" for="showIdentification">
                                                    Mostrar Número de Identificación
                                                </label>
                                            </div>
                                            <small class="text-muted">
                                                Identificación: <?php echo htmlspecialchars($profesor['numero_identificacion'] ?? 'No especificado'); ?>
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Información de contacto visible públicamente -->
                                    <div class="public-contact mt-4">
                                        <h6>Información de contacto pública:</h6>
                                        <div class="contact-preview">
                                            <?php if (isset($profesor['mostrar_email']) && $profesor['mostrar_email']): ?>
                                                <div class="contact-item">
                                                    <i class="bi bi-envelope"></i>
                                                    <span><?php echo htmlspecialchars($profesor['email'] ?? ''); ?></span>
                                                </div>
                                            <?php endif; ?>

                                            <?php if (isset($profesor['mostrar_telefono']) && $profesor['mostrar_telefono']): ?>
                                                <div class="contact-item">
                                                    <i class="bi bi-telephone"></i>
                                                    <span><?php echo htmlspecialchars($profesor['telefono'] ?? ''); ?></span>
                                                </div>
                                            <?php endif; ?>

                                            <?php if (isset($profesor['mostrar_identificacion']) && $profesor['mostrar_identificacion']): ?>
                                                <div class="contact-item">
                                                    <i class="bi bi-card-text"></i>
                                                    <span><?php echo htmlspecialchars($profesor['nro_identificacion'] ?? ''); ?></span>
                                                </div>
                                            <?php endif; ?>

                                            <?php
                                            $tieneContactoPublico = (isset($profesor['mostrar_email']) && $profesor['mostrar_email']) ||
                                                (isset($profesor['mostrar_telefono']) && $profesor['mostrar_telefono']) ||
                                                (isset($profesor['mostrar_identificacion']) && $profesor['mostrar_identificacion']);
                                            if (!$tieneContactoPublico):
                                            ?>
                                                <p class="text-muted">No has habilitado ninguna información de contacto para mostrar públicamente.</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para cambiar foto -->
<?php if ($esProfesorLogueado): ?>
    <div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="photoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="photoModalLabel">Cambiar Foto de Perfil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nuevaImagen" class="form-label">Seleccionar nueva imagen</label>
                            <input type="file" class="form-control" id="nuevaImagen" name="nuevaImagen"
                                accept="image/jpeg,image/png" required>
                            <div class="form-text">Solo se permiten archivos JPG y PNG. Tamaño máximo: 2MB.</div>
                        </div>
                        <div id="imagePreview" style="display: none;">
                            <img id="previewImg" src="" alt="Vista previa" class="img-fluid">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="idClienteImagen" value="<?php echo $profesor['id']; ?>">
                        <input type="hidden" name="pagina" value="perfilProfesor">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Cambiar Foto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    // Pasar datos del profesor a JavaScript
    window.profesorData = {
        id: <?php echo json_encode($profesor['id'] ?? ''); ?>,
        email: <?php echo json_encode($profesor['email'] ?? ''); ?>,
        telefono: <?php echo json_encode($profesor['telefono'] ?? ''); ?>,
        numero_identificacion: <?php echo json_encode($profesor['numero_identificacion'] ?? ''); ?>,
        nro_identificacion: <?php echo json_encode($profesor['nro_identificacion'] ?? ''); ?>,
        mostrar_email: <?php echo json_encode((isset($profesor['mostrar_email']) && $profesor['mostrar_email']) ? true : false); ?>,
        mostrar_telefono: <?php echo json_encode((isset($profesor['mostrar_telefono']) && $profesor['mostrar_telefono']) ? true : false); ?>,
        mostrar_identificacion: <?php echo json_encode((isset($profesor['mostrar_identificacion']) && $profesor['mostrar_identificacion']) ? true : false); ?>
    };
    // Hacer clickeable las tarjetas de cursos
    document.querySelectorAll('.course-card[data-url]').forEach(card => {
        card.addEventListener('click', function() {
            window.location.href = this.getAttribute('data-url');
        });
    });
</script>

<script src="vistas/assets/js/pages/perfilProfesor.js"></script>