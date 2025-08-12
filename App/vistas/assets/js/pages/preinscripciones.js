/**
 * JavaScript específico para preinscripciones.php
 * Vista de gestión de preinscripciones del estudiante
 */

document.addEventListener('DOMContentLoaded', function () {
    initPreinscripciones();
});

function initPreinscripciones() {
    // Inicializar funcionalidades base
    initSearchFunctionality();
    initCourseCards();
    initQuickActions();
    initResponsiveNavbar();

    // Funcionalidades específicas de preinscripciones
    initPreregistrationActions();
    initPriceCalculator();
    updateTotalCount();
    initEmptyStateActions();
}

// ===== FUNCIONALIDADES ESPECÍFICAS DE PREINSCRIPCIONES =====

// Inicializar acciones de preinscripción
function initPreregistrationActions() {
    // Botones de acción masiva
    const completeAllBtn = document.querySelector('[onclick*="completarTodasInscripciones"]');
    const clearAllBtn = document.querySelector('[onclick*="limpiarPreinscripciones"]');

    if (completeAllBtn) {
        completeAllBtn.addEventListener('mouseenter', function () {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 8px 25px rgba(255, 141, 20, 0.3)';
        });

        completeAllBtn.addEventListener('mouseleave', function () {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
        });
    }

    if (clearAllBtn) {
        clearAllBtn.addEventListener('mouseenter', function () {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 8px 25px rgba(138, 156, 172, 0.3)';
        });

        clearAllBtn.addEventListener('mouseleave', function () {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
        });
    }
}

// Calculadora de precios
function initPriceCalculator() {
    updateTotalPrice();
}

// Completar inscripción individual
function completarInscripcion(courseId) {
    Swal.fire({
        title: '¿Completar inscripción?',
        text: 'Serás redirigido al proceso de pago',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ff8d14',
        cancelButtonColor: '#8a9cac',
        confirmButtonText: 'Sí, inscribirme',
        cancelButtonText: 'Cancelar',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return new Promise((resolve) => {
                setTimeout(() => {
                    resolve();
                }, 1000);
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar éxito y redirigir
            Swal.fire({
                title: '¡Redirigiendo!',
                text: 'Te estamos llevando al proceso de pago...',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location.href = `/cursosApp/App/inscripcion/${courseId}`;
            });
        }
    });
}

// Remover preinscripción
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
            // Animar la eliminación
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

// Completar todas las inscripciones
function completarTodasInscripciones() {
    const preregistrations = document.querySelectorAll('.course-card.preregistered');
    const totalCourses = preregistrations.length;

    if (totalCourses === 0) {
        Swal.fire({
            title: 'No hay preinscripciones',
            text: 'Necesitas tener cursos en tu lista de preinscripciones',
            icon: 'info',
            confirmButtonColor: '#ff8d14'
        });
        return;
    }

    Swal.fire({
        title: `¿Inscribirse a ${totalCourses} curso${totalCourses > 1 ? 's' : ''}?`,
        text: 'Serás redirigido al proceso de pago múltiple',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ff8d14',
        cancelButtonColor: '#8a9cac',
        confirmButtonText: 'Sí, proceder',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '/cursosApp/App/inscripcionMultiple';
        }
    });
}

