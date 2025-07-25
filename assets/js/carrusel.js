/**
 * Script para controlar el carrusel de la página de inicio
 */
document.addEventListener('DOMContentLoaded', function () {
    // Inicializar el carrusel con opciones personalizadas
    var carouselElement = document.querySelector('#carouselCursos');

    if (carouselElement) {
        // Si se está usando Bootstrap 5+, el carrusel ya estará inicializado
        // pero podemos configurar opciones adicionales si es necesario

        // Pausar el carrusel al pasar el mouse por encima
        carouselElement.addEventListener('mouseenter', function () {
            bootstrap.Carousel.getInstance(carouselElement).pause();
        });

        // Reanudar la reproducción automática al quitar el mouse
        carouselElement.addEventListener('mouseleave', function () {
            bootstrap.Carousel.getInstance(carouselElement).cycle();
        });

        // Actualizar dinámicamente los indicadores si se agregan/eliminan slides
        function updateIndicators() {
            var slides = carouselElement.querySelectorAll('.carousel-item');
            var indicators = carouselElement.querySelector('.carousel-indicators');

            if (indicators) {
                indicators.innerHTML = '';

                slides.forEach(function (slide, index) {
                    var button = document.createElement('button');
                    button.setAttribute('type', 'button');
                    button.setAttribute('data-bs-target', '#carouselCursos');
                    button.setAttribute('data-bs-slide-to', index);

                    if (index === 0) {
                        button.classList.add('active');
                        button.setAttribute('aria-current', 'true');
                    }

                    button.setAttribute('aria-label', 'Slide ' + (index + 1));
                    indicators.appendChild(button);
                });
            }
        }

        // Función para cargar cursos destacados en el carrusel (ejemplo)
        function loadFeaturedCourses() {
            // Esta función podría reemplazar el contenido estático con datos dinámicos
            // Por ejemplo, hacer una solicitud AJAX para obtener los cursos destacados

            /*
            fetch('/api/featured-courses')
                .then(response => response.json())
                .then(data => {
                    // Actualizar el carrusel con los datos recibidos
                    // ...
                    
                    // Actualizar los indicadores después de cambiar el contenido
                    updateIndicators();
                });
            */
        }

        // Para una futura implementación de carga dinámica:
        // loadFeaturedCourses();
    }
});
