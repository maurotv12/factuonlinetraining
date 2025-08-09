// JavaScript para la página de crear curso
document.addEventListener('DOMContentLoaded', function () {
    console.log('Crear curso - JavaScript cargado');

    // Inicializar validaciones
    inicializarValidacionViñetas();
    inicializarValidacionFormulario();
    configurarVistaPrevia();
});

/**
 * Configurar validación de campos de viñetas
 */
function inicializarValidacionViñetas() {
    const camposViñetas = ['lo_que_aprenderas', 'requisitos', 'para_quien'];

    camposViñetas.forEach(id => {
        const textarea = document.getElementById(id);
        if (textarea) {
            // Validación en tiempo real
            textarea.addEventListener('blur', validarLineaViñeta);
            textarea.addEventListener('input', mostrarContadorCaracteres);

            // Agregar contador de caracteres
            agregarContadorCaracteres(textarea);
        }
    });
}

/**
 * Validar límite de caracteres por línea en campos de viñetas
 */
function validarLineaViñeta(e) {
    const textarea = e.target;
    const lines = textarea.value.split('\n');
    const maxCaracteres = 100;

    // Limpiar mensajes de error previos
    limpiarMensajeError(textarea);

    // Verificar cada línea
    let lineaProblematica = -1;

    for (let i = 0; i < lines.length; i++) {
        const linea = lines[i].trim();
        if (linea.length > maxCaracteres) {
            lineaProblematica = i;
            break;
        }
    }

    if (lineaProblematica >= 0) {
        mostrarMensajeError(
            textarea,
            `La línea ${lineaProblematica + 1} excede el límite de ${maxCaracteres} caracteres.`
        );

        // Resaltar el área del problema
        resaltarLineaProblematica(textarea, lineaProblematica);
        return false;
    }

    return true;
}

/**
 * Mostrar contador de caracteres para cada línea
 */
function mostrarContadorCaracteres(e) {
    const textarea = e.target;
    const lines = textarea.value.split('\n');
    const maxCaracteres = 70;

    // Encontrar la línea actual
    const cursorPos = textarea.selectionStart;
    const textBeforeCursor = textarea.value.substring(0, cursorPos);
    const currentLineIndex = textBeforeCursor.split('\n').length - 1;

    if (lines[currentLineIndex]) {
        const caracteresActuales = lines[currentLineIndex].length;
        const caracteresRestantes = maxCaracteres - caracteresActuales;

        actualizarContadorCaracteres(textarea, caracteresActuales, caracteresRestantes);
    }
}

/**
 * Agregar contador de caracteres visual
 */
function agregarContadorCaracteres(textarea) {
    const contador = document.createElement('div');
    contador.className = 'contador-caracteres';
    contador.style.cssText = `
        font-size: 0.8rem;
        color: #6c757d;
        margin-top: 0.25rem;
        text-align: right;
    `;

    const contenedor = textarea.parentNode;
    contenedor.appendChild(contador);

    // Almacenar referencia para actualizar
    textarea.contadorElemento = contador;
}

/**
 * Actualizar contador de caracteres
 */
function actualizarContadorCaracteres(textarea, actuales, restantes) {
    if (textarea.contadorElemento) {
        const color = restantes < 10 ? '#dc3545' : restantes < 20 ? '#f7c00bff' : '#6c757d';
        textarea.contadorElemento.innerHTML = `
            <span style="color: ${color};">
                Línea actual: ${actuales}/70 caracteres
                ${restantes < 0 ? `(${Math.abs(restantes)} caracteres de más)` : ''}
            </span>
        `;
    }
}

/**
 * Resaltar línea problemática
 */
function resaltarLineaProblematica(textarea, lineaIndex) {
    const lines = textarea.value.split('\n');
    let startPos = 0;

    // Calcular posición de inicio de la línea problemática
    for (let i = 0; i < lineaIndex; i++) {
        startPos += lines[i].length + 1; // +1 por el \n
    }

    const endPos = startPos + lines[lineaIndex].length;

    // Seleccionar la línea problemática
    textarea.setSelectionRange(startPos, endPos);
    textarea.focus();
}

/**
 * Mostrar mensaje de error
 */
