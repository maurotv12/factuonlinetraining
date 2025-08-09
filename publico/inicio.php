<!DOCTYPE html>
<html lang="en">
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/cursosapp/assets/plantilla/head.php";
?>

<body>
   <?php
   include $_SERVER['DOCUMENT_ROOT'] . "/cursosapp/assets/plantilla/menu.php";
   require_once $_SERVER['DOCUMENT_ROOT'] . "/cursosapp/publico/controladores/cursosInicio.controlador.php";
   require_once $_SERVER['DOCUMENT_ROOT'] . "/cursosapp/publico/controladores/cursosDestacados.controlador.php";
   require_once $_SERVER['DOCUMENT_ROOT'] . "/cursosapp/publico/modelos/cursosInicio.modelo.php";

   // Obtener todos los cursos
   $cursos = ControladorCursosInicio::ctrMostrarCursosInicio();
   if (!$cursos) {
      $cursos = [];
   }
   if (isset($cursos['id'])) {
      $cursos = [$cursos];
   }

   // Obtener cursos destacados para el carrusel
   $cursosDestacados = ControladorCursosDestacados::ctrObtenerCursosDestacados(3);
   $cursosCarrusel = ControladorCursosDestacados::ctrFormatearCursosParaCarrusel($cursosDestacados);

   // Si no hay cursos destacados, mostrar al menos contenido estático en el carrusel
   if (empty($cursosCarrusel)) {
      $cursosCarrusel = [
         [
            'id' => 0,
            'titulo' => 'Aprende Animación',
            'descripcion' => 'Descubre técnicas profesionales de animación en nuestros cursos',
            'imagen' => 'assets/img/slider1.jpg',
            'url' => '#',
            'precio' => 'Varios precios'
         ],
         [
            'id' => 0,
            'titulo' => 'Cine y Dirección',
            'descripcion' => 'Aprende a dirigir cortometrajes y películas con nuestros expertos',
            'imagen' => 'assets/img/slider2.jpg',
            'url' => '#',
            'precio' => 'Varios precios'
         ],
         [
            'id' => 0,
            'titulo' => 'Stop Motion',
            'descripcion' => 'Domina las técnicas de Stop Motion con proyectos prácticos',
            'imagen' => 'assets/img/slider3.jpg',
            'url' => '#',
            'precio' => 'Varios precios'
         ]
      ];
   }
   ?>

   <!-- Sección del Carrusel -->
   <section id="carrusel-destacados" class="py-4">
      <div class="container">
         <div class="row">
            <div class="col-md-12">
               <h2 class="titleFes mb-3">Destacados <small>
                     <p>Nuestros mejores cursos</p>
                  </small></h2>

               <div id="carouselCursos" class="carousel slide" data-bs-ride="carousel">
                  <div class="carousel-indicators">
                     <?php foreach ($cursosCarrusel as $key => $slide): ?>
                        <button type="button" data-bs-target="#carouselCursos" data-bs-slide-to="<?= $key ?>"
                           <?= ($key === 0) ? 'class="active" aria-current="true"' : '' ?>
                           aria-label="Slide <?= $key + 1 ?>"></button>
                     <?php endforeach; ?>
                  </div>
                  <div class="carousel-inner">
                     <div class="carousel-item active">
                        <div class="d-block w-100 carousel-img-container" style="height: 400px; background-image: url('assets/img/slider1.jpg'); background-size: cover; background-position: center;">
                           <div class="carousel-caption d-none d-md-block">
                              <h3>Aprende Animación</h3>
                              <p>Descubre técnicas profesionales de animación en nuestros cursos</p>
                              <a href="#" class="btn btn-primary">Ver cursos</a>
                           </div>
                        </div>
                     </div>
                     <div class="carousel-item">
                        <div class="d-block w-100 carousel-img-container" style="height: 400px; background-image: url('assets/img/slider2.jpg'); background-size: cover; background-position: center;">
                           <div class="carousel-caption d-none d-md-block">
                              <h3>Cine y Dirección</h3>
                              <p>Aprende a dirigir cortometrajes y películas con nuestros expertos</p>
                              <a href="#" class="btn btn-primary">Explorar</a>
                           </div>
                        </div>
                     </div>
                     <div class="carousel-item">
                        <div class="d-block w-100 carousel-img-container" style="height: 400px; background-image: url('assets/img/slider3.jpg'); background-size: cover; background-position: center;">
                           <div class="carousel-caption d-none d-md-block">
                              <h3>Stop Motion</h3>
                              <p>Domina las técnicas de Stop Motion con proyectos prácticos</p>
                              <a href="#" class="btn btn-primary">Inscríbete</a>
                           </div>
                        </div>
                     </div>
                  </div>
                  <button class="carousel-control-prev" type="button" data-bs-target="#carouselCursos" data-bs-slide="prev">
                     <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                     <span class="visually-hidden">Anterior</span>
                  </button>
                  <button class="carousel-control-next" type="button" data-bs-target="#carouselCursos" data-bs-slide="next">
                     <span class="carousel-control-next-icon" aria-hidden="true"></span>
                     <span class="visually-hidden">Siguiente</span>
                  </button>
               </div>
            </div>
         </div>
      </div>
   </section>

   <section id="categorias">
      <div class="container">
         <div class="row">
            <div class="col-md-12 col-sm-12">
               <div>
                  <h2 class="titleFes" id="testimonial">Cursos <small>
                        <p>Animación, cine, stop motion</p>
                     </small></h2>
               </div>
            </div>
         </div>
      </div>
   </section>


   <section id="cardscursos">
      <div class="container">
         <div class="row row-eq-height">
            <?php if (count($cursos) === 0): ?>
               <p>No hay cursos para mostrar.</p>
            <?php else: ?>
               <?php foreach ($cursos as $key => $value): ?>
                  <?php
                  $descripcion = $value["descripcion"];
                  $resulDescripcion = substr($descripcion, 0, 100);
                  $nCurso = $value["nombre"];
                  $rnCurso = substr($nCurso, 0, 30);
                  ?>
                  <div class="col-md-4 col-sm-6 team-marg mb-4">
                     <div class="team-thumb card-curso">
                        <div class="team-image">
                           <a href="<?= $value["url_amiga"] ?>"><img src="App/<?= $value["banner"] ?>" class="img-responsive post-curso" alt="Curso"></a>
                        </div>
                        <div class="team-info">
                           <h3><a href="<?= $value["url_amiga"] ?>"><?= $rnCurso ?></a></h3>
                           <a href="<?= $value["url_amiga"] ?>"><span><?= $resulDescripcion ?></span></a>
                           <div class="curso-footer">
                              <h4 class="valorC">$ <?= $value["valor"] == 0 ? "Gratis" : $value["valor"] ?></h4>
                              <p>Profesor: <?= $value["id_persona"] ?></p>
                              <div class="d-grid gap-2">
                                 <a class="ingresar-btn btn btn-default" href="<?= $value["url_amiga"] ?>" role="button">Ver Curso</a>
                              </div>
                           </div>
                        </div>
                        <ul class="social-icon mt-auto">
                           <li><a href="https://www.instagram.com/festivaldecine_calibelula/" class="fa fa-instagram" target="_blank"></a></li>
                           <li><a href="https://www.facebook.com/festivaldecinecalibelula/" class="fa fa-facebook-square" target="_blank"></a></li>
                           <li><a href="https://www.youtube.com/channel/UCWbTp6hNKlX7QPKsNMYbCWg" class="fa fa-youtube-play" target="_blank"></a></li>
                           <li><a href="https://twitter.com/FCalibelula" class="fa fa-twitter" target="_blank"></a></li>
                        </ul>
                     </div>
                  </div>
               <?php endforeach; ?>
            <?php endif; ?>
         </div>
      </div>
   </section>

   <!-- Script para el carrusel -->
   <script src="/cursosapp/assets/js/carrusel.js"></script>

</body>

</html>