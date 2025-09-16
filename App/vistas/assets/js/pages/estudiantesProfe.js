/**
 * JavaScript para gestión de estudiantes del profesor
 * Manejo de tabla de estudiantes con inscripciones pendientes
 */

// Variables globales
let datosEstudiantes = [];
let datosOriginales = [];

// Inicialización cuando el documento esté listo
$(document).ready(function () {
    console.log('Iniciando estudiantesProfe.js');
    inicializarEventos();
    cargarEstudiantes();
});

/**
 * Inicializar todos los eventos de la página
 */
function inicializarEventos() {
    // Botón refrescar
    $('#btnRefrescar').on('click', function () {
        cargarEstudiantes();
    });

    // Búsqueda en tiempo real
    $('#buscarEstudiante').on('input', function () {
        filtrarTabla();
    });

    // Filtros
    $('#filtroEstado, #filtroCurso').on('change', function () {
        filtrarTabla();
    });

    // Confirmar activación
    $('#btnConfirmarActivacion').on('click', function () {
        activarInscripcion();
    });

    console.log('Eventos inicializados correctamente');
}

/**
 * Cargar estudiantes desde el servidor
 */
function cargarEstudiantes() {
    console.log('Cargando estudiantes...');

    // Mostrar loading
    $('#loadingEstudiantes').show();
    $('#tablaEstudiantes, #sinDatos').hide();

    $.ajax({
        url: 'ajax/usuarios.ajax.php',
        method: 'POST',
        data: {
            accion: 'cargar_estudiantes_pendientes'
        },
        dataType: 'json',
        success: function (response) {
            console.log('Respuesta del servidor:', response);

            if (response.success) {
                datosEstudiantes = response.data || [];
                datosOriginales = [...datosEstudiantes];

                construirTabla(datosEstudiantes);
                llenarFiltros(datosEstudiantes);
                actualizarContadores(datosEstudiantes);

                $('#loadingEstudiantes').hide();

                if (datosEstudiantes.length > 0) {
                    $('#tablaEstudiantes').show();
                } else {
                    $('#sinDatos').show();
                }
            } else {
                console.error('Error en la respuesta:', response.message);
                mostrarError('Error al cargar estudiantes: ' + response.message);
                $('#loadingEstudiantes').hide();
                $('#sinDatos').show();
            }
        },
        error: function (xhr, status, error) {
            console.error('Error AJAX:', error);
            mostrarError('Error de conexión al cargar estudiantes');
            $('#loadingEstudiantes').hide();
            $('#sinDatos').show();
        }
    });
}

/**
 * Construir la tabla HTML con los datos de estudiantes
 */
function construirTabla(estudiantes) {
    console.log('Construyendo tabla con', estudiantes.length, 'estudiantes');

    const tbody = $('#bodyTablaEstudiantes');
    tbody.empty();

    if (estudiantes.length === 0) {
        return;
    }

    estudiantes.forEach(function (estudiante) {
        const fila = construirFilaEstudiante(estudiante);
        tbody.append(fila);
    });

    console.log('Tabla construida correctamente');
}

/**
 * Construir una fila individual de estudiante
 */
