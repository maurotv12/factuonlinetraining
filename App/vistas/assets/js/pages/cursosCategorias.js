/**
 * JavaScript específico para cursosCategorias.php
 * Vista de exploración de cursos por categorías
 */

document.addEventListener('DOMContentLoaded', function () {
    initCursosCategorias();
});

function initCursosCategorias() {
    // Inicializar funcionalidades base
    initSearchFunctionality();
    initCourseCards();
    initQuickActions();
    initResponsiveNavbar();

    // Funcionalidades específicas de categorías
    initCategoryFilters();
    initCategoryAnimations();
    updateCategoryInfo();
}

// ===== FUNCIONALIDADES ESPECÍFICAS DE CATEGORÍAS =====

// Inicializar filtros de categorías
function initCategoryFilters() {
    const filterButtons = document.querySelectorAll('.filter-btn');

    filterButtons.forEach(button => {
        if (!button.classList.contains('active')) {
            button.addEventListener('mouseenter', function () {
                this.style.background = 'var(--primary)';
                this.style.color = 'white';
                this.style.transform = 'translateY(-2px)';
            });

            button.addEventListener('mouseleave', function () {
                this.style.background = 'transparent';
                this.style.color = 'var(--primary)';
                this.style.transform = 'translateY(0)';
            });
        }

        // Efecto de click
        button.addEventListener('click', function () {
            // Remover clase activa de todos los botones
            filterButtons.forEach(btn => btn.classList.remove('active'));
            // Agregar clase activa al botón clickeado
            this.classList.add('active');

            // Efecto visual de click
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 150);
        });
    });
}

// Animaciones específicas para categorías
function initCategoryAnimations() {
    const categoryButtons = document.querySelectorAll('.filter-btn');

    // Animar botones de categoría al cargar
    categoryButtons.forEach((button, index) => {
        button.style.opacity = '0';
        button.style.transform = 'translateY(20px)';

        setTimeout(() => {
            button.style.transition = 'all 0.4s ease';
            button.style.opacity = '1';
            button.style.transform = 'translateY(0)';
        }, 100 + (index * 50));
    });
}

// Filtrar por categoría
function filterCategory(categoryId, categoryName) {
    // Actualizar URL
    const url = new URL(window.location);
    if (categoryId) {
        url.searchParams.set('categoria', categoryId);
    } else {
        url.searchParams.delete('categoria');
    }

    // Actualizar título de sección antes de la redirección
    const sectionTitle = document.getElementById('sectionTitle');
    if (sectionTitle) {
        if (categoryId) {
            sectionTitle.textContent = `Cursos de ${categoryName}`;
        } else {
            sectionTitle.textContent = 'Todos los cursos disponibles';
        }
    }

    // Mostrar loading mientras cambia
    showLoadingCards();

    // Redirigir con la nueva URL
    window.location.href = url.toString();
}

// Actualizar información de categoría actual
function updateCategoryInfo() {
    const urlParams = new URLSearchParams(window.location.search);
    const categoriaSeleccionada = urlParams.get('categoria');

    if (categoriaSeleccionada) {
        // Destacar el botón de categoría activa
        const activeButton = document.querySelector(`[onclick*="${categoriaSeleccionada}"]`);
        if (activeButton) {
            activeButton.classList.add('active');
        }
    }
}

// Búsqueda específica para categorías
function searchInCategory(query) {
    const urlParams = new URLSearchParams(window.location.search);
    const categoriaActual = urlParams.get('categoria');

    showLoadingCards();

    fetch('/cursosApp/App/ajax/buscarCursos.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `query=${encodeURIComponent(query)}&categoria=${categoriaActual || ''}`
    })
        .then(response => response.json())
        .then(data => {
            displayCourses(data.courses || []);
            updateResultsCount(data.total || 0, query);
        })
        .catch(error => {
            console.error('Error en búsqueda por categoría:', error);
            showErrorMessage('Error al buscar cursos. Intente nuevamente.');
        });
}

// Mostrar estadísticas por categoría
function showCategoryStats(categoryId) {
    // Aquí se puede implementar mostrar estadísticas específicas de la categoría
    console.log(`Mostrando estadísticas para categoría: ${categoryId}`);
}

// ===== FUNCIONES PÚBLICAS ESPECÍFICAS =====
window.filterCategory = filterCategory;
window.searchInCategory = searchInCategory;
window.showCategoryStats = showCategoryStats;

console.log('Vista de categorías de cursos cargada correctamente');
