<?php
$rutaInicio = ControladorGeneral::ctrRuta(); //Ruta al login o registro
$ruta = ControladorGeneral::ctrRutaApp(); //Ruta dentro de dashboard

if (!isset($_SESSION["validarSesion"])) {
    echo '<script>
				window.location = "' . $rutaInicio . 'login";
		 	 </script>';
    return;
}

$item = "id";
$valor = $_SESSION["idU"];
$usuario = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tablero :: Cursos</title>

    <!-- Favicons -->
    <link rel="icon" href="/cursosApp/assets/favicon.ico" sizes="32x32" />

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="/cursosApp/App/vistas/assets/vendors/iconly/bold.css">

    <link rel="stylesheet" href="/cursosApp/App/vistas/assets/vendors/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="/cursosApp/App/vistas/assets/css/pages/menu.css">
    <link rel="stylesheet" href="/cursosApp/App/vistas/assets/css/pages/usuarios.css">
    <link rel="stylesheet" href="/cursosApp/App/vistas/assets/css/pages/crearCurso.css">
    <link rel="stylesheet" href="/cursosApp/App/vistas/assets/css/pages/estudiante.css">

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

    <!-- Bootstrap CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <!-- SWEET ALERT 2 -->
    <script src="/cursosApp/App/vistas/assets/vendors/sweetalert2/sweetalert2.all.min.js"></script>
    <!-- DATATABLES -->
    <link href="https://cdn.datatables.net/v/bs5/dt-2.3.2/datatables.min.css" rel="stylesheet" integrity="sha384-nt2TuLL4RlRQ9x6VTFgp009QD7QLRCYX17dKj9bj51w2jtWUGFMVTveRXfdgrUdx" crossorigin="anonymous">

</head>

<body>

    <div id="app">
        <?php
        include "plantillaPartes/menu.php";
        ?>
        <div id="main" class="main-content">
            <?php
            include "plantillaPartes/header.php";

            /*=============================================
            Páginas del sitio con control de acceso por roles
            =============================================*/
            include ControladorGeneral::ctrCargarPaginaConAcceso();

            include "plantillaPartes/footer.php";
            ?>
        </div>
    </div>

    <!-- Cargar librerías primero -->
    <script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/dt-2.3.2/datatables.min.js" integrity="sha384-rL0MBj9uZEDNQEfrmF51TAYo90+AinpwWp2+duU1VDW/RG7flzbPjbqEI3hlSRUv" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Script de inicialización después de las librerías -->
    <script>
        // Verificar que jQuery esté disponible antes de usarlo
        if (typeof $ !== 'undefined') {
            $(document).ready(function() {
                // Solo inicializar DataTable si no se ha cargado un script específico para la página
                // y si la tabla existe
                if ($('#table_id').length &&
                    !window.dataTableInitialized &&
                    !$('script[src*="usuariosAdmin.js"]').length &&
                    !$('script[src*="listadoCursos.js"]').length) {

                    $('#table_id').DataTable({
                        "language": {
                            "url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
                        },
                    });

                    window.dataTableInitialized = true;
                }
            });
        } else {
            console.warn('jQuery no está disponible');
        }
    </script>
</body>

</html>