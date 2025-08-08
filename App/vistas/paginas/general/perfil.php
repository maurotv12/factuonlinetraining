<?php
// Procesar actualización de perfil si viene del formulario
$actualizarPerfil = new ControladorUsuarios();
$actualizarPerfil->ctrActualizarPerfilUsuario();

// Procesar cambio de foto
$actualizarFoto = new ControladorUsuarios();
$actualizarFoto->ctrCambiarFoto();

// Procesar cambio de contraseña
$actualizarPassword = new ControladorAutenticacion();
$actualizarPassword->ctrCambiarPassword();
?>


<div class="page-content">
  <div class="page-title">
    <div class="row">
      <div class="col-12 col-md-6 order-md-1 order-last">
        <h3>Mi Perfil</h3>
        <div class="widget-user-header">
          <h4 class="widget-user-username">@<?php echo htmlspecialchars($usuario["usuarioLink"] ?? $usuario["nombre"]); ?></h4>
          <h6 class="widget-user-desc">Miembro desde: <?php echo date('F Y', strtotime($usuario["fecha_registro"])); ?></h6>
        </div>
      </div>
      <div class="col-12 col-md-6 order-md-2 order-first">
        <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
          <ol class="breadcrumb">
            <li class="breadcrumb-item active" aria-current="page">Perfil</li>
            <li class="breadcrumb-item"><a href="inicio">Inicio</a></li>
          </ol>
        </nav>
      </div>
    </div>
  </div>

  <div class="row">
    <!-- Columna izquierda - Información del perfil -->
    <div class="col-12 col-lg-4">
      <!-- Card de foto y datos básicos -->
      <div class="card">
        <div class="card-body text-center">
          <!-- Foto del perfil -->
          <div class="profile-photo-container">
            <div class="avatar avatar-xl me-3">
              <img src="<?php echo !empty($usuario["foto"]) ? $usuario["foto"] : 'vistas/img/usuarios/default.png'; ?>"
                alt="Foto de <?php echo htmlspecialchars($usuario["nombre"]); ?>"
                class="profile-photo">
            </div>
            <div class="photo-overlay" onclick="openPhotoModal()">
              <i class="bi bi-camera"></i>
              <span>Cambiar foto</span>
            </div>
          </div>

          <!-- Información básica -->
          <div class="profile-info">
            <h5 class="font-bold"><?php echo htmlspecialchars($usuario["nombre"]); ?></h5>
            <p class="profile-email"><?php echo htmlspecialchars($usuario["email"]); ?></p>

            <?php if (!empty($usuario["pais"]) || !empty($usuario["ciudad"])): ?>
              <div class="profile-location">
                <i class="bi bi-geo-alt"></i>
                <span>
                  <?php
                  $ubicacion = [];
                  if (!empty($usuario["ciudad"])) $ubicacion[] = $usuario["ciudad"];
                  if (!empty($usuario["pais"])) $ubicacion[] = $usuario["pais"];
                  echo implode(', ', $ubicacion);
                  ?>
                </span>
              </div>
            <?php endif; ?>
          </div>

          <!-- Botones de acción -->
          <div class="mt-4">
            <button type="button" class="btn btn-primary me-2" onclick="openPhotoModal()">
              <i class="bi bi-camera"></i> Cambiar Foto
            </button>
            <button type="button" class="btn btn-outline-accent" onclick="changePassword()">
              <i class="bi bi-key"></i> Cambiar Contraseña
            </button>
          </div>
        </div>
      </div>

      <!-- Card de información adicional -->
      <div class="card">
        <div class="card-header">
          <h6 class="card-title">Información Adicional</h6>
        </div>
        <div class="card-body">
          <?php if (!empty($usuario["profesion"])): ?>
            <div class="mb-3">
              <strong>Profesión:</strong>
              <p class="text-gray-custom mb-0"><?php echo htmlspecialchars($usuario["profesion"]); ?></p>
            </div>
          <?php endif; ?>


          <?php if (!empty($usuario["telefono"]) && ($usuario["mostrar_telefono"] ?? 0)): ?>
            <div class="mb-3">
              <strong>Teléfono:</strong>
              <p class="text-gray-custom mb-0"><?php echo htmlspecialchars($usuario["telefono"]); ?></p>
            </div>
          <?php endif; ?>

          <?php if (!empty($usuario["numero_identificacion"]) && ($usuario["mostrar_identificacion"] ?? 0)): ?>
            <div class="mb-3">
              <strong>Identificación:</strong>
              <p class="text-gray-custom mb-0"><?php echo htmlspecialchars($usuario["numero_identificacion"]); ?></p>
            </div>
          <?php endif; ?>

          <div class="mb-3">
            <strong>Fecha de registro:</strong>
            <p class="text-gray-custom mb-0"><?php echo date('d/m/Y', strtotime($usuario["fecha_registro"])); ?></p>
          </div>

          <div>
            <strong>Estado:</strong>
            <span class="badge bg-success">Activo</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Columna derecha - Formulario editable -->
    <div class="col-12 col-lg-8">
      <div class="card">
        <div class="card-header">
          <h6 class="card-title">Información Personal</h6>
          <small class="text-gray-custom">Haz clic en cualquier campo para editarlo</small>
        </div>
        <div class="card-body">
          <!-- Campo Nombre -->
          <div class="editable-field" data-field="nombre">
            <label class="form-label">Nombre Completo *</label>
            <div class="display-value">
              <span id="display-nombre"><?php echo htmlspecialchars($usuario["nombre"]); ?></span>
              <i class="bi bi-pencil edit-icon"></i>
            </div>
            <div class="edit-mode">
              <input type="text" class="form-control" value="<?php echo htmlspecialchars($usuario["nombre"]); ?>" placeholder="Ingresa tu nombre completo">
              <div class="edit-actions">
                <button type="button" class="btn btn-success btn-sm" onclick="saveField('nombre')">
                  <i class="bi bi-check"></i> Guardar
                </button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="cancelEdit('nombre')">
                  <i class="bi bi-x"></i> Cancelar
                </button>
              </div>
            </div>
          </div>

          <!-- Campo Email -->
          <div class="editable-field" data-field="email">
            <label class="form-label">Correo Electrónico *</label>
            <div class="display-value">
              <span id="display-email"><?php echo htmlspecialchars($usuario["email"]); ?></span>
              <i class="bi bi-pencil edit-icon"></i>
            </div>
            <div class="edit-mode">
              <input type="email" class="form-control" value="<?php echo htmlspecialchars($usuario["email"]); ?>" placeholder="Ingresa tu correo electrónico">
              <div class="edit-actions">
                <button type="button" class="btn btn-success btn-sm" onclick="saveField('email')">
                  <i class="bi bi-check"></i> Guardar
                </button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="cancelEdit('email')">
                  <i class="bi bi-x"></i> Cancelar
                </button>
              </div>
            </div>
          </div>

          <!-- Campo País -->
          <div class="editable-field" data-field="pais">
            <label class="form-label">País</label>
            <div class="display-value">
              <span id="display-pais"><?php echo !empty($usuario["pais"]) ? htmlspecialchars($usuario["pais"]) : 'No especificado'; ?></span>
              <i class="bi bi-pencil edit-icon"></i>
            </div>
            <div class="edit-mode">
              <input type="text" class="form-control" value="<?php echo htmlspecialchars($usuario["pais"] ?? ''); ?>" placeholder="Ingresa tu país">
              <div class="edit-actions">
                <button type="button" class="btn btn-success btn-sm" onclick="saveField('pais')">
                  <i class="bi bi-check"></i> Guardar
                </button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="cancelEdit('pais')">
                  <i class="bi bi-x"></i> Cancelar
                </button>
              </div>
            </div>
          </div>

          <!-- Campo Ciudad -->
          <div class="editable-field" data-field="ciudad">
            <label class="form-label">Ciudad</label>
            <div class="display-value">
              <span id="display-ciudad"><?php echo !empty($usuario["ciudad"]) ? htmlspecialchars($usuario["ciudad"]) : 'No especificada'; ?></span>
              <i class="bi bi-pencil edit-icon"></i>
            </div>
            <div class="edit-mode">
              <input type="text" class="form-control" value="<?php echo htmlspecialchars($usuario["ciudad"] ?? ''); ?>" placeholder="Ingresa tu ciudad">
              <div class="edit-actions">
                <button type="button" class="btn btn-success btn-sm" onclick="saveField('ciudad')">
                  <i class="bi bi-check"></i> Guardar
                </button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="cancelEdit('ciudad')">
                  <i class="bi bi-x"></i> Cancelar
                </button>
              </div>
            </div>
          </div>

          <!-- Campo Profesión -->
          <div class="editable-field" data-field="profesion">
            <label class="form-label">Profesión</label>
            <div class="display-value">
              <span id="display-profesion"><?php echo !empty($usuario["profesion"]) ? htmlspecialchars($usuario["profesion"]) : 'No especificada'; ?></span>
              <i class="bi bi-pencil edit-icon"></i>
            </div>
            <div class="edit-mode">
              <input type="text" class="form-control" value="<?php echo htmlspecialchars($usuario["profesion"] ?? ''); ?>" placeholder="Ej: Contador, Ingeniero, Médico, etc.">
              <div class="edit-actions">
                <button type="button" class="btn btn-success btn-sm" onclick="saveField('profesion')">
                  <i class="bi bi-check"></i> Guardar
                </button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="cancelEdit('profesion')">
                  <i class="bi bi-x"></i> Cancelar
                </button>
              </div>
            </div>
          </div>

          <!-- Campo Teléfono -->
          <div class="editable-field" data-field="telefono">
            <label class="form-label">Teléfono</label>
            <div class="display-value">
              <span id="display-telefono"><?php echo !empty($usuario["telefono"]) ? htmlspecialchars($usuario["telefono"]) : 'No especificado'; ?></span>
              <i class="bi bi-pencil edit-icon"></i>
            </div>
            <div class="edit-mode">
              <input type="tel" class="form-control" value="<?php echo htmlspecialchars($usuario["telefono"] ?? ''); ?>" placeholder="Ej: +57 300 123 4567">
              <div class="edit-actions">
                <button type="button" class="btn btn-success btn-sm" onclick="saveField('telefono')">
                  <i class="bi bi-check"></i> Guardar
                </button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="cancelEdit('telefono')">
                  <i class="bi bi-x"></i> Cancelar
                </button>
              </div>
            </div>
          </div>

          <!-- Campo Dirección -->
          <div class="editable-field" data-field="direccion">
            <label class="form-label">Dirección</label>
            <div class="display-value">
              <span id="display-direccion"><?php echo !empty($usuario["direccion"]) ? htmlspecialchars($usuario["direccion"]) : 'No especificada'; ?></span>
              <i class="bi bi-pencil edit-icon"></i>
            </div>
            <div class="edit-mode">
              <input type="text" class="form-control" value="<?php echo htmlspecialchars($usuario["direccion"] ?? ''); ?>" placeholder="Ej: Calle 123 #45-67">
              <div class="edit-actions">
                <button type="button" class="btn btn-success btn-sm" onclick="saveField('direccion')">
                  <i class="bi bi-check"></i> Guardar
                </button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="cancelEdit('direccion')">
                  <i class="bi bi-x"></i> Cancelar
                </button>
              </div>
            </div>
          </div>

          <!-- Campo Número de Identificación -->
          <div class="editable-field" data-field="numero_identificacion">
            <label class="form-label">Número de Identificación</label>
            <div class="display-value">
              <span id="display-numero_identificacion"><?php echo !empty($usuario["numero_identificacion"]) ? htmlspecialchars($usuario["numero_identificacion"]) : 'No especificado'; ?></span>
              <i class="bi bi-pencil edit-icon"></i>
            </div>
            <div class="edit-mode">
              <input type="text" class="form-control" value="<?php echo htmlspecialchars($usuario["numero_identificacion"] ?? ''); ?>" placeholder="Ej: 1234567890">
              <div class="edit-actions">
                <button type="button" class="btn btn-success btn-sm" onclick="saveField('numero_identificacion')">
                  <i class="bi bi-check"></i> Guardar
                </button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="cancelEdit('numero_identificacion')">
                  <i class="bi bi-x"></i> Cancelar
                </button>
              </div>
            </div>
          </div>

          <!-- Campo Biografía -->
          <div class="editable-field d-none" data-field="biografia">
            <label class="form-label">Biografía (Opcional)</label>
            <div class="display-value">
              <span id="display-biografia"><?php echo !empty($usuario["biografia"]) ? nl2br(htmlspecialchars($usuario["biografia"])) : 'No especificada'; ?></span>
              <i class="bi bi-pencil edit-icon"></i>
            </div>
            <div class="edit-mode">
              <textarea class="form-control" rows="4" placeholder="Escribe una breve descripción sobre ti, tu experiencia o intereses..."><?php echo htmlspecialchars($usuario["biografia"] ?? ''); ?></textarea>
              <div class="edit-actions">
                <button type="button" class="btn btn-success btn-sm" onclick="saveField('biografia')">
                  <i class="bi bi-check"></i> Guardar
                </button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="cancelEdit('biografia')">
                  <i class="bi bi-x"></i> Cancelar
                </button>
              </div>
            </div>
          </div>

          <!-- Configuración de Privacidad -->
          <div class="mt-4">
            <h6 class="text-primary-custom mb-3">
              <i class="bi bi-shield-lock"></i> Configuración de Privacidad
            </h6>

            <!-- Mostrar Email -->
            <div class="privacy-field mb-3">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="mostrar_email"
                  <?php echo ($usuario["mostrar_email"] ?? 0) ? 'checked' : ''; ?>
                  onchange="updatePrivacySetting('mostrar_email', this.checked)">
                <label class="form-check-label" for="mostrar_email">
                  <strong>Mostrar email públicamente</strong>
                  <small class="d-block text-gray-custom">Otros usuarios podrán ver tu correo electrónico</small>
                </label>
              </div>
            </div>

            <!-- Mostrar Teléfono -->
            <div class="privacy-field mb-3">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="mostrar_telefono"
                  <?php echo ($usuario["mostrar_telefono"] ?? 0) ? 'checked' : ''; ?>
                  onchange="updatePrivacySetting('mostrar_telefono', this.checked)">
                <label class="form-check-label" for="mostrar_telefono">
                  <strong>Mostrar teléfono públicamente</strong>
                  <small class="d-block text-gray-custom">Otros usuarios podrán ver tu número de teléfono</small>
                </label>
              </div>
            </div>

            <!-- Mostrar Identificación -->
            <div class="privacy-field mb-3">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="mostrar_identificacion"
                  <?php echo ($usuario["mostrar_identificacion"] ?? 0) ? 'checked' : ''; ?>
                  onchange="updatePrivacySetting('mostrar_identificacion', this.checked)">
                <label class="form-check-label" for="mostrar_identificacion">
                  <strong>Mostrar número de identificación públicamente</strong>
                  <small class="d-block text-gray-custom">Otros usuarios podrán ver tu número de identificación</small>
                </label>
              </div>
            </div>
          </div>

          <!-- Información de ayuda -->
          <div class="mt-4 p-3" style="background-color: rgba(54, 130, 196, 0.1); border-radius: 8px;">
            <h6 class="text-primary-custom mb-2">
              <i class="bi bi-info-circle"></i> Consejos para tu perfil
            </h6>
            <ul class="text-gray-custom mb-0" style="font-size: 0.9rem;">
              <li>Mantén tu información actualizada para una mejor experiencia</li>
              <li>Una foto de perfil clara ayuda a otros usuarios a identificarte</li>
              <li>Los campos marcados con * son obligatorios</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal para cambiar foto -->
