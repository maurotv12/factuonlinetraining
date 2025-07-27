/**
 * Crear Curso - Funcionalidades JavaScript
 * Gestiona la validación, previsualización y envío del formulario de crear curso
 */

// Variables globales
let currentStep = 1;
const totalSteps = 3;

// Inicializar cuando la página esté lista
document.addEventListener('DOMContentLoaded', function () {
    inicializarFormulario();
    configurarValidaciones();
    configurarPrevistalizaciones();
    configurarContadores();
});

/**
 * Inicializa los componentes del formulario
 */
function inicializarFormulario() {
    // Configurar drag & drop para archivos
    configurarDragAndDrop();

    // Configurar navegación por pasos
    configurarNavegacionPasos();

    // Configurar autocompletado de URL amigable
    configurarUrlAmigable();

    // Configurar validación en tiempo real
    configurarValidacionTiempoReal();
}

/**
 * Configura drag & drop para upload de archivos
 */
function configurarDragAndDrop() {
    const fileAreas = document.querySelectorAll('.file-upload-area');

    fileAreas.forEach(area => {
        const input = area.querySelector('input[type="file"]');

        area.addEventListener('dragover', (e) => {
            e.preventDefault();
            area.classList.add('dragover');
        });

        area.addEventListener('dragleave', () => {
            area.classList.remove('dragover');
        });

        area.addEventListener('drop', (e) => {
            e.preventDefault();
            area.classList.remove('dragover');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                input.files = files;
                previewFile(input);
            }
        });

        area.addEventListener('click', () => {
            input.click();
        });

        input.addEventListener('change', () => {
            previewFile(input);
        });
    });
}

/**
 * Previsualiza archivos seleccionados
 */
function previewFile(input) {
    const file = input.files[0];
    if (!file) return;

    const area = input.closest('.file-upload-area');
    const preview = area.querySelector('.file-preview');

    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.innerHTML = `
                <img src="${e.target.result}" class="preview-image" alt="Vista previa">
                <p class="mt-2 mb-0"><strong>${file.name}</strong></p>
                <small class="text-muted">${(file.size / 1024 / 1024).toFixed(2)} MB</small>
            `;
        };
        reader.readAsDataURL(file);
    } else if (file.type.startsWith('video/')) {
        preview.innerHTML = `
            <i class="bi bi-camera-video-fill" style="font-size: 3rem; color: #667eea;"></i>
            <p class="mt-2 mb-0"><strong>${file.name}</strong></p>
            <small class="text-muted">${(file.size / 1024 / 1024).toFixed(2)} MB</small>
        `;
    }
}

/**
 * Configura la navegación por pasos del formulario
 */
function configurarNavegacionPasos() {
    const nextBtns = document.querySelectorAll('.btn-next-step');
    const prevBtns = document.querySelectorAll('.btn-prev-step');

    nextBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            if (validarPasoActual()) {
                siguientePaso();
            }
        });
    });

    prevBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            pasoAnterior();
        });
    });
}

/**
 * Avanza al siguiente paso
 */
function siguientePaso() {
    if (currentStep < totalSteps) {
        document.querySelector(`#paso-${currentStep}`).style.display = 'none';
        currentStep++;
        document.querySelector(`#paso-${currentStep}`).style.display = 'block';
        actualizarIndicadorPasos();
    }
}

/**
 * Retrocede al paso anterior
 */
function pasoAnterior() {
    if (currentStep > 1) {
        document.querySelector(`#paso-${currentStep}`).style.display = 'none';
        currentStep--;
        document.querySelector(`#paso-${currentStep}`).style.display = 'block';
        actualizarIndicadorPasos();
    }
}

/**
 * Actualiza el indicador visual de pasos
 */
function actualizarIndicadorPasos() {
    const steps = document.querySelectorAll('.step');
    steps.forEach((step, index) => {
        step.classList.remove('active', 'completed');
        if (index + 1 === currentStep) {
            step.classList.add('active');
        } else if (index + 1 < currentStep) {
            step.classList.add('completed');
        }
    });
}

/**
 * Valida el paso actual antes de continuar
 */
function validarPasoActual() {
    const pasoActual = document.querySelector(`#paso-${currentStep}`);
    const campos = pasoActual.querySelectorAll('input[required], select[required], textarea[required]');
    let valido = true;

    campos.forEach(campo => {
        if (!campo.value.trim()) {
            mostrarErrorCampo(campo, 'Este campo es obligatorio');
            valido = false;
        } else {
            limpiarErrorCampo(campo);
        }
    });

    // Validaciones específicas por paso
    if (currentStep === 1) {
        valido = validarPaso1() && valido;
    } else if (currentStep === 2) {
        valido = validarPaso2() && valido;
    }

    return valido;
}

/**
 * Validaciones específicas del paso 1
 */
function validarPaso1() {
    const nombre = document.querySelector('input[name="nombre"]');
    const precio = document.querySelector('input[name="precio"]');

    // Validar longitud del nombre
    if (nombre.value.length < 5) {
        mostrarErrorCampo(nombre, 'El nombre debe tener al menos 5 caracteres');
        return false;
    }

    // Validar precio
    if (precio.value <= 0) {
        mostrarErrorCampo(precio, 'El precio debe ser mayor a 0');
        return false;
    }

    return true;
}

