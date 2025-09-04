/**
 * JavaScript para Ver Curso - Vista Profesor
 * Funcionalidades de edición dinámica, gestión de secciones, videos y archivos
 */

document.addEventListener('DOMContentLoaded', function () {
    // Elementos principales
    const cursoId = document.querySelector('[data-curso-id]')?.dataset.cursoId;

    console.log('Curso ID detectado:', cursoId);

    if (!cursoId) {
        console.error('No se pudo obtener el ID del curso');
        mostrarNotificacion('Error: No se pudo obtener el ID del curso', 'error');
        return;
    }

    // Inicializar todas las funcionalidades
    inicializarEdicionCampos();
    inicializarReproductorVideo();
    inicializarGestionSecciones();
    inicializarSubidaArchivos();

    /**
     * Inicializar edición de campos del curso
     */
    function inicializarEdicionCampos() {
        const camposEditables = [
            'nombre', 'descripcion', 'lo_que_aprenderas',
            'requisitos', 'para_quien', 'valor', 'id_categoria'
        ];

        camposEditables.forEach(campo => {
            const botonEditar = document.getElementById(`btn-editar-${campo}`);
            if (botonEditar) {
                botonEditar.addEventListener('click', () => habilitarEdicion(campo));
            }
        });

        // Inicializar drag & drop para archivos
        inicializarDragDrop();
    }

    /**
     * Habilitar edición de un campo
     */
    function habilitarEdicion(campo) {
        const display = document.getElementById(`${campo}-display`);
        const valor = display.textContent.trim();

        // Guardar el valor original para cancelación
        display.dataset.valorOriginal = display.innerHTML;

        let inputHtml = '';

        switch (campo) {
            case 'nombre':
            case 'valor':
                inputHtml = `
                    <div class="input-group">
                        <input type="text" class="form-control" id="${campo}-input" value="${valor}">
                        <button class="btn btn-success btn-sm" onclick="guardarCampo('${campo}')">
                            <i class="bi bi-check"></i>
                        </button>
                        <button class="btn btn-secondary btn-sm" onclick="cancelarEdicion('${campo}')">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>`;
                break;

            case 'descripcion':
            case 'lo_que_aprenderas':
            case 'requisitos':
            case 'para_quien':
                inputHtml = `
                    <div>
                        <textarea class="form-control mb-2" id="${campo}-input" rows="4">${valor}</textarea>
                        <div class="d-flex gap-2">
                            <button class="btn btn-success btn-sm" onclick="guardarCampo('${campo}')">
                                <i class="bi bi-check"></i> Guardar
                            </button>
                            <button class="btn btn-secondary btn-sm" onclick="cancelarEdicion('${campo}')">
                                <i class="bi bi-x"></i> Cancelar
                            </button>
                        </div>
                    </div>`;
                break;

            case 'id_categoria':
                // Obtener categorías y crear select
                // Para la categoría, necesitamos obtener el ID actual del dataset
                const categoriaActualId = display.dataset.categoriaId;
                obtenerCategorias().then(categorias => {
                    let options = '';
                    categorias.forEach(cat => {
                        const selected = cat.id == categoriaActualId ? 'selected' : '';
                        options += `<option value="${cat.id}" ${selected}>${cat.nombre}</option>`;
                    });

                    inputHtml = `
                        <div class="input-group">
                            <select class="form-control" id="${campo}-input">
                                ${options}
                            </select>
                            <button class="btn btn-success btn-sm" onclick="guardarCampo('${campo}')">
                                <i class="bi bi-check"></i>
                            </button>
                            <button class="btn btn-secondary btn-sm" onclick="cancelarEdicion('${campo}')">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>`;

                    display.innerHTML = inputHtml;
                    document.getElementById(`${campo}-input`).focus();
                });
                return;
        }

        display.innerHTML = inputHtml;
        document.getElementById(`${campo}-input`).focus();
    }

    /**
     * Guardar campo editado
     */
    window.guardarCampo = function (campo) {
        const input = document.getElementById(`${campo}-input`);
        const valor = input.value.trim();

        console.log('Guardando campo:', campo, 'Valor:', valor, 'Curso ID:', cursoId);

        if (!valor && campo !== 'valor') {
            mostrarNotificacion('El campo no puede estar vacío', 'error');
            return;
        }

        // Validaciones específicas
        if (campo === 'nombre' && valor.length < 10) {
            mostrarNotificacion('El nombre debe tener al menos 10 caracteres', 'error');
            return;
        }

        // Mostrar loading
        const btnGuardar = event.target;
        const iconoOriginal = btnGuardar.innerHTML;
        btnGuardar.innerHTML = '<i class="spinner-border spinner-border-sm"></i>';
        btnGuardar.disabled = true;

        // Datos a enviar
        const datosEnviar = {
            accion: 'actualizarCampo',
            idCurso: cursoId,
            campo: campo,
            valor: valor
        };

        console.log('Datos a enviar:', datosEnviar);

        // Enviar datos al servidor
        fetch('/cursosApp/App/ajax/cursos.ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(datosEnviar)
        })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                return response.text();
            })
            .then(text => {
                console.log('Response text:', text);
                try {
                    const data = JSON.parse(text);
                    console.log('Parsed data:', data);

                    if (data.success) {
                        actualizarVisualizacionCampo(campo, valor, data.valorFormateado);
                        mostrarNotificacion(data.mensaje, 'success');
                    } else {
                        mostrarNotificacion(data.mensaje, 'error');
                        cancelarEdicion(campo);
                    }
                } catch (e) {
                    console.error('Error parsing JSON:', e);
                    console.error('Response text was:', text);
                    mostrarNotificacion('Error: Respuesta inválida del servidor', 'error');
                    cancelarEdicion(campo);
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                mostrarNotificacion('Error de conexión', 'error');
                cancelarEdicion(campo);
            })
            .finally(() => {
                btnGuardar.innerHTML = iconoOriginal;
                btnGuardar.disabled = false;
            });
    };

    /**
     * Cancelar edición
     */
    window.cancelarEdicion = function (campo) {
        const display = document.getElementById(`${campo}-display`);
        const valorOriginal = display.dataset.valorOriginal;
        display.innerHTML = valorOriginal;
    };

    /**
     * Actualizar visualización del campo
     */
    function actualizarVisualizacionCampo(campo, valor, valorFormateado) {
        const display = document.getElementById(`${campo}-display`);

        // Actualizar el contenido
        display.innerHTML = valorFormateado || valor;
        display.dataset.valorOriginal = valorFormateado || valor;

        // Para categoría, también actualizar el data-categoria-id
        if (campo === 'id_categoria') {
            display.dataset.categoriaId = valor;
        }
    }

    /**
     * Inicializar reproductor de video
     */
    function inicializarReproductorVideo() {
        const video = document.getElementById('videoPlayer');
        if (!video) return;

        // Configurar controles personalizados si es necesario
        video.addEventListener('loadedmetadata', function () {
            console.log('Video cargado:', this.duration + ' segundos');
        });

        video.addEventListener('error', function () {
            mostrarNotificacion('Error al cargar el video', 'error');
        });
    }

    /**
     * Inicializar gestión de secciones
     */
    function inicializarGestionSecciones() {
        // Botón para agregar nueva sección
        const btnAgregarSeccion = document.getElementById('btn-agregar-seccion');
        if (btnAgregarSeccion) {
            btnAgregarSeccion.addEventListener('click', mostrarModalNuevaSeccion);
        }

        // Botón crear primera sección
        const btnCrearPrimera = document.getElementById('btn-crear-primera-seccion');
        if (btnCrearPrimera) {
            btnCrearPrimera.addEventListener('click', mostrarModalNuevaSeccion);
        }

        // Event listeners para secciones existentes
        document.querySelectorAll('.seccion-item').forEach(seccion => {
            const btnEditar = seccion.querySelector('.btn-editar-seccion');
            const btnEliminar = seccion.querySelector('.btn-eliminar-seccion');

            if (btnEditar) {
                btnEditar.addEventListener('click', () => editarSeccion(seccion.dataset.seccionId));
            }

            if (btnEliminar) {
                btnEliminar.addEventListener('click', () => eliminarSeccion(seccion.dataset.seccionId));
            }
        });
    }

    /**
     * Mostrar modal para nueva sección
     */
    function mostrarModalNuevaSeccion() {
        const modal = document.getElementById('modalSeccion') || crearModalSeccion();

        // Limpiar formulario
        document.getElementById('seccion-id').value = '';
        document.getElementById('seccion-titulo').value = '';
        document.getElementById('seccion-descripcion').value = '';

        // Mostrar modal
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
    }

    /**
     * Crear modal para secciones
     */
    function crearModalSeccion() {
        const modalHtml = `
            <div class="modal fade" id="modalSeccion" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Gestionar Sección</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form id="form-seccion">
                                <input type="hidden" id="seccion-id">
                                <div class="mb-3">
                                    <label for="seccion-titulo" class="form-label">Título de la sección *</label>
                                    <input type="text" class="form-control" id="seccion-titulo" required maxlength="255">
                                </div>
                                <div class="mb-3">
                                    <label for="seccion-descripcion" class="form-label">Descripción</label>
                                    <textarea class="form-control" id="seccion-descripcion" rows="3"></textarea>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-primary" id="btn-guardar-seccion">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>`;

        document.body.insertAdjacentHTML('beforeend', modalHtml);

        // Agregar event listener al botón guardar
        document.getElementById('btn-guardar-seccion').addEventListener('click', guardarSeccion);

        return document.getElementById('modalSeccion');
    }

    /**
     * Guardar sección
     */
    function guardarSeccion() {
        const id = document.getElementById('seccion-id').value;
        const titulo = document.getElementById('seccion-titulo').value.trim();
        const descripcion = document.getElementById('seccion-descripcion').value.trim();

        if (!titulo) {
            mostrarNotificacion('El título es obligatorio', 'error');
            return;
        }

        const datos = {
            accion: id ? 'actualizarSeccion' : 'crearSeccion',
            idCurso: cursoId,
            titulo: titulo,
            descripcion: descripcion
        };

        if (id) {
            datos.idSeccion = id;
        }

        fetch('/cursosApp/App/ajax/curso_secciones.ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(datos)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarNotificacion(data.mensaje, 'success');
                    bootstrap.Modal.getInstance(document.getElementById('modalSeccion')).hide();
                    // Recargar la página o actualizar la lista de secciones
                    setTimeout(() => location.reload(), 1000);
                } else {
                    mostrarNotificacion(data.mensaje, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarNotificacion('Error de conexión', 'error');
            });
    }

    /**
     * Inicializar subida de archivos
     */
    function inicializarSubidaArchivos() {
        // Funcionalidad drag & drop
        inicializarDragDrop();

        // Botones de subida de video promocional
        const botonesSubirPromo = document.querySelectorAll('#btn-subir-promo, .edit-video-btn');
        botonesSubirPromo.forEach(btn => {
            btn.addEventListener('click', () => {
                const input = document.createElement('input');
                input.type = 'file';
                input.accept = 'video/mp4';
                input.onchange = (e) => {
                    const file = e.target.files[0];
                    if (file && validarVideo(file)) {
                        subirVideoPromocional(file);
                    }
                };
                input.click();
            });
        });

        // Botones de agregar multimedia
        const btnAgregarVideo = document.querySelector('.add-video-btn:not(#btn-subir-promo)');
        const btnAgregarImagen = document.querySelector('.add-image-btn');

        if (btnAgregarVideo) {
            btnAgregarVideo.addEventListener('click', () => {
                const input = document.createElement('input');
                input.type = 'file';
                input.accept = 'video/mp4';
                input.onchange = (e) => {
                    const file = e.target.files[0];
                    if (file && validarVideo(file)) {
                        subirVideoPromocional(file);
                    }
                };
                input.click();
            });
        }
    }

    /**
     * Editar sección existente
     */
    window.editarSeccion = function (id) {
        // Obtener datos de la sección y mostrar modal
        fetch('/cursosApp/App/ajax/curso_secciones.ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                accion: 'obtenerSecciones',
                idCurso: cursoId
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const seccion = data.secciones.find(s => s.seccion_id == id);
                    if (seccion) {
                        const modal = document.getElementById('modalSeccion') || crearModalSeccion();
                        document.getElementById('seccion-id').value = seccion.seccion_id;
                        document.getElementById('seccion-titulo').value = seccion.seccion_titulo;
                        document.getElementById('seccion-descripcion').value = seccion.seccion_descripcion || '';

                        const bsModal = new bootstrap.Modal(modal);
                        bsModal.show();
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarNotificacion('Error al obtener datos de la sección', 'error');
            });
    };

    /**
     * Eliminar sección
     */
    window.eliminarSeccion = function (id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Esta acción eliminará la sección y todo su contenido',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/cursosApp/App/ajax/curso_secciones.ajax.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        accion: 'eliminarSeccion',
                        idSeccion: id
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            mostrarNotificacion(data.mensaje, 'success');
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            mostrarNotificacion(data.mensaje, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        mostrarNotificacion('Error de conexión', 'error');
                    });
            }
        });
    };

    /**
     * Inicializar drag & drop
     */
    function inicializarDragDrop() {
        const dropAreas = document.querySelectorAll('.drop-area');

        dropAreas.forEach(area => {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                area.addEventListener(eventName, preventDefaults, false);
            });

            ['dragenter', 'dragover'].forEach(eventName => {
                area.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                area.addEventListener(eventName, unhighlight, false);
            });

            area.addEventListener('drop', handleDrop, false);

            // Click para seleccionar archivos
            area.addEventListener('click', function () {
                const input = this.querySelector('.file-input');
                if (input) {
                    input.click();
                }
            });

            // Evento change para inputs de archivo
            const fileInput = area.querySelector('.file-input');
            if (fileInput) {
                fileInput.addEventListener('change', function (e) {
                    const files = e.target.files;
                    const seccionId = area.dataset.seccionId;
                    handleFiles(files, 'auto', seccionId);
                });
            }
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        function highlight(e) {
            e.currentTarget.classList.add('drag-over');
        }

        function unhighlight(e) {
            e.currentTarget.classList.remove('drag-over');
        }

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            const seccionId = e.currentTarget.dataset.seccionId;

            handleFiles(files, 'auto', seccionId);
        }
    }

    /**
     * Manejar archivos subidos
     */
    function handleFiles(files, tipo, seccionId) {
        Array.from(files).forEach(file => {
            if (tipo === 'auto') {
                // Detectar tipo automáticamente
                if (file.type === 'video/mp4') {
                    if (validarVideo(file)) {
                        subirVideo(file, seccionId);
                    }
                } else if (file.type === 'application/pdf') {
                    if (validarPDF(file)) {
                        subirPDF(file, seccionId);
                    }
                } else {
                    mostrarNotificacion('Tipo de archivo no soportado. Solo MP4 y PDF', 'error');
                }
            }
        });
    }

    /**
     * Validar archivo de video
     */
    function validarVideo(file) {
        if (file.size > 100 * 1024 * 1024) {
            mostrarNotificacion('El video es demasiado grande (máximo 100MB)', 'error');
            return false;
        }
        return true;
    }

    /**
     * Validar archivo PDF
     */
    function validarPDF(file) {
        if (file.size > 10 * 1024 * 1024) {
            mostrarNotificacion('El PDF es demasiado grande (máximo 10MB)', 'error');
            return false;
        }
        return true;
    }

    /**
     * Subir video
     */
    function subirVideo(file, seccionId) {
        const titulo = prompt('Título del video:');
        if (!titulo) return;

        const formData = new FormData();
        formData.append('video', file);
        formData.append('accion', 'subirVideo');
        formData.append('idSeccion', seccionId);
        formData.append('titulo', titulo);
        formData.append('descripcion', '');

        const progressBar = crearBarraProgreso(file.name, 'video');

        fetch('/cursosApp/App/ajax/subir_contenido.ajax.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                progressBar.remove();
                if (data.success) {
                    mostrarNotificacion(data.mensaje, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    mostrarNotificacion(data.mensaje, 'error');
                }
            })
            .catch(error => {
                progressBar.remove();
                console.error('Error:', error);
                mostrarNotificacion('Error al subir el video', 'error');
            });
    }

    /**
     * Subir PDF
     */
    function subirPDF(file, seccionId) {
        const titulo = prompt('Título del documento:');
        if (!titulo) return;

        const formData = new FormData();
        formData.append('pdf', file);
        formData.append('accion', 'subirPDF');
        formData.append('idSeccion', seccionId);
        formData.append('titulo', titulo);
        formData.append('descripcion', '');

        const progressBar = crearBarraProgreso(file.name, 'pdf');

        fetch('/cursosApp/App/ajax/subir_contenido.ajax.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                progressBar.remove();
                if (data.success) {
                    mostrarNotificacion(data.mensaje, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    mostrarNotificacion(data.mensaje, 'error');
                }
            })
            .catch(error => {
                progressBar.remove();
                console.error('Error:', error);
                mostrarNotificacion('Error al subir el PDF', 'error');
            });
    }

    /**
     * Subir video promocional
     */
    function subirVideoPromocional(file) {
        const formData = new FormData();
        formData.append('video', file);
        formData.append('accion', 'subirVideoPromocional');
        formData.append('idCurso', cursoId);

        const progressBar = crearBarraProgreso(file.name, 'video');

        fetch('/cursosApp/App/ajax/subir_contenido.ajax.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                progressBar.remove();
                if (data.success) {
                    mostrarNotificacion(data.mensaje, 'success');
                    // Actualizar el video promocional en la página
                    setTimeout(() => location.reload(), 1000);
                } else {
                    mostrarNotificacion(data.mensaje, 'error');
                }
            })
            .catch(error => {
                progressBar.remove();
                console.error('Error:', error);
                mostrarNotificacion('Error al subir el video promocional', 'error');
            });
    }

    /**
     * Crear barra de progreso
     */
    function crearBarraProgreso(nombreArchivo, tipo) {
        const icono = tipo === 'video' ? 'bi-camera-video' : 'bi-file-earmark-pdf';
        const progressHtml = `
            <div class="upload-progress position-fixed" style="top: 20px; right: 20px; z-index: 1050; background: white; padding: 15px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); min-width: 300px;">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi ${icono} me-2 text-primary"></i>
                    <span class="filename fw-bold">${nombreArchivo}</span>
                </div>
                <div class="progress mb-2">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" 
                         role="progressbar" style="width: 100%"></div>
                </div>
                <small class="text-muted">Subiendo archivo...</small>
            </div>`;

        const div = document.createElement('div');
        div.innerHTML = progressHtml;
        const element = div.firstElementChild;
        document.body.appendChild(element);

        return element;
    }

    /**
     * Obtener categorías disponibles
     */
    async function obtenerCategorias() {
        try {
            const response = await fetch('/cursosApp/App/ajax/cursos.ajax.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    accion: 'obtenerCategorias'
                })
            });

            const data = await response.json();
            return data.success ? data.categorias : [];
        } catch (error) {
            console.error('Error al obtener categorías:', error);
            return [];
        }
    }

    /**
     * Mostrar notificación
     */
    function mostrarNotificacion(mensaje, tipo = 'info') {
        const iconos = {
            'success': 'success',
            'error': 'error',
            'warning': 'warning',
            'info': 'info'
        };

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: iconos[tipo],
                title: tipo === 'success' ? '¡Éxito!' : tipo === 'error' ? 'Error' : 'Información',
                text: mensaje,
                confirmButtonText: 'Aceptar',
                timer: tipo === 'success' ? 3000 : undefined
            });
        } else {
            // Fallback si SweetAlert no está disponible
            const tipoClass = tipo === 'success' ? 'alert-success' :
                tipo === 'error' ? 'alert-danger' :
                    tipo === 'warning' ? 'alert-warning' : 'alert-info';

            const alertHtml = `
                <div class="alert ${tipoClass} alert-dismissible fade show position-fixed" 
                     style="top: 20px; right: 20px; z-index: 1050; min-width: 300px;" role="alert">
                    ${mensaje}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>`;

            document.body.insertAdjacentHTML('beforeend', alertHtml);

            if (tipo === 'success') {
                setTimeout(() => {
                    const alert = document.querySelector('.alert:last-child');
                    if (alert) alert.remove();
                }, 3000);
            }
        }
    }

    /**
     * Funciones globales para compatibilidad
     */
    window.habilitarEdicion = habilitarEdicion;
});
