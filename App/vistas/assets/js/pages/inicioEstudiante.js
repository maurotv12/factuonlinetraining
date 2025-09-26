/**
 * JavaScript específico para inicioEstudiante.php
 * Vista principal de inicio para estudiantes
 */

document.addEventListener('DOMContentLoaded', function () {
    initInicioEstudiante();
});

function initInicioEstudiante() {
    // Inicializar funcionalidades base
    initSearchFunctionality();
    initCourseCards();
    initQuickActions();
    initResponsiveNavbar();

    // NO cargar cursos automáticamente - ya están cargados desde PHP
    // loadCourses();

    // Funcionalidades específicas de inicio
    initWelcomeAnimations();
    initActionCards();
    updateInitialCount();
}// ===== FUNCIONALIDADES ESPECÍFICAS DE INICIO =====

// Animaciones de bienvenida
function initWelcomeAnimations() {
    const sectionTitle = document.querySelector('.section-title');
    const actionCards = document.querySelectorAll('.action-card');

    if (sectionTitle) {
        sectionTitle.style.opacity = '0';
        sectionTitle.style.transform = 'translateY(20px)';

        setTimeout(() => {
            sectionTitle.style.transition = 'all 0.6s ease';
            sectionTitle.style.opacity = '1';
            sectionTitle.style.transform = 'translateY(0)';
        }, 200);
    }

    // Animar las action cards con retraso escalonado
    actionCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';

        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 400 + (index * 100));
    });
}

// Funcionalidad mejorada para las action cards
function initActionCards() {
    const actionCards = document.querySelectorAll('.action-card');

    actionCards.forEach(card => {
        card.addEventListener('mouseenter', function () {
            this.style.transform = 'translateY(-10px) scale(1.02)';
            this.style.boxShadow = '0 12px 40px rgba(4, 12, 44, 0.15)';
        });

        card.addEventListener('mouseleave', function () {
            this.style.transform = 'translateY(0) scale(1)';
            this.style.boxShadow = '0 4px 20px rgba(4, 12, 44, 0.1)';
        });

        // Efecto de click
        card.addEventListener('mousedown', function () {
            this.style.transform = 'translateY(-5px) scale(0.98)';
        });

        card.addEventListener('mouseup', function () {
            this.style.transform = 'translateY(-10px) scale(1.02)';
        });
    });
}

// Actualizar contador inicial
function updateInitialCount() {
    const coursesContainer = document.getElementById('coursesContainer');
    if (coursesContainer) {
        const existingCourses = coursesContainer.querySelectorAll('.course-card').length;
        if (existingCourses > 0) {
            updateResultsCount(existingCourses);
        }
    }
}

// Funciones específicas para la vista de inicio
function navigateToCategories() {
    window.location.href = '/factuonlinetraining/App/cursosCategorias';
}

function navigateToPreregistrations() {
    window.location.href = '/factuonlinetraining/App/preinscripciones';
}

function navigateToCourses() {
    window.location.href = '/factuonlinetraining/App/cursosEstudiante';
}

// ===== FUNCIONES PÚBLICAS ESPECÍFICAS =====
window.navigateToCategories = navigateToCategories;
window.navigateToPreregistrations = navigateToPreregistrations;
window.navigateToCourses = navigateToCourses;

console.log('Vista de inicio de estudiante cargada correctamente');
