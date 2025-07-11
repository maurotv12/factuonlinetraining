<?php

/**
// @grcarvajal grcarvajal@gmail.com **Gildardo Restrepo Carvajal**
// 27/05/2022 aplicación cursos
 */
require_once "controladores/cursosInicio.controlador.php";
//Modelos
require_once "modelos/cursosInicio.modelo.php";

if (isset($_GET["pagina"])) {
  $item = "urlAmiga";
  $valor = $_GET["pagina"];
  $curso = ControladorCursosInicio::ctrMostrarUnCursoInicio($item, $valor);
  if (isset($curso["urlAmiga"])) {
    include "curso.php";
  } else {
    include "inicio.php";
  }
} else {
  include "inicio.php";
}
