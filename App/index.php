<?php

/**
@grcarvajal grcarvajal@gmail.com **Gildardo Restrepo Carvajal**
12/06/2022 Plataforma Calibelula mostrar Cursos
Inicio de la aplicación se inicializan los controladores y modelos y se redirige al inicio de la app
 */
require_once "controladores/general.controlador.php";
require_once "controladores/usuarios.controlador.php";
require_once "controladores/inscripciones.controlador.php";
require_once "controladores/cursos.controlador.php";
require_once "controladores/autenticacion.controlador.php";

require_once "modelos/general.modelo.php";
require_once "modelos/usuarios.modelo.php";
require_once "modelos/inscripciones.modelo.php";
require_once "modelos/cursos.modelo.php";

//extensiones para librerias de composer o terceros
require_once "extensiones/vendor/autoload.php";


$plantilla = new ControladorGeneral();
$plantilla->ctrPlantilla();