function construirFilaEstudiante(estudiante) {
    // Obtener iniciales para el avatar
    const iniciales = obtenerIniciales(estudiante.nombre_completo);

    // Construir badges de cursos pendientes
    const cursosPendientes = estudiante.cursos_pendientes || [];
    const cursosActivos = estudiante.cursos_activos || [];

    const badgesPendientes = cursosPendientes.map(curso =>
        `<span class="curso-badge curso-pendiente" title="${curso.nombre_curso}">
            ${curso.nombre_curso.substring(0, 20)}${curso.nombre_curso.length > 20 ? '...' : ''}
        </span>`
    ).join('');

    const badgesActivos = cursosActivos.map(curso =>
        `<span class="curso-badge curso-activo" title="${curso.nombre_curso}">
            ${curso.nombre_curso.substring(0, 20)}${curso.nombre_curso.length > 20 ? '...' : ''}
        </span>`
    ).join('');

    return `
        <tr>
            <td>
                <div class="student-info">
                    <div class="student-avatar">${iniciales}</div>
                    <div class="student-details">
                        <div class="student-name">${estudiante.nombre_completo}</div>
                        <div class="student-id">ID: ${estudiante.idU}</div>
                    </div>
                </div>
            </td>
            <td>
                <i class="fas fa-envelope text-muted me-1"></i>
                ${estudiante.email || 'Sin email'}
            </td>
            <td>
                <i class="fas fa-phone text-muted me-1"></i>
                ${estudiante.telefono || 'Sin teléfono'}
            </td>
            <td>
                <div class="d-flex flex-wrap">
                    ${badgesPendientes || '<span class="text-muted">Sin pendientes</span>'}
                </div>
                <small class="text-muted">${cursosPendientes.length} curso(s)</small>
            </td>
            <td>
                <div class="d-flex flex-wrap">
                    ${badgesActivos || '<span class="text-muted">Sin activos</span>'}
                </div>
                <small class="text-muted">${cursosActivos.length} curso(s)</small>
            </td>
            <td>
                <div class="btn-group" role="group">
                    <button class="btn btn-primary btn-sm" onclick="verDetallesEstudiante(${estudiante.idU})" title="Ver detalles">
                        <i class="fas fa-eye"></i>
                    </button>
                    ${cursosPendientes.length > 0 ? `
                        <button class="btn btn-success btn-sm" onclick="mostrarModalActivacion(${estudiante.idU})" title="Activar inscripciones">
                            <i class="fas fa-check"></i>
                        </button>
                    ` : ''}
                </div>
            </td>
        </tr>
    `;
}

/**
 * Obtener iniciales del nombre completo
 */
function obtenerIniciales(nombreCompleto) {
    if (!nombreCompleto) return 'U';

    const palabras = nombreCompleto.trim().split(' ');
    if (palabras.length === 1) {
        return palabras[0].substring(0, 2).toUpperCase();
    }

    return (palabras[0].charAt(0) + palabras[1].charAt(0)).toUpperCase();
}

/**
 * Llenar los filtros con datos únicos
 */
function llenarFiltros(estudiantes) {
    const selectCurso = $('#filtroCurso');
    const cursosUnicos = new Set();

    // Obtener todos los cursos únicos
    estudiantes.forEach(estudiante => {
        const todosCursos = [
            ...(estudiante.cursos_pendientes || []),
            ...(estudiante.cursos_activos || [])
        ];

        todosCursos.forEach(curso => {
            cursosUnicos.add(JSON.stringify({
                id: curso.idCurso,
                nombre: curso.nombre_curso
            }));
        });
    });

    // Limpiar y llenar select de cursos
    selectCurso.find('option:not(:first)').remove();

    Array.from(cursosUnicos)
        .map(cursoStr => JSON.parse(cursoStr))
        .sort((a, b) => a.nombre.localeCompare(b.nombre))
        .forEach(curso => {
            selectCurso.append(`<option value="${curso.id}">${curso.nombre}</option>`);
        });

    console.log('Filtros actualizados con', cursosUnicos.size, 'cursos únicos');
}

/**
 * Filtrar tabla según criterios de búsqueda
 */
function filtrarTabla() {
    const textoBusqueda = $('#buscarEstudiante').val().toLowerCase();
    const filtroEstado = $('#filtroEstado').val();
    const filtroCurso = $('#filtroCurso').val();

    console.log('Filtrando con:', { textoBusqueda, filtroEstado, filtroCurso });

    const estudiantesFiltrados = datosOriginales.filter(estudiante => {
        // Filtro por texto
        const coincideTexto = !textoBusqueda ||
            estudiante.nombre_completo.toLowerCase().includes(textoBusqueda) ||
            estudiante.email.toLowerCase().includes(textoBusqueda);

        // Filtro por estado
        let coincideEstado = true;
        if (filtroEstado) {
            const cursosPendientes = estudiante.cursos_pendientes || [];
            const cursosActivos = estudiante.cursos_activos || [];

            switch (filtroEstado) {
                case 'pendiente':
                    coincideEstado = cursosPendientes.length > 0;
                    break;
                case 'activo':
                    coincideEstado = cursosActivos.length > 0;
                    break;
                case 'cancelado':
                    // Implementar lógica para cancelados si es necesario
                    coincideEstado = false;
                    break;
            }
        }

        // Filtro por curso
        let coincideCurso = true;
        if (filtroCurso) {
            const todosCursos = [
                ...(estudiante.cursos_pendientes || []),
                ...(estudiante.cursos_activos || [])
            ];
            coincideCurso = todosCursos.some(curso => curso.idCurso == filtroCurso);
        }

        return coincideTexto && coincideEstado && coincideCurso;
    });

    construirTabla(estudiantesFiltrados);
    actualizarContadores(estudiantesFiltrados);
}

