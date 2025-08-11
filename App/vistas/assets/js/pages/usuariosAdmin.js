// JavaScript para la gestión de usuarios administrativos
document.addEventListener('DOMContentLoaded', function () {
    // Marcar que este script maneja la inicialización de DataTable
    window.dataTableInitialized = true;

    // Inicializar componentes
    inicializarDataTable();
    configurarModalRoles();
    configurarModalCursos();
    configurarEventosTabla();
    configurarTooltips();
});

/**
 * Inicializar DataTable con configuración personalizada
 */
function inicializarDataTable() {
    try {
        if (typeof $ !== 'undefined' && $.fn.DataTable) {
            // Verificar si DataTables ya está inicializado
            if ($.fn.DataTable.isDataTable('#table_id')) {
                // Destruir la instancia existente antes de crear una nueva
                $('#table_id').DataTable().destroy();
            }

            $('#table_id').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
                },
                responsive: true,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
                order: [[15, 'asc']], // Ordenar por fecha de registro ascendente
                columnDefs: [
                    {
                        targets: [1, 9, 12, 13, 16], // Foto, biografía, cursos, roles, acciones
                        orderable: false,
                        searchable: false
                    },
                    {
                        targets: [9], // Biografía
                        width: "200px"
                    },
                    {
                        targets: [12], // Cursos asignados - más espacio
                        width: "220px",
                        className: "text-left"
                    },
                    {
                        targets: [13], // Roles
                        width: "130px"
                    }
                ],
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                    '<"row"<"col-sm-12"tr>>' +
                    '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                drawCallback: function () {
                    // Reconfigurar eventos después de cada redibujado
                    configurarModalRoles();
                    configurarModalCursos();
                    configurarTooltips();
                }
            });

        }
    } catch (error) {
        console.error('Error al inicializar DataTable:', error);
    }
}

/**
 * Configurar el modal de roles
 */
