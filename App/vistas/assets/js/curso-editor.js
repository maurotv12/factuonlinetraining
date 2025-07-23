// Variables globales
let seccionesCount = 0;
let contenidoCount = 0;

// Inicializar cuando se carga la página
$(document).ready(function () {
    initializeSortable();
    bindEvents();
});

// Función para crear nueva sección
function agregarSeccion() {
    const seccionHtml = `
    <div class="card mb-3 seccion-card" data-seccion-id="nueva-${seccionesCount}">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="fas fa-grip-vertical me-2 text-muted sortable-handle"></i>
                <input type="text" class="form-control form-control-sm titulo-seccion" 
                       placeholder="Título de la sección" style="border: none; background: transparent;">
            </div>
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-primary btn-sm agregar-contenido">
                    <i class="fas fa-plus"></i> Contenido
                </button>
                <button class="btn btn-outline-danger btn-sm eliminar-seccion">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <textarea class="form-control form-control-sm mb-3 descripcion-seccion" 
                      rows="2" placeholder="Descripción de la sección (opcional)"></textarea>
            <div class="contenido-lista sortable-contenido">
                <!-- Los contenidos se agregarán aquí -->
            </div>
        </div>
    </div>`;

    $('#seccionesList').append(seccionHtml);
    seccionesCount++;
    initializeSortable();
}

// Función para agregar contenido a una sección
function agregarContenido(seccionCard, tipo = 'video') {
    const contenidoId = `contenido-${contenidoCount}`;
    const iconos = {
        'video': 'fas fa-play-circle text-primary',
        'pdf': 'fas fa-file-pdf text-danger',
        'texto': 'fas fa-file-text text-info'
    };

    const contenidoHtml = `
    <div class="contenido-item mb-2 p-3 border rounded" data-contenido-id="${contenidoId}">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center flex-grow-1">
                <i class="fas fa-grip-vertical me-2 text-muted sortable-handle"></i>
                <i class="${iconos[tipo]} me-2"></i>
                <input type="text" class="form-control form-control-sm titulo-contenido" 
                       placeholder="Título del contenido" style="border: none; background: transparent;">
            </div>
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-primary btn-sm editar-contenido" 
                        data-contenido-id="${contenidoId}" data-tipo="${tipo}">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-outline-danger btn-sm eliminar-contenido">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        <div class="mt-2">
            <small class="text-muted">Tipo: ${tipo.charAt(0).toUpperCase() + tipo.slice(1)}</small>
            <div class="archivo-info mt-1" style="display: none;">
                <small class="text-success">
                    <i class="fas fa-check-circle"></i> Archivo cargado
                </small>
            </div>
        </div>
        <input type="hidden" class="contenido-tipo" value="${tipo}">
        <input type="hidden" class="contenido-archivo-url" value="">
    </div>`;

    seccionCard.find('.contenido-lista').append(contenidoHtml);
    contenidoCount++;
    initializeSortable();
}

// Función para manejar eventos
function bindEvents() {
    // Agregar sección
    $(document).on('click', '#agregarSeccion', function () {
        agregarSeccion();
    });

    // Agregar contenido
    $(document).on('click', '.agregar-contenido', function () {
        const seccionCard = $(this).closest('.seccion-card');
        $('#modalTipoContenido').modal('show');
        $('#modalTipoContenido').data('seccion-card', seccionCard);
    });

    // Confirmar tipo de contenido
    $(document).on('click', '.btn-tipo-contenido', function () {
        const tipo = $(this).data('tipo');
        const seccionCard = $('#modalTipoContenido').data('seccion-card');
        agregarContenido(seccionCard, tipo);
        $('#modalTipoContenido').modal('hide');
    });

    // Editar contenido
    $(document).on('click', '.editar-contenido', function () {
        const contenidoId = $(this).data('contenido-id');
        const tipo = $(this).data('tipo');
        const contenidoItem = $(`[data-contenido-id="${contenidoId}"]`);

        // Llenar modal con datos actuales
        $('#modalContenido #tituloContenido').val(contenidoItem.find('.titulo-contenido').val());
        $('#modalContenido #tipoContenido').val(tipo);
        $('#modalContenido #descripcionContenido').val('');

        // Mostrar/ocultar campos según tipo
        toggleCamposPorTipo(tipo);

        $('#modalContenido').modal('show');
        $('#modalContenido').data('contenido-item', contenidoItem);
        $('#modalContenido').data('editing', true);
    });

    // Eliminar sección
    $(document).on('click', '.eliminar-seccion', function () {
        const seccionCard = $(this).closest('.seccion-card');

        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción eliminará la sección y todo su contenido",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                seccionCard.remove();
                Swal.fire('Eliminado', 'La sección ha sido eliminada', 'success');
            }
        });
    });

    // Eliminar contenido
    $(document).on('click', '.eliminar-contenido', function () {
        const contenidoItem = $(this).closest('.contenido-item');

        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción eliminará este contenido",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                contenidoItem.remove();
                Swal.fire('Eliminado', 'El contenido ha sido eliminado', 'success');
            }
        });
    });

    // Cambio de tipo de contenido en modal
    $('#tipoContenido').on('change', function () {
        toggleCamposPorTipo($(this).val());
    });

    // Guardar contenido desde modal
    $('#guardarContenido').on('click', function () {
        guardarContenido();
    });

    // Guardar curso
    $('#guardarCurso').on('click', function () {
        guardarCurso();
    });
}

