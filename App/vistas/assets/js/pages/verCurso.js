/**
 * JavaScript para la vista de curso
 * Maneja la reproducción de videos, navegación por contenido y interacciones
 */

document.addEventListener('DOMContentLoaded', function () {
    console.log('Vista de curso cargada');

    // Inicializar funcionalidades
    inicializarReproductorVideo();
    configurarNavegacionContenido();
    configurarAccordion();
    configurarTabs();
    configurarPreloader();
    configurarScrollSuave();
});

/**
 * Configurar el reproductor de video principal
 */
function inicializarReproductorVideo() {
    const videoPlayer = document.getElementById('videoPlayer');

    if (videoPlayer) {
        // Configurar eventos del video
        videoPlayer.addEventListener('loadstart', function () {
            mostrarLoading(videoPlayer.parentElement);
        });

        videoPlayer.addEventListener('loadeddata', function () {
            ocultarLoading(videoPlayer.parentElement);
        });

        videoPlayer.addEventListener('error', function () {
            ocultarLoading(videoPlayer.parentElement);
            mostrarErrorVideo(videoPlayer.parentElement);
        });

        // Configurar controles personalizados si es necesario
        configurarControlesVideo(videoPlayer);
    }
}

/**
 * Configurar controles adicionales del video
 */
function configurarControlesVideo(video) {
    // Añadir funcionalidades adicionales como:
    // - Control de velocidad
    // - Marcadores de tiempo
    // - Notas del estudiante

    video.addEventListener('timeupdate', function () {
        // Guardar progreso del video si es necesario
        const currentTime = video.currentTime;
        const duration = video.duration;

        if (duration > 0) {
            const progress = (currentTime / duration) * 100;
            // Aquí podrías enviar el progreso al servidor
            guardarProgresoVideo(progress);
        }
    });
}

/**
 * Función para reproducir contenido desde la sidebar
 */
function reproducirContenido(url, tipo, titulo) {
    console.log(`Reproduciendo: ${titulo} (${tipo})`);

    const videoContainer = document.querySelector('.video-container');
    const videoPlayer = document.getElementById('videoPlayer');

    if (!url) {
        mostrarNotificacion('El contenido no está disponible', 'warning');
        return;
    }

    // Mostrar loading
    mostrarLoading(videoContainer);

    if (tipo === 'video' && videoPlayer) {
        // Cambiar el video actual
        const currentSrc = videoPlayer.querySelector('source').src;

        if (currentSrc !== url) {
            videoPlayer.querySelector('source').src = url;
            videoPlayer.load();

            // Actualizar overlay con el nuevo título
            const overlay = videoContainer.querySelector('.video-overlay .video-title');
            if (overlay) {
                overlay.textContent = titulo;
            }

            // Scroll suave al video
            videoContainer.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }

        // Reproducir automáticamente
        videoPlayer.addEventListener('loadeddata', function () {
            videoPlayer.play().catch(e => {
                console.log('Autoplay bloqueado por el navegador');
            });
            ocultarLoading(videoContainer);
        }, { once: true });

    } else if (tipo === 'documento') {
        // Manejar documentos
        abrirDocumento(url, titulo);
        ocultarLoading(videoContainer);
    } else {
        // Otros tipos de contenido
        abrirContenidoGenerico(url, titulo, tipo);
        ocultarLoading(videoContainer);
    }

    // Marcar como contenido activo
    marcarContenidoActivo(titulo);
}

/**
 * Abrir documento en nueva ventana o modal
 */
function abrirDocumento(url, titulo) {
    // Verificar la extensión del archivo
    const extension = url.split('.').pop().toLowerCase();

    if (['pdf', 'doc', 'docx', 'ppt', 'pptx'].includes(extension)) {
        // Abrir en nueva ventana
        window.open(url, '_blank');
        mostrarNotificacion(`Abriendo documento: ${titulo}`, 'info');
    } else {
        // Descargar archivo
        const link = document.createElement('a');
        link.href = url;
        link.download = titulo;
        link.click();
        mostrarNotificacion(`Descargando: ${titulo}`, 'success');
    }
}

/**
 * Abrir contenido genérico
 */
function abrirContenidoGenerico(url, titulo, tipo) {
    window.open(url, '_blank');
    mostrarNotificacion(`Abriendo ${tipo}: ${titulo}`, 'info');
}

/**
 * Marcar contenido como activo visualmente
 */
function marcarContenidoActivo(titulo) {
    // Remover clase activa de todos los elementos
    document.querySelectorAll('.contenido-item').forEach(item => {
        item.classList.remove('activo');
    });

    // Buscar y marcar el elemento activo
    document.querySelectorAll('.contenido-item').forEach(item => {
        const itemTitulo = item.querySelector('.item-titulo').textContent;
        if (itemTitulo === titulo) {
            item.classList.add('activo');

            // Scroll suave al elemento si no está visible
            if (!isElementInViewport(item)) {
                item.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });
            }
        }
    });
}

