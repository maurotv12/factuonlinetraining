// JavaScript para el listado de cursos
document.addEventListener('DOMContentLoaded', function () {
    console.log('Listado de cursos cargado');

    // Marcar que este script maneja la inicialización de DataTable
    window.dataTableInitialized = true;

    // Inicializar DataTable si está disponible
    if (typeof $ !== 'undefined' && $.fn.DataTable) {
        inicializarDataTable();
    }

    // Configurar eventos de los botones
    configurarEventosBotones();

    // Configurar tooltips
    configurarTooltips();

    // Configurar previsualización de imágenes
    configurarPrevisualizacionImagenes();
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
                order: [[7, 'desc']], // Ordenar por fecha descendente
                columnDefs: [
                    {
                        targets: [1, 8], // Imagen y acciones
                        orderable: false,
                        searchable: false
                    },
                    {
                        targets: 4, // Valor
                        render: function (data, type, row) {
                            if (type === 'display') {
                                return '$' + Number(data).toLocaleString('es-CO');
                            }
                            return data;
                        }
                    }
                ],
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                    '<"row"<"col-sm-12"tr>>' +
                    '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                drawCallback: function () {
                    // Reconfigurar eventos después de cada redibujado
                    configurarEventosBotones();
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
 * Configurar eventos de los botones de acción
 */
function configurarEventosBotones() {
    // Botones de ver curso
    document.querySelectorAll('.btn-info').forEach(btn => {
        btn.addEventListener('click', function (e) {
            // Añadir efecto de carga
            mostrarCargando(this);
        });
    });

    // Botones de editar curso
    document.querySelectorAll('.btn-warning').forEach(btn => {
        btn.addEventListener('click', function (e) {
            // Añadir efecto de carga
            mostrarCargando(this);
        });
    });
}

/**
 * Mostrar indicador de carga en el botón
 */
function mostrarCargando(boton) {
    const iconoOriginal = boton.querySelector('i').className;
    const textoOriginal = boton.innerHTML;

    boton.disabled = true;
    boton.innerHTML = '<i class="bi bi-arrow-clockwise spin"></i> Cargando...';

    // Restaurar el botón después de un tiempo
    setTimeout(() => {
        boton.disabled = false;
        boton.innerHTML = textoOriginal;
    }, 2000);
}

/**
 * Configurar tooltips para mejor UX
 */
function configurarTooltips() {
    // Tooltips para badges de estado
    document.querySelectorAll('.badge').forEach(badge => {
        badge.setAttribute('title', 'Estado del curso: ' + badge.textContent.trim());
    });

    // Tooltips para botones
    document.querySelectorAll('.btn-info').forEach(btn => {
        btn.setAttribute('title', 'Ver detalles del curso');
    });

    document.querySelectorAll('.btn-warning').forEach(btn => {
        btn.setAttribute('title', 'Editar curso');
    });

    // Inicializar tooltips de Bootstrap si está disponible
    if (typeof bootstrap !== 'undefined') {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
}

/**
 * Configurar previsualización de imágenes
 */
function configurarPrevisualizacionImagenes() {
    document.querySelectorAll('.banner-mini').forEach(img => {
        img.addEventListener('click', function () {
            mostrarModalImagen(this.src, this.alt);
        });

        // Añadir cursor pointer
        img.style.cursor = 'pointer';
    });
}

/**
 * Mostrar modal con imagen ampliada
 */
function mostrarModalImagen(src, alt) {
    // Crear modal dinámicamente
    const modalHtml = `
        <div class="modal fade" id="modalImagenCurso" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
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
    const modalExistente = document.getElementById('modalImagenCurso');
    if (modalExistente) {
        modalExistente.remove();
    }

    // Añadir modal al DOM
    document.body.insertAdjacentHTML('beforeend', modalHtml);

    // Mostrar modal
    if (typeof bootstrap !== 'undefined') {
        const modal = new bootstrap.Modal(document.getElementById('modalImagenCurso'));
        modal.show();

        // Eliminar modal del DOM al cerrar
        document.getElementById('modalImagenCurso').addEventListener('hidden.bs.modal', function () {
            this.remove();
        });
    }
}

/**
 * Filtros avanzados para la tabla
 */
function configurarFiltrosAvanzados() {
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
            $('#table_id').DataTable().column(5).search(this.value).draw();
        }
    });

    // Añadir filtro al header si existe DataTable
    const tableHeader = document.querySelector('.dataTables_length');
    if (tableHeader) {
        tableHeader.appendChild(filtroEstado);
    }
}

/**
 * Exportar datos de la tabla
 */
function exportarDatos(formato) {
    if (!$.fn.DataTable.isDataTable('#table_id')) {
        console.error('DataTable no está inicializado');
        return;
    }

    const table = $('#table_id').DataTable();

    switch (formato) {
        case 'excel':
            // Implementar exportación a Excel
            console.log('Exportando a Excel...');
            break;
        case 'pdf':
            // Implementar exportación a PDF
            console.log('Exportando a PDF...');
            break;
        case 'csv':
            // Implementar exportación a CSV
            console.log('Exportando a CSV...');
            break;
    }
}

/**
 * Refrescar datos de la tabla
 */
function refrescarTabla() {
    if ($.fn.DataTable.isDataTable('#table_id')) {
        $('#table_id').DataTable().ajax.reload();
    } else {
        location.reload();
    }
}

// Estilos dinámicos para animaciones
const style = document.createElement('style');
style.textContent = `
    .spin {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .btn:disabled {
        opacity: 0.7;
    }
`;
document.head.appendChild(style);
