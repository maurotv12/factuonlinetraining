<?php
// Iniciar sesión una sola vez al principio
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar acceso (solo profesores)
if (!ControladorGeneral::ctrUsuarioTieneAlgunRol(['profesor'])) {
    echo '<div class="alert alert-danger">No tienes permisos para acceder a esta página.</div>';
    return;
}

// Importaciones necesarias
require_once "controladores/cursos.controlador.php";

// **CAMBIO CLAVE:** Usar el método unificado en lugar del específico de profesores
$resultado = ControladorCursos::ctrProcesarFormularioCreacion();

// Usar el controlador para cargar datos necesarios
$datosCreacion = ControladorCursos::ctrCargarCreacionCurso();
$categorias = $datosCreacion['categorias'];

// Obtener datos del profesor logueado
$idProfesorLogueado = $_SESSION['id'] ?? null;
$nombreProfesorLogueado = $_SESSION['nombre'] ?? 'Profesor';
?>

<div class="crear-curso-profesor-container">
    <div class="row">
        <div class="col-12">
            <!-- Badge información del profesor -->
            <div class="profesor-info-badge">
                <i class="fas fa-user-tie"></i>
                Creando curso como: <strong><?php echo htmlspecialchars($nombreProfesorLogueado); ?></strong>
            </div>

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
                            document.getElementById('form-crear-curso-profesor').reset();
                        });
                    </script>
                <?php endif; ?>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-plus-circle"></i>
                        Crear Nuevo Curso
                        <span class="curso-draft-indicator">Borrador</span>
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Barra de progreso -->
                    <div class="form-progress">
                        <div class="form-progress-bar" id="progress-bar"></div>
                    </div>

                    <form id="form-crear-curso-profesor" method="POST" enctype="multipart/form-data">
                        <!-- Campo oculto para ID del profesor -->
                        <input type="hidden" name="id_profesor" value="<?php echo htmlspecialchars($idProfesorLogueado); ?>">

                        <!-- Información básica del curso -->
                        <div class="section-header">
                            <h5><i class="fas fa-info-circle"></i> Información Básica</h5>
                        </div>

                        <div class="mb-3">
                            <label for="nombre" class="form-label">
                                Nombre del Curso <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                            <div class="contador-caracteres">
                                <span id="contador-nombre">0</span>/100 caracteres
                            </div>
                            <div id="mensaje-nombre"></div>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">
                                Descripción del Curso <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="6" required
                                placeholder="Describe detalladamente de qué trata tu curso, qué lo hace único y por qué los estudiantes deberían elegirlo..."></textarea>
                            <div class="contador-caracteres">
                                <span id="contador-descripcion">0</span>/2000 caracteres
                            </div>
                            <div class="help-text">Una buena descripción ayuda a los estudiantes a entender el valor de tu curso.</div>
                        </div>

                        <!-- Contenido del curso -->
                        <div class="section-header">
                            <h5><i class="fas fa-graduation-cap"></i> Contenido del Curso</h5>
                        </div>

                        <div class="mb-3">
                            <label for="lo_que_aprenderas" class="form-label">
                                ¿Qué aprenderán los estudiantes? <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" id="lo_que_aprenderas" name="lo_que_aprenderas" rows="6" required
                                placeholder="Ejemplo:&#10;Aprenderás a utilizar herramientas avanzadas de diseño gráfico.&#10;Dominarás las técnicas de ilustración digital.&#10;Crearás proyectos profesionales desde cero."></textarea>
                            <div class="form-text">Cada línea se mostrará como una viñeta en la vista del curso. Máximo 10 líneas.</div>
                            <div id="error-aprendizajes"></div>
                        </div>

                        <div class="mb-3">
                            <label for="requisitos" class="form-label">
                                Requisitos Previos
                            </label>
                            <textarea class="form-control" id="requisitos" name="requisitos" rows="4"
                                placeholder="Ejemplo:&#10;Conocimientos básicos de diseño.&#10;Computador con Adobe Photoshop instalado.&#10;Ganas de aprender y practicar."></textarea>
                            <div class="form-text">Cada línea se mostrará como una viñeta. Si no hay requisitos específicos, puedes dejarlo vacío.</div>
                            <div id="error-requisitos"></div>
                        </div>

                        <div class="mb-3">
                            <label for="para_quien" class="form-label">
                                ¿Para quién es este curso? <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" id="para_quien" name="para_quien" rows="4" required
                                placeholder="Ejemplo:&#10;Diseñadores gráficos que quieran mejorar sus habilidades.&#10;Emprendedores que deseen crear sus propias piezas gráficas.&#10;Estudiantes de arte y diseño."></textarea>
                            <div class="form-text">Cada línea se mostrará como una viñeta. Define claramente tu audiencia objetivo.</div>
                            <div id="error-para-quien"></div>
                        </div>

                        <!-- Configuración del curso -->
                        <div class="section-header">
                            <h5><i class="fas fa-cog"></i> Configuración</h5>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_categoria" class="form-label">
                                        Categoría <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="id_categoria" name="id_categoria" required>
                                        <option value="">Selecciona una categoría</option>
                                        <?php foreach ($categorias as $categoria): ?>
                                            <option value="<?php echo $categoria['id']; ?>">
                                                <?php echo htmlspecialchars($categoria['nombre']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="estado" class="form-label">
                                        Estado del Curso <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="estado" name="estado" required>
                                        <option value="borrador">Borrador (No publicado)</option>
                                        <option value="revision">En Revisión</option>
                                        <option value="activo">Activo (Publicado)</option>
                                    </select>
                                    <div class="help-text">Los cursos en borrador no aparecen públicamente hasta ser activados.</div>
                                </div>
                            </div>
                        </div>

                        <!-- Archivos multimedia -->
                        <div class="section-header">
                            <h5><i class="fas fa-image"></i> Archivos Multimedia</h5>
                        </div>

                        <div class="mb-3">
                            <label for="imagen" class="form-label">
                                Banner del Curso <span class="text-danger">*</span>
                            </label>
                            <input class="form-control" type="file" id="imagen" name="imagen" accept="image/*" required>
                            <div class="form-text">
                                Formatos permitidos: JPG, PNG, GIF, WebP. Resolución recomendada: 1200x675px (16:9)
                            </div>
                            <div id="vista-previa-imagen"></div>
                            <div id="error-imagen"></div>
                        </div>

                        <div class="mb-3">
                            <label for="video_promocional" class="form-label">
                                Video Promocional
                            </label>
                            <input class="form-control" type="file" id="video_promocional" name="video_promocional" accept="video/*">
                            <div class="form-text">
                                Formatos permitidos: MP4, AVI, MOV. Duración máxima: 2 minutos. Tamaño máximo: 100MB
                            </div>
                            <div id="info-video"></div>
                            <div class="help-text">El video promocional ayuda a los estudiantes a conocer mejor tu curso.</div>
                        </div>

                        <!-- Precio -->
                        <div class="section-header">
                            <h5><i class="fas fa-dollar-sign"></i> Precio</h5>
                        </div>

                        <div class="mb-3">
                            <label for="valor" class="form-label">
                                Precio del Curso <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="valor" name="valor" min="0" step="1000" required>
                                <span class="input-group-text">COP</span>
                            </div>
                            <div class="help-text">Precio en pesos colombianos. Usa 0 para cursos gratuitos.</div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="listadoCursosProfesor" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver al Listado
                            </a>
                            <button type="submit" class="btn-crear-curso-profesor" id="btn-crear-curso">
                                <i class="fas fa-plus-circle"></i> Crear Curso
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Incluir el archivo JavaScript específico para profesores -->
<script src="vistas/assets/js/pages/crearCursoProfe.js"></script>

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
                window.location = "listadoCursosProfesor";
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