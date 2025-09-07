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
     * Inicializar gestión de secciones - Estructura simple
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

        // Inicializar botones de crear contenido
        inicializarBotonesCrearContenido();

        // Inicializar drag & drop para todas las áreas de subida
        inicializarDropAreas();
    }

    /**
     * Toggle para mostrar/ocultar contenido de sección
     */
    window.toggleSeccion = function (seccionId) {
        const content = document.getElementById(`seccion-content-${seccionId}`);
        if (content) {
            content.classList.toggle('show');

            // Si la sección se está mostrando, agregar el botón de crear contenido
            if (content.classList.contains('show')) {
                agregarBotonCrearContenido(seccionId);
            }
        }
    };

    /**
     * Inicializar áreas de drag & drop
     */
    function inicializarDropAreas() {
        const dropAreas = document.querySelectorAll('.drop-area');

        dropAreas.forEach(area => {
            const fileInput = area.querySelector('.file-input');

            // Click para seleccionar archivos
            area.addEventListener('click', () => fileInput.click());

            // Drag & drop events
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                area.addEventListener(eventName, preventDefaults, false);
            });

            ['dragenter', 'dragover'].forEach(eventName => {
                area.addEventListener(eventName, () => area.classList.add('dragover'), false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                area.addEventListener(eventName, () => area.classList.remove('dragover'), false);
            });

            area.addEventListener('drop', handleDrop, false);

            // File input change
            fileInput.addEventListener('change', function () {
                const files = this.files;
                const tipo = area.dataset.tipo;
                const seccionId = area.dataset.seccionId;
                handleFiles(files, tipo, seccionId);
            });
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        function handleDrop(e) {
            const files = e.dataTransfer.files;
            const tipo = e.currentTarget.dataset.tipo;
            const seccionId = e.currentTarget.dataset.seccionId;
            handleFiles(files, tipo, seccionId);
        }
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

            // Eliminar funcionalidad de drag & drop automática
            // Solo mostrar mensaje informativo
            mostrarNotificacion('Usa el botón "Crear Contenido" para subir archivos', 'info');
        }
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

    // ========================================
    // GESTIÓN DE CONTENIDO Y ASSETS
    // ========================================

    /**
     * Inicializar botones de crear contenido en cada sección
     */
    function inicializarBotonesCrearContenido() {
        // Agregar botones de crear contenido a cada sección visible
        const secciones = document.querySelectorAll('.seccion-container');
        secciones.forEach(seccion => {
            const seccionId = seccion.dataset.seccionId;
            const seccionContent = document.getElementById(`seccion-content-${seccionId}`);

            // Solo agregar botón si la sección está visible
            if (seccionContent && seccionContent.classList.contains('show')) {
                agregarBotonCrearContenido(seccionId);
            }
        });
    }

    /**
     * Agregar botón "Crear Contenido" a una sección
     */
    function agregarBotonCrearContenido(seccionId) {
        console.log('Agregando botón a sección:', seccionId);

        const seccionContent = document.getElementById(`seccion-content-${seccionId}`);
        if (!seccionContent) {
            console.log('No se encontró seccion-content para:', seccionId);
            return;
        }

        // Verificar si ya existe el botón
        if (seccionContent.querySelector('.btn-crear-contenido')) {
            console.log('Botón ya existe para sección:', seccionId);
            return;
        }

        // Crear el botón
        const btnCrearContenido = document.createElement('button');
        btnCrearContenido.className = 'btn btn-sm btn-success btn-crear-contenido mb-3';
        btnCrearContenido.innerHTML = '<i class="bi bi-plus-circle"></i> Crear Contenido';
        btnCrearContenido.onclick = () => mostrarModalCrearContenido(seccionId);

        // Insertar el botón después de la descripción pero antes del contenido existente
        const descripcion = seccionContent.querySelector('.seccion-description');
        const contenidoItems = seccionContent.querySelector('.contenido-items');

        if (descripcion) {
            descripcion.insertAdjacentElement('afterend', btnCrearContenido);
        } else if (contenidoItems) {
            contenidoItems.insertAdjacentElement('beforebegin', btnCrearContenido);
        } else {
            seccionContent.insertBefore(btnCrearContenido, seccionContent.firstChild);
        }

        console.log('Botón creado exitosamente para sección:', seccionId);
    }

    /**
     * Mostrar modal para crear contenido
     */
    function mostrarModalCrearContenido(seccionId) {
        const modal = document.getElementById('modalContenido') || crearModalContenido();

        // Limpiar formulario
        document.getElementById('contenido-id').value = '';
        document.getElementById('contenido-seccion-id').value = seccionId;
        document.getElementById('contenido-titulo').value = '';

        // Limpiar áreas de archivos
        document.getElementById('video-file-info').innerHTML = '';
        document.getElementById('pdf-files-info').innerHTML = '';

        // Resetear inputs de archivos
        document.getElementById('contenido-video').value = '';
        document.getElementById('contenido-pdfs').value = '';

        // Actualizar título del modal
        document.querySelector('#modalContenido .modal-title').textContent = 'Crear Nuevo Contenido';

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
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Gestionar Contenido</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form id="form-contenido">
                                <input type="hidden" id="contenido-id">
                                <input type="hidden" id="contenido-seccion-id">
                                
                                <!-- Información básica -->
                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="contenido-titulo" class="form-label">Título del contenido *</label>
                                            <input type="text" class="form-control" id="contenido-titulo" required maxlength="255">
                                        </div>
                                    </div>
                                </div>

                                <!-- Subida de archivos -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="contenido-video" class="form-label">Video (MP4 - Opcional)</label>
                                            <input type="file" class="form-control" id="contenido-video" accept="video/mp4">
                                            <div class="form-text">Máximo 1 video por contenido. Límite: 10 minutos, HD 1280x720, 100MB</div>
                                            <div id="video-file-info" class="mt-2"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="contenido-pdfs" class="form-label">Archivos PDF (Opcional)</label>
                                            <input type="file" class="form-control" id="contenido-pdfs" accept=".pdf" multiple>
                                            <div class="form-text">Múltiples PDFs permitidos. Máximo 10MB cada uno</div>
                                            <div id="pdf-files-info" class="mt-2"></div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-primary" id="btn-guardar-contenido">
                                <span class="btn-text">Crear Contenido</span>
                                <span class="btn-loading d-none">
                                    <span class="spinner-border spinner-border-sm me-1"></span>
                                    Procesando...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>`;

        document.body.insertAdjacentHTML('beforeend', modalHtml);

        // Agregar event listeners
        const btnGuardarContenido = document.getElementById('btn-guardar-contenido');
        btnGuardarContenido.addEventListener('click', guardarContenido);

        // Event listeners para mostrar info de archivos seleccionados
        document.getElementById('contenido-video').addEventListener('change', mostrarInfoVideo);
        document.getElementById('contenido-pdfs').addEventListener('change', mostrarInfoPDFs);

        return document.getElementById('modalContenido');
    }

    /**
     * Mostrar información del video seleccionado
     */
    function mostrarInfoVideo() {
        const fileInput = document.getElementById('contenido-video');
        const infoDiv = document.getElementById('video-file-info');

        if (fileInput.files.length > 0) {
            const file = fileInput.files[0];

            // Validar video
            if (!validarVideo(file)) {
                fileInput.value = '';
                infoDiv.innerHTML = '';
                return;
            }

            infoDiv.innerHTML = `
                <div class="alert alert-info py-2">
                    <i class="bi bi-camera-video"></i> 
                    <strong>${file.name}</strong> 
                    <small>(${(file.size / (1024 * 1024)).toFixed(2)} MB)</small>
                </div>`;
        } else {
            infoDiv.innerHTML = '';
        }
    }

    /**
     * Mostrar información de los PDFs seleccionados
     */
    function mostrarInfoPDFs() {
        const fileInput = document.getElementById('contenido-pdfs');
        const infoDiv = document.getElementById('pdf-files-info');

        if (fileInput.files.length > 0) {
            let html = '<div class="alert alert-info py-2"><i class="bi bi-file-pdf"></i> <strong>Archivos seleccionados:</strong><ul class="mb-0 mt-1">';

            for (let i = 0; i < fileInput.files.length; i++) {
                const file = fileInput.files[i];

                // Validar PDF
                if (!validarPDF(file)) {
                    fileInput.value = '';
                    infoDiv.innerHTML = '';
                    return;
                }

                html += `<li>${file.name} <small>(${(file.size / (1024 * 1024)).toFixed(2)} MB)</small></li>`;
            }

            html += '</ul></div>';
            infoDiv.innerHTML = html;
        } else {
            infoDiv.innerHTML = '';
        }
    }

    /**
     * Guardar contenido con assets
     */
    function guardarContenido() {
        const id = document.getElementById('contenido-id').value;
        const seccionId = document.getElementById('contenido-seccion-id').value;
        const titulo = document.getElementById('contenido-titulo').value.trim();
        const videoFile = document.getElementById('contenido-video').files[0];
        const pdfFiles = document.getElementById('contenido-pdfs').files;

        // Validaciones
        if (!titulo) {
            mostrarNotificacion('El título es obligatorio', 'error');
            return;
        }

        if (!seccionId) {
            mostrarNotificacion('Error: ID de sección no válido', 'error');
            return;
        }

        // Mostrar loading
        const btnGuardar = document.getElementById('btn-guardar-contenido');
        const btnText = btnGuardar.querySelector('.btn-text');
        const btnLoading = btnGuardar.querySelector('.btn-loading');

        btnText.classList.add('d-none');
        btnLoading.classList.remove('d-none');
        btnGuardar.disabled = true;

        // Crear contenido primero
        const datosContenido = {
            accion: id ? 'actualizarContenido' : 'crearContenido',
            idSeccion: seccionId,
            titulo: titulo
        };

        if (id) {
            datosContenido.id = id;
        }

        fetch('/cursosApp/App/ajax/curso_secciones.ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(datosContenido)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const contenidoId = data.id || id;

                    // Si hay archivos, subirlos
                    if (videoFile || pdfFiles.length > 0) {
                        return subirAssetsContenido(contenidoId, seccionId, videoFile, pdfFiles);
                    } else {
                        mostrarNotificacion(data.mensaje, 'success');
                        cerrarModalYRecargar();
                    }
                } else {
                    throw new Error(data.mensaje);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarNotificacion(error.message || 'Error al guardar contenido', 'error');
            })
            .finally(() => {
                // Restaurar botón
                btnText.classList.remove('d-none');
                btnLoading.classList.add('d-none');
                btnGuardar.disabled = false;
            });
    }

    /**
     * Subir assets del contenido
     */
    function subirAssetsContenido(contenidoId, seccionId, videoFile, pdfFiles) {
        const promesas = [];

        // Subir video si existe
        if (videoFile) {
            const formDataVideo = new FormData();
            formDataVideo.append('accion', 'subirVideoContenido');
            formDataVideo.append('video', videoFile);
            formDataVideo.append('idContenido', contenidoId);
            formDataVideo.append('idCurso', cursoId);
            formDataVideo.append('idSeccion', seccionId);

            const promesaVideo = fetch('/cursosApp/App/ajax/curso_secciones.ajax.php', {
                method: 'POST',
                body: formDataVideo
            }).then(response => response.json());

            promesas.push(promesaVideo);
        }

        // Subir PDFs si existen
        if (pdfFiles.length > 0) {
            for (let i = 0; i < pdfFiles.length; i++) {
                const formDataPDF = new FormData();
                formDataPDF.append('accion', 'subirPDFContenido');
                formDataPDF.append('pdf', pdfFiles[i]);
                formDataPDF.append('idContenido', contenidoId);
                formDataPDF.append('idCurso', cursoId);
                formDataPDF.append('idSeccion', seccionId);

                const promesaPDF = fetch('/cursosApp/App/ajax/curso_secciones.ajax.php', {
                    method: 'POST',
                    body: formDataPDF
                }).then(response => response.json());

                promesas.push(promesaPDF);
            }
        }

        return Promise.all(promesas)
            .then(resultados => {
                console.log('Resultados de subida de assets:', resultados);
                let todoExitoso = true;
                let mensajes = [];

                resultados.forEach(resultado => {
                    if (resultado.success) {
                        mensajes.push(resultado.mensaje);
                    } else {
                        todoExitoso = false;
                        mensajes.push(resultado.mensaje);
                    }
                });

                if (todoExitoso) {
                    mostrarNotificacion('Contenido y archivos guardados exitosamente', 'success');
                    cerrarModalYRecargar();
                } else {
                    mostrarNotificacion('Contenido guardado, pero algunos archivos no se pudieron subir: ' + mensajes.join(', '), 'warning');
                }
            });
    }

    /**
     * Cerrar modal y recargar página
     */
    function cerrarModalYRecargar() {
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalContenido'));
        if (modal) modal.hide();

        setTimeout(() => location.reload(), 1000);
    }

    /**
     * Editar contenido existente
     */
    window.editarContenido = function (contenidoId) {
        // Obtener datos del contenido
        fetch('/cursosApp/App/ajax/curso_secciones.ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                accion: 'obtenerAssetsContenido',
                idContenido: contenidoId
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.contenido) {
                    const contenido = data.contenido;
                    const modal = document.getElementById('modalContenido') || crearModalContenido();

                    // Llenar formulario
                    document.getElementById('contenido-id').value = contenido.id;
                    document.getElementById('contenido-seccion-id').value = contenido.id_seccion;
                    document.getElementById('contenido-titulo').value = contenido.titulo;

                    // Mostrar assets existentes
                    mostrarAssetsExistentes(data.assets);

                    // Actualizar título del modal
                    document.querySelector('#modalContenido .modal-title').textContent = 'Editar Contenido';
                    document.getElementById('btn-guardar-contenido').querySelector('.btn-text').textContent = 'Actualizar Contenido';

                    // Mostrar modal
                    const bsModal = new bootstrap.Modal(modal);
                    bsModal.show();
                } else {
                    mostrarNotificacion('Error al cargar datos del contenido', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarNotificacion('Error de conexión', 'error');
            });
    };

    /**
     * Mostrar assets existentes en el modal de edición
     */
    function mostrarAssetsExistentes(assets) {
        const videoInfo = document.getElementById('video-file-info');
        const pdfInfo = document.getElementById('pdf-files-info');

        // Limpiar info
        videoInfo.innerHTML = '';
        pdfInfo.innerHTML = '';

        if (!assets || assets.length === 0) return;

        // Separar assets por tipo
        const videos = assets.filter(asset => asset.asset_tipo === 'video');
        const pdfs = assets.filter(asset => asset.asset_tipo === 'pdf');

        // Mostrar videos existentes
        if (videos.length > 0) {
            let videoHtml = '<div class="alert alert-success py-2"><strong>Video actual:</strong><ul class="mb-0 mt-1">';
            videos.forEach(video => {
                videoHtml += `
                    <li class="d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-camera-video"></i> ${video.nombre_original}</span>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarAsset(${video.id}, ${video.id_contenido})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </li>`;
            });
            videoHtml += '</ul></div>';
            videoInfo.innerHTML = videoHtml;
        }

        // Mostrar PDFs existentes
        if (pdfs.length > 0) {
            let pdfHtml = '<div class="alert alert-success py-2"><strong>PDFs actuales:</strong><ul class="mb-0 mt-1">';
            pdfs.forEach(pdf => {
                pdfHtml += `
                    <li class="d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-file-pdf"></i> ${pdf.nombre_original}</span>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarAsset(${pdf.id}, ${pdf.id_contenido})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </li>`;
            });
            pdfHtml += '</ul></div>';
            pdfInfo.innerHTML = pdfHtml;
        }
    }

    /**
     * Eliminar asset
     */
    window.eliminarAsset = function (assetId, contenidoId) {
        if (!confirm('¿Estás seguro de que quieres eliminar este archivo?')) {
            return;
        }

        fetch('/cursosApp/App/ajax/curso_secciones.ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                accion: 'eliminarAsset',
                idAsset: assetId,
                idContenido: contenidoId
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarNotificacion(data.mensaje, 'success');
                    // Recargar assets en el modal
                    editarContenido(contenidoId);
                } else {
                    mostrarNotificacion(data.mensaje, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarNotificacion('Error de conexión', 'error');
            });
    };

    /**
     * Eliminar contenido completo
     */
    window.eliminarContenido = function (contenidoId) {
        if (!confirm('¿Estás seguro de que quieres eliminar este contenido y todos sus archivos asociados?')) {
            return;
        }

        fetch('/cursosApp/App/ajax/curso_secciones.ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                accion: 'eliminarContenido',
                id: contenidoId
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
    };

    /**
     * Funciones globales para compatibilidad
     */
    window.habilitarEdicion = habilitarEdicion;
});