/**
 * Actualizar contadores en la interfaz
 */
function actualizarContadores(estudiantes) {
    $('#totalEstudiantes').text(estudiantes.length + ' estudiante' + (estudiantes.length !== 1 ? 's' : ''));
}

/**
 * Ver detalles de un estudiante específico
 */
function verDetallesEstudiante(idEstudiante) {
    console.log('Viendo detalles del estudiante:', idEstudiante);

    const estudiante = datosEstudiantes.find(est => est.idU == idEstudiante);
    if (!estudiante) {
        mostrarError('Estudiante no encontrado');
        return;
    }

    // Construir contenido del modal
    const contenido = `
        <div class="row">
            <div class="col-md-6">
                <h6 class="fw-bold mb-3">Información Personal</h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <strong>Nombre:</strong> ${estudiante.nombre_completo}
                    </li>
                    <li class="mb-2">
                        <strong>Email:</strong> ${estudiante.email || 'Sin email'}
                    </li>
                    <li class="mb-2">
                        <strong>Teléfono:</strong> ${estudiante.telefono || 'Sin teléfono'}
                    </li>
                    <li class="mb-2">
                        <strong>ID Usuario:</strong> ${estudiante.idU}
                    </li>
                </ul>
            </div>
            <div class="col-md-6">
                <h6 class="fw-bold mb-3">Estadísticas</h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <strong>Inscripciones Pendientes:</strong> 
                        <span class="badge bg-warning">${(estudiante.cursos_pendientes || []).length}</span>
                    </li>
                    <li class="mb-2">
                        <strong>Inscripciones Activas:</strong> 
                        <span class="badge bg-success">${(estudiante.cursos_activos || []).length}</span>
                    </li>
                </ul>
            </div>
        </div>
        
        <hr>
        
        <div class="row">
            <div class="col-12">
                <h6 class="fw-bold mb-3">Cursos Pendientes</h6>
                ${construirListaCursos(estudiante.cursos_pendientes || [], 'pendiente')}
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-12">
                <h6 class="fw-bold mb-3">Cursos Activos</h6>
                ${construirListaCursos(estudiante.cursos_activos || [], 'activo')}
            </div>
        </div>
    `;

    $('#contenidoModalEstudiante').html(contenido);
    $('#modalDetallesEstudiante').modal('show');
}

/**
 * Construir lista de cursos para el modal
 */