/**
 * Configurar navegación por contenido con teclado
 */
function configurarNavegacionContenido() {
    document.addEventListener('keydown', function (e) {
        if (e.ctrlKey || e.metaKey) return; // Ignorar si Ctrl/Cmd está presionado

        const contenidoItems = Array.from(document.querySelectorAll('.contenido-item'));
        const activeItem = document.querySelector('.contenido-item.activo');

        if (!activeItem || contenidoItems.length === 0) return;

        const currentIndex = contenidoItems.indexOf(activeItem);

        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                const nextIndex = (currentIndex + 1) % contenidoItems.length;
                simularClick(contenidoItems[nextIndex]);
                break;

            case 'ArrowUp':
                e.preventDefault();
                const prevIndex = currentIndex === 0 ? contenidoItems.length - 1 : currentIndex - 1;
                simularClick(contenidoItems[prevIndex]);
                break;

            case ' ': // Espacebar
                e.preventDefault();
                const video = document.getElementById('videoPlayer');
                if (video) {
                    video.paused ? video.play() : video.pause();
                }
                break;
        }
    });
}

/**
 * Simular click en un elemento de contenido
 */
function simularClick(element) {
    const btnPreview = element.querySelector('.btn-preview');
    if (btnPreview) {
        btnPreview.click();
    }
}

/**
 * Configurar accordion con comportamiento mejorado
 */
function configurarAccordion() {
    const accordionButtons = document.querySelectorAll('.accordion-button');

    accordionButtons.forEach(button => {
        button.addEventListener('click', function () {
            // Añadir pequeña animación
            const icon = this.querySelector('i');
            if (icon) {
                icon.style.transform = 'rotate(180deg)';
                setTimeout(() => {
                    icon.style.transform = '';
                }, 300);
            }

            // Guardar estado del accordion en localStorage
            const targetId = this.getAttribute('data-bs-target');
            const isExpanded = !this.classList.contains('collapsed');
            localStorage.setItem(`accordion-${targetId}`, isExpanded);
        });
    });

    // Restaurar estado del accordion
    restaurarEstadoAccordion();
}

/**
 * Restaurar estado del accordion desde localStorage
 */
function restaurarEstadoAccordion() {
    const accordionButtons = document.querySelectorAll('.accordion-button');

    accordionButtons.forEach(button => {
        const targetId = button.getAttribute('data-bs-target');
        const savedState = localStorage.getItem(`accordion-${targetId}`);

        if (savedState === 'true' && button.classList.contains('collapsed')) {
            // Expandir automáticamente
            setTimeout(() => {
                button.click();
            }, 100);
        }
    });
}

/**
 * Configurar tabs con navegación mejorada
 */
function configurarTabs() {
    const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');

    tabButtons.forEach(button => {
        button.addEventListener('shown.bs.tab', function (e) {
            const targetTab = e.target.getAttribute('data-bs-target');

            // Guardar tab activo en localStorage
            localStorage.setItem('active-tab', targetTab);

            // Scroll suave al contenido de la tab
            const tabContent = document.querySelector(targetTab);
            if (tabContent) {
                setTimeout(() => {
                    tabContent.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest'
                    });
                }, 100);
            }
        });
    });

    // Restaurar tab activo
    const activeTab = localStorage.getItem('active-tab');
    if (activeTab) {
        const tabButton = document.querySelector(`[data-bs-target="${activeTab}"]`);
        if (tabButton) {
            setTimeout(() => {
                tabButton.click();
            }, 100);
        }
    }
}

/**
 * Configurar preloader/loading
 */
function configurarPreloader() {
    // Ocultar preloader inicial si existe
    const preloader = document.querySelector('.preloader');
    if (preloader) {
        setTimeout(() => {
            preloader.style.opacity = '0';
            setTimeout(() => {
                preloader.style.display = 'none';
            }, 300);
        }, 500);
    }
}

/**
 * Mostrar indicador de loading
 */
function mostrarLoading(container) {
    const loadingOverlay = document.createElement('div');
    loadingOverlay.className = 'loading-overlay';
    loadingOverlay.innerHTML = '<div class="loading-spinner"></div>';

    container.style.position = 'relative';
    container.appendChild(loadingOverlay);
}

/**
 * Ocultar indicador de loading
 */
function ocultarLoading(container) {
    const loadingOverlay = container.querySelector('.loading-overlay');
    if (loadingOverlay) {
        loadingOverlay.remove();
    }
}

/**
 * Mostrar error de video
 */
