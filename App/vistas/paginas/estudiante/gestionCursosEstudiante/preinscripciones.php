<?php
// Obtener preinscripciones del usuario
require_once "controladores/cursos.controlador.php";
require_once "controladores/usuarios.controlador.php";
require_once "controladores/inscripciones.controlador.php";

// Obtener información del usuario actual
$idUsuario = $_SESSION["idU"];
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

            <?php if (count($datosPreinscripciones) > 0): ?>
                <div style="display: flex; gap: 1rem;">
                    <button class="course-btn" onclick="completarTodasInscripciones()" style="background: var(--accent);">
                        <i class="bi bi-credit-card"></i>
                        Completar todas las inscripciones
                    </button>
                    <button class="course-btn" onclick="limpiarPreinscripciones()" style="background: var(--gray);">
                        <i class="bi bi-trash"></i>
                        Limpiar lista
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <!-- Grid de preinscripciones -->
        <div class="courses-grid" id="preregistrationsContainer">
            <?php if (count($datosPreinscripciones) > 0): ?>
                <?php foreach ($datosPreinscripciones as $preinscripcion):
                    $curso = $preinscripcion['curso'];
                    $fechaPreinscripcion = new DateTime($preinscripcion['fecha_preinscripcion']);
                ?>
                    <div class="course-card preregistered" data-course-id="<?php echo $curso['id']; ?>" data-preinscripcion-id="<?php echo $preinscripcion['id']; ?>">
                        <!-- Badge de preinscripción -->
                        <div class="course-badge" style="background: var(--accent); color: white;">
                            Preinscrito
                        </div>

                        <img src="<?php echo !empty($curso['imagen']) ? '/cursosApp/storage/public/courses/' . $curso['imagen'] : '/cursosApp/App/vistas/assets/img/default-course.jpg'; ?>"
                            alt="<?php echo htmlspecialchars($curso['titulo']); ?>"
                            class="course-image"
                            onerror="this.onerror=null; this.src='/cursosApp/storage/public/banners/default/defaultCurso.png'">

                        <div class="course-content">
                            <h3 class="course-title">
                                <?php echo htmlspecialchars($curso['titulo']); ?>
                            </h3>

                            <div class="course-professor">
                                <i class="bi bi-person-circle"></i>
                                <span><?php echo htmlspecialchars($curso['instructor_nombre'] . ' ' . $curso['instructor_apellido']); ?></span>
                            </div>

                            <!-- Fecha de preinscripción -->
                            <div class="preregistration-date" style="margin-bottom: 1rem;">
                                <i class="bi bi-clock" style="color: var(--gray); margin-right: 0.5rem;"></i>
                                <span style="color: var(--gray); font-size: 0.85rem;">
                                    Guardado el <?php echo $fechaPreinscripcion->format('d/m/Y'); ?>
                                </span>
                            </div>

                            <div class="course-footer">
                                <span class="course-price">
                                    <?php
                                    if ($curso['precio'] && $curso['precio'] > 0) {
                                        echo '$' . number_format($curso['precio'], 2);
                                    } else {
                                        echo 'Gratis';
                                    }
                                    ?>
                                </span>
                                <div style="display: flex; gap: 0.5rem;">
                                    <button class="course-btn" onclick="completarInscripcion(<?php echo $preinscripcion['id']; ?>)" style="background: var(--accent); font-size: 0.8rem; padding: 0.4rem 0.8rem;">
                                        Inscribirse
                                    </button>
                                    <button class="course-btn" onclick="removerPreinscripcion(<?php echo $preinscripcion['id']; ?>)" style="background: var(--gray); font-size: 0.8rem; padding: 0.4rem 0.8rem;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-preregistrations" style="grid-column: 1 / -1; text-align: center; padding: 4rem 2rem;">
                    <i class="bi bi-cart-x" style="font-size: 5rem; color: var(--gray); margin-bottom: 2rem;"></i>
                    <h3 style="color: var(--dark); margin-bottom: 1rem;">No tienes preinscripciones</h3>
                    <p style="color: var(--gray); margin-bottom: 2rem; line-height: 1.6;">
                        Cuando encuentres cursos que te interesen, puedes guardarlos aquí para inscribirte más tarde.<br>
                        ¡Explora nuestro catálogo y encuentra el curso perfecto para ti!
                    </p>
                    <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                        <a href="/cursosApp/App/inicioEstudiante" class="course-btn" style="text-decoration: none;">
                            <i class="bi bi-house"></i>
                            Ir al inicio
                        </a>
                        <a href="/cursosApp/App/cursosCategorias" class="course-btn" style="text-decoration: none; background: var(--accent);">
                            <i class="bi bi-compass"></i>
                            Explorar cursos
                        </a>
                    </div>
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