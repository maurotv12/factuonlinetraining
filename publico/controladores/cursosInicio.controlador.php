<?php
//importar controlador de rutas
require_once "ruta.controlador.php";


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
     public static function ctrMostrarUnCursoInicio($item, $valor)
     {
          $tabla = "curso";
          $cursos = ModeloCursosInicio::mdlMostrarCursosInicio($tabla, $item, $valor);
          return $cursos;
     }

     /*--==========================================
  Consultar los datos de un curso en inicio
============================================--*/
     public static function ctrConsultarUnCursoInicio($item, $valor, $tabla)
     {
          $resul = ModeloCursosInicio::mdlConsultarUnCursoInicio($item, $valor, $tabla);
          return $resul;
     }

     /*--==========================================
     Procesar biografía del profesor para vista
     ============================================--*/
     public static function ctrProcesarBiografiaProfesor($biografia, $maxWords = 40, $maxChars = 226)
     {
          if (empty($biografia)) {
               return [
                    'bioShort' => '',
                    'bioFull' => '',
                    'showVerMas' => false
               ];
          }

          $bioFull = $biografia;
          $bioShort = $biografia;
          $showVerMas = false;

          // Verificar si excede los límites
          if (str_word_count($biografia) > $maxWords || strlen($biografia) > $maxChars) {
               // Cortar por caracteres primero
               $bioShort = mb_substr($biografia, 0, $maxChars);

               // Luego verificar por palabras
               $words = explode(' ', $bioShort);
               if (count($words) > $maxWords) {
                    $bioShort = implode(' ', array_slice($words, 0, $maxWords));
               }

               $showVerMas = true;
          }

          return [
               'bioShort' => $bioShort,
               'bioFull' => $bioFull,
               'showVerMas' => $showVerMas
          ];
     }
}
