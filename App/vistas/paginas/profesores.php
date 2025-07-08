<?php 
if($usuario["rol"] != "admin"){
  echo '<script>
  window.location = "'.$ruta.'inicio";
  </script>';
  return;
}
$item = null;
$valor = null;
$aliados = ControladorAliados::ctrMostrarAliados($item, $valor);
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
                    <h3>Aliados PAWers </h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active">Citas</li>
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
            <div class="table-responsive">
              <table id="table_id" class="table table-bordered dt-responsive">
                <thead>
                  <tr>
                    <th>Aliado</th>
                    <th>Nombre acompañante</th>
                    <th>Tipo animal</th>
                    <th>Nombre animal</th>
                    <th>Diponibilidad</th>
                    <th>e-mail</th>
                    <th>Teléfono</th>
                  </tr>
                </thead>
                <tbody>
              <?php foreach ($aliados as $key => $value): ?>
                   <tr>
                    <td><?php echo $value["aliado"]?>
                    <td><?php echo $value["nombreAcomp"]?></td>
                    <td><?php echo $value["tipoAnimal"]?></td>
                    <td><?php echo $value["nombreAnimal"]?></td>
                    <td><?php echo $value["disponibilidad"]?></td>
                    <td><?php echo $value["email"]?></td>
                    <td><?php echo $value["telefono"]?></td>
                  </tr>
                <?php endforeach ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

  </section>
  <!-- /.content -->
