<?php

/**
 * Controlador para gestionar los cursos destacados que aparecen en el carrusel
 */
class ControladorCursosDestacados
{

    /**
     * Obtiene los cursos destacados para mostrar en el carrusel
     * @param int $limite Cantidad máxima de cursos a obtener
     * @return array Lista de cursos destacados
     */
    static public function ctrObtenerCursosDestacados($limite = 3)
    {
        $tabla = "curso";

        // Llamamos al modelo para obtener los cursos destacados
        return ModeloCursosInicio::mdlObtenerCursosDestacados($tabla, $limite);
    }

    /**
     * Formatea los datos de los cursos para el carrusel
     * @param array $cursos Lista de cursos obtenidos de la base de datos
     * @return array Lista de cursos formateados para el carrusel
     */
    static public function ctrFormatearCursosParaCarrusel($cursos)
    {
        $cursosFormateados = [];

        if (!$cursos) {
            return $cursosFormateados;
        }

        // Si solo hay un curso, convertirlo en array para procesarlo de la misma forma
        if (isset($cursos['id'])) {
            $cursos = [$cursos];
        }

        foreach ($cursos as $curso) {
            $cursosFormateados[] = [
                'id' => $curso['id'],
                'titulo' => $curso['nombre'],
                'descripcion' => substr($curso['descripcion'], 0, 150) . '...',
                'imagen' => 'App/' . $curso['banner'],
                'url' => $curso['url_amiga'],
                'precio' => $curso['valor'] == 0 ? "Gratis" : '$ ' . $curso['valor']
            ];
        }

        return $cursosFormateados;
    }
}
