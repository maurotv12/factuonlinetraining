<?php 
if($usuario["rol"] != "admin"){
  echo '<script>
  window.location = "'.$ruta.'eKlycsApp/inicio";
  </script>';
  return;
}
$item = null;
$valor = null;
$usuarios = ControladorUsuarios::ctrMostrarusuarios($item, $valor);
?>

  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
        </div>
          <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Usuarios registrados PAWers </h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active">Usuarios</li>
                            <li class="breadcrumb-item"><a href="inicio">Inicio</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
          </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>
  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-12 col-lg-12">
        <div class="card sobraCrearLink">
          <div class="card-body">
            <table id="table_id" class="table table-bordered dt-responsive table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Foto</th>
                  <th>Usuario</th>
                  <th>Nombre</th>
                  <th>e-Mail</th>
                  <th>Estado</th>
                  <th>País</th>
                  <th>Ciudad</th>
                  <th>Fecha registro</th>
                </tr>
              </thead>
              <tbody>
            <?php foreach ($usuarios as $key => $value): ?>
                 <tr>
                  <td><?php echo($key+1); ?></td>
                  <td><img src="<?php echo $value["foto"]?>" class="img-fluid avatar avatar-xl me-3" width="30px"></td>
                  <td><?php echo $value["usuarioLink"]?></td>
                  <td><?php echo $value["nombre"]?></td>
                  <td><?php echo $value["email"]?></td>
                  <td><?php echo $value["estado"]?></td>
                  <td><?php echo $value["pais"]?></td>
                  <td><?php echo $value["ciudad"]?></td>
                  <td><?php echo $value["fechaR"]?></td>
                </tr>

              <?php endforeach ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

  </section>
  <!-- /.content -->
