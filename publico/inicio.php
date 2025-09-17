<!DOCTYPE html>
<html lang="en">
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/cursosapp/assets/plantilla/head.php";
?>

<!-- CSS específico para el carrusel -->
<link rel="stylesheet" href="/cursosapp/assets/css/carrusel.css">

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

   // Carrusel publicitario con imágenes motivacionales - no requiere base de datos
   $cursosCarrusel = [
      [
         'id' => 1,
         'titulo' => '¡Comienza Tu Aventura Creativa!',
         'descripcion' => 'Descubre el mundo de la animación y da vida a tus ideas. ¡Tu creatividad no tiene límites!',
         'imagen' => '/cursosapp/storage/public/carrusel/1.png',
         'url' => '#cardscursos',
         'cta' => 'Explorar Cursos'
      ],
      [
         'id' => 2,
         'titulo' => 'Convierte Tu Pasión en Profesión',
         'descripcion' => 'Aprende de los mejores profesionales y transforma tu hobby en una carrera exitosa',
         'imagen' => '/cursosapp/storage/public/carrusel/2.jpg',
         'url' => '#cardscursos',
         'cta' => 'Ver Todos los Cursos'
      ],
      [
         'id' => 3,
         'titulo' => 'El Futuro Está en Tus Manos',
         'descripcion' => 'Domina las técnicas más innovadoras y conviértete en el creador que siempre soñaste ser',
         'imagen' => '/cursosapp/storage/public/carrusel/3.png',
         'url' => '#cardscursos',
         'cta' => 'Empezar Ahora'
      ]
   ];
   ?>

   <!-- Sección del Carrusel -->
   <section id="carrusel-destacados" class=" mt-5">
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
                     <?php foreach ($cursosCarrusel as $key => $slide): ?>
                        <div class="carousel-item <?= ($key === 0) ? 'active' : '' ?>">
                           <div class="d-block w-100 carousel-img-container"
                              style="height: 400px; background-image: url('<?= $slide['imagen'] ?>'); background-size: cover; background-position: center;">
                              <div class="carousel-caption d-none d-md-block">
                                 <h3><?= htmlspecialchars($slide['titulo']) ?></h3>
                                 <p><?= htmlspecialchars($slide['descripcion']) ?></p>
                                 <a href="<?= $slide['url'] ?>" class="btn btn-primary btn-lg"><?= htmlspecialchars($slide['cta']) ?></a>
                              </div>
                           </div>
                        </div>
                     <?php endforeach; ?>
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
                  <div class="col-lg-4 col-md-6 col-sm-12 team-marg mb-4">
                     <div class="team-thumb card-curso">
                        <div class="team-image">
                           <a href="<?= $value["url_amiga"] ?>"><img src="<?= $value["banner"] ?>" class="img-responsive post-curso" alt="Curso"></a>
                        </div>
                        <div class="team-info pb-4">
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