function configurarModalRoles() {
    // Obtener referencias a los elementos del DOM
    const modalRoles = document.getElementById('modalRoles');
    const btnsCambiarRoles = document.querySelectorAll('.cambiar-roles');

    if (!modalRoles || btnsCambiarRoles.length === 0) {
        console.log('Modal de roles o botones no encontrados');
        return;
    }

    // Roles por usuario (debería estar disponible desde PHP)
    const rolesPorUsuario = window.rolesPorUsuario || {};

    // Agregar evento a los botones de cambiar roles
    btnsCambiarRoles.forEach(btn => {
        btn.addEventListener('click', function () {
            // Obtener datos del usuario
            const idUsuario = this.getAttribute('data-id');
            const nombreUsuario = this.getAttribute('data-nombre');

            // Actualizar el modal con los datos del usuario
            const inputId = document.getElementById('idUsuario');
            const spanNombre = document.getElementById('nombreUsuarioModal');

            if (inputId) inputId.value = idUsuario;
            if (spanNombre) spanNombre.textContent = nombreUsuario;

            // Desmarcar todos los checkboxes primero
            document.querySelectorAll('.role-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });

            // Marcar los roles actuales del usuario
            if (rolesPorUsuario[idUsuario]) {
                rolesPorUsuario[idUsuario].forEach(rol => {
                    const checkbox = document.getElementById('rol' + rol.id);
                    if (checkbox) {
                        checkbox.checked = true;
                    }
                });
            }

            // Añadir efectos visuales
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cargando...';

            setTimeout(() => {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-user-tag"></i> Cambiar Roles';
            }, 1000);
        });
    });

    // Configurar el formulario de roles
    const formRoles = document.getElementById('formCambiarRoles');
    if (formRoles) {
        formRoles.addEventListener('submit', function (e) {
            // Mostrar indicador de carga
            mostrarCargandoModal();
        });
    }
}

/**
 * Configurar el modal de cursos
 */
function configurarModalCursos() {
    // Obtener referencias a los elementos del DOM
    const modalCursos = document.getElementById('modalCursos');
    const btnsVerCursos = document.querySelectorAll('.ver-cursos');

    if (!modalCursos || btnsVerCursos.length === 0) {
        console.log('Modal de cursos o botones no encontrados');
        return;
    }

    // Cursos por profesor (debería estar disponible desde PHP)
    const cursosPorProfesor = window.cursosPorProfesor || {};

    // Agregar evento a los botones de ver cursos
    btnsVerCursos.forEach(btn => {
        btn.addEventListener('click', function () {
            const usuarioId = this.getAttribute('data-usuario-id');
            const usuarioNombre = this.getAttribute('data-usuario-nombre');

            // Actualizar el título del modal
            const spanNombre = document.getElementById('nombreUsuarioCursos');
            if (spanNombre) spanNombre.textContent = usuarioNombre;

            // Mostrar indicador de carga
            mostrarCargandoCursos();

            // Simular un pequeño delay para mejorar la experiencia del usuario
            setTimeout(() => {
                cargarCursosEnModal(usuarioId, cursosPorProfesor);
            }, 500);

            // Añadir efectos visuales al botón
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cargando...';

            setTimeout(() => {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-book"></i> Ver Cursos (' +
                    (cursosPorProfesor[usuarioId] ? cursosPorProfesor[usuarioId].length : 0) + ')';
            }, 1000);
        });
    });
}

/**
 * Cargar cursos en el modal
 */
function cargarCursosEnModal(usuarioId, cursosPorProfesor) {
    const listaCursos = document.getElementById('listaCursos');

    if (!listaCursos) {
        console.error('Container de lista de cursos no encontrado');
        return;
    }

    // Limpiar contenido anterior
    listaCursos.innerHTML = '';

    // Verificar si el usuario tiene cursos asignados
    if (!cursosPorProfesor[usuarioId] || cursosPorProfesor[usuarioId].length === 0) {
        listaCursos.innerHTML = `
            <div class="sin-cursos">
                <i class="fas fa-book-open"></i>
                <h5>Sin cursos asignados</h5>
                <p class="text-muted">Este usuario no tiene cursos asignados actualmente.</p>
            </div>
        `;
        return;
    }

    // Crear la lista de cursos
    const cursos = cursosPorProfesor[usuarioId];
    let cursosHtml = '';

    cursos.forEach((curso, index) => {
        const descripcion = curso.descripcion || 'Sin descripción disponible';
        const estado = curso.estado || 'activo';
        const fechaCreacion = curso.fecha_registro || 'No disponible';
        const precio = curso.valor || '0';

        cursosHtml += `
            <div class="curso-item" style="animation-delay: ${index * 0.1}s">
                <div class="curso-titulo">
                    <i class="fas fa-graduation-cap"></i>
                    ${escapeHtml(curso.nombre)}
                </div>
                <div class="curso-descripcion">
                    ${escapeHtml(descripcion.substring(0, 150))}${descripcion.length > 150 ? '...' : ''}
                </div>
                <div class="curso-meta">
                    <div class="curso-meta-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Creado: ${formatearFecha(fechaCreacion)}</span>
                    </div>
                    <div class="curso-meta-item">
                        <i class="fas fa-dollar-sign"></i>
                        <span>Valor: $${formatearPrecio(precio)}</span>
                    </div>
                    <div class="curso-meta-item">
                        <span class="curso-estado ${estado.toLowerCase()}">${estado}</span>
                    </div>
                </div>
            </div>
        `;
    });

    listaCursos.innerHTML = cursosHtml;

    // Agregar animación de entrada
    setTimeout(() => {
        const items = listaCursos.querySelectorAll('.curso-item');
        items.forEach((item, index) => {
            setTimeout(() => {
                item.style.opacity = '0';
                item.style.transform = 'translateY(20px)';
                item.style.transition = 'all 0.3s ease';

                requestAnimationFrame(() => {
                    item.style.opacity = '1';
                    item.style.transform = 'translateY(0)';
                });
            }, index * 100);
        });
    }, 100);
}

/**
 * Mostrar indicador de carga para cursos
 */
function mostrarCargandoCursos() {
    const listaCursos = document.getElementById('listaCursos');
    if (listaCursos) {
        listaCursos.innerHTML = `
            <div class="loading-cursos">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-3 text-muted">Cargando cursos...</p>
            </div>
        `;
    }
}

/**
 * Escapar HTML para prevenir XSS
 */
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function (m) { return map[m]; });
}