<div class="modal fade" id="modal" tabindex="-1" aria-labelledby="photoModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="photoModalLabel">Cambiar Foto de Perfil</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="file-upload-container">
          <div class="upload-icon">
            <i class="bi bi-cloud-upload"></i>
          </div>
          <div class="upload-text">
            <strong>Arrastra tu imagen aquí o haz clic para seleccionar</strong>
          </div>
          <div class="text-gray-custom">
            Formatos soportados: JPG, PNG. Tamaño máximo: 5MB
          </div>
          <input type="file" id="photo-input" class="file-input" accept="image/jpeg,image/png">
        </div>
        <div id="image-preview-container" style="display: none;"></div>

        <!-- Formulario tradicional como fallback -->
        <form method="post" enctype="multipart/form-data" class="mt-4">
          <div class="mb-3">
            <label for="nuevaImagen" class="form-label">O selecciona un archivo:</label>
            <input type="file" class="form-control" id="nuevaImagen" name="nuevaImagen" accept="image/jpeg,image/png">
          </div>
          <input type="hidden" name="idClienteImagen" value="<?php echo $usuario['id']; ?>">
          <input type="hidden" name="pagina" value="perfil">
          <button type="submit" class="btn btn-primary">Subir Foto</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal para cambiar contraseña -->
