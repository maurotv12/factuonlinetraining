/**
 * Editor dinámico de cursos - Vista integrada
 * Permite editar cursos directamente desde la vista de visualización
 */

// Variables globales
let idCurso;
let modoEdicion = false;
let datosOriginales = {};

// Inicializar cuando la página esté lista
document.addEventListener('DOMContentLoaded', function () {
    // Obtener ID del curso desde un input oculto
    idCurso = document.getElementById('idCurso').value;

    // Inicializar funcionalidades
    inicializarListeners();
    inicializarValidacionNombre();
    guardarDatosOriginales();

    // Hacer las secciones colapsables por defecto
    document.querySelectorAll('.section-content').forEach((element, index) => {
        if (index > 0) { // Mantener la primera abierta
            element.style.display = 'none';
        }
    });
});

/**
 * Inicializa todos los listeners de eventos
 */
function inicializarListeners() {
    // Botón para activar/desactivar modo edición
    const btnToggleEdit = document.getElementById('btnToggleEdit');
    if (btnToggleEdit) {
        btnToggleEdit.addEventListener('click', toggleModoEdicion);
    }

    // Listeners para formularios de secciones
    const tipoContenido = document.getElementById('tipoContenido');
    if (tipoContenido) {
        tipoContenido.addEventListener('change', function () {
            toggleCamposPorTipo(this.value);
        });
    }

    // Botón para guardar contenido
    const btnGuardarContenido = document.getElementById('guardarContenido');
    if (btnGuardarContenido) {
        btnGuardarContenido.addEventListener('click', guardarContenido);
    }

    // Listeners para guardar cambios
    const btnGuardarCambios = document.getElementById('btnGuardarCambios');
    if (btnGuardarCambios) {
        btnGuardarCambios.addEventListener('click', guardarCambiosCurso);
    }

    const btnCancelarCambios = document.getElementById('btnCancelarCambios');
    if (btnCancelarCambios) {
        btnCancelarCambios.addEventListener('click', cancelarCambios);
    }
}

/**
 * Guarda los datos originales del curso
 */
function guardarDatosOriginales() {
    datosOriginales = {
        nombre: document.getElementById('nombre')?.value || '',
        descripcion: document.getElementById('descripcion')?.value || '',
        lo_que_aprenderas: document.getElementById('lo_que_aprenderas')?.value || '',
        requisitos: document.getElementById('requisitos')?.value || '',
        para_quien: document.getElementById('para_quien')?.value || '',
        valor: document.getElementById('valor')?.value || '',
        id_categoria: document.getElementById('id_categoria')?.value || '',
        id_persona: document.getElementById('id_persona')?.value || '',
        estado: document.getElementById('estado')?.value || ''
    };
}

/**
 * Activa o desactiva el modo edición
 */
function toggleModoEdicion() {
    modoEdicion = !modoEdicion;

    const btnToggleEdit = document.getElementById('btnToggleEdit');
    const editorControls = document.getElementById('editorControls');
    const camposEdicion = document.querySelectorAll('.campo-editable');
    const camposSelect = document.querySelectorAll('.select-editable');

    if (modoEdicion) {
        // Activar modo edición
        btnToggleEdit.innerHTML = '<i class="bi bi-eye"></i> Ver curso';
        btnToggleEdit.classList.remove('btn-primary');
        btnToggleEdit.classList.add('btn-secondary');

        if (editorControls) {
            editorControls.style.display = 'block';
        }

        // Hacer campos editables
        camposEdicion.forEach(campo => {
            campo.removeAttribute('readonly');
            campo.classList.add('editable-active');
        });

        camposSelect.forEach(campo => {
            campo.removeAttribute('disabled');
            campo.classList.add('editable-active');
        });

        // Mostrar elementos de edición
        document.querySelectorAll('.edit-only').forEach(el => {
            el.style.display = 'block';
        });

    } else {
        // Desactivar modo edición
        btnToggleEdit.innerHTML = '<i class="bi bi-pencil"></i> Editar Curso';
        btnToggleEdit.classList.remove('btn-secondary');
        btnToggleEdit.classList.add('btn-primary');

        if (editorControls) {
            editorControls.style.display = 'none';
        }

        // Hacer campos de solo lectura
        camposEdicion.forEach(campo => {
            campo.setAttribute('readonly', 'readonly');
            campo.classList.remove('editable-active');
        });

        camposSelect.forEach(campo => {
            campo.setAttribute('disabled', 'disabled');
            campo.classList.remove('editable-active');
        });

        // Ocultar elementos de edición
        document.querySelectorAll('.edit-only').forEach(el => {
            el.style.display = 'none';
        });
    }
}

