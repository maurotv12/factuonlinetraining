<?php 
include "assets/plantilla/head.php";
include "assets/plantilla/menu.php"; 
?>
<!--==================================================================
@grcarvajal grcarvajal@gmail.com **Gildardo Restrepo Carvajal**
26/05/2022 CursosApp
===================================================================-->
<!-- CATEGORIAS -->
<?php
require_once "controladores/cursosInicio.controlador.php";
//Modelos
require_once "modelos/cursosInicio.modelo.php";
?>
<section id="categorias">
  <div class="container">
       <div class="row">
            <div class="col-md-12 col-sm-12">
               <div>
                <h2 class="titleFes" id="testimonial">Cursos <small><p>Animación, cine, stop motion</p></small></h2>
               </div>
            </div>
            <?php
               $cursos = new ControladorCursosInicio();
               $cursos -> ctrMostrarCursosInicio();
               ?>
       </div>
  </div>
</section>
<?php include "assets/plantilla/footer.php"; ?>
     