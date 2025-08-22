/**
 * Editor de cursos - Funcionalidades JavaScript
 * Gestiona la manipulación de secciones y contenido de los cursos
 */

// Variables globales
let idCurso;

// Inicializar cuando la página esté lista
document.addEventListener('DOMContentLoaded', function () {
    // Obtener ID del curso desde un input oculto
    idCurso = document.getElementById('idCurso').value;

    // Hacer las secciones colapsables por defecto
    document.querySelectorAll('.section-content').forEach((element, index) => {
        if (index > 0) { // Mantener la primera abierta
            element.style.display = 'none';
        }
    });

    // Inicializar listeners para formularios
    inicializarListeners();

    // Inicializar validación de nombre único
    inicializarValidacionNombreEdicion();

    // Agregar validación al formulario antes del envío
    const formCursoBasico = document.getElementById('formCursoBasico');
    if (formCursoBasico) {
        formCursoBasico.addEventListener('submit', function (e) {
            const inputNombre = document.getElementById('nombre');
            if (inputNombre && inputNombre.dataset.nombreValido === 'false') {
                e.preventDefault();
                Swal.fire('Error', 'Por favor, usa un nombre diferente para el curso. Este nombre ya está en uso.', 'error');
                return false;
            }
        });
    }
});

/**
 * Inicializa todos los listeners de eventos
 */
function inicializarListeners() {
    // Cambio de tipo de contenido en modal
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
    // Llamada AJAX para crear sección
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
 * Abre el modal para editar un contenido existente
 */
function editarContenido(idContenido) {
    // Obtener datos del contenido mediante AJAX
    fetch('/cursosApp/App/ajax/curso_secciones.ajax.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            'action': 'obtener_contenido',
            'id': idContenido
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Cargar datos en el modal
                document.getElementById('idContenido').value = data.contenido.id;
                document.getElementById('idSeccion').value = data.contenido.id_seccion;
                document.getElementById('tituloContenido').value = data.contenido.titulo;
                document.getElementById('descripcionContenido').value = data.contenido.descripcion || '';
                document.getElementById('tipoContenido').value = data.contenido.tipo;
                document.getElementById('duracionContenido').value = data.contenido.duracion || '';

                // Configurar campos según tipo
                toggleCamposPorTipo(data.contenido.tipo);

                // Cambiar título del modal
                document.getElementById('modalContenidoLabel').textContent = 'Editar contenido';

                // Mostrar modal
                new bootstrap.Modal(document.getElementById('modalContenido')).show();
            } else {
                Swal.fire('Error', data.message || 'Error al obtener el contenido', 'error');
            }
        })
        .catch(error => {
            Swal.fire('Error', 'Error de conexión', 'error');
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
            // Eliminar mediante AJAX
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
            // Eliminar mediante AJAX
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
 * Actualiza el título de una sección
 */
function actualizarTituloSeccion(idSeccion, nuevoTitulo) {
    fetch('/cursosApp/App/ajax/curso_secciones.ajax.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            'action': 'actualizar_seccion',
            'id': idSeccion,
            'titulo': nuevoTitulo
        })
    })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                Swal.fire('Error', data.message || 'Error al actualizar título', 'error');
            }
        })
        .catch(error => {
            // Silenciar errores de actualización de título
        });
}

/**
 * Inicializar validación del nombre único del curso para edición
 */
function inicializarValidacionNombreEdicion() {
    const inputNombre = document.getElementById('nombre');
    if (inputNombre) {
        let timeoutId;
        const nombreOriginal = inputNombre.value.trim(); // Guardar nombre original

        // Validación en tiempo real mientras escribe (con debounce)
        inputNombre.addEventListener('input', function () {
            const nombreActual = this.value.trim();

            // Limpiar mensajes de error previos cuando el usuario escriba
            limpiarMensajeErrorNombre(this);

            // Solo validar si el nombre cambió y tiene al menos 3 caracteres
            if (nombreActual.length >= 3 && nombreActual !== nombreOriginal) {
                // Debounce para evitar múltiples peticiones
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => validarNombreUnicoEdicion(nombreActual), 800);
            } else if (nombreActual === nombreOriginal) {
                // Si volvió al nombre original, limpiar validaciones
                limpiarMensajeErrorNombre(this);
                this.dataset.nombreValido = 'true';
            } else if (nombreActual.length < 3 && nombreActual.length > 0) {
                // Si tiene menos de 3 caracteres, no es válido pero no mostrar error aún
                this.dataset.nombreValido = 'true';
            }
        });

        // Validación al salir del campo
        inputNombre.addEventListener('blur', function () {
            const nombreActual = this.value.trim();

            // Solo validar si el nombre cambió
            if (nombreActual.length >= 3 && nombreActual !== nombreOriginal) {
                // Cancelar timeout anterior y validar inmediatamente
                clearTimeout(timeoutId);
                validarNombreUnicoEdicion(nombreActual);
            }
        });
    }
}

/**
 * Validar que el nombre del curso sea único (versión para edición)
 */
function validarNombreUnicoEdicion(nombre) {
    const inputNombre = document.getElementById('nombre');

    if (!inputNombre || !idCurso) return;

    // Limpiar errores previos
    limpiarMensajeErrorNombre(inputNombre);

    // Mostrar indicador de carga
    mostrarIndicadorCargaNombre(inputNombre, true);

    // Hacer petición AJAX para validar
    fetch('ajax/validaciones.ajax.php', {
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
            return response.text(); // Usar text() primero para debug
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
