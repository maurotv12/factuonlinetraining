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

    // Obtener los nuevos campos
    $lo_que_aprenderas = isset($_POST['lo_que_aprenderas']) ? $_POST['lo_que_aprenderas'] : '';
    $requisitos = isset($_POST['requisitos']) ? $_POST['requisitos'] : '';
    $para_quien = isset($_POST['para_quien']) ? $_POST['para_quien'] : '';

    // Enviar datos al controlador
    $datosCurso = array(
        "url_amiga" => $url_amiga,
        "nombre" => $nombre,
        "descripcion" => $descripcion,
        "lo_que_aprenderas" => $lo_que_aprenderas,
        "requisitos" => $requisitos,
        "para_quien" => $para_quien,
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
                <label for="lo_que_aprenderas" class="form-label">Lo que aprenderás con este curso <span class="text-muted">(Una frase por línea, máximo 70 caracteres cada una)</span></label>
                <textarea class="form-control" id="lo_que_aprenderas" name="lo_que_aprenderas" rows="5" placeholder="Ejemplo:&#10;Aprenderás a utilizar herramientas avanzadas de diseño gráfico.&#10;Dominarás las técnicas de ilustración digital."></textarea>
                <div class="form-text">Cada línea se mostrará como una viñeta en la vista del curso.</div>
            </div>

            <div class="mb-3">
                <label for="requisitos" class="form-label">Requisitos <span class="text-muted">(Una frase por línea, máximo 70 caracteres cada una)</span></label>
                <textarea class="form-control" id="requisitos" name="requisitos" rows="4" placeholder="Ejemplo:&#10;Conocimientos básicos de diseño.&#10;Computador con Adobe Photoshop instalado."></textarea>
                <div class="form-text">Cada línea se mostrará como una viñeta en la vista del curso.</div>
            </div>

            <div class="mb-3">
                <label for="para_quien" class="form-label">Para quién es este curso <span class="text-muted">(Una frase por línea, máximo 70 caracteres cada una)</span></label>
                <textarea class="form-control" id="para_quien" name="para_quien" rows="4" placeholder="Ejemplo:&#10;Diseñadores gráficos que quieran mejorar sus habilidades.&#10;Emprendedores que deseen crear sus propias piezas gráficas."></textarea>
                <div class="form-text">Cada línea se mostrará como una viñeta en la vista del curso.</div>
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

    <script>
        // Función para validar el límite de caracteres por línea
        function validarLineaViñeta(e) {
            const textarea = e.target;
            const lines = textarea.value.split('\n');
            const maxCaracteres = 70;

            // Verificar cada línea
            let lineaDemasiado = lines.findIndex(line => line.length > maxCaracteres);

            // Si hay una línea con más de 70 caracteres, mostrar advertencia
            if (lineaDemasiado >= 0) {
                alert(`La línea ${lineaDemasiado + 1} excede el límite de ${maxCaracteres} caracteres.`);
                // Resaltar el área del problema
                textarea.setSelectionRange(
                    textarea.value.split('\n').slice(0, lineaDemasiado).join('\n').length + (lineaDemasiado > 0 ? 1 : 0),
                    textarea.value.split('\n').slice(0, lineaDemasiado + 1).join('\n').length
                );
                textarea.focus();
            }
        }

        // Asignar validación a los campos de viñetas
        document.getElementById('lo_que_aprenderas').addEventListener('change', validarLineaViñeta);
        document.getElementById('requisitos').addEventListener('change', validarLineaViñeta);
        document.getElementById('para_quien').addEventListener('change', validarLineaViñeta);

        // Validar antes de enviar el formulario
        document.getElementById('form-crear-curso').addEventListener('submit', function(e) {
            const textareas = ['lo_que_aprenderas', 'requisitos', 'para_quien'];
            const maxCaracteres = 70;

            for (let id of textareas) {
                const textarea = document.getElementById(id);
                if (!textarea.value) continue; // Saltamos si está vacío

                const lines = textarea.value.split('\n');

                // Verificar cada línea
                let lineaDemasiado = lines.findIndex(line => line.length > maxCaracteres);

                if (lineaDemasiado >= 0) {
                    e.preventDefault();
                    alert(`El campo "${textarea.previousElementSibling.innerText.split(' ')[0]}" tiene una línea (${lineaDemasiado + 1}) que excede el límite de ${maxCaracteres} caracteres.`);
                    textarea.focus();

                    // Resaltar el área del problema
                    textarea.setSelectionRange(
                        textarea.value.split('\n').slice(0, lineaDemasiado).join('\n').length + (lineaDemasiado > 0 ? 1 : 0),
                        textarea.value.split('\n').slice(0, lineaDemasiado + 1).join('\n').length
                    );

                    return;
                }
            }
        });
    </script>
</body>

</html>