// Función para mostrar/ocultar campos según tipo de contenido
function toggleCamposPorTipo(tipo) {
    const campoArchivo = $('#modalContenido .campo-archivo');
    const campoDuracion = $('#modalContenido .campo-duracion');

    if (tipo === 'video') {
        campoArchivo.show();
        campoDuracion.show();
        $('#archivoContenido').attr('accept', '.mp4,.avi,.mov,.wmv');
    } else if (tipo === 'pdf') {
        campoArchivo.show();
        campoDuracion.hide();
        $('#archivoContenido').attr('accept', '.pdf');
    } else {
        campoArchivo.hide();
        campoDuracion.hide();
    }
}

// Función para guardar contenido
function guardarContenido() {
    const formData = new FormData();
    const titulo = $('#tituloContenido').val().trim();
    const tipo = $('#tipoContenido').val();
    const descripcion = $('#descripcionContenido').val().trim();
    const duracion = $('#duracionContenido').val();
    const archivo = $('#archivoContenido')[0].files[0];

    if (!titulo) {
        Swal.fire('Error', 'El título es obligatorio', 'error');
        return;
    }

    if ((tipo === 'video' || tipo === 'pdf') && !archivo && !$('#modalContenido').data('editing')) {
        Swal.fire('Error', 'Debes seleccionar un archivo', 'error');
        return;
    }

    // Agregar datos al FormData
    formData.append('action', 'crear_contenido');
    formData.append('titulo', titulo);
    formData.append('tipo', tipo);
    formData.append('descripcion', descripcion);
    formData.append('duracion', duracion);

    if (archivo) {
        formData.append('archivo', archivo);
    }

    // Mostrar loading
    Swal.fire({
        title: 'Guardando...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Simular guardado (aquí iría la llamada AJAX real)
    setTimeout(() => {
        const contenidoItem = $('#modalContenido').data('contenido-item');

        // Actualizar datos del contenido
        contenidoItem.find('.titulo-contenido').val(titulo);
        contenidoItem.find('.contenido-tipo').val(tipo);

        if (archivo) {
            contenidoItem.find('.archivo-info').show();
        }

        Swal.fire('Éxito', 'Contenido guardado correctamente', 'success');
        $('#modalContenido').modal('hide');

        // Limpiar modal
        $('#modalContenido form')[0].reset();
    }, 1500);
}

// Función para guardar todo el curso
function guardarCurso() {
    const cursoData = recopilarDatosCurso();

    if (!validarDatosCurso(cursoData)) {
        return;
    }

    // Mostrar loading
    Swal.fire({
        title: 'Guardando curso...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Aquí iría la llamada AJAX para guardar todo
    console.log('Datos del curso:', cursoData);

    setTimeout(() => {
        Swal.fire('Éxito', 'Curso guardado correctamente', 'success');
    }, 2000);
}

// Función para recopilar todos los datos del curso
function recopilarDatosCurso() {
    const curso = {
        id: $('#cursoId').val(),
        titulo: $('#tituloCurso').val(),
        descripcion: $('#descripcionCurso').val(),
        precio: $('#precioCurso').val(),
        categoria: $('#categoriaCurso').val(),
        nivel: $('#nivelCurso').val(),
        duracion: $('#duracionCurso').val(),
        secciones: []
    };

    $('.seccion-card').each(function () {
        const seccion = {
            id: $(this).data('seccion-id'),
            titulo: $(this).find('.titulo-seccion').val(),
            descripcion: $(this).find('.descripcion-seccion').val(),
            orden: $(this).index() + 1,
            contenidos: []
        };

        $(this).find('.contenido-item').each(function () {
            const contenido = {
                id: $(this).data('contenido-id'),
                titulo: $(this).find('.titulo-contenido').val(),
                tipo: $(this).find('.contenido-tipo').val(),
                archivo_url: $(this).find('.contenido-archivo-url').val(),
                orden: $(this).index() + 1
            };
            seccion.contenidos.push(contenido);
        });

        curso.secciones.push(seccion);
    });

    return curso;
}

// Función para validar datos del curso
function validarDatosCurso(curso) {
    if (!curso.titulo.trim()) {
        Swal.fire('Error', 'El título del curso es obligatorio', 'error');
        return false;
    }

    if (!curso.descripcion.trim()) {
        Swal.fire('Error', 'La descripción del curso es obligatoria', 'error');
        return false;
    }

    if (curso.secciones.length === 0) {
        Swal.fire('Error', 'Debes agregar al menos una sección', 'error');
        return false;
    }

    for (let seccion of curso.secciones) {
        if (!seccion.titulo.trim()) {
            Swal.fire('Error', 'Todas las secciones deben tener título', 'error');
            return false;
        }
    }

    return true;
}

// Función para inicializar sortable (drag and drop)
function initializeSortable() {
    // Sortable para secciones
    if (typeof Sortable !== 'undefined') {
        const seccionesList = document.getElementById('seccionesList');
        if (seccionesList) {
            new Sortable(seccionesList, {
                handle: '.sortable-handle',
                animation: 150,
                onEnd: function (evt) {
                    // Actualizar orden en la base de datos si es necesario
                    console.log('Sección movida de', evt.oldIndex, 'a', evt.newIndex);
                }
            });
        }

        // Sortable para contenidos dentro de cada sección
        $('.sortable-contenido').each(function () {
            new Sortable(this, {
                handle: '.sortable-handle',
                animation: 150,
                onEnd: function (evt) {
                    console.log('Contenido movido de', evt.oldIndex, 'a', evt.newIndex);
                }
            });
        });
    }
}
