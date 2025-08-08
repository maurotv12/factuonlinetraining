/**
 * Perfil General JavaScript
 * Funcionalidades para edición dinámica del perfil de usuario
 */

// Variables globales
let currentEditField = null;
let originalValues = {};

// Inicialización cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function () {
    initializePerfil();
    setupImageUpload();
    setupFormValidation();
});

/**
 * Inicializar funcionalidades del perfil
 */
function initializePerfil() {
    // Guardar valores originales
    saveOriginalValues();

    // Configurar eventos de los campos editables
    setupEditableFields();

    // Configurar eventos de teclado
    setupKeyboardEvents();

    // Configurar tooltips si Bootstrap está disponible
    if (typeof bootstrap !== 'undefined') {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
}

/**
 * Guardar valores originales de los campos
 */
function saveOriginalValues() {
    const fields = ['nombre', 'email', 'pais', 'ciudad', 'contenido', 'profesion', 'telefono', 'direccion', 'numero_identificacion', 'biografia'];
    fields.forEach(field => {
        const element = document.getElementById(`display-${field}`);
        if (element) {
            originalValues[field] = element.textContent.trim();
        }
    });
}

/**
 * Configurar campos editables
 */
function setupEditableFields() {
    const editableFields = document.querySelectorAll('.editable-field');

    editableFields.forEach(field => {
        const displayElement = field.querySelector('.display-value');
        const fieldName = field.dataset.field;

        if (displayElement) {
            displayElement.addEventListener('click', () => editField(fieldName));

            // Hacer el campo accesible por teclado
            displayElement.setAttribute('tabindex', '0');
            displayElement.addEventListener('keypress', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    editField(fieldName);
                }
            });
        }
    });
}

/**
 * Configurar eventos de teclado globales
 */
function setupKeyboardEvents() {
    document.addEventListener('keydown', function (e) {
        // Escape para cancelar edición
        if (e.key === 'Escape' && currentEditField) {
            cancelEdit(currentEditField);
        }

        // Ctrl+S para guardar (solo si hay campo en edición)
        if (e.ctrlKey && e.key === 's' && currentEditField) {
            e.preventDefault();
            saveField(currentEditField);
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

    const fieldContainer = document.querySelector(`[data-field="${fieldName}"]`);
    if (!fieldContainer) return;

    const displayElement = fieldContainer.querySelector('.display-value');
    const editElement = fieldContainer.querySelector('.edit-mode');
    const inputElement = fieldContainer.querySelector('.form-control');

    if (displayElement && editElement && inputElement) {
        // Ocultar display y mostrar edit
        displayElement.style.display = 'none';
        editElement.classList.add('active');

        // Enfocar el input
        inputElement.focus();
        inputElement.select();

        // Configurar eventos del input
        inputElement.addEventListener('blur', handleInputBlur);
        inputElement.addEventListener('keypress', handleInputKeypress);
    }
}

/**
 * Manejar pérdida de foco del input
 */
function handleInputBlur(e) {
    // No cancelar automáticamente al perder foco
    // El usuario debe hacer clic en guardar o cancelar
}

/**
 * Manejar teclas en el input
 */
function handleInputKeypress(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        saveField(currentEditField);
    }
}

/**
 * Cancelar edición de un campo
 */
function cancelEdit(fieldName) {
    const fieldContainer = document.querySelector(`[data-field="${fieldName}"]`);
    if (!fieldContainer) return;

    const displayElement = fieldContainer.querySelector('.display-value');
    const editElement = fieldContainer.querySelector('.edit-mode');
    const inputElement = fieldContainer.querySelector('.form-control');

    if (displayElement && editElement && inputElement) {
        // Restaurar valor original
        inputElement.value = originalValues[fieldName] || '';

        // Mostrar display y ocultar edit
        displayElement.style.display = 'flex';
        editElement.classList.remove('active');

        // Limpiar eventos
        inputElement.removeEventListener('blur', handleInputBlur);
        inputElement.removeEventListener('keypress', handleInputKeypress);
    }

    currentEditField = null;
}

/**
 * Guardar campo editado
 */
async function saveField(fieldName) {
    const fieldContainer = document.querySelector(`[data-field="${fieldName}"]`);
    if (!fieldContainer) return;

    const inputElement = fieldContainer.querySelector('.form-control');
    const editElement = fieldContainer.querySelector('.edit-mode');

    if (!inputElement || !editElement) return;

    const newValue = inputElement.value.trim();

    // Validar que el valor no esté vacío para campos requeridos
    const requiredFields = ['nombre', 'email'];
    if (requiredFields.includes(fieldName) && !newValue) {
        showAlert('Este campo es obligatorio', 'danger');
        inputElement.focus();
        return;
    }

    // Validar email si es el campo de email
    if (fieldName === 'email' && !isValidEmail(newValue)) {
        showAlert('Por favor ingresa un email válido', 'danger');
        inputElement.focus();
        return;
    }

    // Mostrar indicador de carga
    editElement.classList.add('loading');

    try {
        const response = await fetch('/cursosApp/App/ajax/usuarios.ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                'accion': 'actualizar_campo',
                'campo': fieldName,
                'valor': newValue
            })
        });

        const result = await response.json();

        if (result.success) {
            // Actualizar el display del campo
            updateDisplayField(fieldName, newValue);

            // Salir del modo edición
            exitEditMode(fieldName);

            // Actualizar valor original
            originalValues[fieldName] = newValue;

            showAlert('Campo actualizado correctamente', 'success');
        } else {
            showAlert(result.message || 'Error al actualizar el campo', 'danger');
        }

    } catch (error) {
        console.error('Error:', error);
        showAlert('Error de conexión. Por favor intenta de nuevo.', 'danger');
    } finally {
        editElement.classList.remove('loading');
    }
}

