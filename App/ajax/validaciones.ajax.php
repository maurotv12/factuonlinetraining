<?php

require_once __DIR__ . "/../modelos/conexion.php";

class AjaxValidaciones
{
    /*=============================================
    Validar nombre único de curso
    =============================================*/
    public function ajaxValidarNombreCurso()
    {
        if (isset($_POST['nombre'])) {
            $nombre = trim($_POST['nombre']);
            $idCursoExcluir = isset($_POST['id_curso']) ? $_POST['id_curso'] : null;

            if (empty($nombre)) {
                echo json_encode([
                    'error' => true,
                    'mensaje' => 'El nombre del curso no puede estar vacío.'
                ]);
                return;
            }

            if (strlen($nombre) < 3) {
                echo json_encode([
                    'error' => true,
                    'mensaje' => 'El nombre del curso debe tener al menos 3 caracteres.'
                ]);
                return;
            }

            $esUnico = $this->validarNombreUnico($nombre, $idCursoExcluir);

            if ($esUnico) {
                echo json_encode([
                    'error' => false,
                    'mensaje' => 'Nombre disponible.'
                ]);
            } else {
                echo json_encode([
                    'error' => true,
                    'mensaje' => 'Ya existe un curso con este nombre.'
                ]);
            }
        } else {
            echo json_encode([
                'error' => true,
                'mensaje' => 'Datos incompletos.'
            ]);
        }
    }

    /*=============================================
    Validar que el nombre del curso sea único (método directo)
    =============================================*/
    private function validarNombreUnico($nombre, $idCursoExcluir = null)
    {
        try {
            $conn = Conexion::conectar();

            // Si estamos editando un curso, excluir el curso actual de la validación
            if ($idCursoExcluir) {
                $stmt = $conn->prepare("SELECT COUNT(*) as total FROM curso WHERE nombre = ? AND id != ?");
                $stmt->execute([$nombre, $idCursoExcluir]);
            } else {
                $stmt = $conn->prepare("SELECT COUNT(*) as total FROM curso WHERE nombre = ?");
                $stmt->execute([$nombre]);
            }

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] == 0; // Retorna true si no existe, false si ya existe
        } catch (Exception $e) {
            error_log("Error validando nombre único: " . $e->getMessage());
            return true; // En caso de error, asumir que es único
        }
    }

    /*=============================================
    Validar URL amigable única
    =============================================*/
    public function ajaxValidarUrlAmigable()
    {
        if (isset($_POST['url_amiga'])) {
            $urlAmiga = trim($_POST['url_amiga']);
            $idCursoExcluir = isset($_POST['id_curso']) ? $_POST['id_curso'] : null;

            if (empty($urlAmiga)) {
                echo json_encode([
                    'error' => true,
                    'mensaje' => 'La URL amigable no puede estar vacía.'
                ]);
                return;
            }

            $esUnica = $this->validarUrlAmigableUnica($urlAmiga, $idCursoExcluir);

            if ($esUnica) {
                echo json_encode([
                    'error' => false,
                    'mensaje' => 'URL disponible.'
                ]);
            } else {
                echo json_encode([
                    'error' => true,
                    'mensaje' => 'Ya existe un curso con esta URL.'
                ]);
            }
        } else {
            echo json_encode([
                'error' => true,
                'mensaje' => 'Datos incompletos.'
            ]);
        }
    }

    /*=============================================
    Validar que la URL amigable sea única (método directo)
    =============================================*/
    private function validarUrlAmigableUnica($urlAmiga, $idCursoExcluir = null)
    {
        try {
            $conn = Conexion::conectar();

            // Si estamos editando un curso, excluir el curso actual de la validación
            if ($idCursoExcluir) {
                $stmt = $conn->prepare("SELECT COUNT(*) as total FROM curso WHERE url_amiga = ? AND id != ?");
                $stmt->execute([$urlAmiga, $idCursoExcluir]);
            } else {
                $stmt = $conn->prepare("SELECT COUNT(*) as total FROM curso WHERE url_amiga = ?");
                $stmt->execute([$urlAmiga]);
            }

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] == 0; // Retorna true si no existe, false si ya existe
        } catch (Exception $e) {
            error_log("Error validando URL amigable única: " . $e->getMessage());
            return true; // En caso de error, asumir que es única
        }
    }
}

/*=============================================
Procesar peticiones AJAX
=============================================*/
if (isset($_POST["accion"])) {
    $validaciones = new AjaxValidaciones();

    switch ($_POST["accion"]) {
        case 'validar_nombre_curso':
            $validaciones->ajaxValidarNombreCurso();
            break;

        case 'validar_url_amigable':
            $validaciones->ajaxValidarUrlAmigable();
            break;

        default:
            echo json_encode([
                'error' => true,
                'mensaje' => 'Acción no válida.'
            ]);
            break;
    }
} else {
    echo json_encode([
        'error' => true,
        'mensaje' => 'No se especificó una acción.'
    ]);
}
