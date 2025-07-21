<?php
// session_start(); // Asegúrate de tener activa la sesión

require_once $_SERVER['DOCUMENT_ROOT'] . "/cursosApp/App/modelos/conexion.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/cursosApp/App/modelos/cursos.modelo.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/cursosApp/App/controladores/cursos.controlador.php";

// Obtener lista de profesores y categorías
$profesores = ControladorCursos::ctrObtenerProfesores();
$categorias = ControladorCursos::ctrObtenerCategorias();

// Procesar el formulario si se envió
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $categoria = $_POST['categoria'];
    $valor = $_POST['precio'];
    $id_persona = isset($_POST['profesor']) ? $_POST['profesor'] : $_SESSION['id_persona']; // Usar el profesor seleccionado o el usuario de la sesión

    // Generar URL amigable
    $url_amiga = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $nombre)));

    // Enviar datos al controlador
    $datosCurso = array(
        "url_amiga" => $url_amiga,
        "nombre" => $nombre,
        "descripcion" => $descripcion,
        "imagen" => isset($_FILES['imagen']) ? $_FILES['imagen'] : null,
        "video" => isset($_FILES['video']) ? $_FILES['video'] : null,
        "valor" => $valor,
        "id_categoria" => $categoria,
        "id_persona" => $id_persona,
        "estado" => "activo"
    );

    $respuesta = ControladorCursos::ctrCrearCurso($datosCurso);

    if ($respuesta === "ok") {
        echo "<script>alert('Curso creado exitosamente'); window.location='cursos';</script>";
        exit;
    } else {
        echo "<script>alert('Error al crear el curso');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Crear Curso</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-5 mb-5">
        <h2 class="mb-4">Crear Nuevo Curso</h2>

        <form id="form-crear-curso" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre del Curso</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required></textarea>
            </div>

            <div class="mb-3">
                <label for="categoria" class="form-label">Categoría</label>
                <select class="form-select" id="categoria" name="categoria" required>
                    <option value="" selected disabled>Selecciona una categoría</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= $cat['nombre'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="profesor" class="form-label">Profesor</label>
                <select class="form-select" id="profesor" name="profesor" required>
                    <option value="" selected disabled>Selecciona un profesor</option>
                    <?php foreach ($profesores as $prof): ?>
                        <option value="<?= $prof['id'] ?>"><?= $prof['nombre'] ?> (<?= $prof['email'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="imagen" class="form-label">Imagen del Curso</label>
                <input class="form-control" type="file" id="imagen" name="imagen" accept="image/*" required>
            </div>

            <div class="mb-3">
                <label for="video" class="form-label">Video Promocional (opcional)</label>
                <input class="form-control" type="file" id="video" name="video" accept="video/*">
            </div>

            <div class="mb-3">
                <label for="precio" class="form-label">Precio (COP)</label>
                <input type="number" class="form-control" id="precio" name="precio" min="0" required>
            </div>

            <button type="submit" class="btn btn-primary">Crear Curso</button>
        </form>
    </div>

    <script src="/cursosapp/assets/js/validarImagenCurso.js"></script>
</body>

</html>