/**
 * Validar formato de email
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Actualizar el display de un campo
 */
function updateDisplayField(fieldName, newValue) {
    const displayElement = document.getElementById(`display-${fieldName}`);

    if (displayElement) {
        if (fieldName === 'biografia') {
            // Para biografía, usar innerHTML para mantener saltos de línea
            displayElement.innerHTML = newValue ? newValue.replace(/\n/g, '<br>') : 'No especificada';
        } else {
            displayElement.textContent = newValue || 'No especificado';
        }

        // Agregar animación
        displayElement.classList.add('fade-in');
        setTimeout(() => {
            displayElement.classList.remove('fade-in');
        }, 300);
    }
}

/**
 * Salir del modo edición
 */
function exitEditMode(fieldName) {
    const fieldContainer = document.querySelector(`[data-field="${fieldName}"]`);
    if (!fieldContainer) return;

    const displayElement = fieldContainer.querySelector('.display-value');
    const editElement = fieldContainer.querySelector('.edit-mode');
    const inputElement = fieldContainer.querySelector('.form-control');

    if (displayElement && editElement && inputElement) {
        // Mostrar display y ocultar edit
        displayElement.style.display = 'flex';
        editElement.classList.remove('active');

        // Limpiar eventos
        inputElement.removeEventListener('blur', handleInputBlur);
        inputElement.removeEventListener('keypress', handleInputKeypress);
    }

    currentEditField = null;
}

/**
 * Configurar carga de imagen
 */
function setupImageUpload() {
    const fileInput = document.getElementById('photo-input');
    const uploadContainer = document.querySelector('.file-upload-container');
    const previewContainer = document.getElementById('image-preview-container');

    if (!fileInput || !uploadContainer) return;

    // Click en el contenedor para abrir selector de archivo
    uploadContainer.addEventListener('click', () => {
        fileInput.click();
    });

    // Cambio en el input de archivo
    fileInput.addEventListener('change', handleFileSelect);

    // Drag and drop
    uploadContainer.addEventListener('dragover', handleDragOver);
    uploadContainer.addEventListener('dragleave', handleDragLeave);
    uploadContainer.addEventListener('drop', handleFileDrop);
}

/**
 * Manejar selección de archivo
 */
function handleFileSelect(e) {
    const file = e.target.files[0];
    if (file) {
        processImageFile(file);
    }
}

/**
 * Manejar drag over
 */
function handleDragOver(e) {
    e.preventDefault();
    e.currentTarget.classList.add('dragover');
}

/**
 * Manejar drag leave
 */
function handleDragLeave(e) {
    e.currentTarget.classList.remove('dragover');
}

/**
 * Manejar drop de archivo
 */
function handleFileDrop(e) {
    e.preventDefault();
    e.currentTarget.classList.remove('dragover');

    const files = e.dataTransfer.files;
    if (files.length > 0) {
        processImageFile(files[0]);
    }
}

/**
 * Procesar archivo de imagen
 */
function processImageFile(file) {
    // Validar tipo de archivo
    if (!file.type.startsWith('image/')) {
        showAlert('Por favor selecciona una imagen válida', 'danger');
        return;
    }

    // Validar tamaño (max 5MB)
    if (file.size > 5 * 1024 * 1024) {
        showAlert('La imagen es muy grande. Máximo 5MB permitido.', 'danger');
        return;
    }

    // Mostrar preview
    const reader = new FileReader();
    reader.onload = function (e) {
        showImagePreview(e.target.result);
    };
    reader.readAsDataURL(file);

    // Subir imagen
    uploadImage(file);
}

/**
 * Mostrar preview de imagen
 */
function showImagePreview(src) {
    const previewContainer = document.getElementById('image-preview-container');
    if (previewContainer) {
        previewContainer.innerHTML = `
            <img src="${src}" alt="Preview" class="image-preview">
        `;
        previewContainer.style.display = 'block';
    }
}

/**
 * Subir imagen al servidor
 */
