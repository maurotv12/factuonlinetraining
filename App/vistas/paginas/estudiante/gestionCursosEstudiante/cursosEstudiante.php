<?php
// Obtener cursos del estudiante
require_once "controladores/cursos.controlador.php";
require_once "controladores/usuarios.controlador.php";
require_once "controladores/inscripciones.controlador.php";

// Obtener información del usuario actual
$idUsuario = $_SESSION["idU"];
$cursos = ControladorCursos::ctrMostrarCursos('estado', 'activo', 'inscripciones', $idUsuario);
$usuario = ControladorUsuarios::ctrMostrarUsuarios("id", $idUsuario);

// Obtener cursos inscritos del usuario
$inscripciones = ControladorInscripciones::ctrMostrarInscripcionesPorUsuario($idUsuario, 'inscrito');
$cursosInscritos = $inscripciones['success'] ? $inscripciones['data'] : [];

// Calcular estadísticas
$estadisticas = [
    'completados' => 0,
    'en_progreso' => 0,
    'no_iniciados' => 0,
    'total_horas' => 0,
    'certificados' => 0
];

foreach ($cursosInscritos as $inscripcion) {
    $estadisticas['total_horas'] += $inscripcion['curso']['duracion_total'] ?? 0;

    switch ($inscripcion['estado']) {
        case 'completado':
            $estadisticas['completados']++;
            $estadisticas['certificados']++;
            break;
        case 'en_progreso':
            $estadisticas['en_progreso']++;
            break;
        case 'inscrito':
            $estadisticas['no_iniciados']++;
            break;
    }
}
?>

<!-- Incluir navbar de estudiante -->
<?php include "vistas/plantillaPartes/navbarEstudiante.php"; ?>

