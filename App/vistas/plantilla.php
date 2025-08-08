<?php
$rutaInicio = ControladorGeneral::ctrRuta(); //Ruta al login o registro
$ruta = ControladorGeneral::ctrRutaApp();

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
    <link rel="stylesheet" href="/cursosApp/App/vistas/assets/vendors/sweetalert2/sweetalert2.min.css">

    <link rel="stylesheet" href="/cursosApp/App/vistas/assets/css/menu.css">
    <link rel="stylesheet" href="/cursosApp/App/vistas/assets/css/usuarios.css">
    <link rel="stylesheet" href="/cursosApp/App/vistas/assets/css/crearCurso.css">
    <link rel="stylesheet" href="/cursosApp/App/vistas/assets/css/estudiante.css">
    <link rel="stylesheet" href="/cursosApp/App/vistas/assets/css/perfil.css">
    <link rel="stylesheet" href="/cursosApp/App/vistas/assets/css/perfilProfesor.css">
    <link rel="stylesheet" href="/cursosApp/App/vistas/assets/css/usuariosAdmin.css">
    <?php
    echo '
    <link rel="stylesheet" href="/cursosApp/App/vistas/assets/css/editarCurso.css?v=' . time() . '">';
    echo '<link rel="stylesheet" href="/cursosApp/App/vistas/assets/css/verCurso.css?v=' . time() . '">';
    ?>
    <link rel="stylesheet" href="/cursosApp/App/vistas/assets/css/listadoCursos.css">

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <!-- Bootstrap CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


    <link rel="stylesheet" href="/cursosApp/App/vistas/assets/css/app.css">
    <!-- SWEET ALERT 2 -->
    <!-- https://sweetalert2.github.io/ -->
    <script src="/cursosApp/App/vistas/assets/vendors/sweetalert2/sweetalert2.all.min.js"></script>
    <!-- DATATABLES -->
    <!-- https://datatables.net/examples/basic_init/zero_configuration.html -->
    <script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
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

    <script>
        $(document).ready(function() {
            // Solo inicializar DataTable si no se ha cargado un script específico para la página
            // y si la tabla existe
            if ($('#table_id').length &&
                !window.dataTableInitialized &&
                !$('script[src*="usuariosAdmin.js"]').length &&
                !$('script[src*="listadoCursos.js"]').length) {

                $('#table_id').DataTable({
                    "language": {
                        "lengthMenu": "Mostrar _MENU_ registros por página",
                        "zeroRecords": "No se encontraron resultados en su búsqueda",
                        "searchPlaceholder": "Buscar registros",
                        "info": "Mostrando registros de _START_ al _END_ de un total de  _TOTAL_ registros",
                        "infoEmpty": "No existen registros",
                        "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                        "search": "Buscar:",
                        "paginate": {
                            "first": "Primero",
                            "last": "Último",
                            "next": "Siguiente",
                            "previous": "Anterior"
                        },
                    }
                });

                window.dataTableInitialized = true;
            }
        });
    </script>

    <script src="/cursosApp/App/vistas/assets/js/crearCurso.js"></script>
    <script src="/cursosApp/App/vistas/assets/js/editarCurso.js?v=<?= time() ?>"></script>
    <script src="/cursosApp/App/vistas/assets/js/estudiante.js"></script>
    <script src="/cursosApp/App/vistas/assets/js/listadoCursos.js"></script>
    <script src="/cursosApp/App/vistas/assets/js/listadoCursosProfe.js"></script>
    <script src="/cursosApp/App/vistas/assets/js/perfil.js"></script>
    <script src="/cursosApp/App/vistas/assets/js/perfilProfesor.js"></script>



</body>

</html>