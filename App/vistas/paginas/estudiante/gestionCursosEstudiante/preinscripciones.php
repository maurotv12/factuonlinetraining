<?php
// Obtener preinscripciones del usuario
require_once "controladores/cursos.controlador.php";
require_once "controladores/usuarios.controlador.php";
require_once "controladores/inscripciones.controlador.php";


// Obtener información del usuario actual
$idUsuario = $_SESSION["idU"];
$cursos = ControladorCursos::ctrMostrarCursos('estado', 'activo', 'preinscripciones', $idUsuario);
$usuario = ControladorUsuarios::ctrMostrarUsuarios("id", $idUsuario);

// Obtener preinscripciones reales del usuario
$preinscripciones = ControladorInscripciones::ctrMostrarPreinscripcionesPorUsuario($idUsuario, 'preinscrito');
$datosPreinscripciones = $preinscripciones['success'] ? $preinscripciones['data'] : [];
?>
<link rel="stylesheet" href="/cursosApp/App/vistas/assets/css/pages/preinscripciones.css">

<!-- Incluir navbar de estudiante -->
<?php include "vistas/plantillaPartes/navbarEstudiante.php"; ?>

<!-- Contenido principal -->
<div class="estudiante-content">
    <div class="content-container">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" style="margin-bottom: 2rem;">
            <ol class="breadcrumb" style="background: none; padding: 0;">
                <li class="breadcrumb-item">
                    <a href="/cursosApp/App/inicioEstudiante" style="color: var(--primary);">Inicio</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page" style="color: var(--gray);">
                    Preinscripciones
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="section-header">
            <h1 class="section-title">
                <i class="bi bi-cart3" style="color: var(--accent); margin-right: 0.5rem;"></i>
                Mis Preinscripciones
            </h1>
            <p class="section-subtitle">
                Cursos guardados listos para completar la inscripción
            </p>
        </div>

        <!-- Información de preinscripciones -->
        <div class="preregistration-info" style="background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 4px 20px rgba(4, 12, 44, 0.1); margin-bottom: 3rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <div>
                    <h3 style="color: var(--dark); margin: 0;">Cursos en tu lista</h3>
                    <p style="color: var(--gray); margin: 0; font-size: 0.9rem;">
                        Total de cursos guardados: <strong id="totalCount"><?php echo count($datosPreinscripciones); ?></strong>
                    </p>
                </div>
                <div style="text-align: right;">
                    <p style="color: var(--gray); margin: 0; font-size: 0.9rem;">Total estimado:</p>
                    <h3 style="color: var(--accent); margin: 0;" id="totalPrice">$0</h3>
                </div>
            </div>


        </div>

        <!-- Grid de cursos -->
        <div class="courses-grid" id="coursesContainer">
            <!-- Los cursos se cargarán dinámicamente aquí -->
            <?php if ($cursos && count($cursos) > 0): ?>
                <?php foreach ($cursos as $curso): ?>
                    <?php
                    $urlVer = "/cursosApp/App/verCursoProfe/" . $curso["url_amiga"];
                    ?>
                    <a href="<?php echo $urlVer; ?>" class="text-decoration-none">
                        <div class="course-card" data-course-id="<?php echo $curso['id']; ?>">
                            <?php if (isset($curso['es_nuevo']) && $curso['es_nuevo']): ?>
                                <div class="course-badge badge-new">Nuevo</div>
                            <?php endif; ?>

                            <img src="<?php
                                        // Usar el controlador para validar la imagen (solo storage)
                                        echo ControladorCursos::ctrValidarImagenCurso($curso['banner']);
                                        ?>"
                                alt="<?php echo htmlspecialchars($curso['nombre']); ?>"
                                class="course-image"
                                onerror="this.onerror=null; this.src='/cursosApp/storage/public/banners/default/defaultCurso.png'">

                            <div class="course-content">
                                <h3 class="course-title">
                                    <?php echo htmlspecialchars($curso['nombre']); ?>
                                </h3>

                                <div class="course-professor">
                                    <i class="bi bi-person-circle"></i>
                                    <span><?php echo htmlspecialchars($curso['profesor'] ?? 'Instructor'); ?></span>
                                </div>

                                <div class="course-footer">
                                    <span class="course-price">
                                        <?php
                                        if ($curso['valor'] && $curso['valor'] > 0) {
                                            echo '$' . number_format($curso['valor'], 0, ',', '.');
                                        } else {
                                            echo 'Gratis';
                                        }
                                        ?>
                                    </span>
                                    <button class="course-btn" onclick="viewCourse(<?php echo $curso['id']; ?>)">
                                        Ver curso
                                    </button>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-courses" style="grid-column: 1 / -1; text-align: center; padding: 3rem;">
                    <i class="bi bi-journal-x" style="font-size: 4rem; color: var(--gray); margin-bottom: 1rem;"></i>
                    <h3 style="color: var(--gray);">No hay cursos disponibles</h3>
                    <p style="color: var(--gray);">Pronto tendremos nuevos cursos para ti.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Recomendaciones -->
        <?php if (count($datosPreinscripciones) === 0): ?>
            <div class="recommendations" style="margin-top: 4rem;">
                <div class="section-header">
                    <h2 class="section-title">Cursos recomendados para ti</h2>
                    <p class="section-subtitle">
                        Basado en tus intereses y tendencias actuales
                    </p>
                </div>

                <div class="courses-grid" id="recommendationsContainer">
                    <!-- Aquí se cargarían cursos recomendados -->
                    <div style="grid-column: 1 / -1; text-align: center; padding: 2rem; color: var(--gray);">
                        <i class="bi bi-lightbulb" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                        <p>Explora nuestras categorías para descubrir cursos increíbles</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php include "vistas/plantillaPartes/footer.php"; ?>

<!-- JavaScript específico para preinscripciones -->
<script src="/cursosApp/App/vistas/assets/js/pages/preinscripciones.js"></script>

<script>
    // Cargar datos iniciales
    document.addEventListener('DOMContentLoaded', function() {
        updateTotalCount();
    });
</script>