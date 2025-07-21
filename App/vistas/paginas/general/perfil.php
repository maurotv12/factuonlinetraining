<div class="page-content">
  <div class="page-title">
    <div class="row">
      <div class="col-12 col-md-6 order-md-1 order-last">
        <h3>Información de perfil </h3>
        <div class="widget-user-header">
          <h4 class="widget-user-username"><?php echo $usuario["usuario_link"]; ?></h4>
          <h6 class="widget-user-desc">Fecha de Registro: <?php echo $usuario["fecha_registro"];  ?></h6>
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
  <section class="row">
    <div class="col-12 col-lg-12">
      <div class="row">

        <div class="col-12 col-lg-4">
          <div class="card sobraCrearLink">
            <div class="card-header">
              <h6 class="card-title"></h6>
            </div>
            <div class="card-body">
              <div class="centrarPerfil">
                <div class="avatar avatar-xl me-3">
                  <img src="<?php echo $usuario["foto"]; ?>" alt="@<?php echo $usuario["usuarioLink"]; ?>">
                </div>
              </div>
              <div class="centrar">
                <br>
                <?php
                $actualizarFoto = new ControladorUsuarios();
                $actualizarFoto->ctrCambiarFoto();
                ?>

                <h5 class="font-bold"><?php echo $usuario["nombre"]; ?></h5>
                <h7 class="text-muted mb-0">
                  <?php echo $usuario["email"]; ?></h7>
                <h7 class="text-muted mb-0">
                  <p><?php echo $usuario["pais"]; ?></p>
                </h7>
                <h6 class="text-muted mb-0">

                </h6>
                <div class="my-4">
                  <a href="javascript:void(0);" class="botonCamara" ata-bs-toggle="tooltip" title="Cambiar foto de perfil" data-bs-toggle="modal" data-bs-target="#modal" onclick="carga_ajax('perfil', '<?php echo $usuario["id"]; ?>', 'modal','vistas/paginas/modalFoto.php');">
                    <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#modal">
                      Cambiar foto
                    </button></a>

                  <a href="javascript:void(0);" ata-bs-toggle="tooltip" title="Cambiar password de perfil" data-bs-toggle="modal" data-bs-target="#modalPassword" onclick="carga_ajaxPassword('<?php echo $usuario["id"]; ?>', 'modalPassword','vistas/paginas/modalPassword.php');">
                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalPassword">
                      Reset password
                    </button></a>
                  <?php
                  $actualizarPassword = new ControladorUsuarios();
                  $actualizarPassword->ctrCambiarPassword();
                  ?>
                </div>
              </div>

            </div>
          </div>
        </div>



        <div class="col-12 col-md-8">
          <div class="card card-primary card-outline sobraCrearLink">
            <div class="col-12 col-md-8">
              <div class="card-header">
                <h6 class="card-title">Actualice sus datos y mejore el procesamiento de eKlycs</h6>
              </div>
              <div class="card-body">
                <br>

                <form method="post">
                  <div class="form-group">
                    <label for="inputName" class="control-label">Nombre completo</label>
                    <div>
                      <input type="text" class="form-control" name="nombreUsuario" value="<?php echo $usuario["nombre"]; ?>" required>
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="inputName" class="control-label">País:</label>
                    <div>
                      <input type="text" class="form-control" placeholder="Ingrese su pais" name="pais" value="<?php echo $usuario["pais"]; ?>" required>
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="inputName" class="control-label">Ciudad:</label>
                    <div>
                      <input type="text" class="form-control" placeholder="Ingrese su ciudad" name="ciudad" value="<?php echo $usuario["ciudad"]; ?>" required>
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="inputName" class="control-label">¿Qué contenido publica?</label>
                    <div>
                      <input type="text" class="form-control" placeholder="¿Su contenido es?" name="contenido" value="<?php echo $usuario["contenido"]; ?>" required>
                    </div>
                  </div>
                  <input type="hidden" value="<?php echo $usuario["email"]; ?>" name="email">
                  <input type="hidden" value="<?php echo $usuario["id"]; ?>" name="idUsuario">
                  <br>
                  <button type="submit" class="btn btn-info btn-block">Actualizar datos</button>
                </form>
                <?php

                $actualizarPerfil = new ControladorUsuarios();
                $actualizarPerfil->ctrActualizarPerfilUsuario();
                ?>

              </div>
            </div>
          </div>
        </div>


      </div>
    </div>
  </section>
</div>



<!--=====================================
VENTANA MODAL EDITAR IMAGEN DE CLIENTE
======================================-->
<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">eKlycs, procesando.....</h4>
      </div>
      <div class="modal-body">
        <h1>eKlycs, procesando.....</h1>
      </div>

    </div>
  </div>
</div>
<!--/modal-->

<!--=====================================
VENTANA MODAL CAMBIAR PASSWORD DE CLIENTE
======================================-->
<div class="modal fade" id="modalPassword" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">eKlycs, procesando.....</h4>
      </div>
      <div class="modal-body">
        <h1>eKlycs, procesando.....</h1>
      </div>

    </div>
  </div>
</div>
<!--/modal-->