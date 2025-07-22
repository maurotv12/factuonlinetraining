<?php
include $_SERVER['DOCUMENT_ROOT'] . "/cursosapp/assets/plantilla/head.php";
include $_SERVER['DOCUMENT_ROOT'] . "/cursosapp/assets/plantilla/menu.php";
?>


<!-- CATEGORIAS -->
<?php
if (!isset($curso) && isset($_GET["pagina"])) {
    require_once $_SERVER['DOCUMENT_ROOT'] . "/cursosApp/publico/controladores/cursosInicio.controlador.php";
    $item = "url_amiga";
    $valor = $_GET["pagina"];
    $curso = ControladorCursosInicio::ctrMostrarUnCursoInicio($item, $valor);
}
?>

<?php
require_once "controladores/cursosInicio.controlador.php";
//Modelos
require_once "modelos/cursosInicio.modelo.php";
require_once "controladores/ruta.controlador.php";

// Obtener información de la categoría del curso
$item = "id";
$valor = $curso["id_categoria"];
$tabla = "categoria";
$cate = ControladorCursosInicio::ctrConsultarUnCursoInicio($item, $valor, $tabla);

// Obtener información del profesor/instructor del curso
$itemProfesor = "id";
$valorProfesor = $curso["id_persona"];
$tablaProfesor = "persona";
$profesor = ControladorCursosInicio::ctrConsultarUnCursoInicio($itemProfesor, $valorProfesor, $tablaProfesor);

// Obtener todas las categorías para mostrar en el sidebar
$todasCategorias = ControladorCursosInicio::ctrMostrarCursosInicio();
// Filtrar para obtener solo categorías únicas
$categorias = [];
$categoriasVistas = [];
foreach ($todasCategorias as $cursoTemp) {
    if (!in_array($cursoTemp["id_categoria"], $categoriasVistas)) {
        $categorias[] = ControladorCursosInicio::ctrConsultarUnCursoInicio("id", $cursoTemp["id_categoria"], "categoria");
        $categoriasVistas[] = $cursoTemp["id_categoria"];
    }
}

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
                <img src="App/<?php echo $curso["banner"]; ?>" class="img-responsive img-fluid rounded" alt="Curso Calibélula">
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
                    src="assets/<?php echo $curso["promo_video"]; ?>" frameborder="0" allowfullscreen>
                </iframe>
            </div>
            <br>

            <!-- Lo que aprenderás con este curso -->
            <?php if (!empty($curso["lo_que_aprenderas"])): ?>
                <div class="aprenderas-container">
                    <h4 class="categoria mb-3">Lo que aprenderás con este curso</h4>
                    <div class="row">
                        <div class="col-md-12">
                            <ul class="list-icon">
                                <?php
                                $aprendizajes = explode("\n", $curso["lo_que_aprenderas"]);
                                foreach ($aprendizajes as $aprendizaje):
                                    if (trim($aprendizaje) !== ""):
                                ?>
                                        <li><i class="fa fa-check-circle text-success"></i> <?php echo htmlspecialchars(trim($aprendizaje)); ?></li>
                                <?php
                                    endif;
                                endforeach;
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Descripción del curso -->
            <p class="categoria">Descripción del cursos</p>
            <p class="descri"><?php echo nl2br($curso["descripcion"]); ?></p>

            <!-- Requisitos del curso -->
            <?php if (!empty($curso["requisitos"])): ?>
                <div class="requisitos-container mt-4">
                    <h4 class="categoria mb-3">Requisitos</h4>
                    <div class="row">
                        <div class="col-md-12">
                            <ul class="list-icon">
                                <?php
                                $reqs = explode("\n", $curso["requisitos"]);
                                foreach ($reqs as $req):
                                    if (trim($req) !== ""):
                                ?>
                                        <li><i class="fa fa-arrow-right text-primary"></i> <?php echo htmlspecialchars(trim($req)); ?></li>
                                <?php
                                    endif;
                                endforeach;
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <form method="post" action="registro/register">
                <input type="hidden" value="<?php echo $curso["id"]; ?>" name="idCurso">
                <div class="d-grid gap-2 mt-4">
                    <input type="submit" id="submit" class="ingresar-btn btn btn-default btn-lg" value="Comprar Curso">
                </div>
            </form>

        </div>

        <div class="col-lg-4 col-sm-4">
            <br>
            <div class="blog_right_sidebar">
                <h3 class="widget_title">Profesor</h3>
                <aside class="single_sidebar_widget author_widget">
                    <?php if (!empty($profesor["foto"]) && $profesor["foto"] != 'vistas/img/usuarios/default/default.png'): ?>
                        <img class="rounded-circle" src="App/vistas/img/usuarios/<?php echo basename($profesor["foto"]); ?>" alt="Foto del profesor" style="width: 80px; height: 80px; object-fit: cover;">
                    <?php else: ?>
                        <img class="rounded-circle" src="assets/img/taller-dm.png" alt="Foto por defecto" style="width: 80px; height: 80px; object-fit: cover;">
                    <?php endif; ?>
                    <h4><?php echo $profesor["nombre"]; ?></h4>
                    <?php if (!empty($profesor["profesion"])): ?>
                        <p><?php echo $profesor["profesion"]; ?></p>
                    <?php endif; ?>
                    <?php if (!empty($profesor["biografia"])): ?>
                        <?php
                        // Procesar la biografía usando el controlador
                        $bioData = ControladorCursosInicio::ctrProcesarBiografiaProfesor($profesor["biografia"]);
                        ?>
                        <p id="bio-short" style="display:block;">
                            <?php echo nl2br(htmlspecialchars($bioData['bioShort'])); ?>
                            <?php if ($bioData['showVerMas']): ?>... <a href="#" id="ver-mas" onclick="document.getElementById('bio-short').style.display='none';document.getElementById('bio-full').style.display='block';return false;">Ver más</a><?php endif; ?>
                        </p>
                        <p id="bio-full" style="display:none;">
                            <?php echo nl2br(htmlspecialchars($bioData['bioFull'])); ?>
                            <a href="#" id="ver-menos" onclick="document.getElementById('bio-full').style.display='none';document.getElementById('bio-short').style.display='block';return false;">Ver menos</a>
                        </p>
                    <?php else: ?>
                        <p>Instructor especializado en <?php echo $cate["nombre"]; ?></p>
                    <?php endif; ?>
                    <div class="br"></div>
                </aside>

                <aside class="single_sidebar_widget popular_post_widget">
                    <h3 class="widget_title">Categorías</h3>
                    <div class="blog_info">
                        <div class="post_tag">
                            <?php foreach ($categorias as $categoria): ?>
                                <a href="#" class="<?php echo ($categoria["id"] == $curso["id_categoria"]) ? 'active' : ''; ?>">
                                    <?php echo $categoria["nombre"]; ?>,
                                </a>
                            <?php endforeach; ?>
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

<!-- SCRIPTS -->