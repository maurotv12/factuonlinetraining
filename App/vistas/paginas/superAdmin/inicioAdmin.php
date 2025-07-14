<div class="row">
    <div class="col-12">
         <h4>Inscripciones pendientes</h4>
        <div class="card sobraCrearLink">
            <div class="card-body">
                <div class="row">
                     <div class="col-12 col-xl-12">
                        <div class="card">
                            
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-lg">
                                        <thead>
                                            <tr>
                                                <th>Curso</th>
                                                <th>Estudiante</th>
                                                <th>Estado</th>
                                                <th>Fecha</th>
                                                <th>Editar</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                            <?php foreach ($inscrip as $key => $value): ?>
                                                <td class="col-auto">
                                                    <p class="mb-0">
                                                    <?php echo $value["idCurso"]?></p>
                                                </td>
                                                <td class="col-auto">
                                                    <p class="mb-0"><?php echo $value["idEstudiante"];  ?></p>
                                                </td>
                                                <td class="col-auto">
                                                    <p class="mb-0"><?php echo $value["estado"]; ?></p>
                                                </td>
                                                <td class="col-auto">
                                                    <p class="mb-0"><?php echo $value["fechaRegistro"]?></p>
                                                </td>
                                                <td>
                                               <a href="javascript:void(0);" ata-bs-toggle="tooltip" title="Editar cita" data-bs-toggle="modal" data-bs-target="#modalInscrip" onclick="carga_ajaxPassword('<?php echo $value["id"]; ?>', 'modalInscrip','vistas/paginas/modalInscrip.php');">
                                                    <button type="submit" class="btn botonEditar"><i class="bi bi-pencil-square"></i></button>
                                                </a>
                                             </td>
                                            </tr>
                                            <?php endforeach ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>