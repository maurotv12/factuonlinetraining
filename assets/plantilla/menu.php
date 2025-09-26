<?php
// Usa ruta absoluta para el require
require_once $_SERVER['DOCUMENT_ROOT'] . "/factuonlinetraining/publico/controladores/ruta.controlador.php";
$rutaInicio = ControladorRuta::ctrRutaInicio();
$rutaLogin = ControladorRuta::ctrRutaLogin(); // Usa la función específica para login
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top custom-navbar">
  <div class="container">
    <!-- lOGO TEXT HERE -->
    <a href="<?php echo $rutaInicio; ?>" class="navbar-brand"><img src="/factuonlinetraining/App/vistas/img/logo.png" style="height: 50px;"></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarScroll">
      <ul class="navbar-nav ms-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
        <li class="nav-item"><a class="nav-link active" aria-current="page" href="<?php echo $rutaInicio; ?>">Inicio</a></li>
        <li class="nav-item"><a class="nav-link" href="#nosotros">Nosotros</a></li>
        <li class="nav-item"><a class="nav-link" href="#contacto">Contactanos </a></li>
        <li><a href="<?php echo $rutaLogin; ?>" class="ingresar-btn btn btn-default">Ingresara</a></li>
      </ul>
    </div>
  </div>
</nav>