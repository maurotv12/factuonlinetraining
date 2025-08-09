document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('#form-crear-curso');
    const inputImagen = document.querySelector('#imagen');

    form.addEventListener('submit', function (e) {
        const file = inputImagen.files[0];

        // Si no hay imagen, deja pasar (ya tienes "required" en HTML)
        if (!file) return;

        const img = new Image();
        const objectUrl = URL.createObjectURL(file);

        img.onload = function () {
            // Validar dimensiones
            if (img.width === 600 && img.height === 400) {
                URL.revokeObjectURL(objectUrl);
                form.submit(); // Tamaño correcto
            } else {
                e.preventDefault(); // Detener el envío
                URL.revokeObjectURL(objectUrl);
                alert('La imagen debe tener exactamente 600x400 píxeles.');
            }
        };

        img.onerror = function () {
            e.preventDefault();
            URL.revokeObjectURL(objectUrl);
            alert('No se pudo cargar la imagen seleccionada.');
        };

        img.src = objectUrl;

        // Detener envío para esperar validación
        e.preventDefault();
    });
});
