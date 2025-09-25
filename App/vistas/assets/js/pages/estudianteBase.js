/**
 * JavaScript base para vistas de estudiante
 * Funcionalidades compartidas entre todas las vistas de estudiante
 */

// ===== FUNCIONALIDADES COMPARTIDAS =====

// Funcionalidad de búsqueda común
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
                    // Recargar la página para mostrar los cursos originales
                    window.location.reload();
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

// Búsqueda de cursos
function searchCourses(query) {
    showLoadingCards();

    fetch('/factuonlinetraining/App/ajax/buscarCursos.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'query=' + encodeURIComponent(query)
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                displayCourses(data.courses || []);
                updateResultsCount(data.total || 0, query);
            } else {
                console.error('Error en búsqueda:', data.error);
                showErrorMessage(data.error || 'Error al buscar cursos');
            }
        })
        .catch(error => {
            console.error('Error en búsqueda:', error);
            showErrorMessage('Error al buscar cursos. Intente nuevamente.');
        });
}

// Cargar cursos
function loadCourses(category = null, forceReload = false) {
    // Si ya hay cursos cargados desde PHP y no es una recarga forzada, no hacer nada
    const container = document.getElementById('coursesContainer');
    if (!forceReload && container && container.children.length > 0) {
        const existingCourses = container.querySelectorAll('.course-card');
        if (existingCourses.length > 0) {
            // Solo reinicializar la funcionalidad de las cards
            initCourseCards();
            return;
        }
    }

    showLoadingCards();

    let url = '/factuonlinetraining/App/ajax/obtenerCursos.php';
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
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                displayCourses(data.courses || []);
                updateResultsCount(data.total || 0);
            } else {
                console.error('Error al cargar cursos:', data.error);
                showErrorMessage(data.error || 'Error al cargar cursos');
            }
        })
        .catch(error => {
            console.error('Error al cargar cursos:', error);
            showErrorMessage('Error al cargar cursos. Intente nuevamente.');
        });
}

// Mostrar cursos
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

// Crear card de curso
function createCourseCard(course) {
    const price = course.valor ? `$${parseInt(course.valor).toLocaleString()}` : 'Gratis';
    const professor = course.profesor || 'Instructor';

    // Manejar la ruta de la imagen
    let image;
    if (course.banner) {
        // Si la ruta comienza con "vistas/", construir ruta completa desde App/
        if (course.banner.startsWith('vistas/')) {
            image = '/factuonlinetraining/App/' + course.banner;
        } else {
            // Si ya tiene ruta completa, usarla tal como está
            image = course.banner;
        }
    } else {
        // Si no hay banner, usar imagen por defecto de storage
        image = '/factuonlinetraining/storage/public/banners/default/defaultCurso.png';
    }

    return `
        <div class="course-card aa" data-course-id="${course.id}">
            ${course.esNuevo ? '<div class="course-badge badge-new">Nuevo</div>' : ''}
            ${course.esPopular ? '<div class="course-badge badge-popular">Popular</div>' : ''}
            
            <img src="${image}" alt="${course.nombre}" class="course-image" 
                 onerror="this.onerror=null; this.src='/factuonlinetraining/storage/public/banners/default/defaultCurso.png'">
            
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

// Funcionalidad de cards
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

// Ver curso
function viewCourse(courseId) {
    window.location.href = `/factuonlinetraining/App/verCurso/${courseId}`;
}

// Acciones rápidas de navegación
function initQuickActions() {
    const categoryBtn = document.getElementById('categoriesBtn');
    const preregistrationBtn = document.getElementById('preregistrationBtn');
    const myCoursesBtn = document.getElementById('myCoursesBtn');

    if (categoryBtn) {
        categoryBtn.addEventListener('click', function (e) {
            e.preventDefault();
            window.location.href = '/factuonlinetraining/App/cursosCategorias';
        });
    }

    if (preregistrationBtn) {
        preregistrationBtn.addEventListener('click', function (e) {
            e.preventDefault();
            window.location.href = '/factuonlinetraining/App/preinscripciones';
        });
    }

    if (myCoursesBtn) {
        myCoursesBtn.addEventListener('click', function (e) {
            e.preventDefault();
            window.location.href = '/factuonlinetraining/App/cursosEstudiante';
        });
    }
}

// Navbar responsive
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

// ===== UTILIDADES COMPARTIDAS =====
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

// ===== FUNCIONES PÚBLICAS =====
window.viewCourse = viewCourse;
window.loadCourses = loadCourses;
window.searchCourses = searchCourses;
window.initSearchFunctionality = initSearchFunctionality;
window.initCourseCards = initCourseCards;
window.initQuickActions = initQuickActions;
window.initResponsiveNavbar = initResponsiveNavbar;
window.showLoadingCards = showLoadingCards;
window.showErrorMessage = showErrorMessage;
window.updateResultsCount = updateResultsCount;
window.displayCourses = displayCourses;
window.createCourseCard = createCourseCard;

console.log('Funcionalidades base de estudiante cargadas');
