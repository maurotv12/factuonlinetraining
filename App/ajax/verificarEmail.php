<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/cursosApp/App/modelos/usuarios.modelo.php";

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['disponible' => false, 'mensaje' => 'Método no permitido']);
        exit;
    }

    if (!isset($_POST['email']) || empty(trim($_POST['email']))) {
        echo json_encode(['disponible' => false, 'mensaje' => 'Email no proporcionado']);
        exit;
    }

    $email = trim($_POST['email']);

    // Validar formato del email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['disponible' => false, 'mensaje' => 'Email inválido']);
        exit;
    }

    // Verificar si el email ya existe en la base de datos
    $tabla = "persona";
    $item = "email";
    $valor = $email;

    $respuesta = ModeloUsuarios::mdlMostrarUsuarios($tabla, $item, $valor);

    if ($respuesta && isset($respuesta["email"])) {
        // Email ya existe
        echo json_encode([
            'disponible' => false,
            'mensaje' => 'Este email ya está registrado'
        ]);
    } else {
        // Email disponible
        echo json_encode([
            'disponible' => true,
            'mensaje' => 'Email disponible'
        ]);
    }
} catch (Exception $e) {
    error_log("Error en verificarEmail.php: " . $e->getMessage());
    echo json_encode([
        'disponible' => false,
        'mensaje' => 'Error interno del servidor'
    ]);
}