function mostrarMensajeError(textarea, mensaje) {
    limpiarMensajeError(textarea);

    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-viñeta text-danger';
    errorDiv.style.cssText = 'font-size: 0.8rem; margin-top: 0.25rem;';
    errorDiv.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${mensaje}`;

    textarea.parentNode.appendChild(errorDiv);
    textarea.errorElemento = errorDiv;
}

/**
 * Limpiar mensaje de error
 */
function limpiarMensajeError(textarea) {
    if (textarea.errorElemento) {
        textarea.errorElemento.remove();
        textarea.errorElemento = null;
    }
}

/**
 * Configurar validación del formulario completo
 */
function inicializarValidacionFormulario() {
    const form = document.getElementById('form-crear-curso');

    if (form) {
        form.addEventListener('submit', validarFormularioCompleto);
    }
}

/**
 * Validar formulario completo antes del envío
 */
function validarFormularioCompleto(e) {
    const camposViñetas = ['lo_que_aprenderas', 'requisitos', 'para_quien'];
    let formularioValido = true;
    let primerCampoConError = null;

    // Validar cada campo de viñetas
    camposViñetas.forEach(id => {
        const textarea = document.getElementById(id);
        if (textarea && textarea.value.trim()) {
            const evento = { target: textarea };
            const esValido = validarLineaViñeta(evento);

            if (!esValido && !primerCampoConError) {
                primerCampoConError = textarea;
                formularioValido = false;
            }
        }
    });

    // Validar imagen
    const imagen = document.getElementById('imagen');
    if (imagen && imagen.files.length > 0) {
        const archivo = imagen.files[0];
        if (!validarTipoImagen(archivo)) {
            mostrarErrorImagen('Solo se permiten archivos de imagen (JPG, PNG, GIF, WebP).');
            formularioValido = false;
            if (!primerCampoConError) primerCampoConError = imagen;
        }
    }

    // Si hay errores, prevenir envío y enfocar primer campo con error
    if (!formularioValido) {
        e.preventDefault();

        if (primerCampoConError) {
            primerCampoConError.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
            primerCampoConError.focus();
        }

        // Mostrar mensaje general
        Swal.fire({
            icon: 'warning',
            title: 'Formulario incompleto',
            text: 'Por favor, corrija los errores antes de continuar.',
            confirmButtonText: 'Entendido'
        });
    }
}

/**
 * Validar tipo de archivo de imagen
 */
function validarTipoImagen(archivo) {
    const tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    return tiposPermitidos.includes(archivo.type);
}

/**
 * Mostrar error de imagen
 */
function mostrarErrorImagen(mensaje) {
    const imagen = document.getElementById('imagen');
    const errorExistente = imagen.parentNode.querySelector('.error-imagen');

    if (errorExistente) {
        errorExistente.remove();
    }

    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-imagen text-danger';
    errorDiv.style.cssText = 'font-size: 0.8rem; margin-top: 0.25rem;';
    errorDiv.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${mensaje}`;

    imagen.parentNode.appendChild(errorDiv);
}

/**
 * Configurar vista previa de archivos
 */
function configurarVistaPrevia() {
    const inputImagen = document.getElementById('imagen');
    const inputVideo = document.getElementById('video');

    if (inputImagen) {
        inputImagen.addEventListener('change', mostrarVistaPreviaImagen);
    }

    if (inputVideo) {
        inputVideo.addEventListener('change', mostrarVistaPreviaVideo);
    }
}

/**
 * Mostrar vista previa de imagen
 */
function mostrarVistaPreviaImagen(e) {
    const archivo = e.target.files[0];
    const contenedor = e.target.parentNode;

    // Limpiar vista previa existente
    const vistaPreviaExistente = contenedor.querySelector('.vista-previa-imagen');
    if (vistaPreviaExistente) {
        vistaPreviaExistente.remove();
    }

    if (archivo && validarTipoImagen(archivo)) {
        const reader = new FileReader();

        reader.onload = function (event) {
            const img = document.createElement('img');
            img.src = event.target.result;
            img.className = 'vista-previa-imagen';
            img.style.cssText = `
                max-width: 200px;
                max-height: 150px;
                margin-top: 0.5rem;
                border: 1px solid #dee2e6;
                border-radius: 0.375rem;
                object-fit: cover;
            `;

            contenedor.appendChild(img);
        };

        reader.readAsDataURL(archivo);
    }
}

/**
 * Mostrar información de video
 */
