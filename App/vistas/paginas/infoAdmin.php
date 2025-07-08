 <?php
 if($usuario["rol"] != "admin"){
  echo '<script>
  window.location = "'.$ruta.'/inicio";
  </script>';
  return;
}
    $tabla = "usuarios";
    $user = ControladorGeneral::ctrContarRegistros($tabla);
    $tabla = "curso";
    $cursos = ControladorGeneral::ctrContarRegistros($tabla);
    $tabla = "inscripciones";
    $inscripciones = ControladorGeneral::ctrContarRegistros($tabla);
    $tabla = "log_ingreso";
    $ingresosApp = ControladorGeneral::ctrContarRegistros($tabla);

?>
 <div class="row">
    <div class="col-6 col-lg-3 col-md-6">
        <div class="card sobraCrearLink">
            <div class="card-body px-3 py-4-5">
                <div class="row">
                    <div class="col-md-4">
                        <div class="stats-icon purple">
                            <i class="iconly-boldBookmark"></i>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h6 class="text-muted font-semibold">Todas los cursos</h6>
                        <h6 class="font-extrabold mb-0"><?php echo $cursos["total"]; ?></h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3 col-md-6">
        <div class="card sobraCrearLink">
            <div class="card-body px-3 py-4-5">
                <div class="row">
                    <div class="col-md-4">
                        <div class="stats-icon purple">
                            <i class="iconly-boldBookmark"></i>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h6 class="text-muted font-semibold">Inscripciones</h6>
                        <h6 class="font-extrabold mb-0"><?php echo $inscripciones["total"]; ?></h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3 col-md-6">
        <div class="card sobraCrearLink">
            <div class="card-body px-3 py-4-5">
                <div class="row">
                    <div class="col-md-4">
                        <div class="stats-icon blue">
                            <i class="iconly-boldProfile"></i>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h6 class="text-muted font-semibold">Usuarios</h6>
                        <h6 class="font-extrabold mb-0"><?php echo $user["total"]; ?></h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3 col-md-6">
        <div class="card sobraCrearLink">
            <div class="card-body px-3 py-4-5">
                <div class="row">
                    <div class="col-md-4">
                         <div class="avatar avatar-lg bg-warning">
                                <span class="avatar-content">IS</span>
                                <span class="avatar-status bg-success"></span>
                            </div>
                    </div>
                    <div class="col-md-8">
                        <h6 class="text-muted font-semibold">Actividad App</h6>
                        <h6 class="font-extrabold mb-0"><?php echo $ingresosApp["total"]; ?></h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
