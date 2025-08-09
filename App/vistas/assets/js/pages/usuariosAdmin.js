// JavaScript para la gestión de usuarios administrativos
document.addEventListener('DOMContentLoaded', function () {
    console.log('Gestión de usuarios cargada');

    // Marcar que este script maneja la inicialización de DataTable
    window.dataTableInitialized = true;

    // Inicializar componentes
    inicializarDataTable();
    configurarModalRoles();
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
                order: [[15, 'desc']], // Ordenar por fecha de registro descendente
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
                    configurarTooltips();
                }
            });

            console.log('DataTable inicializado correctamente');
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
