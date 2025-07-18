<?php
// session_start();
require_once "modelos/conexion.php";
require_once "modelos/cursos.modelo.php";
require_once "controladores/cursos.controlador.php";
require_once "controladores/general.controlador.php";

// Validar que venga el ID del curso
if (!isset($_GET['id'])) {
    echo "<script>alert('Curso no encontrado'); window.location = 'cursos';</script>";
    exit;
}

if (!isset($_GET['id'])) {
    echo "<script>alert('Curso no encontrado'); window.location = 'index.php?pagina=superAdmin/gestionCursos/listadoCursos';</script>";
    exit;
}
$idCurso = $_GET['id'];
$curso = ControladorCursos::ctrMostrarCursos("id", $idCurso);

// Verificar que exista
if (!$curso) {
    echo "<script>alert('Curso no encontrado'); window.location = 'index.php?pagina=superAdmin/gestionCursos/listadoCursos';</script>";
    exit;
}

// Conexión para datos adicionales
$conn = Conexion::conectar();
$baseUrl = ControladorGeneral::ctrRuta();
// Obtener categoría
$stmtCategoria = $conn->prepare("SELECT nombre FROM categoria WHERE id = ?");
$stmtCategoria->execute([$curso["id_categoria"]]);
$categoria = $stmtCategoria->fetchColumn();

// Obtener profesor
$stmtProfesor = $conn->prepare("SELECT nombre, profesion, email, foto FROM persona WHERE id = ?");
$stmtProfesor->execute([$curso["id_persona"]]);
$profesor = $stmtProfesor->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Detalle del Curso</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .banner {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
        }

        .profesor-img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #ccc;
        }
    </style>
</head>

<body>

    <div class="container my-5">
        <a href="cursos" class="btn btn-secondary mb-4">&larr; Volver a cursos</a>

        <div class="row">
            <!-- Imagen del curso -->
            <div class="col-md-6">
                <?php if ($curso["banner"]) : ?>
                    <img src="<?= $baseUrl . $curso["banner"] ?>" alt="Banner" class="banner mb-3">
                <?php endif; ?>

                <?php if ($curso["promo_video"]) : ?>
                    <div class="ratio ratio-16x9">
                        <video controls>
                            <source src="<?= $curso["promo_video"] ?>" type="video/mp4">
                            Tu navegador no soporta videos.
                        </video>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Información del curso -->
            <div class="col-md-6">
                <h2><?= $curso["nombre"] ?></h2>
                <p><strong>Categoría:</strong> <?= $categoria ?: 'Sin categoría' ?></p>
                <p><strong>Precio:</strong> $<?= number_format($curso["valor"], 0, ',', '.') ?></p>
                <p><strong>Estado:</strong> <span class="badge bg-<?= $curso["estado"] == 'activo' ? 'success' : 'secondary' ?>"><?= $curso["estado"] ?></span></p>
                <p><strong>Descripción:</strong><br><?= nl2br($curso["descripcion"]) ?></p>
                <p><strong>Registrado el:</strong> <?= date("Y-m-d H:i", strtotime($curso["fecha_registro"])) ?></p>
            </div>
        </div>

        <hr class="my-5">

        <!-- Información del profesor -->
        <div class="row">
            <div class="col-md-2 text-center">
                <img src="<?= $profesor["foto"] ?>" class="profesor-img" alt="Foto del profesor">
            </div>
            <div class="col-md-10">
                <h4>Profesor: <?= $profesor["nombre"] ?></h4>
                <p><strong>Profesión:</strong> <?= $profesor["profesion"] ?: 'No especificada' ?></p>
                <p><strong>Correo:</strong> <?= $profesor["email"] ?></p>
            </div>
        </div>
    </div>

</body>

</html>