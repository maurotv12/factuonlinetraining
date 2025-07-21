<!DOCTYPE html>
<html lang="en">
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/cursosapp/assets/plantilla/head.php";
?>

<body>
   <?php
   include $_SERVER['DOCUMENT_ROOT'] . "/cursosapp/assets/plantilla/menu.php";
   require_once $_SERVER['DOCUMENT_ROOT'] . "/cursosapp/publico/controladores/cursosInicio.controlador.php";
   require_once $_SERVER['DOCUMENT_ROOT'] . "/cursosapp/publico/modelos/cursosInicio.modelo.php";

   $cursos = ControladorCursosInicio::ctrMostrarCursosInicio();
   if (!$cursos) {
      $cursos = [];
   }
   if (isset($cursos['id'])) {
      $cursos = [$cursos];
   }
   ?>
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
         <div class="row">
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
                     <div class="team-thumb">
                        <div class="team-image">
                           <a href="<?= $value["url_amiga"] ?>"><img src="App/<?= $value["banner"] ?>" class="img-responsive" alt="Curso Calibélula"></a>
                        </div>
                        <div class="team-info">
                           <h3><a href="<?= $value["url_amiga"] ?>"><?= $rnCurso ?></a></h3>
                           <a href="<?= $value["url_amiga"] ?>"><span><?= $resulDescripcion ?></span></a>
                           <h4 class="valorC">$ <?= $value["valor"] == 0 ? "Gratis" : $value["valor"] ?></h4>
                           <p>Profesor: <?= $value["id_persona"] ?></p>
                           <div class="d-grid gap-2">
                              <a class="ingresar-btn btn btn-default" href="<?= $value["url_amiga"] ?>" role="button">Ver Curso</a>
                           </div>
                        </div>
                        <ul class="social-icon">
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

</body>

</html>