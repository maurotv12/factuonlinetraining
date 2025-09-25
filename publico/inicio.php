<!DOCTYPE html>
<html lang="en">
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/factuonlinetraining/assets/plantilla/head.php";
?>

<!-- CSS específico para el carrusel -->
<link rel="stylesheet" href="/factuonlinetraining/assets/css/carrusel.css">
<link rel="stylesheet" href="/factuonlinetraining/assets/css/cursosInicio.css">

<body>
   <?php
   include $_SERVER['DOCUMENT_ROOT'] . "/factuonlinetraining/assets/plantilla/menu.php";
   require_once $_SERVER['DOCUMENT_ROOT'] . "/factuonlinetraining/publico/controladores/cursosInicio.controlador.php";
   require_once $_SERVER['DOCUMENT_ROOT'] . "/factuonlinetraining/publico/modelos/cursosInicio.modelo.php";

   // Obtener parámetro de categoría para filtrado
   $categoriaSeleccionada = isset($_GET['categoria']) ? $_GET['categoria'] : 'todas';

   // Obtener cursos (filtrados o todos)
   $cursos = ControladorCursosInicio::ctrMostrarCursosPorCategoria($categoriaSeleccionada);
   if (!$cursos) {
      $cursos = [];
   }
   $categorias = ControladorCursosInicio::ctrObtenerCategorias();

   // Carrusel publicitario con imágenes motivacionales - no requiere base de datos
   $cursosCarrusel = [
      [
         'id' => 1,
         'titulo' => '¡Comienza Tu Aventura Creativa!',
         'descripcion' => 'Descubre el mundo de la animación y da vida a tus ideas. ¡Tu creatividad no tiene límites!',
         'imagen' => '/factuonlinetraining/storage/public/carrusel/1.png',
         'url' => '#cardscursos',
         'cta' => 'Explorar Cursos'
      ],
      [
         'id' => 2,
         'titulo' => 'Convierte Tu Pasión en Profesión',
         'descripcion' => 'Aprende de los mejores profesionales y transforma tu hobby en una carrera exitosa',
         'imagen' => '/factuonlinetraining/storage/public/carrusel/2.jpg',
         'url' => '#cardscursos',
         'cta' => 'Ver Todos los Cursos'
      ],
      [
         'id' => 3,
         'titulo' => 'El Futuro Está en Tus Manos',
         'descripcion' => 'Domina las técnicas más innovadoras y conviértete en el creador que siempre soñaste ser',
         'imagen' => '/factuonlinetraining/storage/public/carrusel/3.png',
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
                        <p>Explora por categorías</p>
                     </small></h2>
               </div>
            </div>
         </div>
         <!-- Filtros de categorías -->
         <div class="row mb-4">
            <div class="col-md-12">
               <div class="category-filters">
                  <div class="d-flex flex-wrap gap-2 justify-content-center">
                     <a href="?categoria=todas"
                        class="btn <?= ($categoriaSeleccionada === 'todas') ? 'btn-primary' : 'btn-outline-primary' ?> mb-2">
                        Todas las categorías
                     </a>
                     <?php foreach ($categorias as $categoria): ?>
                        <a href="?categoria=<?= $categoria['id'] ?>"
                           class="btn <?= ($categoriaSeleccionada == $categoria['id']) ? 'btn-primary' : 'btn-outline-primary' ?> mb-2">
                           <?= htmlspecialchars($categoria['nombre']) ?>
                        </a>
                     <?php endforeach; ?>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </section>


   <section id="cardscursos">
      <div class="container">
         <!-- Título de la sección actual -->
         <div class="row mb-3">
            <div class="col-md-12">
               <div class="section-title-with-count">
                  <h3>
                     <?php
                     if ($categoriaSeleccionada === 'todas') {
                        echo "Todos los cursos";
                     } else {
                        // Buscar el nombre de la categoría seleccionada
                        $nombreCategoria = 'Categoría';
                        foreach ($categorias as $cat) {
                           if ($cat['id'] == $categoriaSeleccionada) {
                              $nombreCategoria = $cat['nombre'];
                              break;
                           }
                        }
                        echo "Cursos de " . htmlspecialchars($nombreCategoria);
                     }
                     ?>
                  </h3>
                  <small class="text-muted d-block">(<?= count($cursos) ?> cursos encontrados)</small>
               </div>
            </div>
         </div>

         <div class="row row-eq-height">
            <?php if (count($cursos) === 0): ?>
               <div class="col-12">
                  <div class="alert alert-info text-center no-courses-message">
                     <h4>No hay cursos disponibles</h4>
                     <p>No se encontraron cursos para la categoría seleccionada. <a href="?categoria=todas">Ver todos los cursos</a></p>
                  </div>
               </div>
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
                              <h4 class="valorC">
                                 <?php if ($value["valor"] == 0): ?>
                                    Gratis
                                 <?php else: ?>
                                    $<?= number_format($value["valor"], 0, ',', '.') ?> COL
                                 <?php endif; ?>
                              </h4>
                              <p>Profesor: <?= !empty($value["nombre_profesor"]) ? htmlspecialchars($value["nombre_profesor"]) : 'No asignado' ?></p>
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
   <script src="/factuonlinetraining/assets/js/carrusel.js"></script>

</body>

</html>