/**
 * Guarda los cambios del curso
 */
function guardarCambiosCurso() {
    const formData = new FormData();

    // Agregar datos básicos
    formData.append('action', 'actualizar_curso');
    formData.append('id', idCurso);
    formData.append('nombre', document.getElementById('nombre')?.value || '');
    formData.append('descripcion', document.getElementById('descripcion')?.value || '');
    formData.append('lo_que_aprenderas', document.getElementById('lo_que_aprenderas')?.value || '');
    formData.append('requisitos', document.getElementById('requisitos')?.value || '');
    formData.append('para_quien', document.getElementById('para_quien')?.value || '');
    formData.append('valor', document.getElementById('valor')?.value || '');
    formData.append('id_categoria', document.getElementById('id_categoria')?.value || '');
    formData.append('id_persona', document.getElementById('id_persona')?.value || '');
    formData.append('estado', document.getElementById('estado')?.value || '');

    // Agregar archivos si se seleccionaron
    const bannerFile = document.getElementById('banner')?.files[0];
    if (bannerFile) {
        formData.append('banner', bannerFile);
    }

    const videoFile = document.getElementById('promo_video')?.files[0];
    if (videoFile) {
        formData.append('promo_video', videoFile);
    }

    // Mostrar loading
    Swal.fire({
        title: 'Guardando cambios...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Enviar datos
    fetch('/cursosApp/App/ajax/curso_secciones.ajax.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Éxito', 'Los cambios se han guardado correctamente', 'success')
                    .then(() => {
                        // Recargar página para mostrar cambios
                        location.reload();
                    });
            } else {
                Swal.fire('Error', data.message || 'Error al guardar los cambios', 'error');
            }
        })
        .catch(error => {
            Swal.fire('Error', 'Error de conexión', 'error');
        });
}

/**
 * Cancela los cambios y restaura valores originales
 */
function cancelarCambios() {
    Swal.fire({
        title: '¿Cancelar cambios?',
        text: 'Se perderán todos los cambios no guardados',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, cancelar',
        cancelButtonText: 'Continuar editando'
    }).then((result) => {
        if (result.isConfirmed) {
            // Restaurar valores originales
            Object.keys(datosOriginales).forEach(key => {
                const elemento = document.getElementById(key);
                if (elemento) {
                    elemento.value = datosOriginales[key];
                }
            });

            // Desactivar modo edición
            modoEdicion = true; // Para que toggle lo desactive
            toggleModoEdicion();
        }
    });
}

/**
 * Inicializar validación del nombre único del curso
 */
function inicializarValidacionNombre() {
    const inputNombre = document.getElementById('nombre');
    if (inputNombre) {
        let timeoutId;
        const nombreOriginal = inputNombre.value.trim();

        inputNombre.addEventListener('input', function () {
            const nombreActual = this.value.trim();

            // Limpiar mensajes de error previos
            limpiarMensajeErrorNombre(this);

            // Solo validar si el nombre cambió y tiene al menos 3 caracteres
            if (nombreActual.length >= 3 && nombreActual !== nombreOriginal) {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => validarNombreUnico(nombreActual), 800);
            } else if (nombreActual === nombreOriginal) {
                limpiarMensajeErrorNombre(this);
                this.dataset.nombreValido = 'true';
            }
        });
    }
}

/**
 * Validar que el nombre del curso sea único
 */
