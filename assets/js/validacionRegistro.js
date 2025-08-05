// Validación en tiempo real para el formulario de registro
document.addEventListener('DOMContentLoaded', function () {
    // Verificar si estamos en una página de registro antes de inicializar
    const isRegistroPage = document.querySelector('input[name="emailRegistro"]') ||
        document.querySelector('input[name="passRegistro"]') ||
        document.querySelector('input[name="nombreRegistro"]');

    if (!isRegistroPage) {
        // No estamos en la página de registro, no ejecutar la validación
        return;
    }

    // Esperar un poco más para asegurar que el contenido dinámico se cargue
    setTimeout(function () {
        inicializarValidacion();
    }, 500);
});

function inicializarValidacion() {
    const emailInput = document.querySelector('input[name="emailRegistro"]');
    const passwordInput = document.querySelector('input[name="passRegistro"]');
    const submitButton = document.querySelector('#submit');
    const nombreInput = document.querySelector('input[name="nombreRegistro"]');

    // Salir si no se encuentran los elementos necesarios
    if (!emailInput || !passwordInput || !submitButton || !nombreInput) {
        // Solo hacer un intento adicional, después salir definitivamente
        let intentos = parseInt(sessionStorage.getItem('validacionIntentos') || '0');
        if (intentos < 3) {
            sessionStorage.setItem('validacionIntentos', (intentos + 1).toString());
            setTimeout(inicializarValidacion, 2000);
        } else {
            // Limpiar contador después de 3 intentos fallidos
            sessionStorage.removeItem('validacionIntentos');
        }
        return;
    }

    // Si llegamos aquí, los elementos existen, limpiar contador
    sessionStorage.removeItem('validacionIntentos');

    // Estados de validación
    let emailValido = false;
    let passwordValido = false;
    let nombreValido = false;
    let emailDisponible = false;

    // Función para actualizar el estado del botón
    function actualizarBoton() {
        if (emailValido && passwordValido && nombreValido && emailDisponible) {
            submitButton.disabled = false;
            submitButton.classList.remove('btn-secondary');
            submitButton.classList.add('btn-primary');
        } else {
            submitButton.disabled = true;
            submitButton.classList.remove('btn-primary');
            submitButton.classList.add('btn-secondary');
        }
    }

    // Función para mostrar mensaje de error
    function mostrarError(input, mensaje) {
        // Remover mensaje anterior si existe
        const errorAnterior = input.parentNode.querySelector('.error-message');
        if (errorAnterior) {
            errorAnterior.remove();
        }

        // Crear nuevo mensaje de error
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message text-danger small mt-1';
        errorDiv.textContent = mensaje;
        input.parentNode.appendChild(errorDiv);

        // Cambiar el estilo del input
        input.classList.add('is-invalid');
        input.classList.remove('is-valid');
    }

    // Función para mostrar mensaje de éxito
    function mostrarExito(input, mensaje = '') {
        // Remover mensaje anterior si existe
        const errorAnterior = input.parentNode.querySelector('.error-message');
        if (errorAnterior) {
            errorAnterior.remove();
        }

        if (mensaje) {
            const exitoDiv = document.createElement('div');
            exitoDiv.className = 'error-message text-success small mt-1';
            exitoDiv.textContent = mensaje;
            input.parentNode.appendChild(exitoDiv);
        }

        // Cambiar el estilo del input
        input.classList.add('is-valid');
        input.classList.remove('is-invalid');
    }

    // Validación del nombre
    nombreInput.addEventListener('input', function () {
        const nombre = this.value.trim();

        if (nombre.length < 2) {
            mostrarError(this, 'El nombre debe tener al menos 2 caracteres');
            nombreValido = false;
        } else if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(nombre)) {
            mostrarError(this, 'El nombre solo puede contener letras y espacios');
            nombreValido = false;
        } else {
            mostrarExito(this);
            nombreValido = true;
        }

        actualizarBoton();
    });

    // Variables para debounce
    let emailTimeout;

    // Validación del email
    emailInput.addEventListener('input', function () {
        const email = this.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        // Limpiar timeout anterior
        clearTimeout(emailTimeout);

        if (!emailRegex.test(email)) {
            mostrarError(this, 'Ingrese un email válido');
            emailValido = false;
            emailDisponible = false;
        } else {
            emailValido = true;
            // Usar debounce para verificar disponibilidad
            emailTimeout = setTimeout(() => {
                verificarEmailDisponible(email);
            }, 800); // Esperar 800ms después de que el usuario deje de escribir
        }

        actualizarBoton();
    });

    // Función para verificar si el email está disponible
    function verificarEmailDisponible(email) {
        // Mostrar indicador de carga
        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'error-message text-info small mt-1';
        loadingDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verificando disponibilidad...';

        // Remover mensaje anterior
        const errorAnterior = emailInput.parentNode.querySelector('.error-message');
        if (errorAnterior) {
            errorAnterior.remove();
        }

        emailInput.parentNode.appendChild(loadingDiv);

        // Realizar petición AJAX
        fetch('/cursosApp/App/ajax/verificarEmail.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'email=' + encodeURIComponent(email)
        })
            .then(response => response.json())
            .then(data => {
                loadingDiv.remove();

                if (data.disponible) {
                    mostrarExito(emailInput, 'Email disponible');
                    emailDisponible = true;
                } else {
                    mostrarError(emailInput, 'Este email ya está registrado');
                    emailDisponible = false;
                }

                actualizarBoton();
            })
            .catch(error => {
                loadingDiv.remove();
                console.error('Error al verificar email:', error);
                mostrarError(emailInput, 'Error al verificar el email');
                emailDisponible = false;
                actualizarBoton();
            });
    }

    // Validación de contraseña
    passwordInput.addEventListener('input', function () {
        const password = this.value;

        // Crear o actualizar indicador de requisitos
        let requirementsDiv = this.parentNode.querySelector('.password-requirements');
        if (!requirementsDiv) {
            requirementsDiv = document.createElement('div');
            requirementsDiv.className = 'password-requirements';

            // Crear elementos individuales para mejor control
            const titulo = document.createElement('h6');
            titulo.textContent = 'Requisitos de la contraseña:';
            requirementsDiv.appendChild(titulo);

            const requisitos = [
                { key: 'length', text: 'Mínimo 8 caracteres' },
                { key: 'uppercase', text: 'Una letra mayúscula' },
                { key: 'lowercase', text: 'Una letra minúscula' },
                { key: 'number', text: 'Un número' }
            ];

            requisitos.forEach(req => {
                const reqDiv = document.createElement('div');
                reqDiv.className = 'requirement invalid';
                reqDiv.setAttribute('data-requirement', req.key);

                const icon = document.createElement('i');
                icon.className = 'fas fa-times text-danger';

                const span = document.createElement('span');
                span.textContent = req.text;

                reqDiv.appendChild(icon);
                reqDiv.appendChild(span);
                requirementsDiv.appendChild(reqDiv);
            });

            this.parentNode.appendChild(requirementsDiv);
        }

        // Validar cada requisito
        const requirements = {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /[0-9]/.test(password)
        };

        let allValid = true;

        // Actualizar indicadores visuales
        Object.keys(requirements).forEach(req => {
            const reqElement = requirementsDiv.querySelector(`[data-requirement="${req}"]`);

            if (!reqElement) {
                console.error('No se encontró elemento para:', req);
                return;
            }

            // Buscar y actualizar el icono
            let iconElement = reqElement.querySelector('i');

            if (!iconElement) {
                // Si no hay icono <i>, crear uno nuevo
                iconElement = document.createElement('i');
                reqElement.insertBefore(iconElement, reqElement.firstChild);
            }

            if (requirements[req]) {
                reqElement.className = 'requirement valid';
                iconElement.className = 'fas fa-check text-success';
            } else {
                reqElement.className = 'requirement invalid';
                iconElement.className = 'fas fa-times text-danger';
                allValid = false;
            }
        });

        // Actualizar estado de validación
        if (allValid && password.length > 0) {
            mostrarExito(this);
            passwordValido = true;
        } else if (password.length > 0) {
            this.classList.add('is-invalid');
            this.classList.remove('is-valid');
            passwordValido = false;
        } else {
            this.classList.remove('is-invalid', 'is-valid');
            passwordValido = false;
        }

        actualizarBoton();
    });

    // Inicializar estado del botón
    actualizarBoton();
} // Cerrar la función inicializarValidacion

// Función para validar políticas (mantener la existente)
function validarPoliticas() {
    const politicas = document.getElementById('politicas');
    if (!politicas.checked) {
        // Verificar si SweetAlert está disponible
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: '¡ATENCIÓN!',
                text: 'Debe aceptar las políticas de privacidad para continuar',
                confirmButtonText: 'Cerrar'
            });
        } else {
            // Fallback si SweetAlert no está disponible
            alert("Debe aceptar las políticas de privacidad para continuar");
        }
        return false;
    }
    return true;
}