function mostrarErrorVideo(container) {
    const errorOverlay = document.createElement('div');
    errorOverlay.className = 'loading-overlay';
    errorOverlay.innerHTML = `
        <div style="text-align: center; color: #dc3545;">
            <i class="bi bi-exclamation-triangle" style="font-size: 3rem; margin-bottom: 1rem;"></i>
            <p>Error al cargar el video</p>
            <button class="btn btn-sm btn-outline-danger" onclick="location.reload()">
                Reintentar
            </button>
        </div>
    `;

    container.appendChild(errorOverlay);
}

/**
 * Guardar progreso del video (simulado)
 */
function guardarProgresoVideo(progress) {
    // En una implementación real, esto enviaría el progreso al servidor
    const cursoId = window.location.pathname.split('/').pop();
    const progressData = {
        curso_id: cursoId,
        progress: progress,
        timestamp: Date.now()
    };

    // Guardar en localStorage por ahora
    localStorage.setItem(`video-progress-${cursoId}`, JSON.stringify(progressData));
}

/**
 * Configurar scroll suave para navegación
 */
function configurarScrollSuave() {
    // Añadir comportamiento de scroll suave a enlaces internos
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

/**
 * Verificar si un elemento está visible en el viewport
 */
function isElementInViewport(el) {
    const rect = el.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

/**
 * Mostrar notificación al usuario
 */
function mostrarNotificacion(mensaje, tipo = 'info') {
    // Crear elemento de notificación
    const notificacion = document.createElement('div');
    notificacion.className = `alert alert-${tipo} notification-toast`;
    notificacion.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="bi bi-${getIconoTipo(tipo)} me-2"></i>
            <span>${mensaje}</span>
            <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;

    // Estilos para la notificación
    notificacion.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        animation: slideInRight 0.3s ease-out;
    `;

    document.body.appendChild(notificacion);

    // Auto-remover después de 5 segundos
    setTimeout(() => {
        if (notificacion.parentElement) {
            notificacion.style.animation = 'slideOutRight 0.3s ease-out';
            setTimeout(() => {
                notificacion.remove();
            }, 300);
        }
    }, 5000);
}

/**
 * Obtener icono según el tipo de notificación
 */
function getIconoTipo(tipo) {
    const iconos = {
        'success': 'check-circle',
        'info': 'info-circle',
        'warning': 'exclamation-triangle',
        'danger': 'x-circle'
    };
    return iconos[tipo] || 'info-circle';
}

/**
 * Función para compartir curso (funcionalidad adicional)
 */
function compartirCurso() {
    if (navigator.share) {
        navigator.share({
            title: document.querySelector('.curso-titulo').textContent,
            text: 'Mira este curso increíble',
            url: window.location.href
        }).catch(console.error);
    } else {
        // Fallback: copiar URL al portapapeles
        navigator.clipboard.writeText(window.location.href).then(() => {
            mostrarNotificacion('URL copiada al portapapeles', 'success');
        }).catch(() => {
            mostrarNotificacion('No se pudo copiar la URL', 'warning');
        });
    }
}

/**
 * Función para marcar curso como favorito (funcionalidad adicional)
 */
function toggleFavorito() {
    const cursoId = window.location.pathname.split('/').pop();
    const isFavorito = localStorage.getItem(`favorito-${cursoId}`) === 'true';

    localStorage.setItem(`favorito-${cursoId}`, !isFavorito);

    mostrarNotificacion(
        isFavorito ? 'Curso removido de favoritos' : 'Curso añadido a favoritos',
        'success'
    );
}

/**
 * Configurar atajos de teclado
 */
document.addEventListener('keydown', function (e) {
    // Solo si no estamos en un input
    if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
        return;
    }

    switch (e.key) {
        case 'f':
            if (e.ctrlKey) {
                e.preventDefault();
                toggleFavorito();
            }
            break;

        case 's':
            if (e.ctrlKey) {
                e.preventDefault();
                compartirCurso();
            }
            break;

        case 'Escape':
            // Cerrar modales o overlays
            const modal = document.querySelector('.modal.show');
            if (modal) {
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) {
                    modalInstance.hide();
                }
            }
            break;
    }
});

// Añadir estilos para las animaciones de notificación
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .contenido-item.activo {
        background: linear-gradient(135deg, #3682c4, #040c2c) !important;
        color: white !important;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(4, 12, 44, 0.15);
    }
    
    .contenido-item.activo .item-titulo,
    .contenido-item.activo .item-duracion {
        color: white !important;
    }
    
    .contenido-item.activo .item-icon {
        background: #ff8d14 !important;
    }
    
    .contenido-item.activo .btn-preview {
        color: white !important;
    }
    
    .contenido-item.activo .btn-preview:hover {
        background: rgba(255, 255, 255, 0.2) !important;
    }
`;
document.head.appendChild(style);
