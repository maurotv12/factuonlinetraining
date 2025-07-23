<?php
require_once "../../../modelos/conexion.php";
require_once "../../../modelos/cursos.modelo.php";
require_once "../../../controladores/cursos.controlador.php";

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'crear_seccion':
            $datos = [
                'id_curso' => $_POST['id_curso'],
                'titulo' => $_POST['titulo'],
                'descripcion' => $_POST['descripcion'] ?? '',
                'orden' => $_POST['orden'] ?? 1,
                'estado' => 'activo'
            ];

            $resultado = ControladorCursos::ctrCrearSeccion($datos);

            if ($resultado != "error") {
                $response['success'] = true;
                $response['message'] = 'Sección creada correctamente';
                $response['id'] = $resultado;
            } else {
                $response['message'] = 'Error al crear la sección';
            }
            break;

        case 'actualizar_seccion':
            $datos = [
                'id' => $_POST['id'],
                'titulo' => $_POST['titulo'],
                'descripcion' => $_POST['descripcion'] ?? '',
                'orden' => $_POST['orden'] ?? 1,
                'estado' => $_POST['estado'] ?? 'activo'
            ];

            $resultado = ControladorCursos::ctrActualizarSeccion($datos);

            if ($resultado == "ok") {
                $response['success'] = true;
                $response['message'] = 'Sección actualizada correctamente';
            } else {
                $response['message'] = 'Error al actualizar la sección';
            }
            break;

        case 'eliminar_seccion':
            $id = $_POST['id'];
            $resultado = ControladorCursos::ctrEliminarSeccion($id);

            if ($resultado == "ok") {
                $response['success'] = true;
                $response['message'] = 'Sección eliminada correctamente';
            } else {
                $response['message'] = 'Error al eliminar la sección';
            }
            break;

        case 'crear_contenido':
            // Manejar subida de archivo si existe
            $archivo_url = '';
            $tamaño_archivo = 0;

            if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] == 0) {
                $tipo = $_POST['tipo'];
                $archivo_url = ControladorCursos::ctrSubirArchivoContenido($_FILES['archivo'], $tipo);
                $tamaño_archivo = $_FILES['archivo']['size'];

                if (!$archivo_url) {
                    $response['message'] = 'Error al subir el archivo';
                    echo json_encode($response);
                    exit;
                }
            }

            $datos = [
                'id_seccion' => $_POST['id_seccion'],
                'titulo' => $_POST['titulo'],
                'descripcion' => $_POST['descripcion'] ?? '',
                'tipo' => $_POST['tipo'],
                'archivo_url' => $archivo_url,
                'duracion' => $_POST['duracion'] ?? null,
                'tamaño_archivo' => $tamaño_archivo,
                'orden' => $_POST['orden'] ?? 1,
                'estado' => 'activo'
            ];

            $resultado = ControladorCursos::ctrCrearContenido($datos);

            if ($resultado != "error") {
                $response['success'] = true;
                $response['message'] = 'Contenido creado correctamente';
                $response['id'] = $resultado;
            } else {
                $response['message'] = 'Error al crear el contenido';
            }
            break;

        case 'actualizar_contenido':
            // Manejar subida de archivo si existe
            $archivo_url = $_POST['archivo_url_actual'] ?? '';
            $tamaño_archivo = $_POST['tamaño_archivo_actual'] ?? 0;

            if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] == 0) {
                $tipo = $_POST['tipo'];
                $nuevo_archivo = ControladorCursos::ctrSubirArchivoContenido($_FILES['archivo'], $tipo);

                if ($nuevo_archivo) {
                    // Eliminar archivo anterior si existe
                    if ($archivo_url && file_exists($archivo_url)) {
                        unlink($archivo_url);
                    }
                    $archivo_url = $nuevo_archivo;
                    $tamaño_archivo = $_FILES['archivo']['size'];
                }
            }

            $datos = [
                'id' => $_POST['id'],
                'titulo' => $_POST['titulo'],
                'descripcion' => $_POST['descripcion'] ?? '',
                'tipo' => $_POST['tipo'],
                'archivo_url' => $archivo_url,
                'duracion' => $_POST['duracion'] ?? null,
                'tamaño_archivo' => $tamaño_archivo,
                'orden' => $_POST['orden'] ?? 1,
                'estado' => $_POST['estado'] ?? 'activo'
            ];

            $resultado = ControladorCursos::ctrActualizarContenido($datos);

            if ($resultado == "ok") {
                $response['success'] = true;
                $response['message'] = 'Contenido actualizado correctamente';
            } else {
                $response['message'] = 'Error al actualizar el contenido';
            }
            break;

        case 'eliminar_contenido':
            $id = $_POST['id'];

            // Obtener datos del contenido para eliminar archivo
            $conn = Conexion::conectar();
            $stmt = $conn->prepare("SELECT archivo_url FROM seccion_contenido WHERE id = ?");
            $stmt->execute([$id]);
            $contenido = $stmt->fetch(PDO::FETCH_ASSOC);

            $resultado = ControladorCursos::ctrEliminarContenido($id);

            if ($resultado == "ok") {
                // Eliminar archivo físico si existe
                if ($contenido && $contenido['archivo_url'] && file_exists($contenido['archivo_url'])) {
                    unlink($contenido['archivo_url']);
                }

                $response['success'] = true;
                $response['message'] = 'Contenido eliminado correctamente';
            } else {
                $response['message'] = 'Error al eliminar el contenido';
            }
            break;

        case 'obtener_contenido':
            $id = $_POST['id'];
            $conn = Conexion::conectar();
            $stmt = $conn->prepare("SELECT * FROM seccion_contenido WHERE id = ?");
            $stmt->execute([$id]);
            $contenido = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($contenido) {
                $response['success'] = true;
                $response['data'] = $contenido;
            } else {
                $response['message'] = 'Contenido no encontrado';
            }
            break;

        default:
            $response['message'] = 'Acción no válida';
    }
}

echo json_encode($response);
