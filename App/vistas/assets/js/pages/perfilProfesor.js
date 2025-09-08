/**
 * Perfil Profesor JavaScript
 * Funcionalidades para edición dinámica del perfil
 */

// Variables globales
let currentEditField = null;
let originalValues = {};

// Inicialización cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function () {
    initializeProfile();
    setupImagePreview();
    setupFormValidation();
});

/**
 * Inicializar funcionalidades del perfil
 */
function initializeProfile() {
    // Configurar tooltips si Bootstrap está disponible
    if (typeof bootstrap !== 'undefined') {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Guardar valores originales
    saveOriginalValues();

    // Configurar eventos de teclado
    setupKeyboardEvents();

    // Inicializar preview de contacto con datos reales
    setTimeout(() => {
        initializeContactPreview();
    }, 100);
}

/**
 * Inicializar el preview de contacto con los datos correctos
 */
function initializeContactPreview() {
    // Verificar que tengamos los datos del profesor
    if (typeof window.profesorData === 'undefined') {
        console.warn('Esperando datos del profesor...');
        // Intentar de nuevo en 500ms
        setTimeout(initializeContactPreview, 500);
        return;
    }

    // Sincronizar checkboxes con los datos del servidor
    const emailCheckbox = document.getElementById('showEmail');
    const phoneCheckbox = document.getElementById('showPhone');
    const identificationCheckbox = document.getElementById('showIdentification');

    if (emailCheckbox) {
        emailCheckbox.checked = window.profesorData.mostrar_email;
    }

    if (phoneCheckbox) {
        phoneCheckbox.checked = window.profesorData.mostrar_telefono;
    }

    if (identificationCheckbox) {
        identificationCheckbox.checked = window.profesorData.mostrar_identificacion;
    }

    // Actualizar el preview inicial
    updateContactPreview();
}

/**
 * Guardar valores originales de los campos
 */
function saveOriginalValues() {
    const fields = ['nombre', 'profesion', 'biografia', 'pais', 'ciudad'];
    fields.forEach(field => {
        const element = document.getElementById(`display${capitalizeFirst(field)}`);
        if (element) {
            originalValues[field] = element.textContent.trim();
        }
    });
}

/**
 * Capitalizar primera letra
 */
function capitalizeFirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

/**
 * Configurar eventos de teclado
 */
function setupKeyboardEvents() {
    document.addEventListener('keydown', function (e) {
        // Escape para cancelar edición
        if (e.key === 'Escape' && currentEditField) {
            cancelEdit(currentEditField);
        }

        // Enter para guardar (solo en inputs, no en textarea)
        if (e.key === 'Enter' && currentEditField) {
            const input = document.getElementById(`input${capitalizeFirst(currentEditField)}`);
            if (input && input.tagName !== 'TEXTAREA') {
                e.preventDefault();
                saveField(currentEditField);
            }
        }
    });
}

/**
 * Activar modo de edición para un campo
 */
function editField(fieldName) {
    // Si hay otro campo en edición, cancelarlo
    if (currentEditField && currentEditField !== fieldName) {
        cancelEdit(currentEditField);
    }

    currentEditField = fieldName;

    const displayElement = document.getElementById(`display${capitalizeFirst(fieldName)}`);
    const editElement = document.getElementById(`edit${capitalizeFirst(fieldName)}`);
    const inputElement = document.getElementById(`input${capitalizeFirst(fieldName)}`);

    if (displayElement && editElement && inputElement) {
        // Ocultar display y mostrar edit
        displayElement.style.display = 'none';
        editElement.style.display = 'block';
        editElement.classList.add('fade-in');

        // Enfocar el input
        setTimeout(() => {
            inputElement.focus();
            if (inputElement.tagName === 'TEXTAREA') {
                inputElement.setSelectionRange(inputElement.value.length, inputElement.value.length);
            } else {
                inputElement.select();
            }
        }, 100);

        // Agregar clase de loading visual
        editElement.classList.add('editing');
    }
}

/**
 * Cancelar edición de un campo
 */
function cancelEdit(fieldName) {
    const displayElement = document.getElementById(`display${capitalizeFirst(fieldName)}`);
    const editElement = document.getElementById(`edit${capitalizeFirst(fieldName)}`);
    const inputElement = document.getElementById(`input${capitalizeFirst(fieldName)}`);

    if (displayElement && editElement && inputElement) {
        // Restaurar valor original
        const originalValue = originalValues[fieldName] || '';
        inputElement.value = originalValue;

        // Mostrar display y ocultar edit
        displayElement.style.display = 'block';
        editElement.style.display = 'none';
        editElement.classList.remove('fade-in', 'editing');

        currentEditField = null;
    }
}

/**
 * Guardar campo editado
 */
async function saveField(fieldName) {
    const inputElement = document.getElementById(`input${capitalizeFirst(fieldName)}`);
    const editElement = document.getElementById(`edit${capitalizeFirst(fieldName)}`);

    if (!inputElement || !editElement) {
        return;
    }

    const newValue = inputElement.value.trim();

    // Validar que el valor no esté vacío para campos requeridos
    const requiredFields = ['nombre', 'profesion'];
    if (requiredFields.includes(fieldName) && !newValue) {
        showAlert('Este campo es requerido', 'danger');
        inputElement.focus();
        return;
    }

    // Mostrar indicador de carga
    editElement.classList.add('loading');

    try {
        // Enviar datos al servidor
        const formData = new FormData();
        formData.append('campo', fieldName);
        formData.append('valor', newValue);
        formData.append('accion', 'actualizar_campo');

        const response = await fetch('ajax/usuarios.ajax.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            // Actualizar display
            updateDisplayField(fieldName, newValue);

            // Salir del modo edición
            exitEditMode(fieldName);

            // Actualizar valor original
            originalValues[fieldName] = newValue;

            showAlert('Campo actualizado correctamente', 'success');
        } else {
            throw new Error(result.message || 'Error al actualizar el campo');
        }

    } catch (error) {
        console.error('Error:', error);
        showAlert('Error al actualizar el campo: ' + error.message, 'danger');
    } finally {
        editElement.classList.remove('loading');
    }
}

/**
 * Actualizar el display de un campo
 */
function updateDisplayField(fieldName, newValue) {
    const displayElement = document.getElementById(`display${capitalizeFirst(fieldName)}`);

    if (displayElement) {
        if (fieldName === 'biografia') {
            // Para biografía, manejar HTML y "Ver más"
            const bioData = processBiografia(newValue);
            displayElement.innerHTML = bioData.html;
        } else if (fieldName === 'nombre') {
            displayElement.textContent = newValue || 'Nombre no disponible';
        } else if (fieldName === 'profesion') {
            displayElement.textContent = newValue || 'Profesión no especificada';
        } else {
            displayElement.textContent = newValue || 'No especificado';
        }

        // Actualizar ubicación si es país o ciudad
        if (fieldName === 'pais' || fieldName === 'ciudad') {
            updateUbicacionDisplay();
        }
    }
}

/**
 * Procesar biografía para mostrar "Ver más/Ver menos"
 */
function processBiografia(biografia) {
    if (!biografia) {
        return {
            html: '<p class="text-muted">No hay biografía disponible.</p>'
        };
    }

    const maxChars = 500;
    const maxWords = 100;

    const words = biografia.split(' ');
    const shouldTruncate = biografia.length > maxChars || words.length > maxWords;

    if (shouldTruncate) {
        let bioShort = biografia.substring(0, maxChars);
        const shortWords = bioShort.split(' ');

        if (shortWords.length > maxWords) {
            bioShort = shortWords.slice(0, maxWords).join(' ');
        }

        return {
            html: `
                <p class="biografia-text">
                    <span id="bioShort">${escapeHtml(bioShort).replace(/\n/g, '<br>')}</span>
                    <span id="bioFull" style="display: none;">${escapeHtml(biografia).replace(/\n/g, '<br>')}</span>
                    <a href="#" class="ver-mas" onclick="toggleBiografia(event)">Ver más</a>
                </p>
            `
        };
    } else {
        return {
            html: `<p class="biografia-text">${escapeHtml(biografia).replace(/\n/g, '<br>')}</p>`
        };
    }
}

/**
 * Escape HTML para prevenir XSS
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Actualizar display de ubicación
 */
function updateUbicacionDisplay() {
    const ciudadElement = document.getElementById('displayCiudad');
    const paisElement = document.getElementById('displayPais');
    const ubicacionElement = document.getElementById('displayUbicacion');

    if (ciudadElement && paisElement && ubicacionElement) {
        const ciudad = ciudadElement.textContent.trim();
        const pais = paisElement.textContent.trim();

        let ubicacion = [];
        if (ciudad && ciudad !== 'No especificada') ubicacion.push(ciudad);
        if (pais && pais !== 'No especificado') ubicacion.push(pais);

        ubicacionElement.textContent = ubicacion.length > 0 ? ubicacion.join(', ') : 'Ubicación no especificada';
    }
}

/**
 * Salir del modo edición
 */
function exitEditMode(fieldName) {
    const displayElement = document.getElementById(`display${capitalizeFirst(fieldName)}`);
    const editElement = document.getElementById(`edit${capitalizeFirst(fieldName)}`);

    if (displayElement && editElement) {
        displayElement.style.display = 'block';
        editElement.style.display = 'none';
        editElement.classList.remove('fade-in', 'editing');

        currentEditField = null;
    }
}

/**
 * Mostrar alerta
 */
function showAlert(message, type = 'info') {
    // Remover alertas existentes
    const existingAlerts = document.querySelectorAll('.alert-dynamic');
    existingAlerts.forEach(alert => alert.remove());

    // Crear nueva alerta
    const alertElement = document.createElement('div');
    alertElement.className = `alert alert-${type} alert-dynamic fade-in`;
    alertElement.innerHTML = `
        <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-triangle' : 'info-circle'}"></i>
        ${message}
        <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
    `;

    // Insertar al principio del contenido
    const container = document.querySelector('.container-fluid');
    if (container) {
        container.insertBefore(alertElement, container.firstChild);

        // Auto-remover después de 5 segundos
        setTimeout(() => {
            if (alertElement.parentNode) {
                alertElement.remove();
            }
        }, 5000);
    }
}

/**
 * Toggle biografía completa/resumida
 */
function toggleBiografia(event) {
    event.preventDefault();

    const bioShort = document.getElementById('bioShort');
    const bioFull = document.getElementById('bioFull');
    const link = event.target;

    if (bioShort && bioFull && link) {
        if (bioShort.style.display !== 'none') {
            // Mostrar biografía completa
            bioShort.style.display = 'none';
            bioFull.style.display = 'inline';
            link.textContent = 'Ver menos';
        } else {
            // Mostrar biografía resumida
            bioShort.style.display = 'inline';
            bioFull.style.display = 'none';
            link.textContent = 'Ver más';
        }
    }
}

/**
 * Actualizar configuración de privacidad
 */
async function updatePrivacySetting(setting, value) {
    try {
        const formData = new FormData();
        formData.append('configuracion', setting);
        formData.append('valor', value ? 1 : 0);
        formData.append('accion', 'actualizar_privacidad');

        const response = await fetch('ajax/usuarios.ajax.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            showAlert('Configuración de privacidad actualizada', 'success');

            // Actualizar preview de contacto público
            updateContactPreview();
        } else {
            throw new Error(result.message || 'Error al actualizar la configuración');
        }

    } catch (error) {
        console.error('Error:', error);
        showAlert('Error al actualizar la configuración: ' + error.message, 'danger');

        // Revertir el checkbox
        const checkbox = document.getElementById(setting.replace('mostrar_', 'show').replace('_', ''));
        if (checkbox) {
            checkbox.checked = !value;
        }
    }
}

/**
 * Actualizar preview de contacto público
 */
function updateContactPreview() {
    const preview = document.querySelector('.contact-preview');
    if (!preview) return;

    // Verificar que tengamos los datos del profesor
    if (typeof window.profesorData === 'undefined') {
        console.warn('Datos del profesor no disponibles para el preview');
        return;
    }

    // Obtener estados actuales de los checkboxes
    const showEmail = document.getElementById('showEmail')?.checked || false;
    const showPhone = document.getElementById('showPhone')?.checked || false;
    const showIdentification = document.getElementById('showIdentification')?.checked || false;

    let html = '';

    // Email
    if (showEmail) {
        const email = window.profesorData.email;
        if (email && email.trim() !== '') {
            html += `
                <div class="contact-item">
                    <i class="bi bi-envelope"></i>
                    <span>${escapeHtml(email)}</span>
                </div>
            `;
        }
    }

    // Teléfono
    if (showPhone) {
        const telefono = window.profesorData.telefono;
        if (telefono && telefono.trim() !== '') {
            html += `
                <div class="contact-item">
                    <i class="bi bi-telephone"></i>
                    <span>${escapeHtml(telefono)}</span>
                </div>
            `;
        }
    }

    // Identificación
    if (showIdentification) {
        const identificacion = window.profesorData.numero_identificacion;
        if (identificacion && identificacion.trim() !== '') {
            html += `
                <div class="contact-item">
                    <i class="bi bi-card-text"></i>
                    <span>${escapeHtml(identificacion)}</span>
                </div>
            `;
        }
    }

    // Si no hay información de contacto habilitada
    if (!html) {
        html = '<p class="text-muted">No has habilitado ninguna información de contacto para mostrar públicamente.</p>';
    }

    // Actualizar el contenido con animación suave
    preview.style.opacity = '0.5';
    setTimeout(() => {
        preview.innerHTML = html;
        preview.style.opacity = '1';
    }, 150);
}

/**
 * Obtener valor de un campo desde los datos del profesor
 */
function getFieldValue(fieldName) {
    // Verificar si tenemos los datos del profesor disponibles
    if (typeof window.profesorData === 'undefined') {
        console.warn('Datos del profesor no disponibles');
        return '';
    }

    // Mapear nombres de campos a las propiedades correctas
    const fieldMapping = {
        'email': 'email',
        'telefono': 'telefono',
        'numero_identificacion': 'numero_identificacion' // Por si acaso usa este nombre
    };

    const actualFieldName = fieldMapping[fieldName] || fieldName;
    return window.profesorData[actualFieldName] || '';
}

/**
 * Configurar modal de foto
 */
function openPhotoModal() {
    const modal = new bootstrap.Modal(document.getElementById('photoModal'));
    modal.show();
}

/**
 * Configurar preview de imagen
 */
function setupImagePreview() {
    const imageInput = document.getElementById('nuevaImagen');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');

    if (imageInput && imagePreview && previewImg) {
        imageInput.addEventListener('change', function (e) {
            const file = e.target.files[0];

            if (file) {
                // Validar tipo de archivo
                if (!file.type.match(/^image\/(jpeg|png)$/)) {
                    showAlert('Solo se permiten archivos JPG y PNG', 'danger');
                    imageInput.value = '';
                    return;
                }

                // Validar tamaño (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    showAlert('El archivo es muy grande. Máximo 2MB', 'danger');
                    imageInput.value = '';
                    return;
                }

                // Mostrar preview
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImg.src = e.target.result;
                    imagePreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                imagePreview.style.display = 'none';
            }
        });
    }
}

