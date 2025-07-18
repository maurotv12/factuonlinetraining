<!-- vistas/paginas/usuarios.php
<script>
    $(document).ready(function() {
        $('#tablaUsuarios').DataTable();
    });
</script> -->

<?php
// if ($usuario["rol"] != "admin") {
//     echo '<script>
//   window.location = "' . $ruta . 'eKlycsApp/inicio";
//   </script>';
//     return;
// }
// $item = null;
// $valor = null;
// $usuarios = ControladorUsuarios::ctrMostrarusuarios($item, $valor);
// 
?>
<?php
$item = null;
$valor = null;
$usuarios = ControladorUsuarios::ctrMostrarusuarios($item, $valor);
?>
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
                                <th>Fecha registro</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $key => $value): ?>
                                <tr>
                                    <td><?php echo ($key + 1); ?></td>
                                    <td><img src="<?php echo $value["foto"] ?>" class="img-fluid avatar avatar-xl me-3" width="30px"></td>
                                    <td><?php echo $value["usuario_link"] ?></td>
                                    <td><?php echo $value["nombre"] ?></td>
                                    <td><?php echo $value["email"] ?></td>
                                    <td><?php echo $value["estado"] ?></td>
                                    <td><?php echo $value["pais"] ?></td>
                                    <td><?php echo $value["fecha_registro"] ?></td>
                                </tr>

                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</section>