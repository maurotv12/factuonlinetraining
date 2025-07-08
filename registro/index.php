<?php
/**
@grcarvajal grcarvajal@gmail.com **Gildardo Restrepo Carvajal**
26/05/2022 Plataforma Cursos Registro
 */
require_once "../controladores/ruta.controlador.php";
require_once "../App/controladores/usuarios.controlador.php";

////Modelos
require_once "../App/modelos/usuarios.modelo.php";

$plantilla = new ControladorRuta();
$plantilla -> ctrPlantilla();
