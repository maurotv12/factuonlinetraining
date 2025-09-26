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
          $cursos = ModeloCursosInicio::mdlMostrarUnCursoInicio($tabla, $item, $valor);
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

     public static function ctrObtenerCategorias()
     {
          $conn = Conexion::conectar();
          $stmt = $conn->query("SELECT id, nombre FROM categoria");
          return $stmt->fetchAll(PDO::FETCH_ASSOC);
     }

     /*--==========================================
     Mostrar cursos filtrados por categoría
     ============================================--*/
     public static function ctrMostrarCursosPorCategoria($idCategoria = null)
     {
          $tabla = "curso";
          $rutaInicio = ControladorRuta::ctrRutaInicio();

          if ($idCategoria && $idCategoria !== 'todas') {
               $cursos = ModeloCursosInicio::mdlMostrarCursosInicio($tabla, "id_categoria", $idCategoria);
          } else {
               $cursos = ModeloCursosInicio::mdlMostrarCursosInicio($tabla, null, null);
          }

          return $cursos ? $cursos : [];
     }

     /*--==========================================
     Obtener todos los datos del curso para la vista
     ============================================--*/
     public static function ctrObtenerDatosCursoCompleto($urlAmiga)
     {
          // Obtener datos del curso
          $curso = self::ctrMostrarUnCursoInicio("url_amiga", $urlAmiga);

          if (!$curso) {
               return null; // Curso no encontrado
          }

          // Obtener datos de la categoría
          $categoria = self::ctrConsultarUnCursoInicio("id", $curso["id_categoria"], "categoria");

          // Obtener datos del profesor
          $profesor = self::ctrConsultarUnCursoInicio("id", $curso["id_persona"], "persona");

          // Procesar biografía del profesor
          $bioData = self::ctrProcesarBiografiaProfesor($profesor["biografia"] ?? '');

          // Obtener y procesar todas las categorías
          $todasCategorias = self::ctrMostrarCursosInicio();
          $categorias = self::procesarCategorias($todasCategorias);

          // Procesar los campos de viñetas del curso
          $aprendizajes = self::procesarViñetas($curso["lo_que_aprenderas"] ?? '');
          $requisitos = self::procesarViñetas($curso["requisitos"] ?? '');
          $paraQuien = self::procesarViñetas($curso["para_quien"] ?? '');

          return [
               'curso' => $curso,
               'categoria' => $categoria,
               'profesor' => array_merge($profesor, ['bioData' => $bioData]),
               'categorias' => $categorias,
               'aprendizajes' => $aprendizajes,
               'requisitos' => $requisitos,
               'paraQuien' => $paraQuien,
               'rutaInicio' => ControladorRuta::ctrRutaInicio()
          ];
     }

     /*--==========================================
     Procesar categorías únicas
     ============================================--*/
     private static function procesarCategorias($todasCategorias)
     {
          $categorias = [];
          $categoriasVistas = [];

          foreach ($todasCategorias as $cursoTemp) {
               if (!in_array($cursoTemp["id_categoria"], $categoriasVistas)) {
                    $categorias[] = self::ctrConsultarUnCursoInicio("id", $cursoTemp["id_categoria"], "categoria");
                    $categoriasVistas[] = $cursoTemp["id_categoria"];
               }
          }

          return $categorias;
     }

     /*--==========================================
     Procesar texto de viñetas (separado por \n)
     ============================================--*/
     private static function procesarViñetas($texto)
     {
          if (empty($texto)) {
               return [];
          }

          $items = explode("\n", $texto);
          $viñetas = [];

          foreach ($items as $item) {
               $item = trim($item);
               if ($item !== '') {
                    $viñetas[] = htmlspecialchars($item);
               }
          }

          return $viñetas;
     }
}
