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
  <!-- Favicons -->
  <link href="/cursosApp/assets/dist/css/bootstrap.min.css" rel="stylesheet">

  <script src="/cursosApp/assets/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/cursosApp/assets/dist/js/bootstrap.min.js"></script>
  <!-- MAIN CSS -->
  <link rel="stylesheet" href="/cursosApp/assets/css/templatemo-style.css">
  <link rel=" stylesheet" href="/cursosApp/assets/css/styleCursos.css">
  <link rel="stylesheet" href="/cursosApp/assets/css/auth.css">
  <link rel="stylesheet" href="/cursosApp/assets/css/cursosInicio.css">

  <link rel="icon" href="../favicon.jpg" sizes="32x32" />
  <link rel="icon" href="../favicon-180.jpg" sizes="192x192" />
  <!-- SWEET ALERT 2 -->
  <!-- https://sweetalert2.github.io/ -->
  <script src="../assets/js/plugins/sweetalert2.all.js"></script>
</head>

<body>



</body>

<!-- MENU -->
<?php include $_SERVER['DOCUMENT_ROOT'] . "/cursosAPP/assets/plantilla/menu.php"; ?>

<?php
// Cargar la página solicitada
// Si la página es login, register o forgot-password, se carga directamente
// include ControladorRuta::ctrCargarPagina();
ControladorRuta::cargarVistaCursoInicio();

// if (isset($_GET["pagina"])) {
//   if (
//     $_GET["pagina"] == "login"  ||
//     $_GET["pagina"] == "register" ||
//     $_GET["pagina"] == "forgot-password"
//   ) {
//     include "paginas/" . $_GET["pagina"] . ".php";
//   }
// } else {
//   include "paginas/register.php";
// }
?>

<?php include $_SERVER['DOCUMENT_ROOT'] . "/cursosAPP/assets/plantilla/footer.php"; ?>

</html>