function mostrarVistaPreviaVideo(e) {
    const archivo = e.target.files[0];
    const contenedor = e.target.parentNode;

    // Limpiar información existente
    const infoExistente = contenedor.querySelector('.info-video');
    if (infoExistente) {
        infoExistente.remove();
    }

    if (archivo) {
        const infoDiv = document.createElement('div');
        infoDiv.className = 'info-video';
        infoDiv.style.cssText = 'margin-top: 0.5rem; font-size: 0.9rem; color: #6c757d;';

        const tamaño = (archivo.size / (1024 * 1024)).toFixed(2);
        infoDiv.innerHTML = `
            <i class="fas fa-video"></i> 
            ${archivo.name} (${tamaño} MB)
        `;

        contenedor.appendChild(infoDiv);
    }
}

/**
 * Utilidades adicionales
 */

// Función para limpiar el formulario
function limpiarFormulario() {
    document.getElementById('form-crear-curso').reset();

    // Limpiar vistas previas y mensajes de error
    document.querySelectorAll('.vista-previa-imagen, .info-video, .error-viñeta, .error-imagen').forEach(el => {
        el.remove();
    });

    // Limpiar contadores
    document.querySelectorAll('.contador-caracteres').forEach(contador => {
        contador.innerHTML = '';
    });
}

// Función para autocompletar campos (útil para desarrollo/testing)
// function autocompletarFormulario() {
//     if (window.location.hostname === 'localhost') {
//         document.getElementById('nombre').value = 'Curso de Ejemplo';
//         document.getElementById('descripcion').value = 'Esta es una descripción de ejemplo para el curso.';
//         document.getElementById('lo_que_aprenderas').value = 'Aprenderás conceptos básicos\nDominarás las herramientas\nAplicarás conocimientos prácticos';
//         document.getElementById('requisitos').value = 'Conocimientos básicos de informática\nComputador con internet\nGanas de aprender';
//         document.getElementById('para_quien').value = 'Principiantes en el tema\nPersonas que quieran mejorar\nEstudiantes y profesionales';
//         document.getElementById('precio').value = '50000';
//     }
// }

// Exponer funciones globalmente para debugging
// window.crearCursoJS = {
//     limpiarFormulario,
//     autocompletarFormulario,
//     validarLineaViñeta
// };
// function inicializarFormulario() {
//     // Configurar drag & drop para archivos
//     configurarDragAndDrop();

//     // Configurar navegación por pasos
//     configurarNavegacionPasos();

//     // Configurar autocompletado de URL amigable
//     configurarUrlAmigable();

//     // Configurar validación en tiempo real
//     configurarValidacionTiempoReal();
// }

/**
 * Configura drag & drop para upload de archivos
 */
// function configurarDragAndDrop() {
//     const fileAreas = document.querySelectorAll('.file-upload-area');

//     fileAreas.forEach(area => {
//         const input = area.querySelector('input[type="file"]');

//         area.addEventListener('dragover', (e) => {
//             e.preventDefault();
//             area.classList.add('dragover');
//         });

//         area.addEventListener('dragleave', () => {
//             area.classList.remove('dragover');
//         });

//         area.addEventListener('drop', (e) => {
//             e.preventDefault();
//             area.classList.remove('dragover');

//             const files = e.dataTransfer.files;
//             if (files.length > 0) {
//                 input.files = files;
//                 previewFile(input);
//             }
//         });

//         area.addEventListener('click', () => {
//             input.click();
//         });

//         input.addEventListener('change', () => {
//             previewFile(input);
//         });
//     });
// }

/**
 * Previsualiza archivos seleccionados
 */
// function previewFile(input) {
//     const file = input.files[0];
//     if (!file) return;

//     const area = input.closest('.file-upload-area');
//     const preview = area.querySelector('.file-preview');

//     if (file.type.startsWith('image/')) {
//         const reader = new FileReader();
//         reader.onload = function (e) {
//             preview.innerHTML = `
//                 <img src="${e.target.result}" class="preview-image" alt="Vista previa">
//                 <p class="mt-2 mb-0"><strong>${file.name}</strong></p>
//                 <small class="text-muted">${(file.size / 1024 / 1024).toFixed(2)} MB</small>
//             `;
//         };
//         reader.readAsDataURL(file);
//     } else if (file.type.startsWith('video/')) {
//         preview.innerHTML = `
//             <i class="bi bi-camera-video-fill" style="font-size: 3rem; color: #667eea;"></i>
//             <p class="mt-2 mb-0"><strong>${file.name}</strong></p>
//             <small class="text-muted">${(file.size / 1024 / 1024).toFixed(2)} MB</small>
//         `;
//     }
// }

