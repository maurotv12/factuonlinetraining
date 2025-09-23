<?php

/**
 * Navbar específico para estudiantes
 * Funcionalidad compartida entre todas las vistas de estudiante
 */

// Obtener la página actual para marcar la opción activa
$currentPage = basename($_SERVER['REQUEST_URI']);
$currentPath = $_SERVER['REQUEST_URI'];

// Función para determinar si una ruta está activa
function isActivePage($page, $currentPath)
{
    return strpos($currentPath, $page) !== false;
}
?>

<!-- Navbar específico para estudiantes -->
<nav class="estudiante-navbar">
    <div class="estudiante-nav-container">
        <div class="nav-content">
            <a class="navbar-brand d-flex align-items-center" href="/cursosApp/App/inicioEstudiante">
                <?php
                // Ruta dinámica que siempre apuntará al logo
                $rutaLogo = $_SERVER['DOCUMENT_ROOT'] . "/cursosApp/App/vistas/img/logo.png";
                $rutaWebLogo = "/cursosApp/App/vistas/img/logo.png";
                ?>
                <img src="<?php echo $rutaWebLogo; ?>" alt="Logo" class="logo-offcanvas me-2" style="height: 80px;">
            </a>

            <div class="search-container">
                <i class="bi bi-search search-icon"></i>
                <input type="text"
                    id="courseSearch"
                    class="search-input"
                    placeholder="Buscar cursos, instructores, categorías...">
            </div>

            <div class="nav-buttons">
                <a href="/cursosApp/App/cursosCategorias"
                    class="nav-btn <?php echo isActivePage('cursosCategorias', $currentPath) ? 'accent' : ''; ?>"
                    id="categoriesBtn">
                    <i class="bi bi-grid-3x3-gap"></i>
                    Categorías
                </a>
                <a href="/cursosApp/App/preinscripciones"
                    class="nav-btn <?php echo isActivePage('preinscripciones', $currentPath) ? 'accent' : ''; ?>"
                    id="preregistrationBtn">
                    <i class="bi bi-cart3"></i>
                    Preinscripciones
                </a>
                <a href="/cursosApp/App/cursosEstudiante"
                    class="nav-btn <?php echo isActivePage('cursosEstudiante', $currentPath) ? 'accent' : ''; ?>"
                    id="myCoursesBtn">
                    <i class="bi bi-journal-bookmark"></i>
                    Mis Cursos
                </a>
            </div>

            <!-- Botón hamburguesa para móviles -->
            <button class="nav-toggle" id="navToggle" style="display: none;">
                <i class="bi bi-list"></i>
            </button>
        </div>

        <!-- Menú móvil -->
        <div class="nav-menu-mobile" id="navMenu" style="display: none;">
            <div class="nav-buttons-mobile">
                <a href="/cursosApp/App/cursosCategorias"
                    class="nav-btn-mobile <?php echo isActivePage('cursosCategorias', $currentPath) ? 'active' : ''; ?>">
                    <i class="bi bi-grid-3x3-gap"></i>
                    Categorías
                </a>
                <a href="/cursosApp/App/preinscripciones"
                    class="nav-btn-mobile <?php echo isActivePage('preinscripciones', $currentPath) ? 'active' : ''; ?>">
                    <i class="bi bi-cart3"></i>
                    Preinscripciones
                </a>
                <a href="/cursosApp/App/cursosEstudiante"
                    class="nav-btn-mobile <?php echo isActivePage('cursosEstudiante', $currentPath) ? 'active' : ''; ?>">
                    <i class="bi bi-journal-bookmark"></i>
                    Mis Cursos
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- JavaScript de navegación de estudiante -->
<script src="/cursosApp/App/vistas/assets/js/pages/estudianteBase.js"></script>

<!-- JavaScript base para funcionalidad de estudiante -->
<script src="/cursosApp/App/vistas/assets/js/pages/estudianteBase.js"></script>

<script>
    // Inicializar funcionalidades del navbar cuando se carga
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar funcionalidades base
        initSearchFunctionality();
        initQuickActions();
        initResponsiveNavbar();

        console.log('Navbar de estudiante inicializado');
    });
</script>

<style>
    /* Estilos específicos para el navbar móvil */
    @media (max-width: 768px) {
        .nav-toggle {
            display: block !important;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--dark);
            cursor: pointer;
            padding: 0.5rem;
        }

        .nav-buttons {
            display: none;
        }

        .search-container {
            flex: 1;
            max-width: 300px;
        }

        .nav-menu-mobile {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            box-shadow: 0 4px 20px rgba(4, 12, 44, 0.1);
            border-radius: 0 0 15px 15px;
            padding: 1rem 0;
            z-index: 1000;
        }

        .nav-menu-mobile.show {
            display: block !important;
        }

        .nav-buttons-mobile {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            padding: 0 1rem;
        }

        .nav-btn-mobile {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: var(--gray);
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .nav-btn-mobile:hover,
        .nav-btn-mobile.active {
            background: var(--primary);
            color: white;
            text-decoration: none;
        }

        .nav-btn-mobile i {
            font-size: 1.1rem;
        }
    }

    @media (min-width: 769px) {
        .nav-menu-mobile {
            display: none !important;
        }
    }
</style>