function construirListaCursos(cursos, tipo) {
    if (cursos.length === 0) {
        return `<p class="text-muted">No hay cursos ${tipo}s</p>`;
    }

    const badgeClass = tipo === 'pendiente' ? 'bg-warning' : 'bg-success';

    return `
        <div class="row">
            ${cursos.map(curso => `
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">${curso.nombre_curso}</h6>
                            <p class="card-text small text-muted">
                                <i class="fas fa-calendar me-1"></i>
                                Inscrito: ${curso.fecha_inscripcion || 'No disponible'}
                            </p>
                            <span class="badge ${badgeClass}">${tipo.charAt(0).toUpperCase() + tipo.slice(1)}</span>
                            ${tipo === 'pendiente' ? `
                                <button class="btn btn-sm btn-success ms-2" onclick="activarInscripcionDirecta(${curso.idInscripcion})">
                                    <i class="fas fa-check me-1"></i>
                                    Activar
                                </button>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `).join('')}
        </div>
    `;
}

/**
 * Mostrar modal de confirmación para activar inscripción
 */
function mostrarModalActivacion(idEstudiante) {
    console.log('Mostrando modal de activación para estudiante:', idEstudiante);

    const estudiante = datosEstudiantes.find(est => est.idU == idEstudiante);
    if (!estudiante) {
        mostrarError('Estudiante no encontrado');
        return;
    }

    const cursosPendientes = estudiante.cursos_pendientes || [];
    if (cursosPendientes.length === 0) {
        mostrarError('Este estudiante no tiene inscripciones pendientes');
        return;
    }

    // Construir datos para el modal
    const contenido = `
        <div class="mb-3">
            <strong>Estudiante:</strong> ${estudiante.nombre_completo}
        </div>
        <div class="mb-3">
            <strong>Cursos pendientes:</strong>
        </div>
        <div class="list-group">
            ${cursosPendientes.map(curso => `
                <div class="list-group-item">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="${curso.idInscripcion}" 
                               id="curso_${curso.idInscripcion}" checked>
                        <label class="form-check-label" for="curso_${curso.idInscripcion}">
                            ${curso.nombre_curso}
                            <small class="text-muted d-block">Inscrito: ${curso.fecha_inscripcion}</small>
                        </label>
                    </div>
                </div>
            `).join('')}
        </div>
    `;

    $('#datosActivacion').html(contenido);
    $('#modalConfirmarActivacion').modal('show');
}

/**
 * Activar inscripción desde el modal de confirmación
 */
function activarInscripcion() {
    const inscripcionesSeleccionadas = $('#datosActivacion input[type="checkbox"]:checked');

    if (inscripcionesSeleccionadas.length === 0) {
        mostrarError('Selecciona al menos una inscripción para activar');
        return;
    }

    const promesas = [];

    inscripcionesSeleccionadas.each(function () {
        const idInscripcion = $(this).val();
        promesas.push(activarInscripcionAjax(idInscripcion));
    });

    // Ejecutar todas las activaciones
    Promise.all(promesas)
        .then(resultados => {
            const exitosos = resultados.filter(r => r.success).length;
            const fallidos = resultados.length - exitosos;

            if (exitosos > 0) {
                mostrarExito(`${exitosos} inscripción(es) activada(s) correctamente`);
                $('#modalConfirmarActivacion').modal('hide');
                cargarEstudiantes(); // Recargar datos
            }

            if (fallidos > 0) {
                mostrarError(`${fallidos} inscripción(es) no pudieron ser activadas`);
            }
        })
        .catch(error => {
            console.error('Error al activar inscripciones:', error);
            mostrarError('Error al activar las inscripciones');
        });
}

/**
 * Activar inscripción directamente (desde modal de detalles)
 */
function activarInscripcionDirecta(idInscripcion) {
    console.log('Activando inscripción directa:', idInscripcion);

    Swal.fire({
        title: '¿Activar inscripción?',
        text: 'Esta acción permitirá al estudiante acceder al curso inmediatamente',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#dc3545',
        confirmButtonText: 'Sí, activar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            activarInscripcionAjax(idInscripcion)
                .then(response => {
                    if (response.success) {
                        mostrarExito('Inscripción activada correctamente');
                        $('#modalDetallesEstudiante').modal('hide');
                        cargarEstudiantes(); // Recargar datos
                    } else {
                        mostrarError(response.message || 'Error al activar la inscripción');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarError('Error de conexión al activar la inscripción');
                });
        }
    });
}

/**
 * Función AJAX para activar una inscripción
 */
function activarInscripcionAjax(idInscripcion) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: 'ajax/usuarios.ajax.php',
            method: 'POST',
            data: {
                accion: 'activar_inscripcion',
                idInscripcion: idInscripcion
            },
            dataType: 'json',
            success: function (response) {
                resolve(response);
            },
            error: function (xhr, status, error) {
                reject(error);
            }
        });
    });
}

/**
 * Funciones de utilidad para mensajes
 */
function mostrarExito(mensaje) {
    Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: mensaje,
        timer: 3000,
        showConfirmButton: false
    });
}

function mostrarError(mensaje) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: mensaje,
        confirmButtonText: 'Entendido'
    });
}

function mostrarInfo(mensaje) {
    Swal.fire({
        icon: 'info',
        title: 'Información',
        text: mensaje,
        confirmButtonText: 'Entendido'
    });
}

// Exportar funciones para uso global si es necesario
window.verDetallesEstudiante = verDetallesEstudiante;
window.mostrarModalActivacion = mostrarModalActivacion;
window.activarInscripcionDirecta = activarInscripcionDirecta;