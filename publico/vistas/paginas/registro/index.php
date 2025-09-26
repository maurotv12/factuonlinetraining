<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/factuonlinetraining/publico/controladores/ruta.controlador.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/factuonlinetraining/App/controladores/usuarios.controlador.php";


////Modelos
require_once $_SERVER['DOCUMENT_ROOT'] . "/factuonlinetraining/App/modelos/usuarios.modelo.php";

$plantilla = new ControladorRuta();
$plantilla->ctrPlantilla();
