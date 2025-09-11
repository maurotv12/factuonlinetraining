<?php
// Obtener categorías y cursos
require_once "controladores/cursos.controlador.php";
require_once "controladores/usuarios.controlador.php";

$categorias = ControladorCursos::ctrObtenerCategorias();
$categoriaSeleccionada = $_GET['categoria'] ?? null;
$cursos = [];

if ($categoriaSeleccionada) {
    $cursos = ControladorCursos::ctrMostrarCursos('id_categoria', $categoriaSeleccionada);
} else {
    $cursos = ControladorCursos::ctrMostrarCursos(null, null);
}

// Obtener información del usuario actual
$idUsuario = $_SESSION["idU"];
$usuario = ControladorUsuarios::ctrMostrarUsuarios("id", $idUsuario);
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
                    <a href="/cursosApp/App/inicioEstudiante" style="color: var(--primary);">Inicio</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page" style="color: var(--gray);">
                    Categorías
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="section-header">
            <h1 class="section-title">Explorar por categorías</h1>
            <p class="section-subtitle">
                Encuentra cursos organizados por temas de tu interés
            </p>
        </div>

        <!-- Filtros de categorías -->
        <div class="categories-filter" style="margin-bottom: 3rem;">
            <div class="filter-buttons" style="display: flex; gap: 1rem; flex-wrap: wrap; justify-content: center;">
                <button class="filter-btn <?php echo !$categoriaSeleccionada ? 'active' : ''; ?>"
                    onclick="filterCategory(null, 'Todos los cursos')"
                    style="padding: 0.7rem 1.5rem; border: 2px solid var(--primary); background: <?php echo !$categoriaSeleccionada ? 'var(--primary)' : 'transparent'; ?>; color: <?php echo !$categoriaSeleccionada ? 'white' : 'var(--primary)'; ?>; border-radius: 25px; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                    <i class="bi bi-grid-3x3-gap"></i>
                    Todas las categorías
                </button>

                <?php if ($categorias && count($categorias) > 0): ?>
                    <?php foreach ($categorias as $categoria): ?>
                        <button class="filter-btn <?php echo $categoriaSeleccionada == $categoria['id'] ? 'active' : ''; ?>"
                            onclick="filterCategory(<?php echo $categoria['id']; ?>, '<?php echo htmlspecialchars($categoria['nombre']); ?>')"
                            style="padding: 0.7rem 1.5rem; border: 2px solid var(--primary); background: <?php echo $categoriaSeleccionada == $categoria['id'] ? 'var(--primary)' : 'transparent'; ?>; color: <?php echo $categoriaSeleccionada == $categoria['id'] ? 'white' : 'var(--primary)'; ?>; border-radius: 25px; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                            <?php echo htmlspecialchars($categoria['nombre']); ?>
                        </button>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sección de cursos -->
        <div class="section-header">
            <h2 class="section-title" id="sectionTitle">
                <?php
                if ($categoriaSeleccionada && $categorias) {
                    foreach ($categorias as $cat) {
                        if ($cat['id'] == $categoriaSeleccionada) {
                            echo 'Cursos de ' . htmlspecialchars($cat['nombre']);
                            break;
                        }
                    }
                } else {
                    echo 'Todos los cursos disponibles';
                }
                ?>
            </h2>
            <p class="section-subtitle" id="resultsCount">
                <?php echo count($cursos ?: []); ?> curso<?php echo count($cursos ?: []) !== 1 ? 's' : ''; ?> encontrado<?php echo count($cursos ?: []) !== 1 ? 's' : ''; ?>
            </p>
        </div>

        <!-- Grid de cursos -->
        <div class="courses-grid" id="coursesContainer">
            <?php if ($cursos && count($cursos) > 0): ?>
                <?php foreach ($cursos as $curso): ?>
                    <div class="course-card" data-course-id="<?php echo $curso['id']; ?>">
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

                            <!-- Categoría del curso -->
                            <div class="course-category" style="margin-bottom: 1rem;">
                                <i class="bi bi-tag" style="color: var(--gray); margin-right: 0.5rem;"></i>
                                <span style="color: var(--gray); font-size: 0.85rem;">
                                    <?php echo htmlspecialchars($curso['categoria'] ?? 'Sin categoría'); ?>
                                </span>
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
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-courses" style="grid-column: 1 / -1; text-align: center; padding: 3rem;">
                    <i class="bi bi-search" style="font-size: 4rem; color: var(--gray); margin-bottom: 1rem;"></i>
                    <h3 style="color: var(--gray);">No hay cursos en esta categoría</h3>
                    <p style="color: var(--gray);">Prueba con otra categoría o explora todos los cursos disponibles.</p>
                    <button class="course-btn" onclick="filterCategory(null, 'Todos los cursos')" style="margin-top: 1rem;">
                        Ver todos los cursos
                    </button>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php include "vistas/plantillaPartes/footer.php"; ?>

<!-- JavaScript específico para categorías -->
<script src="/cursosApp/App/vistas/assets/js/pages/cursosCategorias.js"></script>

<script>
    // Función para ver curso
    window.viewCourse = function(courseId) {
        window.location.href = `/cursosApp/App/verCurso/${courseId}`;
    };

    // Estilos dinámicos para botones de filtro
    document.addEventListener('DOMContentLoaded', function() {
        const filterButtons = document.querySelectorAll('.filter-btn');

        filterButtons.forEach(button => {
            if (!button.classList.contains('active')) {
                button.addEventListener('mouseenter', function() {
                    this.style.background = 'var(--primary)';
                    this.style.color = 'white';
                });

                button.addEventListener('mouseleave', function() {
                    this.style.background = 'transparent';
                    this.style.color = 'var(--primary)';
                });
            }
        });
    });
</script>

<style>
    .categories-filter .filter-btn.active {
        background: var(--primary) !important;
        color: white !important;
    }

    .course-category {
        font-size: 0.85rem;
        color: var(--gray);
        margin-bottom: 1rem;
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