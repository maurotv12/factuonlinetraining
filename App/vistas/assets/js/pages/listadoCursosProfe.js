// JavaScript para el listado de cursos de profesores
document.addEventListener('DOMContentLoaded', function () {
    console.log('Listado de cursos de profesor cargado');

    // Marcar que este script maneja la inicialización de DataTable
    window.dataTableInitialized = true;

    // Inicializar DataTable si está disponible
    if (typeof $ !== 'undefined' && $.fn.DataTable) {
        inicializarDataTableProfe();
    }

    // Configurar eventos de los botones
    configurarEventosBotones();

    // Configurar tooltips
    configurarTooltips();

    // Configurar previsualización de imágenes
    configurarPrevisualizacionImagenes();

    // Configurar estadísticas del profesor
    mostrarEstadisticasProfesor();
});

/**
 * Inicializar DataTable con configuración personalizada para profesores
 */
function inicializarDataTableProfe() {
    try {
        if (typeof $ !== 'undefined' && $.fn.DataTable) {
            $('#table_id').DataTable({
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
                },
                "responsive": true,
                "pageLength": 10,
                "lengthMenu": [[5, 10, 25, 50], [5, 10, 25, 50]],
                "order": [[]], // Ordenar lo que esté en backend
                "columnDefs": [
                    { "orderable": false, "targets": 8 }, // Columna de acciones no ordenable
                    { "searchable": false, "targets": [0, 1, 8] }, // # e imagen no buscables
                    { "className": "text-center", "targets": [0, 1, 4, 5, 6, 8] }
                ],
                "dom": '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l<"ms-3"f>><"d-flex align-items-center"B>>rtip',
                "buttons": [
                    {
                        extend: 'excel',
                        text: '<i class="bi bi-file-excel"></i> Excel',
                        className: 'btn btn-success btn-sm',
                        exportOptions: {
                            columns: [0, 2, 3, 4, 5, 6, 7] // Excluir imagen y acciones
                        }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="bi bi-file-pdf"></i> PDF',
                        className: 'btn btn-danger btn-sm',
                        exportOptions: {
                            columns: [0, 2, 3, 4, 5, 6, 7]
                        }
                    }
                ]
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
            if (this.getAttribute('href')) {
                mostrarCargando(this);
            }
        });
    });

    // Botones de editar curso
    document.querySelectorAll('.btn-warning').forEach(btn => {
        btn.addEventListener('click', function (e) {
            if (this.getAttribute('href')) {
                mostrarCargando(this);
            }
        });
    });

    // Botones de gestionar contenido
    document.querySelectorAll('.btn-success').forEach(btn => {
        if (btn.hasAttribute('onclick')) {
            btn.addEventListener('click', function (e) {
                mostrarCargando(this);
            });
        }
    });
}

/**
 * Mostrar indicador de carga en el botón
 */
function mostrarCargando(boton) {
    const iconoOriginal = boton.querySelector('i').className;
    const textoOriginal = boton.innerHTML;

    boton.disabled = true;
    boton.innerHTML = '<i class="bi bi-arrow-clockwise spin"></i>';

    // Restaurar el botón después de un tiempo
    setTimeout(() => {
        boton.disabled = false;
        boton.innerHTML = textoOriginal;
    }, 1500);
}

/**
 * Configurar tooltips para mejor UX
 */
function configurarTooltips() {
    // Tooltips para badges de estado
    document.querySelectorAll('.badge').forEach(badge => {
        if (badge.textContent.trim() === 'activo') {
            badge.setAttribute('title', 'Curso visible para estudiantes');
        } else if (badge.textContent.trim() === 'inactivo') {
            badge.setAttribute('title', 'Curso oculto para estudiantes');
        }
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
        // Configurar imagen por defecto en caso de error
        img.addEventListener('error', function () {
            if (this.src.indexOf('defaultCurso.png') === -1) {
                console.log('Error cargando imagen:', this.src);
                this.src = '/cursosApp/storage/public/banners/default/defaultCurso.png';
                this.alt = 'Imagen por defecto del curso';
            }
        });

        img.addEventListener('click', function () {
            mostrarModalImagen(this.src, this.alt);
        });

        // Añadir cursor pointer
        img.style.cursor = 'pointer';
    });

    // Verificar imágenes existentes
    verificarImagenesCursos();
}

/**
 * Verificar todas las imágenes de cursos y aplicar imagen por defecto si es necesario
 */
