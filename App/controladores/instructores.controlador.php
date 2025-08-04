<?php

class ControladorInstructores
{
    /**
     * Mostrar todas las solicitudes pendientes o no
     */
    public static function ctrMostrarSolicitudes($estado = null)
    {
        return ModeloInstructores::mdlMostrarSolicitudes($estado);
    }

    /**
     * Cambiar el estado de una solicitud (aprobar o rechazar)
     */
    public static function ctrCambiarEstadoSolicitud($idSolicitud, $nuevoEstado)
    {
        // Validar entrada
        if (!in_array($nuevoEstado, ['aprobada', 'rechazada'])) {
            return false;
        }
        // Cambiar estado en la tabla de solicitudes
        $respuesta = ModeloInstructores::mdlActualizarEstadoSolicitud($idSolicitud, $nuevoEstado);

        // Si se aprueba, también se cambia el rol del usuario a "instructor"
        if ($respuesta && $nuevoEstado === 'aprobada') {
            $idUsuario = ModeloInstructores::mdlObtenerIdUsuarioPorSolicitud($idSolicitud);
            ModeloUsuarios::mdlActualizarRol($idUsuario, 'instructor');
        }

        return $respuesta;
    }
}