/**
 * Formatear fecha para mostrar
 */
function formatearFecha(fecha) {
    if (!fecha || fecha === 'No disponible') return 'No disponible';

    try {
        const fechaObj = new Date(fecha);
        return fechaObj.toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    } catch (e) {
        return fecha;
    }
}

/**
 * Formatear precio para mostrar
 */
function formatearPrecio(precio) {
    if (!precio || isNaN(precio)) return '0';

    return new Intl.NumberFormat('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(precio);
}

/**
 * Configurar eventos adicionales de la tabla
 */
function configurarEventosTabla() {
    // Eventos para mostrar/ocultar biografías
    configurarEventosBiografia();

    // Eventos para los avatares
    configurarEventosAvatares();

    // Eventos para badges de cursos y roles
    configurarEventosBadges();
}

/**
 * Configurar eventos para biografías expandibles
 */
function configurarEventosBiografia() {
    document.addEventListener('click', function (e) {
        if (e.target.closest('a[onclick*="bio-"]')) {
            e.preventDefault();
            // El evento ya está manejado por el onclick inline
            // Pero podemos añadir efectos adicionales aquí
            const link = e.target.closest('a');
            link.style.opacity = '0.7';
            setTimeout(() => {
                link.style.opacity = '1';
            }, 300);
        }
    });
}

/**
 * Configurar eventos para avatares
 */
function configurarEventosAvatares() {
    document.querySelectorAll('.avatar').forEach(avatar => {
        avatar.addEventListener('click', function () {
            mostrarModalImagen(this.src, 'Foto de perfil');
        });

        avatar.style.cursor = 'pointer';
        avatar.title = 'Click para ver imagen completa';
    });
}

/**
 * Configurar eventos para badges informativos
 */
function configurarEventosBadges() {
    // Tooltips para badges de cursos
    document.querySelectorAll('.cursos-asignados .badge').forEach(badge => {
        badge.title = 'Curso: ' + badge.textContent.trim();
    });

    // Tooltips para badges de roles
    document.querySelectorAll('.roles-container .badge').forEach(badge => {
        badge.title = 'Rol: ' + badge.textContent.trim();
    });
}

/**
 * Mostrar modal con imagen ampliada
 */
function mostrarModalImagen(src, alt) {
    const modalHtml = `
        <div class="modal fade" id="modalImagenUsuario" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Vista previa de imagen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="${src}" alt="${alt}" class="img-fluid rounded" style="max-height: 70vh;">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Eliminar modal existente si lo hay
    const modalExistente = document.getElementById('modalImagenUsuario');
    if (modalExistente) {
        modalExistente.remove();
    }

    // Añadir modal al DOM
    document.body.insertAdjacentHTML('beforeend', modalHtml);

    // Mostrar modal
    if (typeof bootstrap !== 'undefined') {
        const modal = new bootstrap.Modal(document.getElementById('modalImagenUsuario'));
        modal.show();

        // Eliminar modal del DOM al cerrar
        document.getElementById('modalImagenUsuario').addEventListener('hidden.bs.modal', function () {
            this.remove();
        });
    }
}

/**
 * Configurar tooltips
 */
function configurarTooltips() {
    // Inicializar tooltips de Bootstrap si está disponible
    if (typeof bootstrap !== 'undefined') {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]:not(.tooltip-initialized)'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            tooltipTriggerEl.classList.add('tooltip-initialized');
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
}

/**
 * Mostrar indicador de carga en el modal
 */
function mostrarCargandoModal() {
    const submitBtn = document.querySelector('#formCambiarRoles button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
    }
}

/**
 * Filtros avanzados para la tabla
 */
function configurarFiltrosAvanzados() {
    // Filtro por rol
    const filtroRol = document.createElement('select');
    filtroRol.className = 'form-select form-select-sm ms-2';
    filtroRol.innerHTML = `
        <option value="">Todos los roles</option>
        <option value="admin">Administrador</option>
        <option value="profesor">Profesor</option>
        <option value="estudiante">Estudiante</option>
    `;

    filtroRol.addEventListener('change', function () {
        if ($.fn.DataTable.isDataTable('#table_id')) {
            $('#table_id').DataTable().column(13).search(this.value).draw();
        }
    });

    // Filtro por estado
    const filtroEstado = document.createElement('select');
    filtroEstado.className = 'form-select form-select-sm ms-2';
    filtroEstado.innerHTML = `
        <option value="">Todos los estados</option>
        <option value="activo">Activo</option>
        <option value="inactivo">Inactivo</option>
    `;

    filtroEstado.addEventListener('change', function () {
        if ($.fn.DataTable.isDataTable('#table_id')) {
            $('#table_id').DataTable().column(14).search(this.value).draw();
        }
    });

    // Añadir filtros al header si existe DataTable
    const tableHeader = document.querySelector('.dataTables_length');
    if (tableHeader) {
        tableHeader.appendChild(filtroRol);
        tableHeader.appendChild(filtroEstado);
    }
}

/**
 * Exportar datos de usuarios
 */
function exportarUsuarios(formato) {
    if (!$.fn.DataTable.isDataTable('#table_id')) {
        console.error('DataTable no está inicializado');
        return;
    }

    const table = $('#table_id').DataTable();

    // Mostrar indicador de carga
    mostrarCargandoExportacion();

    switch (formato) {
        case 'excel':
            console.log('Exportando usuarios a Excel...');
            // Implementar exportación a Excel
            break;
        case 'pdf':
            console.log('Exportando usuarios a PDF...');
            // Implementar exportación a PDF
            break;
        case 'csv':
            console.log('Exportando usuarios a CSV...');
            // Implementar exportación a CSV
            break;
    }

    // Ocultar indicador de carga después de un tiempo
    setTimeout(ocultarCargandoExportacion, 2000);
}

/**
 * Mostrar indicador de carga para exportación
 */
function mostrarCargandoExportacion() {
    const overlay = document.createElement('div');
    overlay.className = 'loading-overlay';
    overlay.id = 'loadingExportacion';
    overlay.innerHTML = `
        <div class="text-center text-white">
            <div class="loading-spinner"></div>
            <p class="mt-3">Exportando datos...</p>
        </div>
    `;
    document.body.appendChild(overlay);
}

/**
 * Ocultar indicador de carga para exportación
 */
function ocultarCargandoExportacion() {
    const overlay = document.getElementById('loadingExportacion');
    if (overlay) {
        overlay.remove();
    }
}

/**
 * Refrescar tabla de usuarios
 */
function refrescarTablaUsuarios() {
    if ($.fn.DataTable.isDataTable('#table_id')) {
        $('#table_id').DataTable().ajax.reload();
    } else {
        location.reload();
    }
}

/**
 * Buscar usuario específico
 */
function buscarUsuario(termino) {
    if ($.fn.DataTable.isDataTable('#table_id')) {
        $('#table_id').DataTable().search(termino).draw();
    }
}

// Hacer disponibles las funciones globalmente
window.exportarUsuarios = exportarUsuarios;
window.refrescarTablaUsuarios = refrescarTablaUsuarios;
window.buscarUsuario = buscarUsuario;
window.configurarFiltrosAvanzados = configurarFiltrosAvanzados;
