<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar que el usuario sea profesor
if (!ControladorGeneral::ctrUsuarioTieneAlgunRol(['profesor', 'admin'])) {
    echo '<script>window.location = "' . $rutaBase . 'accesoDenegado";</script>';
    return;
}
?>

<!-- CSS específico para estudiantes -->
<link rel="stylesheet" href="vistas/assets/css/pages/estudiantesProfe.css">

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Header de la página -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="mb-0">
                                <i class="fas fa-users me-2"></i>
                                Gestión de Estudiantes
                            </h4>
                            <p class="mb-0 small">Administra las inscripciones pendientes de tus estudiantes</p>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-light btn-sm" id="btnRefrescar" type="button">
                                <i class="fas fa-sync-alt me-1"></i>
                                Actualizar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros y búsqueda -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" class="form-control" id="buscarEstudiante"
                                    placeholder="Buscar estudiante...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="filtroEstado">
                                <option value="">Todos los estados</option>
                                <option value="pendiente">Pendientes</option>
                                <option value="activo">Activos</option>
                                <option value="cancelado">Cancelados</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="filtroCurso">
                                <option value="">Todos los cursos</option>
                                <!-- Se llenarán dinámicamente -->
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de estudiantes -->
            <div class="card shadow">
                <div class="card-header bg-white">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="mb-0">
                                <i class="fas fa-table me-2"></i>
                                Lista de Estudiantes
                            </h5>
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-info" id="totalEstudiantes">0 estudiantes</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Loading -->
                    <div id="loadingEstudiantes" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2 text-muted">Cargando estudiantes...</p>
                    </div>

                    <!-- Tabla -->
                    <div id="tablaEstudiantes" class="table-responsive" style="display: none;">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Estudiante</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>Inscripciones Pendientes</th>
                                    <th>Inscripciones Activas</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="bodyTablaEstudiantes">
                                <!-- Se llenará dinámicamente -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Mensaje sin datos -->
                    <div id="sinDatos" class="text-center py-5" style="display: none;">
                        <div class="text-muted">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <h5>No hay estudiantes para mostrar</h5>
                            <p>No se encontraron estudiantes con inscripciones pendientes en tus cursos.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver detalles del estudiante -->
<div class="modal fade" id="modalDetallesEstudiante" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user me-2"></i>
                    Detalles del Estudiante
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="contenidoModalEstudiante">
                <!-- Se llenará dinámicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para confirmar activación de inscripción -->
<div class="modal fade" id="modalConfirmarActivacion" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle me-2"></i>
                    Confirmar Activación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="fas fa-exclamation-triangle fa-2x text-warning mb-3"></i>
                    <h6>¿Estás seguro de activar esta inscripción?</h6>
                    <p class="text-muted mb-0">Esta acción permitirá al estudiante acceder al curso inmediatamente.</p>
                </div>
                <hr>
                <div id="datosActivacion">
                    <!-- Se llenarán dinámicamente -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="btnConfirmarActivacion">
                    <i class="fas fa-check me-1"></i>
                    Activar Inscripción
                </button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript específico para estudiantes -->
<script src="vistas/assets/js/pages/estudiantesProfe.js"></script>