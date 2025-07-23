<?php
// Verificar acceso (solo administradores)
if (!ControladorGeneral::ctrUsuarioTieneAlgunRol(['admin', 'superadmin'])) {
    echo '<div class="alert alert-danger">No tienes permisos para acceder a esta página.</div>';
    return;
}

require_once "modelos/conexion.php";
require_once "modelos/cursos.modelo.php";
require_once "controladores/cursos.controlador.php";

// Obtener el ID del curso de la URL
$idCurso = isset($_GET['id']) ? $_GET['id'] : null;

if (!$idCurso) {
    echo '<div class="alert alert-danger">ID de curso no válido.</div>';
    return;
}

// Obtener los datos del curso
$curso = ControladorCursos::ctrMostrarCursos("id", $idCurso);

if (!$curso) {
    echo '<div class="alert alert-danger">Curso no encontrado.</div>';
    return;
}

// Obtener datos adicionales
$categorias = ControladorCursos::ctrObtenerCategorias();
$profesores = ControladorCursos::ctrObtenerProfesores();

// Obtener conexión para las secciones
$conn = Conexion::conectar();

// Obtener secciones del curso
$stmtSecciones = $conn->prepare("
    SELECT * FROM curso_secciones 
    WHERE id_curso = ? 
    ORDER BY orden ASC
");
$stmtSecciones->execute([$idCurso]);
$secciones = $stmtSecciones->fetchAll(PDO::FETCH_ASSOC);

// Obtener contenido de cada sección
$contenidoSecciones = [];
foreach ($secciones as $seccion) {
    $stmtContenido = $conn->prepare("
        SELECT * FROM seccion_contenido 
        WHERE id_seccion = ? 
        ORDER BY orden ASC
    ");
    $stmtContenido->execute([$seccion['id']]);
    $contenidoSecciones[$seccion['id']] = $stmtContenido->fetchAll(PDO::FETCH_ASSOC);
}

// Procesar actualización del curso básico
if (isset($_POST['actualizarCurso'])) {
    $datosActualizar = [
        'id' => $idCurso,
        'nombre' => $_POST['nombre'],
        'descripcion' => $_POST['descripcion'],
        'lo_que_aprenderas' => $_POST['lo_que_aprenderas'],
        'requisitos' => $_POST['requisitos'],
        'para_quien' => $_POST['para_quien'],
        'valor' => $_POST['valor'],
        'id_categoria' => $_POST['id_categoria'],
        'id_persona' => $_POST['id_persona'],
        'estado' => $_POST['estado']
    ];

    $respuesta = ModeloCursos::mdlActualizarCurso($datosActualizar);
    
    if ($respuesta == "ok") {
        echo '<script>
            Swal.fire({
                icon: "success",
                title: "¡Curso actualizado!",
                text: "Los datos del curso se han actualizado correctamente.",
                confirmButtonText: "Aceptar"
            }).then(() => {
                window.location.reload();
            });
        </script>';
    }
}
?>

<style>
.course-editor {
    background: #f8f9fa;
    min-height: 100vh;
    padding: 20px 0;
}

.course-header {
    background: white;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.section-card {
    background: white;
    border-radius: 10px;
    margin-bottom: 15px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    border: 1px solid #e9ecef;
}

.section-header {
    padding: 15px 20px;
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    border-radius: 10px 10px 0 0;
    cursor: pointer;
    display: flex;
    justify-content: between;
    align-items: center;
}

.section-content {
    padding: 15px 20px;
}

.content-item {
    display: flex;
    align-items: center;
    padding: 10px;
    margin: 5px 0;
    background: #f8f9fa;
    border-radius: 5px;
    border-left: 4px solid #007bff;
}

.content-item.video {
    border-left-color: #dc3545;
}

.content-item.pdf {
    border-left-color: #28a745;
}

.drag-handle {
    cursor: move;
    margin-right: 10px;
    color: #6c757d;
}

.btn-add-section {
    background: linear-gradient(45deg, #007bff, #0056b3);
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 25px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-add-section:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,123,255,0.3);
}

.content-actions {
    margin-left: auto;
}

.section-stats {
    font-size: 0.875rem;
    color: #6c757d;
    margin-left: auto;
}
</style>

<div class="course-editor">
    <div class="container-fluid">
        <!-- Header del curso -->
        <div class="course-header">
            <div class="row">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center mb-3">
                        <a href="superAdmin/gestionCursos/listadoCursos" class="btn btn-outline-secondary me-3">
                            <i class="bi bi-arrow-left"></i> Volver al listado
                        </a>
                        <h2 class="mb-0"><?= htmlspecialchars($curso['nombre']) ?></h2>
                    </div>
                    
                    <!-- Formulario de datos básicos del curso -->
                    <form method="post" id="formCursoBasico">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nombre del curso</label>
                                    <input type="text" class="form-control" name="nombre" 
                                           value="<?= htmlspecialchars($curso['nombre']) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Valor</label>
                                    <input type="number" class="form-control" name="valor" 
                                           value="<?= $curso['valor'] ?>" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Estado</label>
                                    <select class="form-control" name="estado" required>
                                        <option value="activo" <?= $curso['estado'] == 'activo' ? 'selected' : '' ?>>Activo</option>
                                        <option value="inactivo" <?= $curso['estado'] == 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Categoría</label>
                                    <select class="form-control" name="id_categoria" required>
                                        <?php foreach ($categorias as $categoria): ?>
                                            <option value="<?= $categoria['id'] ?>" 
                                                    <?= $categoria['id'] == $curso['id_categoria'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($categoria['nombre']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Profesor</label>
                                    <select class="form-control" name="id_persona" required>
                                        <?php foreach ($profesores as $profesor): ?>
                                            <option value="<?= $profesor['id'] ?>" 
                                                    <?= $profesor['id'] == $curso['id_persona'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($profesor['nombre']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" name="descripcion" rows="4"><?= htmlspecialchars($curso['descripcion']) ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Lo que aprenderás</label>
                                    <textarea class="form-control" name="lo_que_aprenderas" rows="6"><?= htmlspecialchars($curso['lo_que_aprenderas']) ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Requisitos</label>
                                    <textarea class="form-control" name="requisitos" rows="6"><?= htmlspecialchars($curso['requisitos']) ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Para quién es este curso</label>
                                    <textarea class="form-control" name="para_quien" rows="6"><?= htmlspecialchars($curso['para_quien']) ?></textarea>
                                </div>
                            </div>
                        </div>

                        <button type="submit" name="actualizarCurso" class="btn btn-primary">
                            <i class="bi bi-save"></i> Actualizar datos básicos
                        </button>
                    </form>
                </div>
                
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <?php if ($curso['banner']): ?>
                                <img src="<?= $curso['banner'] ?>" class="img-fluid rounded mb-3" alt="Banner del curso">
                            <?php else: ?>
                                <div class="bg-light p-4 rounded mb-3">
                                    <i class="bi bi-image" style="font-size: 3rem; color: #ccc;"></i>
                                    <p class="text-muted">Sin imagen</p>
                                </div>
                            <?php endif; ?>
                            
                            <div class="row text-center">
                                <div class="col-4">
                                    <h6><?= count($secciones) ?></h6>
                                    <small class="text-muted">Secciones</small>
                                </div>
                                <div class="col-4">
                                    <?php 
                                    $totalContenido = 0;
                                    foreach ($contenidoSecciones as $contenido) {
                                        $totalContenido += count($contenido);
                                    }
                                    ?>
                                    <h6><?= $totalContenido ?></h6>
                                    <small class="text-muted">Elementos</small>
                                </div>
                                <div class="col-4">
                                    <h6><?= $curso['estado'] == 'activo' ? 'Activo' : 'Inactivo' ?></h6>
                                    <small class="text-muted">Estado</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Secciones del curso -->
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4><i class="bi bi-collection"></i> Contenido del curso</h4>
                    <button class="btn btn-add-section" onclick="agregarSeccion()">
                        <i class="bi bi-plus-circle"></i> Agregar sección
                    </button>
                </div>

                <div id="secciones-container">
                    <?php foreach ($secciones as $index => $seccion): ?>
                        <div class="section-card" data-seccion-id="<?= $seccion['id'] ?>">
                            <div class="section-header" onclick="toggleSeccion(<?= $seccion['id'] ?>)">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-grip-vertical drag-handle me-2"></i>
                                    <strong><?= $index + 1 ?>. <?= htmlspecialchars($seccion['titulo']) ?></strong>
                                </div>
                                <div class="section-stats">
                                    <?= count($contenidoSecciones[$seccion['id']] ?? []) ?> elementos
                                    <i class="bi bi-chevron-down ms-2"></i>
                                </div>
                            </div>
                            
                            <div class="section-content" id="seccion-<?= $seccion['id'] ?>">
                                <div class="row mb-3">
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" 
                                               value="<?= htmlspecialchars($seccion['titulo']) ?>"
                                               onchange="actualizarTituloSeccion(<?= $seccion['id'] ?>, this.value)"
                                               placeholder="Título de la sección">
                                    </div>
                                    <div class="col-md-4">
                                        <div class="btn-group w-100">
                                            <button class="btn btn-outline-primary btn-sm" 
                                                    onclick="agregarContenido(<?= $seccion['id'] ?>, 'video')">
                                                <i class="bi bi-camera-video"></i> Video
                                            </button>
                                            <button class="btn btn-outline-success btn-sm" 
                                                    onclick="agregarContenido(<?= $seccion['id'] ?>, 'pdf')">
                                                <i class="bi bi-file-pdf"></i> PDF
                                            </button>
                                            <button class="btn btn-outline-danger btn-sm" 
                                                    onclick="eliminarSeccion(<?= $seccion['id'] ?>)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="contenido-lista" id="contenido-<?= $seccion['id'] ?>">
                                    <?php if (isset($contenidoSecciones[$seccion['id']])): ?>
                                        <?php foreach ($contenidoSecciones[$seccion['id']] as $contenido): ?>
                                            <div class="content-item <?= $contenido['tipo'] ?>" data-contenido-id="<?= $contenido['id'] ?>">
                                                <i class="bi bi-grip-vertical drag-handle"></i>
                                                <i class="bi bi-<?= $contenido['tipo'] == 'video' ? 'camera-video' : 'file-pdf' ?> me-2"></i>
                                                <span class="flex-grow-1"><?= htmlspecialchars($contenido['titulo']) ?></span>
                                                <small class="text-muted me-2"><?= $contenido['duracion'] ?? '00:00' ?></small>
                                                <div class="content-actions">
                                                    <button class="btn btn-sm btn-outline-primary me-1" 
                                                            onclick="editarContenido(<?= $contenido['id'] ?>)">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger" 
                                                            onclick="eliminarContenido(<?= $contenido['id'] ?>)">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?php if (empty($secciones)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-collection" style="font-size: 3rem; color: #ccc;"></i>
                            <h5 class="text-muted mt-3">No hay secciones creadas</h5>
                            <p class="text-muted">Comienza agregando tu primera sección al curso</p>
                            <button class="btn btn-primary" onclick="agregarSeccion()">
                                <i class="bi bi-plus-circle"></i> Crear primera sección
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<!-- Modal para agregar/editar contenido -->
<div class="modal fade" id="modalContenido" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalContenidoLabel">Agregar contenido</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formContenido">
                    <input type="hidden" id="idSeccion" name="idSeccion">
                    <input type="hidden" id="idContenido" name="idContenido">
                    <input type="hidden" id="tipoContenido" name="tipo">
                    
                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" class="form-control" id="tituloContenido" name="titulo" required>
                    </div>
                    
                    <div class="mb-3" id="campoArchivo">
                        <label class="form-label">Archivo</label>
                        <input type="file" class="form-control" id="archivoContenido" name="archivo" 
                               accept=".mp4,.avi,.mov,.pdf">
                    </div>
                    
                    <div class="mb-3" id="campoDuracion" style="display: none;">
                        <label class="form-label">Duración (mm:ss)</label>
                        <input type="text" class="form-control" id="duracionContenido" name="duracion" 
                               placeholder="05:30">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Descripción (opcional)</label>
                        <textarea class="form-control" id="descripcionContenido" name="descripcion" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarContenido()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales
let idCurso = <?= $idCurso ?>;

// Funciones principales
function agregarSeccion() {
    Swal.fire({
        title: 'Nueva sección',
        input: 'text',
        inputLabel: 'Título de la sección',
        inputPlaceholder: 'Ej: Introducción al curso',
        showCancelButton: true,
        confirmButtonText: 'Crear',
        cancelButtonText: 'Cancelar',
        inputValidator: (value) => {
            if (!value) {
                return 'El título es requerido';
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Aquí haríamos la llamada AJAX para crear la sección
            crearSeccion(result.value);
        }
    });
}

function crearSeccion(titulo) {
    // Simular creación de sección (aquí iría la llamada AJAX real)
    console.log('Creando sección:', titulo);
    
    // Por ahora recargar la página
    location.reload();
}

function toggleSeccion(idSeccion) {
    const contenido = document.getElementById(`seccion-${idSeccion}`);
    const icono = contenido.parentElement.querySelector('.bi-chevron-down, .bi-chevron-up');
    
    if (contenido.style.display === 'none') {
        contenido.style.display = 'block';
        icono.className = 'bi bi-chevron-up ms-2';
    } else {
        contenido.style.display = 'none';
        icono.className = 'bi bi-chevron-down ms-2';
    }
}

function agregarContenido(idSeccion, tipo) {
    document.getElementById('idSeccion').value = idSeccion;
    document.getElementById('idContenido').value = '';
    document.getElementById('tipoContenido').value = tipo;
    document.getElementById('modalContenidoLabel').textContent = `Agregar ${tipo === 'video' ? 'video' : 'PDF'}`;
    
    // Mostrar/ocultar campos según el tipo
    if (tipo === 'video') {
        document.getElementById('campoDuracion').style.display = 'block';
        document.getElementById('archivoContenido').accept = '.mp4,.avi,.mov';
    } else {
        document.getElementById('campoDuracion').style.display = 'none';
        document.getElementById('archivoContenido').accept = '.pdf';
    }
    
    // Limpiar formulario
    document.getElementById('formContenido').reset();
    document.getElementById('idSeccion').value = idSeccion;
    document.getElementById('tipoContenido').value = tipo;
    
    // Mostrar modal
    new bootstrap.Modal(document.getElementById('modalContenido')).show();
}

function guardarContenido() {
    const form = document.getElementById('formContenido');
    const formData = new FormData(form);
    
    // Aquí iría la llamada AJAX para guardar el contenido
    console.log('Guardando contenido...');
    
    // Por ahora simular éxito
    Swal.fire({
        icon: 'success',
        title: '¡Contenido agregado!',
        text: 'El contenido se ha agregado correctamente.',
        confirmButtonText: 'Aceptar'
    }).then(() => {
        location.reload();
    });
}

function editarContenido(idContenido) {
    // Aquí cargaríamos los datos del contenido para editarlo
    console.log('Editando contenido:', idContenido);
}

function eliminarContenido(idContenido) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Aquí iría la llamada AJAX para eliminar
            console.log('Eliminando contenido:', idContenido);
            location.reload();
        }
    });
}

function eliminarSeccion(idSeccion) {
    Swal.fire({
        title: '¿Eliminar sección?',
        text: 'Se eliminará la sección y todo su contenido',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Aquí iría la llamada AJAX para eliminar
            console.log('Eliminando sección:', idSeccion);
            
            // Llamada AJAX real para eliminar sección
            $.ajax({
                url: 'ajax/curso_secciones.ajax.php',
                method: 'POST',
                data: {
                    action: 'eliminar_seccion',
                    id: idSeccion
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Error de conexión', 'error');
                }
            });
        }
    });
}

function actualizarTituloSeccion(idSeccion, nuevoTitulo) {
    // Llamada AJAX para actualizar el título
    $.ajax({
        url: 'ajax/curso_secciones.ajax.php',
        method: 'POST',
        data: {
            action: 'actualizar_seccion',
            id: idSeccion,
            titulo: nuevoTitulo
        },
        dataType: 'json',
        success: function(response) {
            if (!response.success) {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function() {
            console.error('Error al actualizar título de sección');
        }
    });
}

function agregarContenido(idSeccion, tipo) {
    // Llenar modal con tipo seleccionado
    $('#modalContenido #tipoContenido').val(tipo);
    toggleCamposPorTipo(tipo);
    
    $('#modalContenido').modal('show');
    $('#modalContenido').data('seccion-id', idSeccion);
    $('#modalContenido').data('editing', false);
}

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

// Event listeners adicionales
$(document).ready(function() {
    // Cambio de tipo de contenido en modal
    $('#tipoContenido').on('change', function() {
        toggleCamposPorTipo($(this).val());
    });
    
    // Guardar contenido desde modal
    $('#guardarContenido').on('click', function() {
        const formData = new FormData();
        const titulo = $('#tituloContenido').val().trim();
        const tipo = $('#tipoContenido').val();
        const descripcion = $('#descripcionContenido').val().trim();
        const duracion = $('#duracionContenido').val();
        const archivo = $('#archivoContenido')[0].files[0];
        const seccionId = $('#modalContenido').data('seccion-id');
        
        if (!titulo) {
            Swal.fire('Error', 'El título es obligatorio', 'error');
            return;
        }
        
        if ((tipo === 'video' || tipo === 'pdf') && !archivo) {
            Swal.fire('Error', 'Debes seleccionar un archivo', 'error');
            return;
        }
        
        // Agregar datos al FormData
        formData.append('action', 'crear_contenido');
        formData.append('id_seccion', seccionId);
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
        
        // Llamada AJAX
        $.ajax({
            url: 'ajax/curso_secciones.ajax.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire('Éxito', response.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Error de conexión', 'error');
            }
        });
    });
});

// Inicializar cuando la página esté lista
document.addEventListener('DOMContentLoaded', function() {
    // Hacer las secciones colapsables por defecto
    document.querySelectorAll('.section-content').forEach((element, index) => {
        if (index > 0) { // Mantener la primera abierta
            element.style.display = 'none';
        }
    });
});
</script>
