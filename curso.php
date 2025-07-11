<?php
include "assets/plantilla/head.php";
include "assets/plantilla/menu.php";
?>

<!--==================================================================
@grcarvajal grcarvajal@gmail.com **Gildardo Restrepo Carvajal**
26/05/2022 Plataforma Calibelula mostrar Cursos
===================================================================-->
<!-- CATEGORIAS -->
<?php
require_once "controladores/cursosInicio.controlador.php";
//Modelos
require_once "modelos/cursosInicio.modelo.php";
require_once "controladores/ruta.controlador.php";
$item = "id";
$valor = $curso["idCategoria"];
$tabla = "categoria";
$cate = ControladorCursosInicio::ctrConsultarUnCursoInicio($item, $valor, $tabla);
$rutaInicio = ControladorRuta::ctrRutaInicio();
?>
<div class="fondoCursoBanner">
    <div class="container">
        <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-4 col-sm-4">
                <h4><span class="tituloCurso"><?php echo $curso["nombre"]; ?></span></h4>
                <p class="categoria">Categoria: <?php echo $cate["nombre"]; ?></p>
                <p class="star">
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                </p>
                <p class="valor">$ <?php echo $curso["valor"]; ?></p>
                <form method="post" action="registro/register">
                    <input type="hidden" value="<?php echo $curso["id"]; ?>" name="idCurso">
                    <div class="d-grid gap-2">
                        <input type="submit" id="submit" class="ingresar-btn btn btn-default btn-lg shadow-lg mt-3" value="Comprar Curso">
                    </div>
                </form>

                <br>
            </div>
            <div class="col-md-6 col-sm-4">
                <br>
                <img src="registro/<?php echo $curso["banner"]; ?>" class="img-responsive img-fluid rounded" alt="Curso Calibélula">
            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-md-7 col-sm-4">
            <br>
            <div id="contenedorVideo">
                <iframe width="640" height="480"
                    src="assets/<?php echo $curso["promoVideo"]; ?>" frameborder="0" allowfullscreen>
                </iframe>
            </div>
            <br>
            <p class="categoria">Descripción del cursos</p>
            <p class="descri"><?php echo $curso["descripcion"]; ?>
            <form method="post" action="registro/register">
                <input type="hidden" value="<?php echo $curso["id"]; ?>" name="idCurso">
                <div class="d-grid gap-2">
                    <input type="submit" id="submit" class="ingresar-btn btn btn-default btn-lg" value="Comprar Curso">
                </div>
            </form>
            </p>

        </div>

        <div class="col-lg-4 col-sm-4">
            <br>
            <div class="blog_right_sidebar">
                <h3 class="widget_title">Profesor</h3>
                <aside class="single_sidebar_widget author_widget">
                    <img class="rounded-circle" src="assets/img/taller-dm.png" alt="">
                    <h4>Patricia Elena Patiño Yepes</h4>
                    <p>Directora del Festival Internacional de Cine Infantil y Juvenil :: Calibélula</p>
                    <p>Comunicadora Social Periodista de la Universidad Autónoma de Occidente; con especializaciones en Administración Pública y Gerencia de Marketing, de la Universidad del Valle; diplomados en proyectos internacionales; documental de creación; gestión financiera y de recursos, entre otros estudios.
                    </p>
                    <div class="br"></div>
                </aside>

                <aside class="single_sidebar_widget popular_post_widget">
                    <h3 class="widget_title">Categorías</h3>
                    <div class="blog_info">
                        <div class="post_tag">
                            <a href="#">Cine,</a>
                            <a class="active" href="#">Stop Motion, </a>
                            <a href="#">Animación 3D, </a>
                            <a href="#">Cursos, </a>
                            <a href="#">Animación, </a>
                            <a href="#">Cine,</a>
                            <a href="#">Películas animadas, </a>
                            <a href="#">Series animadas.</a>
                        </div>
                    </div>
                </aside>
            </div>
            <br>
        </div>

        <div class="row">
            <div class="col-md-12 col-sm-6">

            </div>
        </div>

    </div>

</div>

<?php include "assets/plantilla/footer.php"; ?>
<!-- SCRIPTS -->