async function uploadImage(file) {
    const formData = new FormData();
    formData.append('imagen', file);
    formData.append('accion', 'actualizar_foto');

    // Mostrar indicador de carga
    const uploadContainer = document.querySelector('.file-upload-container');
    uploadContainer.classList.add('loading');

    try {
        const response = await fetch('/cursosApp/App/ajax/usuarios.ajax.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            // Actualizar imagen de perfil
            updateProfileImage(result.nueva_ruta);
            showAlert('Foto de perfil actualizada correctamente', 'success');

            // Cerrar modal si está abierto
            const modal = document.getElementById('modal');
            if (modal && typeof bootstrap !== 'undefined') {
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) {
                    modalInstance.hide();
                }
            }
        } else {
            showAlert(result.message || 'Error al actualizar la foto', 'danger');
        }

    } catch (error) {
        console.error('Error:', error);
        showAlert('Error de conexión. Por favor intenta de nuevo.', 'danger');
    } finally {
        uploadContainer.classList.remove('loading');
    }
}

/**
 * Actualizar imagen de perfil en la interfaz
 */
function updateProfileImage(newSrc) {
    const profileImages = document.querySelectorAll('.avatar img, .profile-photo');
    profileImages.forEach(img => {
        img.src = newSrc + '?t=' + Date.now(); // Cache busting
    });
}

/**
 * Configurar validación de formularios
 */
function setupFormValidation() {
    const forms = document.querySelectorAll('form');

    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            if (!validateForm(this)) {
                e.preventDefault();
            }
        });
    });
}

/**
 * Validar formulario
 */
function validateForm(form) {
    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            showFieldError(field, 'Este campo es obligatorio');
            isValid = false;
        } else if (field.type === 'email' && !isValidEmail(field.value)) {
            showFieldError(field, 'Por favor ingresa un email válido');
            isValid = false;
        } else {
            clearFieldError(field);
        }
    });

    return isValid;
}

/**
 * Mostrar error en campo
 */
function showFieldError(field, message) {
    clearFieldError(field);

    field.classList.add('is-invalid');

    const errorDiv = document.createElement('div');
    errorDiv.className = 'invalid-feedback';
    errorDiv.textContent = message;

    field.parentNode.appendChild(errorDiv);
}

/**
 * Limpiar error de campo
 */
function clearFieldError(field) {
    field.classList.remove('is-invalid');

    const errorDiv = field.parentNode.querySelector('.invalid-feedback');
    if (errorDiv) {
        errorDiv.remove();
    }
}

/**
 * Mostrar alerta
 */
function showAlert(message, type = 'info') {
    // Buscar contenedor de alertas o crear uno
    let alertContainer = document.getElementById('alert-container');

    if (!alertContainer) {
        alertContainer = document.createElement('div');
        alertContainer.id = 'alert-container';
        alertContainer.style.position = 'fixed';
        alertContainer.style.top = '20px';
        alertContainer.style.right = '20px';
        alertContainer.style.zIndex = '9999';
        alertContainer.style.width = '300px';
        document.body.appendChild(alertContainer);
    }

    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} fade-in`;
    alertDiv.style.marginBottom = '10px';
    alertDiv.innerHTML = `
        <span>${message}</span>
        <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
    `;

    alertContainer.appendChild(alertDiv);

    // Auto-remove después de 5 segundos
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

/**
 * Actualizar contraseña
 */
function changePassword() {
    // Esta función puede expandirse para manejar cambio de contraseña
    const modal = document.getElementById('modalPassword');
    if (modal && typeof bootstrap !== 'undefined') {
        const modalInstance = new bootstrap.Modal(modal);
        modalInstance.show();
    }
}

/**
 * Manejar errores globales
 */
window.addEventListener('error', function (e) {
    console.error('Error global:', e.error);

    // Solo mostrar alertas para errores relacionados con perfil
    if (e.filename && e.filename.includes('perfil.js')) {
        if (window.location.hostname === 'localhost') {
            showAlert('Error inesperado en perfil. Revisa la consola para más detalles.', 'danger');
        } else {
            showAlert('Ha ocurrido un error inesperado. Por favor, recarga la página.', 'danger');
        }
    }
});

/**
 * Actualizar configuración de privacidad
 */
function updatePrivacySetting(field, value) {
    const data = new FormData();
    data.append('action', 'actualizar_privacidad');
    data.append('campo', field);
    data.append('valor', value ? '1' : '0');

    fetch('ajax/usuarios.ajax.php', {
        method: 'POST',
        body: data
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(`Configuración de privacidad actualizada correctamente`, 'success');

                // Recargar la página para reflejar los cambios en la información visible
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showAlert(data.message || 'Error al actualizar la configuración', 'danger');

                // Revertir el checkbox
                const checkbox = document.getElementById(field);
                if (checkbox) {
                    checkbox.checked = !value;
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error de conexión. Por favor, intenta de nuevo.', 'danger');

            // Revertir el checkbox
            const checkbox = document.getElementById(field);
            if (checkbox) {
                checkbox.checked = !value;
            }
        });
}

/**
 * Funciones de utilidad para debug (solo en desarrollo)
 */
if (window.location.hostname === 'localhost') {
    window.debugPerfil = {
        currentEditField: () => currentEditField,
        originalValues: () => originalValues,
        editField: editField,
        saveField: saveField,
        cancelEdit: cancelEdit,
        updatePrivacySetting: updatePrivacySetting
    };
}
