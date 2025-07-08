<?php 
if($usuario["rol"] != "admin"){
  echo '<script>
  window.location = "'.$ruta.'inicio";
  </script>';
  return;
}
$item = null;
$valor = null;
$citas = ControladorCitas::ctrMostrarCitas($item, $valor);
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
                    <h3>Actividad registros PAWers </h3>
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
              <table id="table_id" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Edad</th>
                    <th>Tipo</th>
                    <th># Mascotas</th>
                    <th>Preferencia</th>
                    <th>Experiencia</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Duración</th>
                    <th>dirección</th>
                    <th>Estado</th>
                    <th>Fecha Registro</th>
                  </tr>
                </thead>
                <tbody>
              <?php foreach ($citas as $key => $value): ?>

                   <tr>
                    <td><?php echo($key+1); ?></td>
                    <td><?php echo $value["paraQuien"]?></td>
                    <td><?php echo $value["edad"]?>
                    <td><?php echo $value["tipo"]?></td>
                    <td><?php echo $value["numeroMascotas"]?></td>
                    <td><?php echo $value["preferencia"]?></td>
                    <td><?php echo $value["tipoExperiencia"]?></td>
                    <td><?php echo $value["fechaCita"]?></td>
                    <td><?php echo $value["horaCita"]?></td>
                    <td><?php echo $value["duracionCita"]?></td>
                    <td><?php echo $value["direccion"]?></td>
                    <td><?php if($value["estado"] == 1) { echo "Pendiente pago"; } else { echo "Pagado"; } ?></td>
                    <td><?php echo $value["fechaR"]?></td>
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
