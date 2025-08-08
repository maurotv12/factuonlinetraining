<?php
$ruta = ControladorRuta::ctrRuta();
$rutaInicio = ControladorRuta::ctrRutaInicio();
// Ruta al inicio de la aplicación
?>


<!-- HEAD -->
<?php include $_SERVER['DOCUMENT_ROOT'] . "/cursosAPP/publico/vistas/paginas/registro/vista/plantillaPartesInicio/head.php"; ?>


<body>
  <!-- MENU -->
  <?php include $_SERVER['DOCUMENT_ROOT'] . "/cursosAPP/publico/vistas/paginas/registro/vista/plantillaPartesInicio/menu.php"; ?>

  </main>
  <?php
  // Cargar la página solicitada
  ControladorRuta::cargarVistaCursoInicio();
  ?>
  <main>


    <!-- Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
    <!-- JavaScript para validación de registro y login (solo si es necesario) -->
    <script src="/cursosApp/assets/js/validacionRegistro.js"></script>
    <script>
      // Cargar script de login solo si estamos en página de login
      if (document.querySelector('input[name="emailIngreso"]')) {
        const script = document.createElement('script');
        script.src = '/cursosApp/assets/js/validacionLogin.js';
        document.head.appendChild(script);
      }
    </script>
    <script src="/cursosapp/assets/js/carrusel.js"></script>


</body>

<footer>
  <?php include $_SERVER['DOCUMENT_ROOT'] . "/cursosAPP/publico/vistas/paginas/registro/vista/plantillaPartesInicio/footer.php"; ?>
</footer>

</html>