<?php

require_once "controladores/cursosInicio.controlador.php";
//Modelos
require_once "modelos/cursosInicio.modelo.php";

if (isset($_GET["pagina"])) {
  $item = "url_amiga";
  $valor = $_GET["pagina"];
  $curso = ControladorCursosInicio::ctrMostrarUnCursoInicio($item, $valor);
  if (isset($curso["url_amiga"])) {
    include "curso.php";
  } else {
    include "inicio.php";
  }
} else {
  include "inicio.php";
}


////Modelos
require_once "../App/modelos/usuarios.modelo.php";

$plantilla = new ControladorRuta();
$plantilla->ctrPlantilla();
