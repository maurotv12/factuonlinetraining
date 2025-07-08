<?php
session_start();
$ruta = ControladorRuta::ctrRuta();
$rutaInicio = ControladorRuta::ctrRutaInicio();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CursosApp :: Cursos y Talleres</title>
     <!-- Favicons -->
    <link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">

     <script src="../assets/dist/js/bootstrap.bundle.min.js"></script>
     <script src="../assets/dist/js/bootstrap.min.js"></script>
     <!-- MAIN CSS -->
     <link rel="stylesheet" href="../assets/css/templatemo-style.css">
     <link rel="stylesheet" href="../assets/css/styleCursos.css">
     <link rel="stylesheet" href="vistas/css/auth.css">

     <link rel="icon" href="../favicon.jpg" sizes="32x32" />
     <link rel="icon" href="../favicon-180.jpg" sizes="192x192" />
     <!-- SWEET ALERT 2 -->
     <!-- https://sweetalert2.github.io/ -->
     <script src="../assets/js/plugins/sweetalert2.all.js"></script>
</head>
<body>
<!-- MENU -->       
<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top custom-navbar">
<div class="container">
      <!-- lOGO TEXT HERE -->
      <a href="<?php echo $rutaInicio; ?>" class="navbar-brand"><img src="">CursosApp</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarScroll">
      <ul class="navbar-nav ms-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
        <li class="nav-item"><a class="nav-link active" aria-current="page" href="<?php echo $rutaInicio; ?>">Inicio</a></li>
        <li class="nav-item"><a class="nav-link" href="#cursos">Cursos</a></li>
        <li class="nav-item"><a class="nav-link" href="#nosotros">Nosotros</a></li>
        <li class="nav-item"><a class="nav-link" href="#contacto">Contactnos </a></li>
        <li><a href="<?php echo $ruta; ?>login" class="ingresar-btn btn btn-default">Ingresar</a></li>
      </ul>
    </div>
  </div>
</nav>
<?php

if(isset($_GET["pagina"]))
{
	if(    $_GET["pagina"] == "login"  ||
	 	     $_GET["pagina"] == "register" ||
         $_GET["pagina"] == "forgot-password")
            {
		        include "paginas/".$_GET["pagina"].".php";
	           }
  }else{
	include "paginas/register.php";
    }
?>
<!-- FOOTER -->
<footer id="footer">
  <div class="container">
       <div class="row">
          <div class="col-md-4 col-sm-6">
               <div class="footer-info">
                    <h2 class="titleFes">CursosApp</h2>
                      <address>
                      <p>CursosApp es un servicio de aprendizaje donde puedes ver una gran variedad de cursos.</p>
                      </address>

                      <ul class="social-icon">
                           <li><a href="#" class="fa fa-facebook" attr="facebook icon" target="_blank"></a></li>
                           <li><a href="#" class="fa fa-instagram" target="_blank"></a></li>
                           <li><a href="#" class="fa fa-youtube-play" target="_blank"></a></li>
                           <li><a href="#" target="_blank" class="fa fa-twitter"></a></li>
                      </ul>
               </div>
          </div>
          <div class="col-md-4 col-sm-6">
               <div class="footer-info">
                         <h2 class="titleFes">Contáctenos</h2>
                    <address>
                         <p>+(57) 317 000 00 00</p>
                         <p><a href="#">cursosApp@gmail.com</a></p>
                         <p>Valle del Cauca,<br> Cali, Colombia</p>
                    </address>
               </div>
          </div>
          <div class="col-md-4 col-sm-6">
             <div class="footer-info">
                  <h2 class="titleFes">Enlaces rápidos</h2>
                <div class="footer_menu">
                  <ul>
                    <li><a href="<?php echo $rutaInicio; ?>">Inicio</a></li>
                    <li><a href="#cursos">Cursos</a></li>
                    <li><a href="#nosotros">Nosotros</a></li>
                    <li><a href="#contacto">Contáctenos</a></li>
                    <li><a href="<?php echo $ruta; ?>login">Ingresar</a></li>
                  </ul>
                  </div>
                  <br> 
             </div>
          </div> 
     </div>
     <div class="col-md-12 col-sm-12">
         <div class="titleFes">
           <p>Copyright &copy; 2025 CursosApp</p>
           <p>Developed by: @grcarvajal / grcarvajal@gmail.com</p>
        </div>
      </div>
  </div>
</footer>
</body>
</html>