/**
 * Configura la navegación por pasos del formulario
 */
// function configurarNavegacionPasos() {
//     const nextBtns = document.querySelectorAll('.btn-next-step');
//     const prevBtns = document.querySelectorAll('.btn-prev-step');

//     nextBtns.forEach(btn => {
//         btn.addEventListener('click', () => {
//             if (validarPasoActual()) {
//                 siguientePaso();
//             }
//         });
//     });

//     prevBtns.forEach(btn => {
//         btn.addEventListener('click', () => {
//             pasoAnterior();
//         });
//     });
// }

/**
 * Avanza al siguiente paso
//  */
// function siguientePaso() {
//     if (currentStep < totalSteps) {
//         document.querySelector(`#paso-${currentStep}`).style.display = 'none';
//         currentStep++;
//         document.querySelector(`#paso-${currentStep}`).style.display = 'block';
//         actualizarIndicadorPasos();
//     }
// }

// /**
//  * Retrocede al paso anterior
//  */
// function pasoAnterior() {
//     if (currentStep > 1) {
//         document.querySelector(`#paso-${currentStep}`).style.display = 'none';
//         currentStep--;
//         document.querySelector(`#paso-${currentStep}`).style.display = 'block';
//         actualizarIndicadorPasos();
//     }
// }

/**
 * Actualiza el indicador visual de pasos
 */
// function actualizarIndicadorPasos() {
//     const steps = document.querySelectorAll('.step');
//     steps.forEach((step, index) => {
//         step.classList.remove('active', 'completed');
//         if (index + 1 === currentStep) {
//             step.classList.add('active');
//         } else if (index + 1 < currentStep) {
//             step.classList.add('completed');
//         }
//     });
// }

/**
 * Valida el paso actual antes de continuar
 */
// function validarPasoActual() {
//     const pasoActual = document.querySelector(`#paso-${currentStep}`);
//     const campos = pasoActual.querySelectorAll('input[required], select[required], textarea[required]');
//     let valido = true;

//     campos.forEach(campo => {
//         if (!campo.value.trim()) {
//             mostrarErrorCampo(campo, 'Este campo es obligatorio');
//             valido = false;
//         } else {
//             limpiarErrorCampo(campo);
//         }
//     });

//     // Validaciones específicas por paso
//     if (currentStep === 1) {
//         valido = validarPaso1() && valido;
//     } else if (currentStep === 2) {
//         valido = validarPaso2() && valido;
//     }

//     return valido;
// }

/**
 * Validaciones específicas del paso 1
 */
// function validarPaso1() {
//     const nombre = document.querySelector('input[name="nombre"]');
//     const precio = document.querySelector('input[name="precio"]');

//     // Validar longitud del nombre
//     if (nombre.value.length < 5) {
//         mostrarErrorCampo(nombre, 'El nombre debe tener al menos 5 caracteres');
//         return false;
//     }

//     // Validar precio
//     if (precio.value <= 0) {
//         mostrarErrorCampo(precio, 'El precio debe ser mayor a 0');
//         return false;
//     }

//     return true;
// }

/**
 * Validaciones específicas del paso 2
 */
// function validarPaso2() {
//     const descripcion = document.querySelector('textarea[name="descripcion"]');

//     // Validar longitud de descripción
//     if (descripcion.value.length < 20) {
//         mostrarErrorCampo(descripcion, 'La descripción debe tener al menos 20 caracteres');
//         return false;
//     }

//     return true;
// }

/**
 * Muestra error en un campo específico
 */
// function mostrarErrorCampo(campo, mensaje) {
//     limpiarErrorCampo(campo);

//     campo.classList.add('is-invalid');

//     const error = document.createElement('div');
//     error.className = 'invalid-feedback';
//     error.textContent = mensaje;

//     campo.parentNode.appendChild(error);
// }

/**
 * Limpia errores de un campo
 */
// function limpiarErrorCampo(campo) {
//     campo.classList.remove('is-invalid');
//     const error = campo.parentNode.querySelector('.invalid-feedback');
//     if (error) {
//         error.remove();
//     }
// }