/**
 * Configurar validación de formularios
 */
function setupFormValidation() {
    // Validación en tiempo real para campos de texto
    const textInputs = document.querySelectorAll('input[type="text"], textarea');

    textInputs.forEach(input => {
        input.addEventListener('input', function () {
            validateField(this);
        });

        input.addEventListener('blur', function () {
            validateField(this);
        });
    });
}

/**
 * Validar campo individual
 */
function validateField(field) {
    const value = field.value.trim();
    const fieldName = field.id.replace('input', '').toLowerCase();

    // Remover clases de validación anteriores
    field.classList.remove('is-valid', 'is-invalid');

    // Validaciones específicas
    switch (fieldName) {
        case 'nombre':
            if (value.length < 2) {
                field.classList.add('is-invalid');
                return false;
            }
            break;

        case 'profesion':
            if (value.length < 2) {
                field.classList.add('is-invalid');
                return false;
            }
            break;

        case 'biografia':
            if (value.length > 10000) {
                field.classList.add('is-invalid');
                return false;
            }
            break;

        case 'pais':
        case 'ciudad':
            if (value && value.length < 2) {
                field.classList.add('is-invalid');
                return false;
            }
            break;
    }

    // Si llegamos aquí, el campo es válido
    if (value) {
        field.classList.add('is-valid');
    }
    return true;
}

/**
 * Manejar errores globales
 */
window.addEventListener('error', function (e) {
    console.error('Error global:', e.error);
    showAlert('Ha ocurrido un error inesperado. Por favor, recarga la página.', 'danger');
});

/**
 * Funciones de utilidad para debug (solo en desarrollo)
 */
if (window.location.hostname === 'localhost') {
    window.profileDebug = {
        getCurrentField: () => currentEditField,
        getOriginalValues: () => originalValues,
        getProfessorData: () => window.profesorData,
        testAlert: (message, type) => showAlert(message, type),
        resetField: (fieldName) => cancelEdit(fieldName),
        updatePreview: () => updateContactPreview(),
        checkData: () => {
            console.log('Datos del profesor:', window.profesorData);
            console.log('Checkboxes estado:');
            console.log('- Email:', document.getElementById('showEmail')?.checked);
            console.log('- Teléfono:', document.getElementById('showPhone')?.checked);
            console.log('- Identificación:', document.getElementById('showIdentification')?.checked);
        }
    };
}
