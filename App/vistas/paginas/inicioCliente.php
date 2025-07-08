<?php
$item = null;
$valor = null;
$cursos = ControladorCursos::ctrMostrarCursos($item, $valor);
?>
<div class="row">
    <div class="col-12">
        <h4>Mis Cursos</h4>
        <div class="row">       
            <?php foreach ($cursos as $key => $value): ?>
            <div class="col-xl-3 col-md-4 col-sm-12">
                <div class="card sobraCrearLink">
                    <div class="card-content">
                        <img src="../<?php echo $value["banner"]; ?>" class="card-img-top img-fluid"
                            alt="singleminded">
                        <div class="card-body">
                            <p class="card-text">
                                <?php 
                                    $nomb = $value["nombre"];
                                    $resulNomb = substr("$nomb", 0, 20);
                                    echo $resulNomb; 
                                ?>
                            </p>
                        </div>
                    </div>
                    <form method="post" action="seguirCurso">
                        <input type="hidden" value="<?php echo $value["id"]; ?>" name="idCurso">
                        <div class="d-grid gap-2">
                            <input type="submit" id="submit" class="seguir-btn btn btn-default btn-lg" value="Seguir el curso">
                        </div>
                    </form>
                </div>
            </div>
            <?php endforeach ?>
        </div>
    </div> 
</div>

<div class="row">
    <div class="col-12">
        <h4>Más Cursos</h4>
        <div class="row">       
            <?php foreach ($cursos as $key => $value): ?>
            <div class="col-xl-3 col-md-4 col-sm-12">
                <div class="card sobraCrearLink">
                    <div class="card-content">
                        <img src="../<?php echo $value["banner"]; ?>" class="card-img-top img-fluid"
                            alt="singleminded">
                        <div class="card-body">
                            <p class="card-text">
                                <?php 
                                    $descripcion = $value["descripcion"];
                                    $resulDescripcion = substr("$descripcion", 0, 40);
                                    echo $resulDescripcion; 
                                ?>
                            </p>
                            $ <?php if($value["valor"] == 0) {echo "Gratis";} else { echo $value["valor"]; } ?>
                        </div>
                    </div>
                    <form method="post" action="registro/register">
                        <input type="hidden" value="<?php echo $value["id"]; ?>" name="idCurso">
                        <div class="d-grid gap-2">
                            <input type="submit" id="submit" class="inscribirse-btn btn btn-default btn-lg" value="Inscribirse">
                        </div>
                    </form>
                </div>
            </div>
            <?php endforeach ?>
        </div>
    </div> 
</div>