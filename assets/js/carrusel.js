/**
 * Script para controlar el carrusel de la página de inicio
 * Controla las imágenes publicitarias motivacionales
 */
document.addEventListener('DOMContentLoaded', function () {
    // Inicializar el carrusel con opciones personalizadas
    var carouselElement = document.querySelector('#carouselCursos');

    if (carouselElement) {
        // Obtener la instancia del carrusel de Bootstrap
        var carouselInstance = bootstrap.Carousel.getOrCreateInstance(carouselElement, {
            interval: 5000, // Cambiar slide cada 5 segundos
            wrap: true,     // Permitir bucle infinito
            pause: 'hover'  // Pausar al pasar el mouse
        });

        // Pausar el carrusel al pasar el mouse por encima
        carouselElement.addEventListener('mouseenter', function () {
            carouselInstance.pause();
        });

        // Reanudar la reproducción automática al quitar el mouse
        carouselElement.addEventListener('mouseleave', function () {
            carouselInstance.cycle();
        });

        // Añadir efecto de transición suave a las imágenes
        var carouselItems = carouselElement.querySelectorAll('.carousel-item');
        carouselItems.forEach(function (item) {
            var imageContainer = item.querySelector('.carousel-img-container');
            if (imageContainer) {
                imageContainer.style.transition = 'transform 0.6s ease-in-out';
            }
        });

        // Eventos para mejorar la experiencia de usuario
        carouselElement.addEventListener('slide.bs.carousel', function (event) {
            // Evento que se dispara antes de cambiar de slide
            console.log('Cambiando al slide:', event.to);
        });

        carouselElement.addEventListener('slid.bs.carousel', function (event) {
            // Evento que se dispara después de cambiar de slide
            var activeItem = carouselElement.querySelector('.carousel-item.active');
            if (activeItem) {
                // Opcional: añadir animaciones adicionales aquí
            }
        });

        // Función para verificar si las imágenes se cargan correctamente
        function checkImageLoading() {
            var imageContainers = carouselElement.querySelectorAll('.carousel-img-container');
            imageContainers.forEach(function (container, index) {
                var bgImage = container.style.backgroundImage;
                if (bgImage) {
                    var imageUrl = bgImage.slice(4, -1).replace(/"/g, "");
                    var img = new Image();
                    img.onload = function () {
                        console.log('Imagen ' + (index + 1) + ' cargada correctamente');
                        container.classList.add('image-loaded');
                    };
                    img.onerror = function () {
                        console.warn('Error al cargar imagen ' + (index + 1) + ':', imageUrl);
                        // Opcional: mostrar imagen de respaldo
                        container.style.backgroundColor = '#f8f9fa';
                        container.innerHTML = '<div class="d-flex align-items-center justify-content-center h-100"><p class="text-muted">Imagen no disponible</p></div>';
                    };
                    img.src = imageUrl;
                }
            });
        }

        // Verificar carga de imágenes
        checkImageLoading();

        // Añadir smooth scroll para los enlaces del carrusel
        var carouselLinks = carouselElement.querySelectorAll('a[href^="#"]');
        carouselLinks.forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                var targetId = this.getAttribute('href');
                var targetElement = document.querySelector(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }
});
