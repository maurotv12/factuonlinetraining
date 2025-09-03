// JavaScript para el listado de cursos
document.addEventListener('DOMContentLoaded', function () {

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

    // Configurar cambio de estado de cursos
    configurarCambioEstadoCurso();

    // Verificar imágenes después de un pequeño delay
    setTimeout(verificarImagenesCursos, 1000);
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
                    configurarPrevisualizacionImagenes();
                }
            });

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
        // Manejar error de imagen no encontrada
        img.addEventListener('error', function () {
            if (this.src !== 'storage/public/banners/default/defaultCurso.png') {
                console.warn('Imagen no encontrada, usando imagen por defecto:', this.src);
                this.src = 'storage/public/banners/default/defaultCurso.png';
                this.alt = 'Imagen no disponible';
                this.title = 'Imagen por defecto - Original no encontrada';
            }
        });

        img.addEventListener('click', function () {
            mostrarModalImagen(this.src, this.alt);
        });

        // Añadir cursor pointer
        img.style.cursor = 'pointer';

        // Añadir clase para identificar que se ha procesado
        img.classList.add('imagen-procesada');
    });
}

/**
 * Mostrar modal con imagen ampliada
 */
function mostrarModalImagen(src, alt) {
    // Eliminar modal existente si lo hay
    const modalExistente = document.getElementById('modalImagenCurso');
    if (modalExistente) {
        modalExistente.remove();
    }

    // Crear modal más simple sin usar Bootstrap Modal JavaScript
    const modalHtml = `
        <div class="modal fade show" id="modalImagenCurso" style="display: block;" tabindex="-1">
            <div class="modal-backdrop fade show"></div>
            <div class="modal-dialog modal-lg modal-dialog-centered" style="z-index: 1060;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Vista previa de imagen</h5>
                        <button type="button" class="btn-close" onclick="cerrarModalImagen()"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="${src}" alt="${alt}" class="img-fluid rounded" style="max-height: 70vh;"
                             onerror="if(this.src.indexOf('defaultCurso.png') === -1) this.src='storage/public/banners/default/defaultCurso.png';">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="cerrarModalImagen()">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Añadir modal al DOM
    document.body.insertAdjacentHTML('beforeend', modalHtml);

    // Añadir evento para cerrar con ESC
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            cerrarModalImagen();
        }
    }, { once: true });

    // Cerrar al hacer clic en el backdrop
    document.querySelector('.modal-backdrop').addEventListener('click', cerrarModalImagen);
}

/**
 * Cerrar modal de imagen
 */
function cerrarModalImagen() {
    const modal = document.getElementById('modalImagenCurso');
    if (modal) {
        modal.remove();
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
 * Verificar y corregir imágenes faltantes
 */
function verificarImagenesCursos() {
    document.querySelectorAll('.banner-mini:not(.imagen-procesada)').forEach(img => {
        // Verificar si la imagen existe
        const testImg = new Image();
        testImg.onload = function () {
            // Imagen existe, no hacer nada
        };
        testImg.onerror = function () {
            // Imagen no existe, cambiar a imagen por defecto
            if (img.src !== 'storage/public/banners/default/defaultCurso.png') {
                console.warn('Imagen no encontrada, cambiando a imagen por defecto:', img.src);
                img.src = 'storage/public/banners/default/defaultCurso.png';
                img.alt = 'Imagen no disponible';
                img.title = 'Imagen por defecto - Original no encontrada';
            }
        };
        testImg.src = img.src;

        // Marcar como procesada
        img.classList.add('imagen-procesada');
    });
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

/**
 * Configurar eventos para cambiar estado del curso
 */
function configurarCambioEstadoCurso() {
    // Obtener todos los botones de cambiar estado
    const btnsCambiarEstado = document.querySelectorAll('.cambiar-estado-curso');
    
    btnsCambiarEstado.forEach(btn => {
        btn.addEventListener('click', function () {
            // Obtener datos del botón clickeado
            const idCurso = this.getAttribute('data-id');
            const nombreCurso = this.getAttribute('data-nombre');
            const estadoActual = this.getAttribute('data-estado-actual');

            // Actualizar el modal con datos del curso
            document.getElementById('idCursoEstado').value = idCurso;
            document.getElementById('nombreCursoModal').textContent = nombreCurso;

            // Desmarcar todos los radio buttons
            document.querySelectorAll('input[name="nuevoEstado"]').forEach(radio => {
                radio.checked = false;
            });

            // Marcar el estado actual
            const radioActual = document.getElementById('estado' + estadoActual.charAt(0).toUpperCase() + estadoActual.slice(1));
            if (radioActual) {
                radioActual.checked = true;
            }
        });
    });
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