function validarNombreUnico(nombre) {
    const inputNombre = document.getElementById('nombre');
    if (!inputNombre || !idCurso) return;

    // Limpiar errores previos
    limpiarMensajeErrorNombre(inputNombre);

    // Mostrar indicador de carga
    mostrarIndicadorCargaNombre(inputNombre, true);

    // Hacer petición AJAX para validar
    fetch('/cursosApp/App/ajax/validaciones.ajax.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'accion=validar_nombre_curso&nombre=' + encodeURIComponent(nombre) + '&id_curso=' + encodeURIComponent(idCurso)
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(text => {
            try {
                const data = JSON.parse(text);
                mostrarIndicadorCargaNombre(inputNombre, false);

                if (data.error) {
                    mostrarErrorNombre(inputNombre, data.mensaje);
                    inputNombre.dataset.nombreValido = 'false';
                } else {
                    mostrarExitoNombre(inputNombre, data.mensaje || 'Nombre disponible');
                    inputNombre.dataset.nombreValido = 'true';
                }
            } catch (parseError) {
                mostrarIndicadorCargaNombre(inputNombre, false);
                mostrarErrorNombre(inputNombre, 'Error de conexión. Intenta nuevamente.');
                inputNombre.dataset.nombreValido = 'false';
            }
        })
        .catch(error => {
            mostrarIndicadorCargaNombre(inputNombre, false);
            inputNombre.dataset.nombreValido = 'true'; // Asumir válido en caso de error de red
        });
}

// ==================== FUNCIONES DE SECCIONES ====================

/**
 * Abre el modal para agregar una nueva sección
 */
function agregarSeccion() {
    Swal.fire({
        title: 'Nueva sección',
        input: 'text',
        inputLabel: 'Título de la sección',
        inputPlaceholder: 'Ej: Introducción al curso',
        showCancelButton: true,
        confirmButtonText: 'Crear',
        cancelButtonText: 'Cancelar',
        inputValidator: (value) => {
            if (!value) {
                return 'El título es requerido';
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            crearSeccion(result.value);
        }
    });
}

/**
 * Crea una nueva sección mediante AJAX
 */
function crearSeccion(titulo) {
    fetch('/cursosApp/App/ajax/curso_secciones.ajax.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            'action': 'crear_seccion',
            'id_curso': idCurso,
            'titulo': titulo
        })
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                Swal.fire('Error', data.message || 'Error al crear la sección', 'error');
            }
        })
        .catch(error => {
            Swal.fire('Error', 'Error de conexión: ' + error.message, 'error');
        });
}

/**
 * Alterna la visibilidad de una sección
 */
function toggleSeccion(idSeccion) {
    const contenido = document.getElementById(`seccion-${idSeccion}`);
    const icono = contenido.parentElement.querySelector('.bi-chevron-down, .bi-chevron-up');

    if (contenido.style.display === 'none') {
        contenido.style.display = 'block';
        icono.className = 'bi bi-chevron-up ms-2';
    } else {
        contenido.style.display = 'none';
        icono.className = 'bi bi-chevron-down ms-2';
    }
}

/**
 * Muestra/oculta campos del modal según el tipo de contenido
 */
function toggleCamposPorTipo(tipo) {
    const campoArchivo = document.getElementById('campoArchivo');
    const campoDuracion = document.getElementById('campoDuracion');
    const archivoContenido = document.getElementById('archivoContenido');

    if (tipo === 'video') {
        if (campoArchivo) campoArchivo.style.display = 'block';
        if (campoDuracion) campoDuracion.style.display = 'block';
        if (archivoContenido) archivoContenido.setAttribute('accept', '.mp4,.avi,.mov,.wmv');
    } else if (tipo === 'pdf') {
        if (campoArchivo) campoArchivo.style.display = 'block';
        if (campoDuracion) campoDuracion.style.display = 'none';
        if (archivoContenido) archivoContenido.setAttribute('accept', '.pdf');
    } else {
        if (campoArchivo) campoArchivo.style.display = 'none';
        if (campoDuracion) campoDuracion.style.display = 'none';
    }
}

/**
 * Abre modal para agregar contenido a una sección
 */
function agregarContenido(idSeccion, tipo) {
    const idSeccionInput = document.getElementById('idSeccion');
    const idContenidoInput = document.getElementById('idContenido');
    const tipoContenidoInput = document.getElementById('tipoContenido');
    const modalLabel = document.getElementById('modalContenidoLabel');
    const formContenido = document.getElementById('formContenido');

    // Configurar modal
    if (idSeccionInput) idSeccionInput.value = idSeccion;
    if (idContenidoInput) idContenidoInput.value = '';
    if (tipoContenidoInput) tipoContenidoInput.value = tipo;
    if (modalLabel) modalLabel.textContent = `Agregar ${tipo === 'video' ? 'video' : 'PDF'}`;

    // Mostrar/ocultar campos según tipo
    toggleCamposPorTipo(tipo);

    // Limpiar formulario
    if (formContenido) formContenido.reset();

    // Mostrar modal
    const modalContenido = document.getElementById('modalContenido');
    if (modalContenido) {
        const modal = new bootstrap.Modal(modalContenido);
        modal.show();
    }
}

/**
 * Guarda el contenido mediante AJAX
 */
