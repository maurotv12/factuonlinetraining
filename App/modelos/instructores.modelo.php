<?php

require_once "conexion.php";

class ModeloInstructores
{
    /**
     * Mostrar todas las solicitudes de instructores
     */
    public static function mdlMostrarSolicitudes($estado = null)
    {
        $stmt = null;

        if ($estado) {
            $stmt = Conexion::conectar()->prepare("SELECT si.id, p.nombre, p.email, si.fecha_solicitud, si.estado
                                                   FROM solicitudes_instructores si
                                                   INNER JOIN persona p ON si.id_persona = p.id
                                                   WHERE si.estado = :estado
                                                   ORDER BY si.fecha_solicitud DESC");
            $stmt->bindParam(":estado", $estado, PDO::PARAM_STR);
        } else {
            $stmt = Conexion::conectar()->prepare("SELECT si.id, p.nombre, p.email, si.fecha_solicitud, si.estado
                                                   FROM solicitudes_instructores si
                                                   INNER JOIN persona p ON si.id_persona = p.id
                                                   ORDER BY si.fecha_solicitud DESC");
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Actualizar el estado de una solicitud
     */
    public static function mdlActualizarEstadoSolicitud($idSolicitud, $estado)
    {
        $stmt = Conexion::conectar()->prepare("UPDATE solicitudes_instructores SET estado = :estado WHERE id = :id");
        $stmt->bindParam(":estado", $estado, PDO::PARAM_STR);
        $stmt->bindParam(":id", $idSolicitud, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Obtener el ID de persona a partir del ID de la solicitud
     */
    public static function mdlObtenerIdUsuarioPorSolicitud($idSolicitud)
    {
        $stmt = Conexion::conectar()->prepare("SELECT id_persona FROM solicitudes_instructores WHERE id = :id");
        $stmt->bindParam(":id", $idSolicitud, PDO::PARAM_INT);
        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado ? $resultado['id_persona'] : null;
    }
}
