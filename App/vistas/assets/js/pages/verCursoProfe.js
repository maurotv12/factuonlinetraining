/**
 * JavaScript para Ver Curso - Vista Profesor
 * Funcionalidades de edición dinámica, gestión de secciones, videos y archivos
 */

document.addEventListener('DOMContentLoaded', function () {
    // Elementos principales
    const cursoId = document.querySelector('[data-curso-id]')?.dataset.cursoId;

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
            const elemento = document.getElementById(`${campo}-display`);
            const btnEditar = document.getElementById(`btn-editar-${campo}`);

            if (elemento && btnEditar) {
                btnEditar.addEventListener('click', () => habilitarEdicion(campo));
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

        let inputHtml = '';

        switch (campo) {
            case 'nombre':
                inputHtml = `
                    <div class="input-group">
                        <input type="text" class="form-control" id="${campo}-input" value="${valor}" maxlength="255">
                        <button class="btn btn-success" onclick="guardarCampo('${campo}')">
                            <i class="bi bi-check"></i>
                        </button>
                        <button class="btn btn-secondary" onclick="cancelarEdicion('${campo}')">
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
                        <textarea class="form-control" id="${campo}-input" rows="5">${valor}</textarea>
                        <div class="mt-2">
                            <button class="btn btn-success" onclick="guardarCampo('${campo}')">
                                <i class="bi bi-check"></i> Guardar
                            </button>
                            <button class="btn btn-secondary" onclick="cancelarEdicion('${campo}')">
                                <i class="bi bi-x"></i> Cancelar
                            </button>
                        </div>
                    </div>`;
                break;

            case 'valor':
                const valorNumerico = valor.replace(/[^\d]/g, '');
                inputHtml = `
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" class="form-control" id="${campo}-input" value="${valorNumerico}" min="0">
                        <button class="btn btn-success" onclick="guardarCampo('${campo}')">
                            <i class="bi bi-check"></i>
                        </button>
                        <button class="btn btn-secondary" onclick="cancelarEdicion('${campo}')">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>`;
                break;

            case 'id_categoria':
                // Obtener categorías disponibles
                obtenerCategorias().then(categorias => {
                    const categoriaActual = display.textContent.trim();
                    let optionsHtml = '';

                    categorias.forEach(cat => {
                        const selected = cat.nombre === categoriaActual ? 'selected' : '';
                        optionsHtml += `<option value="${cat.id}" ${selected}>${cat.nombre}</option>`;
                    });

                    display.innerHTML = `
                        <div class="input-group">
                            <select class="form-control" id="${campo}-input">
                                ${optionsHtml}
                            </select>
                            <button class="btn btn-success" onclick="guardarCampo('${campo}')">
                                <i class="bi bi-check"></i>
                            </button>
                            <button class="btn btn-secondary" onclick="cancelarEdicion('${campo}')">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>`;
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

        if (!valor && campo !== 'valor') {
            mostrarNotificacion('El campo no puede estar vacío', 'warning');
            return;
        }

        // Validaciones específicas
        if (campo === 'nombre' && valor.length < 10) {
            mostrarNotificacion('El nombre debe tener al menos 10 caracteres', 'warning');
            return;
        }

        // Mostrar loading
        const btnGuardar = event.target;
        btnGuardar.innerHTML = '<i class="spinner-border spinner-border-sm"></i>';
        btnGuardar.disabled = true;

        // Enviar datos al servidor
        fetch('/cursosApp/App/ajax/cursos.ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                accion: 'actualizarCampo',
                idCurso: cursoId,
                campo: campo,
                valor: valor
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarNotificacion('Campo actualizado correctamente', 'success');
                    actualizarVisualizacionCampo(campo, valor, data.valorFormateado);
                } else {
                    mostrarNotificacion(data.mensaje || 'Error al actualizar', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarNotificacion('Error al actualizar el campo', 'error');
            })
            .finally(() => {
                btnGuardar.innerHTML = '<i class="bi bi-check"></i>';
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
        display.dataset.valorOriginal = valorFormateado || valor;
        display.innerHTML = valorFormateado || valor;
    }

    /**
     * Inicializar reproductor de video
     */
    function inicializarReproductorVideo() {
        const video = document.getElementById('videoPlayer');
        if (!video) return;

        // Configurar controles personalizados si es necesario
        video.addEventListener('loadedmetadata', function () {
            console.log('Video cargado:', {
                duración: this.duration,
                resolución: `${this.videoWidth}x${this.videoHeight}`
            });
        });

        video.addEventListener('error', function () {
            console.error('Error al cargar el video');
            mostrarNotificacion('Error al cargar el video promocional', 'error');
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

        // Event listeners para secciones existentes
        document.querySelectorAll('.seccion-item').forEach(seccion => {
            const id = seccion.dataset.seccionId;

            // Botón editar sección
            const btnEditar = seccion.querySelector('.btn-editar-seccion');
            if (btnEditar) {
                btnEditar.addEventListener('click', () => editarSeccion(id));
            }

            // Botón eliminar sección
            const btnEliminar = seccion.querySelector('.btn-eliminar-seccion');
            if (btnEliminar) {
                btnEliminar.addEventListener('click', () => eliminarSeccion(id));
            }

            // Botón agregar contenido
            const btnAgregarContenido = seccion.querySelector('.btn-agregar-contenido');
            if (btnAgregarContenido) {
                btnAgregarContenido.addEventListener('click', () => mostrarModalNuevoContenido(id));
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
            mostrarNotificacion('El título es obligatorio', 'warning');
            return;
        }

        const btnGuardar = document.getElementById('btn-guardar-seccion');
        btnGuardar.innerHTML = '<i class="spinner-border spinner-border-sm"></i> Guardando...';
        btnGuardar.disabled = true;

        const accion = id ? 'actualizarSeccion' : 'crearSeccion';

        fetch('/cursosApp/App/ajax/curso_secciones.ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                accion: accion,
                id: id,
                idCurso: cursoId,
                titulo: titulo,
                descripcion: descripcion
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarNotificacion(`Sección ${id ? 'actualizada' : 'creada'} correctamente`, 'success');
                    bootstrap.Modal.getInstance(document.getElementById('modalSeccion')).hide();
                    location.reload(); // Recargar para mostrar cambios
                } else {
                    mostrarNotificacion(data.mensaje || 'Error al guardar', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarNotificacion('Error al guardar la sección', 'error');
            })
            .finally(() => {
                btnGuardar.innerHTML = 'Guardar';
                btnGuardar.disabled = false;
            });
    }

    /**
     * Inicializar subida de archivos
     */
    function inicializarSubidaArchivos() {
        // Event listeners para inputs de archivos
        document.querySelectorAll('.file-input').forEach(input => {
            input.addEventListener('change', function (e) {
                const files = e.target.files;
                const dropArea = this.closest('.drop-area');
                const tipo = dropArea.dataset.tipo;
                const seccionId = dropArea.dataset.seccionId;

                handleFiles(files, tipo, seccionId);
            });
        });

        // Event listeners para botones de crear primera sección
        const btnCrearPrimera = document.getElementById('btn-crear-primera-seccion');
        if (btnCrearPrimera) {
            btnCrearPrimera.addEventListener('click', mostrarModalNuevaSeccion);
        }
    }

    /**
     * Editar sección existente
     */
    window.editarSeccion = function (id) {
        // Obtener datos de la sección
        const seccionElement = document.querySelector(`[data-seccion-id="${id}"]`);
        const titulo = seccionElement.querySelector('.accordion-button span').textContent;
        const descripcion = seccionElement.querySelector('.text-muted')?.textContent || '';

        const modal = document.getElementById('modalSeccion') || crearModalSeccion();

        // Llenar formulario con datos existentes
        document.getElementById('seccion-id').value = id;
        document.getElementById('seccion-titulo').value = titulo;
        document.getElementById('seccion-descripcion').value = descripcion;
        document.querySelector('#modalSeccion .modal-title').textContent = 'Editar Sección';

        // Mostrar modal
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
    };

    /**
     * Eliminar sección
     */
    window.eliminarSeccion = function (id) {
        if (!confirm('¿Estás seguro de eliminar esta sección? Se eliminará todo su contenido.')) {
            return;
        }

        fetch('/cursosApp/App/ajax/curso_secciones.ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                accion: 'eliminarSeccion',
                id: id
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarNotificacion('Sección eliminada correctamente', 'success');
                    // Eliminar elemento del DOM
                    document.querySelector(`[data-seccion-id="${id}"]`).remove();

                    // Si no quedan secciones, mostrar mensaje vacío
                    if (document.querySelectorAll('.seccion-item').length === 0) {
                        location.reload();
                    }
                } else {
                    mostrarNotificacion(data.mensaje || 'Error al eliminar', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarNotificacion('Error al eliminar la sección', 'error');
            });
    };

    /**
     * Mostrar modal para nuevo contenido
     */
    function mostrarModalNuevoContenido(seccionId) {
        const modal = document.getElementById('modalContenido') || crearModalContenido();

        // Limpiar formulario
        document.getElementById('contenido-seccion-id').value = seccionId;
        document.getElementById('contenido-titulo').value = '';
        document.getElementById('contenido-descripcion').value = '';

        // Mostrar modal
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
    }

    /**
     * Crear modal para contenido
     */
    function crearModalContenido() {
        const modalHtml = `
            <div class="modal fade" id="modalContenido" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Agregar Contenido</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form id="form-contenido">
                                <input type="hidden" id="contenido-seccion-id">
                                <div class="mb-3">
                                    <label for="contenido-titulo" class="form-label">Título *</label>
                                    <input type="text" class="form-control" id="contenido-titulo" required>
                                </div>
                                <div class="mb-3">
                                    <label for="contenido-descripcion" class="form-label">Descripción</label>
                                    <textarea class="form-control" id="contenido-descripcion" rows="2"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Tipo de contenido</label>
                                    <div class="row">
                                        <div class="col-6">
                                            <label class="upload-option">
                                                <input type="radio" name="tipo-contenido" value="video" checked>
                                                <div class="upload-card">
                                                    <i class="bi bi-camera-video"></i>
                                                    <span>Video HD</span>
                                                    <small>MP4, máx 10min</small>
                                                </div>
                                            </label>
                                        </div>
                                        <div class="col-6">
                                            <label class="upload-option">
                                                <input type="radio" name="tipo-contenido" value="pdf">
                                                <div class="upload-card">
                                                    <i class="bi bi-file-pdf"></i>
                                                    <span>Documento PDF</span>
                                                    <small>Máx 10MB</small>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="archivo-contenido" class="form-label">Archivo *</label>
                                    <input type="file" class="form-control" id="archivo-contenido" required>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-primary" id="btn-subir-contenido">
                                <i class="bi bi-upload"></i> Subir Contenido
                            </button>
                        </div>
                    </div>
                </div>
            </div>`;

        document.body.insertAdjacentHTML('beforeend', modalHtml);

        // Event listeners
        document.getElementById('btn-subir-contenido').addEventListener('click', subirContenido);

        // Cambiar input file según tipo seleccionado
        document.querySelectorAll('input[name="tipo-contenido"]').forEach(radio => {
            radio.addEventListener('change', function () {
                const fileInput = document.getElementById('archivo-contenido');
                if (this.value === 'video') {
                    fileInput.accept = 'video/mp4,video/avi,video/mov';
                } else if (this.value === 'pdf') {
                    fileInput.accept = '.pdf';
                }
            });
        });

        return document.getElementById('modalContenido');
    }

    /**
     * Subir contenido desde modal
     */
    function subirContenido() {
        const seccionId = document.getElementById('contenido-seccion-id').value;
        const titulo = document.getElementById('contenido-titulo').value.trim();
        const descripcion = document.getElementById('contenido-descripcion').value.trim();
        const tipo = document.querySelector('input[name="tipo-contenido"]:checked').value;
        const archivo = document.getElementById('archivo-contenido').files[0];

        if (!titulo || !archivo) {
            mostrarNotificacion('Por favor completa todos los campos requeridos', 'warning');
            return;
        }

        // Validar archivo
        if (tipo === 'video') {
            if (!validarVideo(archivo)) return;
        } else if (tipo === 'pdf') {
            if (!validarPDF(archivo)) return;
        }

        const btnSubir = document.getElementById('btn-subir-contenido');
        btnSubir.innerHTML = '<i class="spinner-border spinner-border-sm"></i> Subiendo...';
        btnSubir.disabled = true;

        // Crear FormData
        const formData = new FormData();
        formData.append(tipo, archivo);
        formData.append('accion', tipo === 'video' ? 'subirVideo' : 'subirPDF');
        formData.append('seccionId', seccionId);
        formData.append('cursoId', cursoId);
        formData.append('titulo', titulo);
        formData.append('descripcion', descripcion);

        fetch('/cursosApp/App/ajax/subir_contenido.ajax.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarNotificacion('Contenido subido correctamente', 'success');
                    bootstrap.Modal.getInstance(document.getElementById('modalContenido')).hide();
                    actualizarListaContenido(seccionId);
                } else {
                    mostrarNotificacion(data.mensaje || 'Error al subir contenido', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarNotificacion('Error al subir el contenido', 'error');
            })
            .finally(() => {
                btnSubir.innerHTML = '<i class="bi bi-upload"></i> Subir Contenido';
                btnSubir.disabled = false;
            });
    }

    /**
     * Actualizar lista de contenido de una sección
     */
    function actualizarListaContenido(seccionId) {
        fetch('/cursosApp/App/ajax/obtener_contenido_seccion.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                seccionId: seccionId
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const contenedorContenido = document.getElementById(`contenido-seccion-${seccionId}`);
                    if (contenedorContenido) {
                        contenedorContenido.innerHTML = generarHTMLContenido(data.contenido);
                    }
                }
            })
            .catch(error => {
                console.error('Error al actualizar contenido:', error);
            });
    }

    /**
     * Generar HTML para lista de contenido
     */
    function generarHTMLContenido(contenido) {
        if (!contenido || contenido.length === 0) {
            return '';
        }

        let html = '<div class="contenido-lista">';
        contenido.forEach(item => {
            const icono = item.tipo === 'video' ? 'camera-video' : 'file-pdf';
            const duracion = item.duracion ? ` (${item.duracion})` : '';

            html += `
                <div class="contenido-item">
                    <i class="bi bi-${icono}"></i>
                    <span>${item.titulo}</span>
                    ${duracion ? `<small class="text-muted">${duracion}</small>` : ''}
                </div>`;
        });
        html += '</div>';

        return html;
    }

    /**
     * Inicializar drag & drop
     */
    function inicializarDragDrop() {
        const dropAreas = document.querySelectorAll('.drop-area');

        dropAreas.forEach(area => {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                area.addEventListener(eventName, preventDefaults, false);
                document.body.addEventListener(eventName, preventDefaults, false);
            });

            ['dragenter', 'dragover'].forEach(eventName => {
                area.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                area.addEventListener(eventName, unhighlight, false);
            });

            area.addEventListener('drop', handleDrop, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        function highlight(e) {
            e.target.closest('.drop-area').classList.add('drag-over');
        }

        function unhighlight(e) {
            e.target.closest('.drop-area').classList.remove('drag-over');
        }

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            const dropArea = e.target.closest('.drop-area');
            const tipo = dropArea.dataset.tipo;
            const seccionId = dropArea.dataset.seccionId;

            handleFiles(files, tipo, seccionId);
        }
    }

    /**
     * Manejar archivos subidos
     */
    function handleFiles(files, tipo, seccionId) {
        ([...files]).forEach(file => {
            if (tipo === 'video') {
                if (validarVideo(file)) {
                    subirVideo(file, seccionId);
                }
            } else if (tipo === 'pdf') {
                if (validarPDF(file)) {
                    subirPDF(file, seccionId);
                }
            }
        });
    }

    /**
     * Validar archivo de video
     */
    function validarVideo(file) {
        const tiposPermitidos = ['video/mp4', 'video/avi', 'video/mov'];
        const tamañoMaximo = 100 * 1024 * 1024; // 100MB

        if (!tiposPermitidos.includes(file.type)) {
            mostrarNotificacion('Solo se permiten videos MP4, AVI o MOV', 'warning');
            return false;
        }

        if (file.size > tamañoMaximo) {
            mostrarNotificacion('El video no puede superar los 100MB', 'warning');
            return false;
        }

        return true;
    }

    /**
     * Validar archivo PDF
     */
    function validarPDF(file) {
        const tiposPermitidos = ['application/pdf'];
        const tamañoMaximo = 10 * 1024 * 1024; // 10MB

        if (!tiposPermitidos.includes(file.type)) {
            mostrarNotificacion('Solo se permiten archivos PDF', 'warning');
            return false;
        }

        if (file.size > tamañoMaximo) {
            mostrarNotificacion('El PDF no puede superar los 10MB', 'warning');
            return false;
        }

        return true;
    }

    /**
     * Subir video
     */
    function subirVideo(file, seccionId) {
        const formData = new FormData();
        formData.append('video', file);
        formData.append('accion', 'subirVideo');
        formData.append('seccionId', seccionId);
        formData.append('cursoId', cursoId);

        // Crear barra de progreso
        const progressContainer = crearBarraProgreso(file.name, 'video');

        fetch('/cursosApp/App/ajax/subir_contenido.ajax.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarNotificacion('Video subido correctamente', 'success');
                    actualizarListaContenido(seccionId);
                } else {
                    mostrarNotificacion(data.mensaje || 'Error al subir video', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarNotificacion('Error al subir el video', 'error');
            })
            .finally(() => {
                progressContainer.remove();
            });
    }

    /**
     * Subir PDF
     */
    function subirPDF(file, seccionId) {
        const formData = new FormData();
        formData.append('pdf', file);
        formData.append('accion', 'subirPDF');
        formData.append('seccionId', seccionId);
        formData.append('cursoId', cursoId);

        const progressContainer = crearBarraProgreso(file.name, 'pdf');

        fetch('/cursosApp/App/ajax/subir_contenido.ajax.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarNotificacion('PDF subido correctamente', 'success');
                    actualizarListaContenido(seccionId);
                } else {
                    mostrarNotificacion(data.mensaje || 'Error al subir PDF', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarNotificacion('Error al subir el PDF', 'error');
            })
            .finally(() => {
                progressContainer.remove();
            });
    }

    /**
     * Crear barra de progreso
     */
    function crearBarraProgreso(nombreArchivo, tipo) {
        const container = document.createElement('div');
        container.className = 'upload-progress mb-2';
        container.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="bi bi-${tipo === 'video' ? 'camera-video' : 'file-pdf'} me-2"></i>
                <span class="filename flex-grow-1">${nombreArchivo}</span>
                <div class="spinner-border spinner-border-sm ms-2" role="status"></div>
            </div>
        `;

        document.body.appendChild(container);
        return container;
    }

    /**
     * Obtener categorías disponibles
     */
    async function obtenerCategorias() {
        try {
            const response = await fetch('/cursosApp/App/ajax/obtenerCategorias.php');
            const data = await response.json();
            return data.categorias || [];
        } catch (error) {
            console.error('Error al obtener categorías:', error);
            return [];
        }
    }

    /**
     * Mostrar notificación
     */
    function mostrarNotificacion(mensaje, tipo = 'info') {
        // Crear elemento de notificación
        const notification = document.createElement('div');
        notification.className = `alert alert-${tipo === 'error' ? 'danger' : tipo} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 400px;';
        notification.innerHTML = `
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(notification);

        // Auto-remover después de 5 segundos
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }
});