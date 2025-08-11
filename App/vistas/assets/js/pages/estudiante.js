/**
 * JavaScript para la vista de estudiante
 * Funcionalidades: búsqueda de cursos, filtros, interacciones
 */

document.addEventListener('DOMContentLoaded', function () {
    initEstudianteView();
});

function initEstudianteView() {
    initSearchFunctionality();
    initCourseCards();
    initQuickActions();
    initResponsiveNavbar();
    loadCourses();
}

// ===== FUNCIONALIDAD DE BÚSQUEDA =====
function initSearchFunctionality() {
    const searchInput = document.getElementById('courseSearch');
    const searchButton = document.getElementById('searchButton');

    if (searchInput) {
        let searchTimeout;

        // Búsqueda en tiempo real
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const query = this.value.trim();
                if (query.length >= 2) {
                    searchCourses(query);
                } else if (query.length === 0) {
                    loadCourses(); // Recargar todos los cursos
                }
            }, 300);
        });

        // Búsqueda al presionar Enter
        searchInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const query = this.value.trim();
                if (query.length >= 2) {
                    searchCourses(query);
                }
            }
        });
    }

    if (searchButton) {
        searchButton.addEventListener('click', function () {
            const query = searchInput.value.trim();
            if (query.length >= 2) {
                searchCourses(query);
            }
        });
    }
}

// ===== BÚSQUEDA DE CURSOS =====
function searchCourses(query) {
    showLoadingCards();

    // Simular búsqueda (aquí irá la llamada AJAX real)
    setTimeout(() => {
        fetch('/cursosApp/App/ajax/buscarCursos.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'query=' + encodeURIComponent(query)
        })
            .then(response => response.json())
            .then(data => {
                displayCourses(data.courses || []);
                updateResultsCount(data.total || 0, query);
            })
            .catch(error => {
                console.error('Error en búsqueda:', error);
                showErrorMessage('Error al buscar cursos. Intente nuevamente.');
            });
    }, 500);
}

// ===== CARGAR CURSOS =====
function loadCourses(category = null) {
    showLoadingCards();

    let url = '/cursosApp/App/ajax/obtenerCursos.php';
    let body = '';

    if (category) {
        body = 'category=' + encodeURIComponent(category);
    }

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: body
    })
        .then(response => response.json())
        .then(data => {
            displayCourses(data.courses || []);
            updateResultsCount(data.total || 0);
        })
        .catch(error => {
            console.error('Error al cargar cursos:', error);
            showErrorMessage('Error al cargar cursos. Intente nuevamente.');
        });
}

// ===== MOSTRAR CURSOS =====
function displayCourses(courses) {
    const container = document.getElementById('coursesContainer');
    if (!container) return;

    if (courses.length === 0) {
        container.innerHTML = `
            <div class="no-courses">
                <i class="bi bi-search" style="font-size: 4rem; color: var(--gray); margin-bottom: 1rem;"></i>
                <h3 style="color: var(--gray);">No se encontraron cursos</h3>
                <p style="color: var(--gray);">Intenta con otros términos de búsqueda o explora nuestras categorías.</p>
            </div>
        `;
        return;
    }

    container.innerHTML = courses.map(course => createCourseCard(course)).join('');

    // Reinicializar funcionalidad de las cards
    initCourseCards();
}

// ===== CREAR CARD DE CURSO =====
function createCourseCard(course) {
    const price = course.precio ? `$${parseInt(course.precio).toLocaleString()}` : 'Gratis';
    const image = course.banner || '/cursosApp/App/vistas/assets/img/cursos/default/defaultCurso.png';
    const professor = course.profesor || 'Instructor';

    return `
        <div class="course-card" data-course-id="${course.id}">
            ${course.esNuevo ? '<div class="course-badge badge-new">Nuevo</div>' : ''}
            ${course.esPopular ? '<div class="course-badge badge-popular">Popular</div>' : ''}
            
            <img src="${image}" alt="${course.nombre}" class="course-image" 
                 onerror="this.src='/cursosApp/App/vistas/img/cursos/default/defaultCurso.png'">
            
            <div class="course-content">
                <h3 class="course-title">${course.nombre}</h3>
                
                <div class="course-professor">
                    <i class="bi bi-person-circle"></i>
                    <span>${professor}</span>
                </div>
                
                <div class="course-footer">
                    <span class="course-price">${price}</span>
                    <button class="course-btn" onclick="viewCourse(${course.id})">
                        Ver curso
                    </button>
                </div>
            </div>
        </div>
    `;
}

