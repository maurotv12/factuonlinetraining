// JavaScript para la gestión de estudiantes del profesor
document.addEventListener('DOMContentLoaded', function () {
    // Variables globales
    let tablaEstudiantesCursos;
    let datosEstudiantes = [];

    // Inicializar componentes
    inicializarPagina();
    configurarEventos();
});

/**
 * Inicializar todos los componentes de la página
 */
function inicializarPagina() {
    mostrarIndicadorCarga();
    cargarDatosEstudiantes();
}

/**
 * Configurar eventos de la página
 */
function configurarEventos() {
    // Botón refrescar
    document.getElementById('btnRefrescar').addEventListener('click', function () {
        refrescarDatos();
    });

    // Botón exportar
    document.getElementById('btnExportar').addEventListener('click', function () {
        exportarDatos();
    });

    // Filtros
    document.getElementById('filtroTipo').addEventListener('change', function () {
        aplicarFiltros();
    });

    document.getElementById('filtroEstado').addEventListener('change', function () {
        aplicarFiltros();
    });
}

/**
 * Cargar datos de estudiantes desde el servidor
 */
function cargarDatosEstudiantes() {
    const formData = new FormData();
    formData.append('accion', 'obtener_estudiantes_cursos_profesor');

    fetch('/cursosApp/App/ajax/usuarios.ajax.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                datosEstudiantes = data.data;
                inicializarTabla();
                actualizarContadores();
            } else {
                mostrarError('Error al cargar los datos: ' + (data.message || 'Error desconocido'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarError('Error de conexión al cargar los datos');
        });
}

/**
 * Inicializar la tabla con los datos cargados
 */
function inicializarTabla() {
    const tablaContainer = document.getElementById('tablaContainer');
    const loadingIndicator = document.getElementById('loadingIndicator');
    const noDataMessage = document.getElementById('noDataMessage');

    if (datosEstudiantes.length === 0) {
        loadingIndicator.style.display = 'none';
        noDataMessage.style.display = 'block';
        return;
    }

    // Verificar si DataTables ya está inicializado
    // Destruir la instancia existente antes de crear una nueva
    if (tablaEstudiantesCursos && $.fn.DataTable.isDataTable('#tablaEstudiantesCursos')) {
        tablaEstudiantesCursos.destroy();
    }

    // Poblar la tabla
    poblarTabla(datosEstudiantes);

    // Inicializar DataTable
    tablaEstudiantesCursos = new DataTable('#tablaEstudiantesCursos', {
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        },
        responsive: true,
        pageLength: 25,
        order: [[5, 'desc']], // Ordenar por fecha de registro descendente
        columnDefs: [
            {
                targets: [6], // Columna de acciones
                orderable: false,
                searchable: false
            }
        ],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
            '<"row"<"col-sm-12"tr>>' +
            '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        drawCallback: function () {
            configurarTooltips();
        }
    });

    // Mostrar la tabla
    loadingIndicator.style.display = 'none';
    tablaContainer.style.display = 'block';
}

/**
 * Poblar la tabla con los datos
 */
function poblarTabla(datos) {
    const tbody = document.querySelector('#tablaEstudiantesCursos tbody');
    tbody.innerHTML = '';

    datos.forEach(registro => {
        const fila = document.createElement('tr');
        fila.innerHTML = `
            <td>
                <div class="student-info">
                    <div class="student-avatar">
                        ${registro.estudiante_foto_validada !== '/cursosApp/storage/public/usuarios/default.png'
                ? `<img src="${registro.estudiante_foto_validada}" alt="${registro.estudiante_nombre}" class="avatar-img">`
                : registro.iniciales}
                    </div>
                    <div class="student-details">
                        <div class="student-name">${escapeHtml(registro.estudiante_nombre)}</div>
                        <div class="student-email">${escapeHtml(registro.estudiante_email)}</div>
                    </div>
                </div>
            </td>
            <td>
                <div class="curso-info">
                    <div class="curso-nombre" title="${escapeHtml(registro.curso_nombre)}">
                        ${truncarTexto(registro.curso_nombre, 30)}
                    </div>
                </div>
            </td>
            <td>
                <span class="badge badge-secondary">
                    ${escapeHtml(registro.categoria_nombre || 'Sin categoría')}
                </span>
            </td>
            <td>
                <span class="badge ${registro.tipo === 'preinscrito' ? 'badge-warning' : 'badge-info'}">
                    ${registro.tipo === 'preinscrito' ? 'Preinscripción' : 'Inscripción'}
                </span>
            </td>
            <td>
                <span class="badge ${obtenerClaseEstado(registro.estado)}">
                    ${capitalizeFirst(registro.estado)}
                </span>
            </td>
            <td>
                <div class="fecha-info">
                    <div class="fecha-principal">${registro.fecha_formateada}</div>
                    <div class="fecha-relativa text-muted">${calcularTiempoRelativo(registro.fecha_registro)}</div>
                </div>
            </td>
            <td>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-info" 
                            onclick="verDetallesEstudiante(${registro.estudiante_id})" 
                            title="Ver detalles del estudiante">
                        <i class="fas fa-eye"></i>
                    </button>
                    ${registro.tipo === 'inscrito' && registro.estado === 'pendiente'
                ? `<button type="button" class="btn btn-sm btn-outline-success" 
                                   onclick="activarInscripcion(${registro.registro_id})" 
                                   title="Activar inscripción">
                               <i class="fas fa-check"></i>
                           </button>`
                : ''}
                </div>
            </td>
        `;
        tbody.appendChild(fila);
    });
}