function verificarImagenesCursos() {
    document.querySelectorAll('.banner-mini').forEach(img => {
        if (img.complete && img.naturalWidth === 0) {
            // La imagen ya se cargó pero falló
            if (img.src.indexOf('defaultCurso.png') === -1) {
                console.log('Imagen no válida detectada:', img.src);
                img.src = '/cursosApp/storage/public/banners/default/defaultCurso.png';
                img.alt = 'Imagen por defecto del curso';
            }
        }
    });
}

/**
 * Mostrar modal con imagen ampliada
 */
function mostrarModalImagen(src, alt) {
    const modalHtml = `
        <div class="modal fade" id="modalImagenCurso" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Vista previa de imagen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="${src}" alt="${alt}" class="img-fluid rounded" style="max-height: 70vh;" 
                             onerror="if(this.src.indexOf('defaultCurso.png') === -1) this.src='/cursosApp/storage/public/banners/default/defaultCurso.png';">
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
 * Mostrar estadísticas del profesor
 */
function mostrarEstadisticasProfesor() {
    const table = document.querySelector('#table_id tbody');
    if (!table) return;

    const filas = table.querySelectorAll('tr');
    if (filas.length === 0 || (filas.length === 1 && filas[0].querySelector('td[colspan]'))) {
        return; // No hay cursos
    }

    let cursosActivos = 0;
    let cursosInactivos = 0;
    let totalSecciones = 0;
    let cursosGratis = 0;
    let cursosPago = 0;

    filas.forEach(fila => {
        const celdas = fila.querySelectorAll('td');
        if (celdas.length > 1) {
            // Estado del curso
            const estadoBadge = celdas[5].querySelector('.badge');
            if (estadoBadge && estadoBadge.textContent.trim() === 'activo') {
                cursosActivos++;
            } else {
                cursosInactivos++;
            }

            // Secciones
            const seccionesBadge = celdas[6].querySelector('.badge');
            if (seccionesBadge) {
                const numSecciones = parseInt(seccionesBadge.textContent.trim()) || 0;
                totalSecciones += numSecciones;
            }

            // Precio
            const precio = celdas[4];
            if (precio.textContent.includes('Gratis')) {
                cursosGratis++;
            } else {
                cursosPago++;
            }
        }
    });

    // Crear card de estadísticas si no existe
    if (!document.querySelector('.estadisticas-profesor')) {
        const estadisticasHtml = `
            <div class="row mb-4 estadisticas-profesor">
                <div class="col-md-3">
                    <div class="card text-center border-success">
                        <div class="card-body">
                            <i class="bi bi-check-circle-fill text-success display-6"></i>
                            <h5 class="card-title mt-2">${cursosActivos}</h5>
                            <p class="card-text text-muted">Cursos Activos</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center border-secondary">
                        <div class="card-body">
                            <i class="bi bi-pause-circle-fill text-secondary display-6"></i>
                            <h5 class="card-title mt-2">${cursosInactivos}</h5>
                            <p class="card-text text-muted">Cursos Inactivos</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center border-info">
                        <div class="card-body">
                            <i class="bi bi-collection-fill text-info display-6"></i>
                            <h5 class="card-title mt-2">${totalSecciones}</h5>
                            <p class="card-text text-muted">Total Secciones</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center border-warning">
                        <div class="card-body">
                            <i class="bi bi-currency-dollar text-warning display-6"></i>
                            <h5 class="card-title mt-2">${cursosPago}/${cursosGratis}</h5>
                            <p class="card-text text-muted">Pago/Gratis</p>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Insertar antes del card principal
        const mainCard = document.querySelector('.listado-cursos-container .card');
        if (mainCard) {
            mainCard.parentNode.insertAdjacentHTML('beforebegin', estadisticasHtml);
        }
    }
}

/**
 * Función para cambiar estado del curso
 */
function cambiarEstadoCurso(idCurso, nuevoEstado) {
    if (confirm(`¿Estás seguro de cambiar el estado del curso a "${nuevoEstado}"?`)) {
        // Aquí iría la llamada AJAX para cambiar el estado
        console.log(`Cambiar curso ${idCurso} a estado ${nuevoEstado}`);

        // Simular cambio exitoso
        setTimeout(() => {
            location.reload();
        }, 1000);
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
    
    .estadisticas-profesor .card {
        transition: all 0.3s ease;
    }
    
    .estadisticas-profesor .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
    }
`;
document.head.appendChild(style);