function guardarContenido() {
    const formContenido = document.getElementById('formContenido');
    if (!formContenido) return;

    const formData = new FormData(formContenido);
    const titulo = formData.get('titulo');
    const tipo = formData.get('tipo');
    const archivo = formData.get('archivo');
    const idContenido = formData.get('idContenido');

    // Validaciones
    if (!titulo || titulo.trim() === '') {
        Swal.fire('Error', 'El título es obligatorio', 'error');
        return;
    }

    if ((tipo === 'video' || tipo === 'pdf') && !archivo && !idContenido) {
        Swal.fire('Error', 'Debes seleccionar un archivo', 'error');
        return;
    }

    // Añadir acción al FormData
    formData.append('action', 'guardar_contenido');

    // Mostrar loading
    Swal.fire({
        title: 'Guardando...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Enviar mediante AJAX
    fetch('/cursosApp/App/ajax/curso_secciones.ajax.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Éxito', data.message || 'Contenido guardado correctamente', 'success')
                    .then(() => {
                        // Cerrar modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('modalContenido'));
                        if (modal) modal.hide();
                        // Recargar página
                        location.reload();
                    });
            } else {
                Swal.fire('Error', data.message || 'Error al guardar el contenido', 'error');
            }
        })
        .catch(error => {
            Swal.fire('Error', 'Error de conexión', 'error');
        });
}

/**
 * Elimina una sección completa
 */
function eliminarSeccion(idSeccion) {
    Swal.fire({
        title: '¿Eliminar sección?',
        text: 'Se eliminará la sección y todo su contenido',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/cursosApp/App/ajax/curso_secciones.ajax.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    'action': 'eliminar_seccion',
                    'id': idSeccion
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        Swal.fire('Error', data.message || 'Error al eliminar la sección', 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error', 'Error de conexión', 'error');
                });
        }
    });
}

/**
 * Elimina un contenido mediante confirmación y AJAX
 */
function eliminarContenido(idContenido) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/cursosApp/App/ajax/curso_secciones.ajax.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    'action': 'eliminar_contenido',
                    'id': idContenido
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        Swal.fire('Error', data.message || 'Error al eliminar', 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error', 'Error de conexión', 'error');
                });
        }
    });
}

// ==================== FUNCIONES DE VALIDACIÓN VISUAL ====================

/**
 * Mostrar indicador de carga para validación de nombre
 */
function mostrarIndicadorCargaNombre(input, mostrar) {
    let indicador = input.parentNode.querySelector('.validacion-carga');

    if (mostrar) {
        if (!indicador) {
            indicador = document.createElement('div');
            indicador.className = 'validacion-carga text-info';
            indicador.style.cssText = 'font-size: 0.8rem; margin-top: 0.25rem;';
            indicador.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Validando nombre...';
            input.parentNode.appendChild(indicador);
        }
    } else if (indicador) {
        indicador.remove();
    }
}

/**
 * Mostrar error de validación de nombre
 */
function mostrarErrorNombre(input, mensaje) {
    limpiarMensajeErrorNombre(input);

    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-nombre text-danger';
    errorDiv.style.cssText = 'font-size: 0.8rem; margin-top: 0.25rem;';
    errorDiv.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${mensaje}`;

    input.parentNode.appendChild(errorDiv);
    input.classList.add('is-invalid');
}

/**
 * Mostrar éxito de validación de nombre
 */
function mostrarExitoNombre(input, mensaje) {
    limpiarMensajeErrorNombre(input);

    const exitoDiv = document.createElement('div');
    exitoDiv.className = 'exito-nombre text-success';
    exitoDiv.style.cssText = 'font-size: 0.8rem; margin-top: 0.25rem;';
    exitoDiv.innerHTML = `<i class="fas fa-check-circle"></i> ${mensaje}`;

    input.parentNode.appendChild(exitoDiv);
    input.classList.remove('is-invalid');
    input.classList.add('is-valid');
}

/**
 * Limpiar mensaje de error del nombre
 */
function limpiarMensajeErrorNombre(input) {
    const contenedor = input.parentNode;
    const errorExistente = contenedor.querySelector('.error-nombre');
    const exitoExistente = contenedor.querySelector('.exito-nombre');

    if (errorExistente) errorExistente.remove();
    if (exitoExistente) exitoExistente.remove();

    input.classList.remove('is-invalid', 'is-valid');
}

/**
 * Reproduce contenido en el reproductor principal
 */
function reproducirContenido(url, tipo, titulo) {
    const videoPlayer = document.getElementById('videoPlayer');
    const mainImage = document.querySelector('.main-image');
    const videoContainer = document.querySelector('.video-wrapper');
    const imageContainer = document.querySelector('.image-wrapper');

    if (tipo === 'video' && videoPlayer) {
        // Mostrar contenedor de video y ocultar imagen
        if (videoContainer) videoContainer.style.display = 'block';
        if (imageContainer) imageContainer.style.display = 'none';

        videoPlayer.src = url;
        videoPlayer.load();

        // Actualizar título del video
        const videoTitle = document.querySelector('.video-title');
        if (videoTitle) {
            videoTitle.textContent = titulo;
        }
    }
}