/**
 * Aplicar filtros a la tabla
 */
function aplicarFiltros() {
    if (!tablaEstudiantesCursos) return;

    const filtroTipo = document.getElementById('filtroTipo').value;
    const filtroEstado = document.getElementById('filtroEstado').value;

    let datosFiltrados = datosEstudiantes;

    if (filtroTipo) {
        datosFiltrados = datosFiltrados.filter(registro => registro.tipo === filtroTipo);
    }

    if (filtroEstado) {
        datosFiltrados = datosFiltrados.filter(registro => registro.estado === filtroEstado);
    }

    // Verificar si DataTables ya está inicializado
    // Destruir la instancia existente antes de crear una nueva
    if (tablaEstudiantesCursos && $.fn.DataTable.isDataTable('#tablaEstudiantesCursos')) {
        tablaEstudiantesCursos.destroy();
    }

    poblarTabla(datosFiltrados);
    inicializarDataTable();
    actualizarContadores(datosFiltrados.length);
}

/**
 * Reinicializar DataTable después del filtrado
 */
function inicializarDataTable() {
    tablaEstudiantesCursos = new DataTable('#tablaEstudiantesCursos', {
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        },
        responsive: true,
        pageLength: 25,
        order: [[5, 'desc']],
        columnDefs: [
            {
                targets: [6],
                orderable: false,
                searchable: false
            }
        ],
        drawCallback: function () {
            configurarTooltips();
        }
    });
}

/**
 * Ver detalles de un estudiante
 */
function verDetallesEstudiante(idEstudiante) {
    // Obtener todos los registros del estudiante
    const registrosEstudiante = datosEstudiantes.filter(r => r.estudiante_id == idEstudiante);

    if (registrosEstudiante.length === 0) return;

    const estudiante = registrosEstudiante[0];

    // Construir el contenido del modal
    let contenido = `
        <div class="row">
            <div class="col-md-4 text-center">
                <div class="student-avatar-large mb-3">
                    ${estudiante.estudiante_foto_validada !== '/cursosApp/storage/public/usuarios/default.png'
            ? `<img src="${estudiante.estudiante_foto_validada}" alt="${estudiante.estudiante_nombre}" class="img-fluid rounded-circle" style="width: 120px; height: 120px;">`
            : `<div class="avatar-large">${estudiante.iniciales}</div>`}
                </div>
                <h5>${escapeHtml(estudiante.estudiante_nombre)}</h5>
                <p class="text-muted">${escapeHtml(estudiante.estudiante_email)}</p>
            </div>
            <div class="col-md-8">
                <h6>Cursos Registrados:</h6>
                <div class="list-group">
    `;

    registrosEstudiante.forEach(registro => {
        contenido += `
            <div class="list-group-item">
                <div class="d-flex w-100 justify-content-between">
                    <h6 class="mb-1">${escapeHtml(registro.curso_nombre)}</h6>
                    <small>${registro.fecha_formateada}</small>
                </div>
                <p class="mb-1">
                    <span class="badge ${registro.tipo === 'preinscrito' ? 'badge-warning' : 'badge-info'} mr-2">
                        ${registro.tipo === 'preinscrito' ? 'Preinscripción' : 'Inscripción'}
                    </span>
                    <span class="badge ${obtenerClaseEstado(registro.estado)}">
                        ${capitalizeFirst(registro.estado)}
                    </span>
                </p>
                <small>Categoría: ${escapeHtml(registro.categoria_nombre || 'Sin categoría')}</small>
            </div>
        `;
    });

    contenido += `
                </div>
            </div>
        </div>
    `;

    document.getElementById('contenidoDetallesEstudiante').innerHTML = contenido;
    $('#modalDetallesEstudiante').modal('show');
}

