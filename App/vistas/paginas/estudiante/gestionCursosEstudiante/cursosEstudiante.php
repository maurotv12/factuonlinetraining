<?php
// Obtener cursos del estudiante
require_once "controladores/cursos.controlador.php";
require_once "controladores/usuarios.controlador.php";
require_once "controladores/inscripciones.controlador.php";

// Obtener información del usuario actual
$idUsuario = $_SESSION["idU"];
$usuario = ControladorUsuarios::ctrMostrarUsuarios("id", $idUsuario);

// Por ahora simulamos los cursos - aquí irá la lógica real
$cursosInscritos = []; // Aquí se cargarían los cursos reales desde la BD
$estadisticas = [
    'completados' => 0,
    'en_progreso' => 0,
    'no_iniciados' => 0,
    'total_horas' => 0,
    'certificados' => 0
];
?>

<!-- Navbar específico para estudiantes -->
<nav class="estudiante-navbar">
    <div class="estudiante-nav-container">
        <div class="nav-content">
            <a href="/cursosApp/App/inicioEstudiante" class="nav-brand">
                <i class="bi bi-book-fill"></i>
                CursosApp
            </a>

            <div class="search-container">
                <i class="bi bi-search search-icon"></i>
                <input type="text"
                    id="courseSearch"
                    class="search-input"
                    placeholder="Buscar en mis cursos...">
            </div>

            <div class="nav-buttons">
                <a href="/cursosApp/App/cursosCategorias" class="nav-btn" id="categoriesBtn">
                    <i class="bi bi-grid-3x3-gap"></i>
                    Categorías
                </a>
                <a href="/cursosApp/App/preinscripciones" class="nav-btn" id="preregistrationBtn">
                    <i class="bi bi-cart3"></i>
                    Preinscripciones
                </a>
                <a href="/cursosApp/App/cursosEstudiante" class="nav-btn accent" id="myCoursesBtn">
                    <i class="bi bi-journal-bookmark"></i>
                    Mis Cursos
                </a>
            </div>
        </div>
    </div>
