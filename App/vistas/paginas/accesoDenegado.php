<?php

/**
 * Página de acceso denegado
 * Se muestra cuando un usuario no tiene permisos para acceder a una página
 */
?>

<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mt-5">
                    <div class="card-header bg-warning">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-exclamation-triangle"></i>
                            Acceso Denegado
                        </h4>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <i class="fas fa-ban fa-5x text-warning"></i>
                        </div>
                        <h3 class="text-warning">Acceso Restringido</h3>
                        <p class="lead">No tienes permisos para acceder a esta página.</p>
                        <p class="text-muted">
                            Si crees que esto es un error, contacta con el administrador del sistema.
                        </p>

                        <div class="mt-4">
                            <a href="inicio" class="btn btn-primary">
                                <i class="fas fa-home"></i>
                                Volver al Inicio
                            </a>
                            <a href="javascript:history.back()" class="btn btn-secondary ml-2">
                                <i class="fas fa-arrow-left"></i>
                                Volver Atrás
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        border-radius: 10px 10px 0 0;
    }

    .fa-ban {
        opacity: 0.7;
    }
</style>