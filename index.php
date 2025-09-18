<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/cursosApp/publico/controladores/cursosInicio.controlador.php";
//Modelos
require_once $_SERVER['DOCUMENT_ROOT'] . "/cursosApp/publico/modelos/cursosInicio.modelo.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/cursosApp/publico/controladores/ruta.controlador.php";

////Modelos
require_once $_SERVER['DOCUMENT_ROOT'] . "/cursosApp/App/modelos/usuarios.modelo.php";

$plantilla = new ControladorRuta();
$plantilla->ctrPlantilla();