<div class="modal fade" id="modalPassword" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="passwordModalLabel">Cambiar Contraseña</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="post">
          <div class="mb-3">
            <label for="passwordActual" class="form-label">Contraseña Actual *</label>
            <input type="password" class="form-control" id="passwordActual" name="passwordActual" required>
          </div>
          <div class="mb-3">
            <label for="passwordNuevo" class="form-label">Nueva Contraseña *</label>
            <input type="password" class="form-control" id="passwordNuevo" name="passwordNuevo" required minlength="8">
            <div class="form-text">Mínimo 8 caracteres</div>
          </div>
          <div class="mb-3">
            <label for="passwordConfirmar" class="form-label">Confirmar Nueva Contraseña *</label>
            <input type="password" class="form-control" id="passwordConfirmar" name="passwordConfirmar" required>
          </div>
          <input type="hidden" name="idClientePass" value="<?php echo $usuario['id']; ?>">
          <button type="submit" class="btn btn-primary">Cambiar Contraseña</button>
        </form>
      </div>
    </div>
  </div>
</div>


<script>
  // Funciones auxiliares para compatibilidad con el sistema existente
  function openPhotoModal() {
    const modal = new bootstrap.Modal(document.getElementById('modal'));
    modal.show();
  }

  function changePassword() {
    const modal = new bootstrap.Modal(document.getElementById('modalPassword'));
    modal.show();
  }

  // Validación de confirmación de contraseña
  document.addEventListener('DOMContentLoaded', function() {
    const passwordNuevo = document.getElementById('passwordNuevo');
    const passwordConfirmar = document.getElementById('passwordConfirmar');

    if (passwordNuevo && passwordConfirmar) {
      passwordConfirmar.addEventListener('input', function() {
        if (passwordNuevo.value !== passwordConfirmar.value) {
          passwordConfirmar.setCustomValidity('Las contraseñas no coinciden');
        } else {
          passwordConfirmar.setCustomValidity('');
        }
      });
    }
  });
</script>