// Limpiar todas las preinscripciones
function limpiarPreinscripciones() {
    const preregistrations = document.querySelectorAll('.course-card.preregistered');
    const totalCourses = preregistrations.length;

    if (totalCourses === 0) {
        Swal.fire({
            title: 'No hay preinscripciones',
            text: 'Tu lista ya está vacía',
            icon: 'info',
            confirmButtonColor: '#ff8d14'
        });
        return;
    }

    Swal.fire({
        title: `¿Limpiar ${totalCourses} preinscripción${totalCourses > 1 ? 'es' : ''}?`,
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#8a9cac',
        confirmButtonText: 'Sí, limpiar todo',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Animar la eliminación de todas las cards
            preregistrations.forEach((card, index) => {
                setTimeout(() => {
                    card.style.animation = 'fadeOut 0.5s ease forwards';
                    setTimeout(() => card.remove(), 500);
                }, index * 100);
            });

            // Mostrar estado vacío después de las animaciones
            setTimeout(() => {
                showEmptyState();
                updateTotalCount(0);
            }, preregistrations.length * 100 + 500);

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

// Remover card de preinscripción con animación
function removePreregistrationCard(courseId) {
    const card = document.querySelector(`[data-course-id="${courseId}"]`);
    if (card) {
        card.style.animation = 'fadeOut 0.5s ease forwards';
        setTimeout(() => {
            card.remove();
            updateTotalCount();

            // Verificar si queda alguna preinscripción
            const remainingCards = document.querySelectorAll('.course-card.preregistered');
            if (remainingCards.length === 0) {
                showEmptyState();
            }
        }, 500);
    }
}

// Mostrar estado vacío
function showEmptyState() {
    const container = document.getElementById('preregistrationsContainer');
    if (container) {
        container.innerHTML = `
            <div class="no-preregistrations" style="grid-column: 1 / -1; text-align: center; padding: 4rem 2rem;">
                <i class="bi bi-cart-x" style="font-size: 5rem; color: var(--gray); margin-bottom: 2rem;"></i>
                <h3 style="color: var(--dark); margin-bottom: 1rem;">No tienes preinscripciones</h3>
                <p style="color: var(--gray); margin-bottom: 2rem; line-height: 1.6;">
                    ¡Explora nuestros cursos y agrega los que te interesen a tu lista de preinscripciones!
                </p>
                <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                    <a href="/cursosApp/App/inicioEstudiante" class="course-btn" style="text-decoration: none;">
                        <i class="bi bi-house"></i> Ir al inicio
                    </a>
                    <a href="/cursosApp/App/cursosCategorias" class="course-btn" style="text-decoration: none; background: var(--primary);">
                        <i class="bi bi-grid-3x3-gap"></i> Ver categorías
                    </a>
                </div>
            </div>
        `;
    }
}

// Actualizar contador total
function updateTotalCount(count = null) {
    const container = document.getElementById('preregistrationsContainer');
    const cards = container.querySelectorAll('.course-card.preregistered');
    const totalCount = count !== null ? count : cards.length;

    // Actualizar contadores en la UI
    const totalCountElement = document.getElementById('totalCount');
    if (totalCountElement) {
        totalCountElement.textContent = totalCount;
    }

    // Calcular y actualizar precio total
    updateTotalPrice();

    // Actualizar contador de resultados
    updateResultsCount(totalCount);
}

// Actualizar precio total
function updateTotalPrice() {
    const cards = document.querySelectorAll('.course-card.preregistered');
    let totalPrice = 0;

    cards.forEach(card => {
        const priceElement = card.querySelector('.course-price');
        if (priceElement && !priceElement.textContent.includes('Gratis')) {
            const priceText = priceElement.textContent.replace(/[^\d]/g, '');
            if (priceText) {
                totalPrice += parseInt(priceText);
            }
        }
    });

    const totalPriceElement = document.getElementById('totalPrice');
    if (totalPriceElement) {
        totalPriceElement.textContent = totalPrice > 0 ?
            '$' + totalPrice.toLocaleString() : '$0';
    }
}

// Inicializar acciones del estado vacío
function initEmptyStateActions() {
    const container = document.getElementById('preregistrationsContainer');
    const noPreregistrations = container.querySelector('.no-preregistrations');

    if (noPreregistrations) {
        // Las preinscripciones están vacías al cargar
        const actionButtons = noPreregistrations.querySelectorAll('.course-btn');
        actionButtons.forEach(button => {
            button.addEventListener('mouseenter', function () {
                this.style.transform = 'translateY(-2px)';
                this.style.boxShadow = '0 8px 25px rgba(4, 12, 44, 0.15)';
            });

            button.addEventListener('mouseleave', function () {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = 'none';
            });
        });
    }
}

// Búsqueda específica en preinscripciones
function searchInPreregistrations(query) {
    const cards = document.querySelectorAll('.course-card.preregistered');
    let visibleCount = 0;

    cards.forEach(card => {
        const title = card.querySelector('.course-title').textContent.toLowerCase();
        const professor = card.querySelector('.course-professor span')?.textContent.toLowerCase() || '';

        if (title.includes(query.toLowerCase()) || professor.includes(query.toLowerCase())) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    updateResultsCount(visibleCount, query);
}

// ===== FUNCIONES PÚBLICAS ESPECÍFICAS =====
window.completarInscripcion = completarInscripcion;
window.removerPreinscripcion = removerPreinscripcion;
window.completarTodasInscripciones = completarTodasInscripciones;
window.limpiarPreinscripciones = limpiarPreinscripciones;
window.removePreregistrationCard = removePreregistrationCard;
window.updateTotalCount = updateTotalCount;
window.searchInPreregistrations = searchInPreregistrations;

console.log('Vista de preinscripciones cargada correctamente');
