<?php
//importar controlador de rutas
require_once "ruta.controlador.php";
/**
@grcarvajal grcarvajal@gmail.com **Gildardo Restrepo Carvajal**
26/05/2022 Plataforma Cursos Ver cursos en inicio
 */

class ControladorCursosInicio
{
     // Mostar Cursos en inicio público
     public static function ctrMostrarCursosInicio()
     {
          $tabla = "curso";
          $item = null;
          $valor = null;
          $rutaInicio = ControladorRuta::ctrRutaInicio();
          $cursos = ModeloCursosInicio::mdlMostrarCursosInicio($tabla, $item, $valor);
          return $cursos; // Solo retorna los datos
     }

     /*--==========================================
     Mostrar cursos en inicio todos o solo 1
============================================--*/
     static public function ctrMostrarUnCursoInicio($item, $valor)
     {
          $tabla = "curso";
          $cursos = ModeloCursosInicio::mdlMostrarCursosInicio($tabla, $item, $valor);
          return $cursos;
     }

     /*--==========================================
  Consultar los datos de un curso en inicio
============================================--*/
     static public function ctrConsultarUnCursoInicio($item, $valor, $tabla)
     {
          $resul = ModeloCursosInicio::mdlConsultarUnCursoInicio($item, $valor, $tabla);
          return $resul;
     }
}
