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
  <title>CursosApp :: Cursos y Talleres</title>

  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons CDN -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

  <!-- Font Awesome CDN para iconos -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

  <!-- SweetAlert2 CDN -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- CSS locales (solo si existen) -->
  <link rel="stylesheet" href="/cursosApp/assets/css/templatemo-style.css">
  <link rel="stylesheet" href="/cursosApp/assets/css/styleCursos.css">
  <link rel="stylesheet" href="/cursosApp/assets/css/auth.css">
  <link rel="stylesheet" href="/cursosApp/assets/css/cursosInicio.css">
  <link rel="stylesheet" href="/cursosApp/assets/css/verCursoPublico.css">
  <!-- CSS para validación de registro -->
  <link rel="stylesheet" href="/cursosApp/assets/css/validacionRegistro.css">

  <!-- Favicons -->
  <link rel="icon" href="/cursosApp/assets/favicon.ico" sizes="32x32" />
</head>

<body>
  <!-- MENU -->
  <?php include $_SERVER['DOCUMENT_ROOT'] . "/cursosAPP/assets/plantilla/menu.php"; ?>

  </main>
  <?php
  // Cargar la página solicitada
  ControladorRuta::cargarVistaCursoInicio();
  ?>
  <main>

    <footer>
      <?php include $_SERVER['DOCUMENT_ROOT'] . "/cursosAPP/assets/plantilla/footer.php"; ?>
    </footer>

    <!-- Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JavaScript para validación de registro -->
    <script src="/cursosApp/assets/js/validacionRegistro.js"></script>

    <!-- JavaScript para validación de login (solo si es necesario) -->
    <script>
      // Cargar script de login solo si estamos en página de login
      if (document.querySelector('input[name="emailIngreso"]')) {
        const script = document.createElement('script');
        script.src = '/cursosApp/assets/js/validacionLogin.js';
        document.head.appendChild(script);
      }
    </script>
</body>

</html>