/**
 * Validaciones específicas del paso 2
 */
function validarPaso2() {
    const descripcion = document.querySelector('textarea[name="descripcion"]');

    // Validar longitud de descripción
    if (descripcion.value.length < 20) {
        mostrarErrorCampo(descripcion, 'La descripción debe tener al menos 20 caracteres');
        return false;
    }

    return true;
}

/**
 * Muestra error en un campo específico
 */
function mostrarErrorCampo(campo, mensaje) {
    limpiarErrorCampo(campo);

    campo.classList.add('is-invalid');

    const error = document.createElement('div');
    error.className = 'invalid-feedback';
    error.textContent = mensaje;

    campo.parentNode.appendChild(error);
}

/**
 * Limpia errores de un campo
 */
function limpiarErrorCampo(campo) {
    campo.classList.remove('is-invalid');
    const error = campo.parentNode.querySelector('.invalid-feedback');
    if (error) {
        error.remove();
    }
}

/**
 * Configura la generación automática de URL amigable
 */
function configurarUrlAmigable() {
    const nombreInput = document.querySelector('input[name="nombre"]');
    const urlDisplay = document.querySelector('#url-preview');

    if (nombreInput && urlDisplay) {
        nombreInput.addEventListener('input', function () {
            const urlAmigable = generarUrlAmigable(this.value);
            urlDisplay.textContent = urlAmigable || 'url-del-curso';
        });
    }
}

/**
 * Genera URL amigable a partir del nombre
 */
function generarUrlAmigable(texto) {
    return texto
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .replace(/^-|-$/g, '');
}

/**
 * Configura contadores de caracteres
 */
function configurarContadores() {
    const textareas = document.querySelectorAll('textarea[maxlength]');

    textareas.forEach(textarea => {
        const maxLength = textarea.getAttribute('maxlength');
        const container = textarea.parentNode;

        // Crear contador
        const counter = document.createElement('small');
        counter.className = 'char-counter';
        counter.textContent = `0/${maxLength}`;

        container.style.position = 'relative';
        container.appendChild(counter);

        // Actualizar contador
        textarea.addEventListener('input', function () {
            const currentLength = this.value.length;
            counter.textContent = `${currentLength}/${maxLength}`;

            if (currentLength > maxLength * 0.9) {
                counter.style.color = '#dc3545';
            } else if (currentLength > maxLength * 0.7) {
                counter.style.color = '#ffc107';
            } else {
                counter.style.color = '#6c757d';
            }
        });
    });
}

/**
 * Configura validaciones adicionales
 */
function configurarValidaciones() {
    const form = document.querySelector('#formCrearCurso');

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            if (validarFormularioCompleto()) {
                mostrarModalConfirmacion();
            }
        });
    }
}

/**
 * Valida todo el formulario antes del envío
 */
function validarFormularioCompleto() {
    // Validar todos los pasos
    for (let i = 1; i <= totalSteps; i++) {
        currentStep = i;
        if (!validarPasoActual()) {
            // Ir al paso con errores
            document.querySelector(`#paso-${currentStep}`).style.display = 'block';
            actualizarIndicadorPasos();
            return false;
        }
    }

    return true;
}

/**
 * Muestra modal de confirmación antes del envío
 */
function mostrarModalConfirmacion() {
    const nombre = document.querySelector('input[name="nombre"]').value;
    const precio = document.querySelector('input[name="precio"]').value;

    Swal.fire({
        title: '¿Crear curso?',
        html: `
            <div class="text-start">
                <p><strong>Nombre:</strong> ${nombre}</p>
                <p><strong>Precio:</strong> $${precio}</p>
                <p>¿Estás seguro de que quieres crear este curso?</p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#667eea',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, crear curso',
        cancelButtonText: 'Cancelar',
        customClass: {
            confirmButton: 'btn btn-primary',
            cancelButton: 'btn btn-secondary'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            enviarFormulario();
        }
    });
}

/**
 * Envía el formulario después de la confirmación
 */
function enviarFormulario() {
    const form = document.querySelector('#formCrearCurso');

    // Mostrar loading
    Swal.fire({
        title: 'Creando curso...',
        text: 'Por favor espera mientras se crea el curso',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Enviar formulario
    form.submit();
}

/**
 * Configura validación en tiempo real
 */
function configurarValidacionTiempoReal() {
    const inputs = document.querySelectorAll('input, textarea, select');

    inputs.forEach(input => {
        input.addEventListener('blur', function () {
            if (this.hasAttribute('required') && !this.value.trim()) {
                mostrarErrorCampo(this, 'Este campo es obligatorio');
            } else {
                limpiarErrorCampo(this);
            }
        });

        input.addEventListener('input', function () {
            if (this.classList.contains('is-invalid')) {
                limpiarErrorCampo(this);
            }
        });
    });
}

/**
 * Configura previsualizaciones adicionales
 */
function configurarPrevistalizaciones() {
    // Previsualización del precio formateado
    const precioInput = document.querySelector('input[name="precio"]');
    if (precioInput) {
        precioInput.addEventListener('input', function () {
            const valor = parseFloat(this.value) || 0;
            const preview = document.querySelector('#precio-preview');
            if (preview) {
                preview.textContent = `$${valor.toLocaleString()}`;
            }
        });
    }
}
