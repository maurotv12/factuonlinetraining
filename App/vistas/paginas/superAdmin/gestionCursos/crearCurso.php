<?php
// Verificar acceso (solo administradores y profesores)
if (!ControladorGeneral::ctrUsuarioTieneAlgunRol(['admin', 'superadmin', 'profesor'])) {
    echo '<div class="alert alert-danger">No tienes permisos para acceder a esta página.</div>';
    return;
}

// Importaciones necesarias
require_once "controladores/cursos.controlador.php";

// Procesar el formulario si se envió
$resultado = ControladorCursos::ctrProcesarFormularioCreacion();

// Usar el controlador para cargar datos necesarios
$datosCreacion = ControladorCursos::ctrCargarCreacionCurso();
$profesores = $datosCreacion['profesores'];
$categorias = $datosCreacion['categorias'];
?>

<div class="crear-curso-container">
    <div class="row">
        <div class="col-12">
            <!-- Mostrar mensajes de resultado -->
            <?php if ($resultado): ?>
                <?php if ($resultado['error']): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Error:</strong> <?php echo $resultado['mensaje']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php if (isset($resultado['campo'])): ?>
                        <script>
                            // Resaltar campo con error al cargar la página
                            document.addEventListener('DOMContentLoaded', function() {
                                const campo = document.getElementById('<?php echo $resultado['campo']; ?>');
                                if (campo) {
                                    campo.classList.add('is-invalid');
                                    campo.scrollIntoView({
                                        behavior: 'smooth',
                                        block: 'center'
                                    });
                                    campo.focus();
                                }
                            });
                        </script>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i>
                        <strong>¡Éxito!</strong> <?php echo $resultado['mensaje']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <script>
                        // Limpiar formulario después de creación exitosa
                        document.addEventListener('DOMContentLoaded', function() {
                            document.getElementById('form-crear-curso').reset();
                        });
                    </script>
                <?php endif; ?>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="bi bi-plus-circle"></i>
                        Crear Nuevo Curso
                    </h4>
                </div>
                <div class="card-body">
                    <form id="form-crear-curso" method="POST" enctype="multipart/form-data">

                        <!-- Información básica del curso -->
                        <div class="section-header">
                            <h5><i class="bi bi-info-circle"></i> Información Básica</h5>
                        </div>

                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre del Curso <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required></textarea>
                        </div>

                        <!-- Contenido del curso -->
                        <div class="section-header">
                            <h5><i class="bi bi-book"></i> Contenido del Curso</h5>
                        </div>

                        <div class="mb-3">
                            <label for="lo_que_aprenderas" class="form-label">
                                Lo que aprenderás con este curso
                                <span class="text-muted">(Una frase por línea, máximo 100 caracteres cada una)</span>
                            </label>
                            <textarea class="form-control" id="lo_que_aprenderas" name="lo_que_aprenderas" rows="5"
                                placeholder="Ejemplo:&#10;Aprenderás a utilizar herramientas avanzadas de diseño gráfico.&#10;Dominarás las técnicas de ilustración digital."></textarea>
                            <div class="form-text">Cada línea se mostrará como una viñeta en la vista del curso.</div>
                        </div>

                        <div class="mb-3">
                            <label for="requisitos" class="form-label">
                                Requisitos
                                <span class="text-muted">(Una frase por línea, máximo 100 caracteres cada una)</span>
                            </label>
                            <textarea class="form-control" id="requisitos" name="requisitos" rows="4"
                                placeholder="Ejemplo:&#10;Conocimientos básicos de diseño.&#10;Computador con Adobe Photoshop instalado."></textarea>
                            <div class="form-text">Cada línea se mostrará como una viñeta en la vista del curso.</div>
                        </div>

                        <div class="mb-3">
                            <label for="para_quien" class="form-label">
                                Para quién es este curso
                                <span class="text-muted">(Una frase por línea, máximo 100 caracteres cada una)</span>
                            </label>
                            <textarea class="form-control" id="para_quien" name="para_quien" rows="4"
                                placeholder="Ejemplo:&#10;Diseñadores gráficos que quieran mejorar sus habilidades.&#10;Emprendedores que deseen crear sus propias piezas gráficas."></textarea>
                            <div class="form-text">Cada línea se mostrará como una viñeta en la vista del curso.</div>
                        </div>

                        <!-- Configuración del curso -->
                        <div class="section-header">
                            <h5><i class="bi bi-gear"></i> Configuración</h5>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="categoria" class="form-label">Categoría <span class="text-danger">*</span></label>
                                    <select class="form-select" id="categoria" name="categoria" required>
                                        <option value="" selected disabled>Selecciona una categoría</option>
                                        <?php foreach ($categorias as $cat): ?>
                                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="profesor" class="form-label">Profesor <span class="text-danger">*</span></label>
                                    <select class="form-select" id="profesor" name="profesor" required>
                                        <option value="" selected disabled>Selecciona un profesor</option>
                                        <?php foreach ($profesores as $prof): ?>
                                            <option value="<?= $prof['id'] ?>">
                                                <?= htmlspecialchars($prof['nombre']) ?> (<?= htmlspecialchars($prof['email']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Archivos multimedia -->
                        <div class="section-header">
                            <h5><i class="bi bi-image"></i> Archivos Multimedia</h5>
                        </div>

                        <div class="mb-3">
                            <label for="imagen" class="form-label">
                                Imagen del Curso <span class="text-danger">*</span>
                                <small class="text-muted">(Dimensiones: 600x400 píxeles)</small>
                            </label>
                            <input class="form-control" type="file" id="imagen" name="imagen" accept="image/*" required>
                            <div class="form-text">Formatos permitidos: JPG, PNG, GIF, WebP. Tamaño aceptado: 600x400px</div>
                        </div>

                        <div class="mb-3">
                            <label for="video" class="form-label">Video Promocional (opcional)</label>
                            <input class="form-control" type="file" id="video" name="video" accept="video/*">
                            <div class="form-text">Formatos de video soportados: MP4, AVI, MOV, WebM</div>
                        </div>

                        <!-- Precio -->
                        <div class="section-header">
                            <h5><i class="bi bi-currency-dollar"></i> Precio</h5>
                        </div>

                        <div class="mb-3">
                            <label for="precio" class="form-label">Precio (COP) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="precio" name="precio" min="0" required>
                                <span class="input-group-text">COP</span>
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="d-flex justify-content-between">
                            <a href="listadoCursos" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Volver al listado
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Crear Curso
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Incluir el archivo JavaScript para la página -->
<script src="vistas/assets/js/pages/crearCurso.js"></script>

<?php
// Mostrar mensajes de resultado después del procesamiento
if ($resultado) {
    if (!$resultado['error']) {
        echo '<script>
            Swal.fire({
                icon: "success",
                title: "¡Curso creado!",
                text: "' . $resultado['mensaje'] . '",
                confirmButtonText: "Aceptar"
            }).then(() => {
                window.location = "listadoCursos";
            });
        </script>';
    } else {
        echo '<script>
            Swal.fire({
                icon: "error", 
                title: "Error",
                text: "' . $resultado['mensaje'] . '",
                confirmButtonText: "Aceptar"
            });
        </script>';
    }
}
?>