/**
 * Activar inscripción de un estudiante
 */
function activarInscripcion(idInscripcion) {
    if (!confirm('¿Estás seguro de que deseas activar esta inscripción?')) {
        return;
    }

    const formData = new FormData();
    formData.append('accion', 'activar_inscripcion');
    formData.append('idInscripcion', idInscripcion);

    fetch('/cursosApp/App/ajax/usuarios.ajax.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarExito('Inscripción activada correctamente');
                refrescarDatos();
            } else {
                mostrarError('Error al activar la inscripción: ' + (data.message || 'Error desconocido'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarError('Error de conexión al activar la inscripción');
        });
}

/**
 * Refrescar datos de la tabla
 */
function refrescarDatos() {
    mostrarIndicadorCarga();
    document.getElementById('tablaContainer').style.display = 'none';
    document.getElementById('noDataMessage').style.display = 'none';

    // Limpiar filtros
    document.getElementById('filtroTipo').value = '';
    document.getElementById('filtroEstado').value = '';

    cargarDatosEstudiantes();
}

/**
 * Exportar datos a CSV
 */
function exportarDatos() {
    const filtroTipo = document.getElementById('filtroTipo').value;
    const filtroEstado = document.getElementById('filtroEstado').value;

    let datosFiltrados = datosEstudiantes;

    if (filtroTipo) {
        datosFiltrados = datosFiltrados.filter(registro => registro.tipo === filtroTipo);
    }

    if (filtroEstado) {
        datosFiltrados = datosFiltrados.filter(registro => registro.estado === filtroEstado);
    }

    // Crear CSV
    let csv = 'Estudiante,Email,Curso,Categoría,Tipo,Estado,Fecha Registro\n';

    datosFiltrados.forEach(registro => {
        csv += `"${registro.estudiante_nombre}","${registro.estudiante_email}","${registro.curso_nombre}","${registro.categoria_nombre || 'Sin categoría'}","${registro.tipo}","${registro.estado}","${registro.fecha_formateada}"\n`;
    });

    // Descargar archivo
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `estudiantes_${new Date().toISOString().slice(0, 10)}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

/**
 * Actualizar contadores
 */
function actualizarContadores(total = null) {
    const totalRegistros = total !== null ? total : datosEstudiantes.length;
    document.getElementById('totalRegistros').textContent = `${totalRegistros} registro${totalRegistros !== 1 ? 's' : ''}`;
}

/**
 * Mostrar indicador de carga
 */
function mostrarIndicadorCarga() {
    document.getElementById('loadingIndicator').style.display = 'block';
}

/**
 * Configurar tooltips
 */
function configurarTooltips() {
    // Configurar tooltips de Bootstrap si están disponibles
    if (typeof $().tooltip === 'function') {
        $('[title]').tooltip();
    }
}

/**
 * Obtener clase CSS para el estado
 */
function obtenerClaseEstado(estado) {
    switch (estado) {
        case 'preinscrito':
            return 'badge-warning';
        case 'pendiente':
            return 'badge-secondary';
        case 'activo':
            return 'badge-success';
        case 'cancelado':
            return 'badge-danger';
        case 'expirado':
            return 'badge-dark';
        default:
            return 'badge-light';
    }
}

/**
 * Funciones de utilidad
 */

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function (m) {
        return map[m];
    });
}

function truncarTexto(texto, maxLength) {
    if (texto.length <= maxLength) return texto;
    return texto.substring(0, maxLength) + '...';
}

function capitalizeFirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function calcularTiempoRelativo(fecha) {
    const ahora = new Date();
    const fechaRegistro = new Date(fecha);
    const diferencia = ahora - fechaRegistro;

    const minutos = Math.floor(diferencia / (1000 * 60));
    const horas = Math.floor(diferencia / (1000 * 60 * 60));
    const dias = Math.floor(diferencia / (1000 * 60 * 60 * 24));

    if (minutos < 60) {
        return `Hace ${minutos} min`;
    } else if (horas < 24) {
        return `Hace ${horas} h`;
    } else {
        return `Hace ${dias} día${dias !== 1 ? 's' : ''}`;
    }
}

function mostrarExito(mensaje) {
    // Implementar notificación de éxito (usar toastr, SweetAlert, etc.)
    alert('✓ ' + mensaje);
}

function mostrarError(mensaje) {
    // Implementar notificación de error (usar toastr, SweetAlert, etc.)
    alert('✗ ' + mensaje);
}

// Hacer disponibles las funciones globalmente
window.verDetallesEstudiante = verDetallesEstudiante;
window.activarInscripcion = activarInscripcion;
window.refrescarDatos = refrescarDatos;
window.exportarDatos = exportarDatos;