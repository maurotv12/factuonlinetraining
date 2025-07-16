<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/cursosapp/publico/controladores/cursosInicio.controlador.php";
//Modelos
require_once $_SERVER['DOCUMENT_ROOT'] . "/cursosapp/publico/modelos/cursosInicio.modelo.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/cursosapp/publico/controladores/ruta.controlador.php";

////Modelos
require_once $_SERVER['DOCUMENT_ROOT'] . "/cursosapp/App/modelos/usuarios.modelo.php";

$plantilla = new ControladorRuta();
$plantilla->ctrPlantilla();