</nav>

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

            <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 15px; box-shadow: 0 4px 20px rgba(4, 12, 44, 0.1); text-align: center;">
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
            </div>
        </div>

        <!-- Filtros -->
        <div class="filters-container" style="background: white; padding: 1.5rem; border-radius: 15px; box-shadow: 0 4px 20px rgba(4, 12, 44, 0.1); margin-bottom: 3rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div class="filter-buttons" style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    <button class="filter-btn active" data-filter="todos" onclick="filterCourses('todos')">
                        Todos
                    </button>
                    <button class="filter-btn" data-filter="en_progreso" onclick="filterCourses('en_progreso')">
                        En progreso
                    </button>
                    <button class="filter-btn" data-filter="completados" onclick="filterCourses('completados')">
                        Completados
                    </button>
                    <button class="filter-btn" data-filter="no_iniciados" onclick="filterCourses('no_iniciados')">
                        No iniciados
                    </button>
                </div>

                <div class="sort-container" style="display: flex; align-items: center; gap: 0.5rem;">
                    <label for="sortSelect" style="color: var(--gray); font-size: 0.9rem;">Ordenar por:</label>
                    <select id="sortSelect" onchange="sortCourses(this.value)" style="padding: 0.5rem; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 0.9rem;">
                        <option value="recientes">Más recientes</option>
                        <option value="progreso">Progreso</option>
                        <option value="nombre">Nombre A-Z</option>
                        <option value="fecha_inscripcion">Fecha de inscripción</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Grid de mis cursos -->
        <div class="courses-grid" id="myCoursesContainer">
            <?php if (count($cursosInscritos) > 0): ?>
                <?php foreach ($cursosInscritos as $curso): ?>
                    <div class="course-card my-course" data-course-id="<?php echo $curso['id']; ?>" data-status="<?php echo $curso['estado'] ?? 'no_iniciado'; ?>">
                        <!-- Badge de estado -->
                        <?php
                        $estado = $curso['estado'] ?? 'no_iniciado';
                        $badgeColor = '#8a9cac';
                        $badgeText = 'No iniciado';

                        switch ($estado) {
                            case 'en_progreso':
                                $badgeColor = '#ff8d14';
                                $badgeText = 'En progreso';
                                break;
                            case 'completado':
                                $badgeColor = '#28a745';
                                $badgeText = 'Completado';
                                break;
                        }
                        ?>
                        <div class="course-badge" style="background: <?php echo $badgeColor; ?>; color: white;">
                            <?php echo $badgeText; ?>
                        </div>

                        <img src="<?php echo ControladorCursos::ctrValidarImagenCurso($curso['banner']); ?>"
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

                            <!-- Progreso del curso -->
                            <div class="course-progress" style="margin: 1rem 0;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                    <span style="color: var(--gray); font-size: 0.85rem;">Progreso</span>
                                    <span style="color: var(--dark); font-size: 0.85rem; font-weight: 600;">
                                        <?php echo $curso['progreso'] ?? 0; ?>%
                                    </span>
                                </div>
                                <div class="progress-bar" style="width: 100%; height: 6px; background: #e0e0e0; border-radius: 3px; overflow: hidden;">
                                    <div class="progress-fill" style="width: <?php echo $curso['progreso'] ?? 0; ?>%; height: 100%; background: var(--accent); transition: width 0.3s ease;"></div>
                                </div>
                            </div>

                            <!-- Información adicional -->
                            <div class="course-info" style="margin-bottom: 1rem;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                                    <span style="color: var(--gray); font-size: 0.8rem;">
                                        <i class="bi bi-clock"></i>
                                        <?php echo $curso['duracion'] ?? '0'; ?> horas
                                    </span>
                                    <span style="color: var(--gray); font-size: 0.8rem;">
                                        <i class="bi bi-calendar"></i>
                                        <?php echo date('d/m/Y', strtotime($curso['fecha_inscripcion'] ?? 'now')); ?>
                                    </span>
                                </div>

                                <?php if ($estado === 'completado' && isset($curso['fecha_completado'])): ?>
                                    <div style="color: #28a745; font-size: 0.8rem;">
                                        <i class="bi bi-check-circle"></i>
                                        Completado el <?php echo date('d/m/Y', strtotime($curso['fecha_completado'])); ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="course-footer">
                                <div style="display: flex; gap: 0.5rem; width: 100%;">
                                    <?php if ($estado === 'completado'): ?>
                                        <button class="course-btn" onclick="verCertificado(<?php echo $curso['id']; ?>)" style="background: #28a745; font-size: 0.8rem; padding: 0.4rem 0.8rem;">
                                            <i class="bi bi-award"></i>
                                            Certificado
                                        </button>
                                        <button class="course-btn" onclick="revisarCurso(<?php echo $curso['id']; ?>)" style="background: var(--primary); font-size: 0.8rem; padding: 0.4rem 0.8rem;">
                                            <i class="bi bi-arrow-clockwise"></i>
                                            Revisar
                                        </button>
                                    <?php else: ?>
                                        <button class="course-btn" onclick="continuarCurso(<?php echo $curso['id']; ?>)" style="background: var(--accent); font-size: 0.8rem; padding: 0.4rem 0.8rem; flex: 1;">
                                            <i class="bi bi-play-circle"></i>
                                            <?php echo $estado === 'no_iniciado' ? 'Comenzar' : 'Continuar'; ?>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-courses" style="grid-column: 1 / -1; text-align: center; padding: 4rem 2rem;">
                    <i class="bi bi-journal-x" style="font-size: 5rem; color: var(--gray); margin-bottom: 2rem;"></i>
                    <h3 style="color: var(--dark); margin-bottom: 1rem;">Aún no tienes cursos</h3>
                    <p style="color: var(--gray); margin-bottom: 2rem; line-height: 1.6;">
                        ¡Comienza tu viaje de aprendizaje hoy!<br>
                        Explora nuestro catálogo y encuentra el curso perfecto para desarrollar nuevas habilidades.
                    </p>
                    <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                        <a href="/cursosApp/App/cursosCategorias" class="course-btn" style="text-decoration: none; background: var(--accent);">
                            <i class="bi bi-compass"></i>
                            Explorar cursos
                        </a>
                        <a href="/cursosApp/App/preinscripciones" class="course-btn" style="text-decoration: none;">
                            <i class="bi bi-cart3"></i>
                            Ver preinscripciones
                        </a>
                    </div>
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

<?php include "vistas/plantillaPartes/footer.php"; ?>

<!-- JavaScript base y específico para mis cursos -->
<script src="/cursosApp/App/vistas/assets/js/pages/estudianteBase.js"></script>
<script src="/cursosApp/App/vistas/assets/js/pages/cursosEstudiante.js"></script>

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