// ===== FUNCIONALIDAD DE CARDS =====
function initCourseCards() {
    const cards = document.querySelectorAll('.course-card');

    cards.forEach(card => {
        card.addEventListener('click', function (e) {
            // Solo procesar clic si no es en un botón
            if (!e.target.classList.contains('course-btn')) {
                const courseId = this.dataset.courseId;
                if (courseId) {
                    viewCourse(courseId);
                }
            }
        });

        // Animación hover mejorada
        card.addEventListener('mouseenter', function () {
            this.style.transform = 'translateY(-8px)';
        });

        card.addEventListener('mouseleave', function () {
            this.style.transform = 'translateY(0)';
        });
    });
}

// ===== VER CURSO =====
function viewCourse(courseId) {
    window.location.href = `/cursosApp/App/verCurso/${courseId}`;
}

// ===== ACCIONES RÁPIDAS =====
function initQuickActions() {
    const categoryBtn = document.getElementById('categoriesBtn');
    const preregistrationBtn = document.getElementById('preregistrationBtn');
    const myCoursesBtn = document.getElementById('myCoursesBtn');

    if (categoryBtn) {
        categoryBtn.addEventListener('click', function (e) {
            e.preventDefault();
            window.location.href = '/cursosApp/App/cursosCategorias';
        });
    }

    if (preregistrationBtn) {
        preregistrationBtn.addEventListener('click', function (e) {
            e.preventDefault();
            window.location.href = '/cursosApp/App/preinscripciones';
        });
    }

    if (myCoursesBtn) {
        myCoursesBtn.addEventListener('click', function (e) {
            e.preventDefault();
            window.location.href = '/cursosApp/App/cursosEstudiante';
        });
    }
}

// ===== NAVBAR RESPONSIVE =====
function initResponsiveNavbar() {
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.getElementById('navMenu');

    if (navToggle && navMenu) {
        navToggle.addEventListener('click', function () {
            navMenu.classList.toggle('active');
            this.classList.toggle('active');
        });

        // Cerrar menú al hacer clic fuera
        document.addEventListener('click', function (e) {
            if (!navToggle.contains(e.target) && !navMenu.contains(e.target)) {
                navMenu.classList.remove('active');
                navToggle.classList.remove('active');
            }
        });
    }
}

// ===== UTILIDADES =====
function showLoadingCards() {
    const container = document.getElementById('coursesContainer');
    if (!container) return;

    const loadingHTML = Array(6).fill(0).map(() => `
        <div class="course-card">
            <div class="loading-skeleton skeleton-card"></div>
        </div>
    `).join('');

    container.innerHTML = loadingHTML;
}

function showErrorMessage(message) {
    const container = document.getElementById('coursesContainer');
    if (!container) return;

    container.innerHTML = `
        <div class="error-message" style="text-align: center; padding: 3rem; grid-column: 1 / -1;">
            <i class="bi bi-exclamation-triangle" style="font-size: 4rem; color: var(--accent); margin-bottom: 1rem;"></i>
            <h3 style="color: var(--dark); margin-bottom: 1rem;">${message}</h3>
            <button class="course-btn" onclick="loadCourses()" style="margin-top: 1rem;">
                Reintentar
            </button>
        </div>
    `;
}

function updateResultsCount(total, query = null) {
    const countElement = document.getElementById('resultsCount');
    if (!countElement) return;

    let text = `${total} curso${total !== 1 ? 's' : ''}`;
    if (query) {
        text += ` encontrado${total !== 1 ? 's' : ''} para "${query}"`;
    }

    countElement.textContent = text;
}

// ===== FILTROS POR CATEGORÍA =====
function filterByCategory(categoryId, categoryName) {
    // Actualizar título de sección
    const sectionTitle = document.getElementById('sectionTitle');
    if (sectionTitle) {
        sectionTitle.textContent = `Cursos de ${categoryName}`;
    }

    loadCourses(categoryId);
}

// ===== FUNCIONES PÚBLICAS =====
window.viewCourse = viewCourse;
window.filterByCategory = filterByCategory;
window.loadCourses = loadCourses;

// ===== INICIALIZACIÓN =====
console.log('Vista de estudiante cargada correctamente');
