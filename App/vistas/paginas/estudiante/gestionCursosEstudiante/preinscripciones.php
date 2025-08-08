<?php
// Obtener preinscripciones del usuario
require_once "controladores/cursos.controlador.php";
require_once "controladores/usuarios.controlador.php";

// Obtener información del usuario actual
$idUsuario = $_SESSION["idU"];
$usuario = ControladorUsuarios::ctrMostrarUsuarios("id", $idUsuario);

// Por ahora simulamos las preinscripciones - aquí irá la lógica real
$preinscripciones = []; // Aquí se cargarían las preinscripciones reales desde la BD
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
                    placeholder="Buscar en tus preinscripciones...">
            </div>

            <div class="nav-buttons">
                <a href="/cursosApp/App/cursosCategorias" class="nav-btn" id="categoriesBtn">
                    <i class="bi bi-grid-3x3-gap"></i>
                    Categorías
                </a>
                <a href="/cursosApp/App/preinscripciones" class="nav-btn accent" id="preregistrationBtn">
                    <i class="bi bi-cart3"></i>
                    Preinscripciones
                </a>
                <a href="/cursosApp/App/cursosEstudiante" class="nav-btn" id="myCoursesBtn">
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
                        Total de cursos guardados: <strong id="totalCount"><?php echo count($preinscripciones); ?></strong>
                    </p>
                </div>
                <div style="text-align: right;">
                    <p style="color: var(--gray); margin: 0; font-size: 0.9rem;">Total estimado:</p>
                    <h3 style="color: var(--accent); margin: 0;" id="totalPrice">$0</h3>
                </div>
            </div>

            <?php if (count($preinscripciones) > 0): ?>
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
            <?php if (count($preinscripciones) > 0): ?>
                <?php foreach ($preinscripciones as $curso): ?>
                    <div class="course-card preregistered" data-course-id="<?php echo $curso['id']; ?>">
                        <!-- Badge de preinscripción -->
                        <div class="course-badge" style="background: var(--accent); color: white;">
                            Preinscrito
                        </div>

                        <img src="<?php echo $curso['banner'] ?: '/cursosApp/App/vistas/assets/images/course-default.jpg'; ?>"
                            alt="<?php echo htmlspecialchars($curso['nombre']); ?>"
                            class="course-image"
                            onerror="this.src='/cursosApp/App/vistas/assets/images/course-default.jpg'">

                        <div class="course-content">
                            <h3 class="course-title">
                                <?php echo htmlspecialchars($curso['nombre']); ?>
                            </h3>

                            <div class="course-professor">
                                <i class="bi bi-person-circle"></i>
                                <span><?php echo htmlspecialchars($curso['profesor'] ?? 'Instructor'); ?></span>
                            </div>

                            <!-- Fecha de preinscripción -->
                            <div class="preregistration-date" style="margin-bottom: 1rem;">
                                <i class="bi bi-clock" style="color: var(--gray); margin-right: 0.5rem;"></i>
                                <span style="color: var(--gray); font-size: 0.85rem;">
                                    Guardado el <?php echo date('d/m/Y'); ?>
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
                                <div style="display: flex; gap: 0.5rem;">
                                    <button class="course-btn" onclick="completarInscripcion(<?php echo $curso['id']; ?>)" style="background: var(--accent); font-size: 0.8rem; padding: 0.4rem 0.8rem;">
                                        Inscribirse
                                    </button>
                                    <button class="course-btn" onclick="removerPreinscripcion(<?php echo $curso['id']; ?>)" style="background: var(--gray); font-size: 0.8rem; padding: 0.4rem 0.8rem;">
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
        <?php if (count($preinscripciones) === 0): ?>
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


<script>
    // Funciones específicas para preinscripciones
    function completarInscripcion(courseId) {
        Swal.fire({
            title: '¿Completar inscripción?',
            text: 'Serás redirigido al proceso de pago',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ff8d14',
            cancelButtonColor: '#8a9cac',
            confirmButtonText: 'Sí, inscribirme',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Aquí iría la lógica para completar la inscripción
                window.location.href = `/cursosApp/App/inscripcion/${courseId}`;
            }
        });
    }

    function removerPreinscripcion(courseId) {
        Swal.fire({
            title: '¿Eliminar de preinscripciones?',
            text: 'El curso será removido de tu lista',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#8a9cac',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Aquí iría la lógica AJAX para remover la preinscripción
                removePreregistrationCard(courseId);

                Swal.fire({
                    title: '¡Eliminado!',
                    text: 'El curso ha sido removido de tus preinscripciones',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    }

    function completarTodasInscripciones() {
        Swal.fire({
            title: '¿Inscribirse a todos los cursos?',
            text: 'Serás redirigido al proceso de pago múltiple',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ff8d14',
            cancelButtonColor: '#8a9cac',
            confirmButtonText: 'Sí, proceder',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Aquí iría la lógica para inscripción múltiple
                window.location.href = '/cursosApp/App/inscripcionMultiple';
            }
        });
    }

    function limpiarPreinscripciones() {
        Swal.fire({
            title: '¿Limpiar todas las preinscripciones?',
            text: 'Esta acción no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#8a9cac',
            confirmButtonText: 'Sí, limpiar todo',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Aquí iría la lógica AJAX para limpiar todas las preinscripciones
                document.getElementById('preregistrationsContainer').innerHTML = `
                <div class="no-preregistrations" style="grid-column: 1 / -1; text-align: center; padding: 4rem 2rem;">
                    <i class="bi bi-check-circle" style="font-size: 5rem; color: var(--accent); margin-bottom: 2rem;"></i>
                    <h3 style="color: var(--dark); margin-bottom: 1rem;">Lista limpiada</h3>
                    <p style="color: var(--gray); margin-bottom: 2rem;">
                        Todas las preinscripciones han sido eliminadas
                    </p>
                    <a href="/cursosApp/App/cursosCategorias" class="course-btn" style="text-decoration: none;">
                        Explorar nuevos cursos
                    </a>
                </div>
            `;

                updateTotalCount(0);

                Swal.fire({
                    title: '¡Limpiado!',
                    text: 'Todas las preinscripciones han sido eliminadas',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    }

    function removePreregistrationCard(courseId) {
        const card = document.querySelector(`[data-course-id="${courseId}"]`);
        if (card) {
            card.style.animation = 'fadeOut 0.3s ease';
            setTimeout(() => {
                card.remove();
                updateTotalCount();
            }, 300);
        }
    }

    function updateTotalCount(count = null) {
        const container = document.getElementById('preregistrationsContainer');
        const cards = container.querySelectorAll('.course-card');
        const totalCount = count !== null ? count : cards.length;

        document.getElementById('totalCount').textContent = totalCount;

        // Calcular precio total
        let totalPrice = 0;
        cards.forEach(card => {
            const priceElement = card.querySelector('.course-price');
            if (priceElement) {
                const priceText = priceElement.textContent.replace(/[^\d]/g, '');
                if (priceText) {
                    totalPrice += parseInt(priceText);
                }
            }
        });

        document.getElementById('totalPrice').textContent = totalPrice > 0 ?
            '$' + totalPrice.toLocaleString() : '$0';
    }

    // Cargar datos iniciales
    document.addEventListener('DOMContentLoaded', function() {
        updateTotalCount();
    });
</script>

<style>
    @keyframes fadeOut {
        from {
            opacity: 1;
            transform: scale(1);
        }

        to {
            opacity: 0;
            transform: scale(0.8);
        }
    }

    .course-card.preregistered {
        border-left: 4px solid var(--accent);
    }

    .preregistration-info {
        border-left: 4px solid var(--accent);
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