<!-- Contenido principal -->
<div class="estudiante-content">
    <div class="content-container">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" style="margin-bottom: 2rem;">
            <ol class="breadcrumb" style="background: none; padding: 0;">
                <li class="breadcrumb-item">
                    <a href="/factuonlinetraining/App/inicioEstudiante" style="color: var(--primary);">Inicio</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page" style="color: var(--gray);">
                    Mis Cursos
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="section-header">
            <h1 class="section-title">
                <i class="bi bi-journal-bookmark" style="color: var(--primary); margin-right: 0.5rem;"></i>
                Mis Cursos
            </h1>
            <p class="section-subtitle">
                Continúa tu aprendizaje donde lo dejaste
            </p>
        </div>

        <!-- Estadísticas de progreso -->
        <div class="stats-container" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
            <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 15px; box-shadow: 0 4px 20px rgba(4, 12, 44, 0.1); text-align: center;">
                <div style="font-size: 2.5rem; color: var(--primary); margin-bottom: 0.5rem;">
                    <i class="bi bi-journal-bookmark"></i>
                </div>
                <h3 style="color: var(--dark); margin: 0; font-size: 2rem;"><?php echo count($cursosInscritos); ?></h3>
                <p style="color: var(--gray); margin: 0; font-size: 0.9rem;">Cursos inscritos</p>
            </div>
            <!-- Futuro Desarrollo -->
            <!-- <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 15px; box-shadow: 0 4px 20px rgba(4, 12, 44, 0.1); text-align: center;">
                <div style="font-size: 2.5rem; color: var(--accent); margin-bottom: 0.5rem;">
                    <i class="bi bi-play-circle"></i>
                </div>
                <h3 style="color: var(--dark); margin: 0; font-size: 2rem;"><?php echo $estadisticas['en_progreso']; ?></h3>
                <p style="color: var(--gray); margin: 0; font-size: 0.9rem;">En progreso</p>
            </div>

            <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 15px; box-shadow: 0 4px 20px rgba(4, 12, 44, 0.1); text-align: center;">
                <div style="font-size: 2.5rem; color: #28a745; margin-bottom: 0.5rem;">
                    <i class="bi bi-check-circle"></i>
                </div>
                <h3 style="color: var(--dark); margin: 0; font-size: 2rem;"><?php echo $estadisticas['completados']; ?></h3>
                <p style="color: var(--gray); margin: 0; font-size: 0.9rem;">Completados</p>
            </div>

            <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 15px; box-shadow: 0 4px 20px rgba(4, 12, 44, 0.1); text-align: center;">
                <div style="font-size: 2.5rem; color: var(--primary); margin-bottom: 0.5rem;">
                    <i class="bi bi-award"></i>
                </div>
                <h3 style="color: var(--dark); margin: 0; font-size: 2rem;"><?php echo $estadisticas['certificados']; ?></h3>
                <p style="color: var(--gray); margin: 0; font-size: 0.9rem;">Certificados</p>
            </div> -->
        </div>

        <!-- Filtros -->
        <div class="filters-container" style="background: white; padding: 1.5rem; border-radius: 15px; box-shadow: 0 4px 20px rgba(4, 12, 44, 0.1); margin-bottom: 3rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div class="filter-buttons" style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    <button class="filter-btn active" data-filter="todos" onclick="filterCourses('todos')">
                        Todos
                    </button>
                    <!-- Futuro Desarrollo -->
                    <!-- <button class="filter-btn" data-filter="en_progreso" onclick="filterCourses('en_progreso')">
                        En progreso
                    </button>
                    <button class="filter-btn" data-filter="completados" onclick="filterCourses('completados')">
                        Completados
                    </button>
                    <button class="filter-btn" data-filter="no_iniciados" onclick="filterCourses('no_iniciados')">
                        No iniciados
                    </button> -->
                </div>

                <div class="sort-container" style="display: flex; align-items: center; gap: 0.5rem;">
                    <label for="sortSelect" style="color: var(--gray); font-size: 0.9rem;">Ordenar por:</label>
                    <select id="sortSelect" onchange="sortCourses(this.value)" style="padding: 0.5rem; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 0.9rem;">
                        <option value="recientes">Más recientes</option>
                        <option value="nombre">Nombre A-Z</option>
                        <option value="fecha_inscripcion">Fecha de inscripción</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Grid de cursos -->
        <div class="courses-grid" id="coursesContainer">
            <!-- Los cursos se cargarán dinámicamente aquí -->
            <?php if ($cursos && count($cursos) > 0): ?>
                <?php foreach ($cursos as $curso): ?>
                    <?php
                    $urlVer = "/factuonlinetraining/App/verCursoProfe/" . $curso["url_amiga"];
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
                                onerror="this.onerror=null; this.src='/factuonlinetraining/storage/public/banners/default/defaultCurso.png'">

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
                                    <button class="course-btn">
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

        <!-- Recomendaciones para continuar aprendiendo -->
        <?php if (count($cursosInscritos) > 0): ?>
            <div class="recommendations" style="margin-top: 4rem;">
                <div class="section-header">
                    <h2 class="section-title">Continúa tu aprendizaje</h2>
                    <p class="section-subtitle">
                        Cursos relacionados que podrían interesarte
                    </p>
                </div>

                <div class="courses-grid" id="recommendationsContainer">
                    <!-- Aquí se cargarían cursos recomendados -->
                    <div style="grid-column: 1 / -1; text-align: center; padding: 2rem; color: var(--gray);">
                        <i class="bi bi-lightbulb" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                        <p>Basado en tus cursos actuales, te recomendaremos nuevas oportunidades de aprendizaje</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<!-- JavaScript específico para mis cursos -->
<script src="/factuonlinetraining/App/vistas/assets/js/pages/cursosEstudiante.js"></script>

<script>
    // Cargar datos iniciales
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar filtros y estadísticas si es necesario
        console.log('Mis cursos cargados');
    });
</script>

<style>
    .filter-btn {
        padding: 0.5rem 1rem;
        border: 1px solid #e0e0e0;
        background: white;
        color: var(--gray);
        border-radius: 25px;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .filter-btn:hover {
        border-color: var(--primary);
        color: var(--primary);
    }

    .filter-btn.active {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    .course-card.my-course {
        border-left: 4px solid var(--primary);
    }

    .progress-bar {
        position: relative;
        overflow: hidden;
    }

    .progress-fill {
        transition: width 0.8s ease;
    }

    .stat-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(4, 12, 44, 0.15);
    }

    .breadcrumb {
        background: none;
        padding: 0;
        margin: 0;
    }

    .breadcrumb-item+.breadcrumb-item::before {
        content: "›";
        color: var(--gray);
    }
</style>