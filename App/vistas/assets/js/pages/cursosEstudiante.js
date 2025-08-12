/**
 * JavaScript específico para cursosEstudiante.php
 * Vista de cursos inscritos del estudiante
 */

document.addEventListener('DOMContentLoaded', function () {
    initCursosEstudiante();
});

function initCursosEstudiante() {
    // Inicializar funcionalidades base
    initSearchFunctionality();
    initCourseCards();
    initQuickActions();
    initResponsiveNavbar();

    // Funcionalidades específicas de mis cursos
    initCourseFilters();
    initProgressAnimations();
    initStatsCards();
    updateMyCourses();
}

// ===== FUNCIONALIDADES ESPECÍFICAS DE MIS CURSOS =====

// Inicializar filtros de cursos
function initCourseFilters() {
    const filterButtons = document.querySelectorAll('.filter-btn');

    filterButtons.forEach(button => {
        button.addEventListener('click', function () {
            const filter = this.getAttribute('data-filter');
            filterCourses(filter);
        });
    });

    // Inicializar ordenamiento
    const sortSelect = document.getElementById('sortSelect');
    if (sortSelect) {
        sortSelect.addEventListener('change', function () {
            sortCourses(this.value);
        });
    }
}

// Animaciones de progreso
function initProgressAnimations() {
    const progressBars = document.querySelectorAll('.progress-fill');

    progressBars.forEach(bar => {
        const progress = bar.getAttribute('data-progress') || 0;
        bar.style.width = '0%';

        setTimeout(() => {
            bar.style.transition = 'width 1s ease';
            bar.style.width = progress + '%';
        }, 300);
    });
}

// Animar cards de estadísticas
function initStatsCards() {
    const statCards = document.querySelectorAll('.stat-card');

    statCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';

        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 100 + (index * 100));

        // Hover effect mejorado
        card.addEventListener('mouseenter', function () {
            this.style.transform = 'translateY(-5px) scale(1.05)';
            this.style.boxShadow = '0 12px 40px rgba(4, 12, 44, 0.15)';
        });

        card.addEventListener('mouseleave', function () {
            this.style.transform = 'translateY(0) scale(1)';
            this.style.boxShadow = '0 4px 20px rgba(4, 12, 44, 0.1)';
        });
    });
}

// Filtrar cursos por estado
function filterCourses(filter) {
    // Actualizar botones activos
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    document.querySelector(`[data-filter="${filter}"]`).classList.add('active');

    // Filtrar cursos
    const courses = document.querySelectorAll('.course-card.my-course');
    let visibleCount = 0;

    courses.forEach(course => {
        const status = course.getAttribute('data-status');

        if (filter === 'todos') {
            course.style.display = 'block';
            visibleCount++;
        } else if (filter === 'en_progreso' && status === 'en_progreso') {
            course.style.display = 'block';
            visibleCount++;
        } else if (filter === 'completados' && status === 'completado') {
            course.style.display = 'block';
            visibleCount++;
        } else if (filter === 'no_iniciados' && status === 'no_iniciado') {
            course.style.display = 'block';
            visibleCount++;
        } else {
            course.style.display = 'none';
        }
    });

    // Actualizar contador
    updateResultsCount(visibleCount);
}

// Ordenar cursos
function sortCourses(sortBy) {
    const container = document.getElementById('myCoursesContainer');
    const courses = Array.from(container.querySelectorAll('.course-card.my-course'));

    courses.sort((a, b) => {
        switch (sortBy) {
            case 'nombre':
                const nameA = a.querySelector('.course-title').textContent.toLowerCase();
                const nameB = b.querySelector('.course-title').textContent.toLowerCase();
                return nameA.localeCompare(nameB);

            case 'progreso':
                const progressA = parseInt(a.getAttribute('data-progress') || 0);
                const progressB = parseInt(b.getAttribute('data-progress') || 0);
                return progressB - progressA;

            case 'fecha_inscripcion':
                const dateA = new Date(a.getAttribute('data-inscription-date') || 0);
                const dateB = new Date(b.getAttribute('data-inscription-date') || 0);
                return dateB - dateA;

            case 'recientes':
            default:
                const recentA = new Date(a.getAttribute('data-last-access') || 0);
                const recentB = new Date(b.getAttribute('data-last-access') || 0);
                return recentB - recentA;
        }
    });

    // Reordenar en el DOM
    courses.forEach(course => {
        container.appendChild(course);
    });
}

// Continuar curso
function continuarCurso(courseId) {
    // Mostrar loading en el botón
    const button = event.target;
    const originalText = button.textContent;
    button.textContent = 'Cargando...';
    button.disabled = true;

    // Simular carga y redirigir
    setTimeout(() => {
        window.location.href = `/cursosApp/App/curso/${courseId}/continuar`;
    }, 500);
}

// Revisar curso completado
function revisarCurso(courseId) {
    window.location.href = `/cursosApp/App/curso/${courseId}/revisar`;
}

// Ver certificado
function verCertificado(courseId) {
    // Abrir en nueva ventana
    window.open(`/cursosApp/App/certificado/${courseId}`, '_blank');
}

// Actualizar información de mis cursos
function updateMyCourses() {
    // Actualizar contador inicial
    const courses = document.querySelectorAll('.course-card.my-course');
    updateResultsCount(courses.length);

    // Calcular estadísticas
    let enProgreso = 0;
    let completados = 0;
    let noIniciados = 0;

    courses.forEach(course => {
        const status = course.getAttribute('data-status');
        switch (status) {
            case 'en_progreso':
                enProgreso++;
                break;
            case 'completado':
                completados++;
                break;
            case 'no_iniciado':
                noIniciados++;
                break;
        }
    });

    // Actualizar contadores en las estadísticas
    updateStatCard('en_progreso', enProgreso);
    updateStatCard('completados', completados);
    updateStatCard('no_iniciados', noIniciados);
}

// Actualizar card de estadística
function updateStatCard(type, value) {
    const statCard = document.querySelector(`[data-stat="${type}"] h3`);
    if (statCard) {
        // Animación de contador
        animateCounter(statCard, 0, value, 1000);
    }
}

// Animar contador
function animateCounter(element, start, end, duration) {
    const startTime = performance.now();

    function updateCounter(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);

        const current = Math.floor(start + (end - start) * progress);
        element.textContent = current;

        if (progress < 1) {
            requestAnimationFrame(updateCounter);
        }
    }

    requestAnimationFrame(updateCounter);
}

// Búsqueda específica en mis cursos
function searchInMyCourses(query) {
    const courses = document.querySelectorAll('.course-card.my-course');
    let visibleCount = 0;

    courses.forEach(course => {
        const title = course.querySelector('.course-title').textContent.toLowerCase();
        const professor = course.querySelector('.course-professor span').textContent.toLowerCase();

        if (title.includes(query.toLowerCase()) || professor.includes(query.toLowerCase())) {
            course.style.display = 'block';
            visibleCount++;
        } else {
            course.style.display = 'none';
        }
    });

    updateResultsCount(visibleCount, query);
}

// ===== FUNCIONES PÚBLICAS ESPECÍFICAS =====
window.filterCourses = filterCourses;
window.sortCourses = sortCourses;
window.continuarCurso = continuarCurso;
window.revisarCurso = revisarCurso;
window.verCertificado = verCertificado;
window.searchInMyCourses = searchInMyCourses;

console.log('Vista de mis cursos cargada correctamente');
