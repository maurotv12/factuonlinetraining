<?php
// Verificar que el usuario sea profesor
if (!ControladorGeneral::ctrUsuarioTieneAlgunRol(['profesor', 'admin'])) {
    echo '<script>window.location = "accesoDenegado";</script>';
    return;
}
?>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-users-cog mr-2"></i>
                    Gestión de Estudiantes
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="inicio">Inicio</a></li>
                    <li class="breadcrumb-item active">Estudiantes</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">

        <!-- Filtros y controles -->
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="filtroTipo">Filtrar por tipo:</label>
                    <select class="form-control" id="filtroTipo">
                        <option value="">Todos</option>
                        <option value="preinscrito">Preinscripciones</option>
                        <option value="inscrito">Inscripciones</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="filtroEstado">Filtrar por estado:</label>
                    <select class="form-control" id="filtroEstado">
                        <option value="">Todos</option>
                        <option value="preinscrito">Preinscrito</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="activo">Activo</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-primary" id="btnRefrescar" title="Refrescar datos">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                    <button type="button" class="btn btn-outline-success" id="btnExportar" title="Exportar datos">
                        <i class="fas fa-download"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Tarjeta principal -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-graduation-cap mr-2"></i>
                    Estudiantes en mis Cursos
                </h3>
                <div class="card-tools">
                    <span class="badge badge-info" id="totalRegistros">0 registros</span>
                </div>
            </div>

            <div class="card-body">
                <!-- Indicador de carga -->
                <div id="loadingIndicator" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                    <p class="mt-2 text-muted">Cargando datos de estudiantes...</p>
                </div>

                <!-- Tabla de estudiantes -->
                <div id="tablaContainer" style="display: none;">
                    <div class="table-responsive">
                        <table id="tablaEstudiantesCursos" class="table table-striped table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Estudiante</th>
                                    <th>Curso</th>
                                    <th>Categoría</th>
                                    <th>Tipo</th>
                                    <th>Estado</th>
                                    <th>Fecha Registro</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Los datos se cargarán dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mensaje cuando no hay datos -->
                <div id="noDataMessage" class="text-center py-5" style="display: none;">
                    <div class="mb-3">
                        <i class="fas fa-users fa-3x text-muted"></i>
                    </div>
                    <h5 class="text-muted">No hay estudiantes registrados</h5>
                    <p class="text-muted">
                        Aún no tienes estudiantes preinscriptos o inscriptos en tus cursos.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal para ver detalles del estudiante -->
<div class="modal fade" id="modalDetallesEstudiante" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-graduate mr-2"></i>
                    Detalles del Estudiante
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="contenidoDetallesEstudiante">
                    <!-- Se cargará dinámicamente -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts específicos para esta página -->
<script src="/cursosApp/App/vistas/assets/js/pages/estudiantesPro.js"></script>

<!-- Estilos específicos para esta página -->
<link rel="stylesheet" href="/cursosApp/App/vistas/assets/css/pages/estudiantesPro.css">