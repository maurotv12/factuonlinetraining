<?php
// Obtener cursos disponibles
require_once "controladores/cursos.controlador.php";
require_once "controladores/usuarios.controlador.php";

$cursos = ControladorCursos::ctrMostrarCursos('estado', 'activo');
$categorias = ControladorCursos::ctrObtenerCategorias();

// Obtener información del usuario actual
$idUsuario = $_SESSION["idU"];
$item = "id";
$valor = $idUsuario;
$usuario = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);
?>

<!-- Incluir navbar de estudiante -->
<?php include "vistas/plantillaPartes/navbarEstudiante.php"; ?>

<!-- Contenido principal -->
<div class="estudiante-content">
    <div class="content-container">

        <!-- Bienvenida y acciones rápidas -->
        <div class="section-header">
            <h1 class="section-title">
                ¡Hola, <?php echo $usuario['nombre']; ?>! 👋
            </h1>
            <p class="section-subtitle">
                Descubre nuevos cursos y continúa tu aprendizaje
            </p>
        </div>

        <!-- Acciones rápidas -->
        <div class="quick-actions">
            <div class="actions-grid">
                <a href="/cursosApp/App/cursosEstudiante" class="action-card">
                    <div class="action-icon">
                        <i class="bi bi-play-circle"></i>
                    </div>
                    <h3 class="action-title">Continuar aprendiendo</h3>
                    <p class="action-description">
                        Accede a tus cursos en progreso y continúa donde lo dejaste
                    </p>
                </a>

                <a href="/cursosApp/App/cursosCategorias" class="action-card">
                    <div class="action-icon">
                        <i class="bi bi-compass"></i>
                    </div>
                    <h3 class="action-title">Explorar categorías</h3>
                    <p class="action-description">
                        Descubre cursos organizados por temas de tu interés
                    </p>
                </a>

                <a href="/cursosApp/App/preinscripciones" class="action-card">
                    <div class="action-icon">
                        <i class="bi bi-bookmark-check"></i>
                    </div>
                    <h3 class="action-title">Preinscripciones</h3>
                    <p class="action-description">
                        Revisa los cursos guardados y completa tu inscripción
                    </p>
                </a>
            </div>
        </div>

        <!-- Sección de cursos -->
        <div class="section-header">
            <h2 class="section-title" id="sectionTitle">Cursos disponibles</h2>
            <p class="section-subtitle" id="resultsCount">
                Cargando cursos...
            </p>
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
                                            echo '$' . number_format($curso['valor'], 0, ',', '.') . ' COL';
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

    </div>
</div>

<?php include "vistas/plantillaPartes/footer.php"; ?>

<!-- JavaScript específico para inicio de estudiante -->
<script src="/cursosApp/App/vistas/assets/js/pages/inicioEstudiante.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Actualizar contador inicial
        const totalCursos = <?php echo count($cursos ?: []); ?>;
        updateResultsCount(totalCursos);

        // Simular función para ver curso (temporal)
        window.viewCourse = function(courseId) {
            // Redirigir a la página de detalle del curso
            window.location.href = `/cursosApp/App/verCurso/${courseId}`;
        };
    });
</script>