/**
 * Configura la generación automática de URL amigable
 */
// function configurarUrlAmigable() {
//     const nombreInput = document.querySelector('input[name="nombre"]');
//     const urlDisplay = document.querySelector('#url-preview');

//     if (nombreInput && urlDisplay) {
//         nombreInput.addEventListener('input', function () {
//             const urlAmigable = generarUrlAmigable(this.value);
//             urlDisplay.textContent = urlAmigable || 'url-del-curso';
//         });
//     }
// }

// /**
//  * Genera URL amigable a partir del nombre
//  */
// function generarUrlAmigable(texto) {
//     return texto
//         .toLowerCase()
//         .trim()
//         .replace(/[^a-z0-9\s-]/g, '')
//         .replace(/\s+/g, '-')
//         .replace(/-+/g, '-')
//         .replace(/^-|-$/g, '');
// }

/**
 * Configura contadores de caracteres
 */
// function configurarContadores() {
//     const textareas = document.querySelectorAll('textarea[maxlength]');

//     textareas.forEach(textarea => {
//         const maxLength = textarea.getAttribute('maxlength');
//         const container = textarea.parentNode;

//         // Crear contador
//         const counter = document.createElement('small');
//         counter.className = 'char-counter';
//         counter.textContent = `0/${maxLength}`;

//         container.style.position = 'relative';
//         container.appendChild(counter);

//         // Actualizar contador
//         textarea.addEventListener('input', function () {
//             const currentLength = this.value.length;
//             counter.textContent = `${currentLength}/${maxLength}`;

//             if (currentLength > maxLength * 0.9) {
//                 counter.style.color = '#dc3545';
//             } else if (currentLength > maxLength * 0.7) {
//                 counter.style.color = '#ffc107';
//             } else {
//                 counter.style.color = '#6c757d';
//             }
//         });
//     });
// }

// /**
//  * Configura validaciones adicionales
//  */
// function configurarValidaciones() {
//     const form = document.querySelector('#formCrearCurso');

//     if (form) {
//         form.addEventListener('submit', function (e) {
//             e.preventDefault();

//             if (validarFormularioCompleto()) {
//                 mostrarModalConfirmacion();
//             }
//         });
//     }
// }

// /**
//  * Valida todo el formulario antes del envío
//  */
// function validarFormularioCompleto() {
//     // Validar todos los pasos
//     for (let i = 1; i <= totalSteps; i++) {
//         currentStep = i;
//         if (!validarPasoActual()) {
//             // Ir al paso con errores
//             document.querySelector(`#paso-${currentStep}`).style.display = 'block';
//             actualizarIndicadorPasos();
//             return false;
//         }
//     }

//     return true;
// }

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
//  */
// function enviarFormulario() {
//     const form = document.querySelector('#formCrearCurso');

//     // Mostrar loading
//     Swal.fire({
//         title: 'Creando curso...',
//         text: 'Por favor espera mientras se crea el curso',
//         allowOutsideClick: false,
//         allowEscapeKey: false,
//         showConfirmButton: false,
//         didOpen: () => {
//             Swal.showLoading();
//         }
//     });

//     // Enviar formulario
//     form.submit();
// }

/**
 * Configura validación en tiempo real
 */
// function configurarValidacionTiempoReal() {
//     const inputs = document.querySelectorAll('input, textarea, select');

//     inputs.forEach(input => {
//         input.addEventListener('blur', function () {
//             if (this.hasAttribute('required') && !this.value.trim()) {
//                 mostrarErrorCampo(this, 'Este campo es obligatorio');
//             } else {
//                 limpiarErrorCampo(this);
//             }
//         });

//         input.addEventListener('input', function () {
//             if (this.classList.contains('is-invalid')) {
//                 limpiarErrorCampo(this);
//             }
//         });
//     });
// }

/**
 * Configura previsualizaciones adicionales
 */
// function configurarPrevistalizaciones() {
//     // Previsualización del precio formateado
//     const precioInput = document.querySelector('input[name="precio"]');
//     if (precioInput) {
//         precioInput.addEventListener('input', function () {
//             const valor = parseFloat(this.value) || 0;
//             const preview = document.querySelector('#precio-preview');
//             if (preview) {
//                 preview.textContent = `$${valor.toLocaleString()}`;
//             }
//         });
//     }
// }
