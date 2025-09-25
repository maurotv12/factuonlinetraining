<?php
$ruta = ControladorRuta::ctrRuta();
$rutaInicio = ControladorRuta::ctrRutaInicio();
// Ruta al inicio de la aplicación
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Factu Online Training :: Cursos y Talleres</title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">

  <!-- Font Awesome CDN para iconos -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

  <!-- SweetAlert2 CDN -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- CSS locales (solo si existen) -->
  <link rel="stylesheet" href="/factuonlinetraining/assets/css/templatemo-style.css">
  <link rel="stylesheet" href="/factuonlinetraining/assets/css/styleCursos.css">
  <link rel="stylesheet" href="/factuonlinetraining/assets/css/auth.css">
  <link rel="stylesheet" href="/factuonlinetraining/assets/css/footer-fix.css">

  <!-- Favicons -->
  <link rel="icon" href="/factuonlinetraining/assets/favicon.ico" sizes="32x32" />
</head>

<body>
  <!-- MENU -->
  <?php include $_SERVER['DOCUMENT_ROOT'] . "/factuonlinetraining/assets/plantilla/menu.php"; ?>

  <!-- CONTENIDO PRINCIPAL -->
  <main class="pt-5">
    <?php
    // Cargar la página solicitada
    ControladorRuta::cargarVistaCursoInicio();
    ?>
  </main>

  <!-- FOOTER -->
  <?php include $_SERVER['DOCUMENT_ROOT'] . "/factuonlinetraining/assets/plantilla/footer.php"; ?>

  <!-- JavaScript para validación de registro -->
  <script src="/factuonlinetraining/assets/js/validacionRegistro.js"></script>

  <!-- JavaScript para validación de login (solo si es necesario) -->
  <script>
    // Cargar script de login solo si estamos en página de login
    if (document.querySelector('input[name="emailIngreso"]')) {
      const script = document.createElement('script');
      script.src = '/factuonlinetraining/assets/js/validacionLogin.js';
      document.head.appendChild(script);
    }
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
</body>

</html>