<?php
// Script temporal para debug de rutas - VERSIÓN FINAL
session_start();

// Simular la ruta que debería funcionar
$_GET['pagina'] = 'editarCurso/peliculas-y-cortometrajes';

require_once "controladores/general.controlador.php";
require_once "controladores/cursos.controlador.php";
require_once "modelos/conexion.php";
require_once "modelos/cursos.modelo.php";

echo "<h1>🔍 Debug de Rutas - Test Final</h1>";

echo "<h2>✅ Probando la corrección:</h2>";

try {
    $resultado = ControladorGeneral::ctrCargarPaginaConAcceso();
    echo "<p><strong>Resultado:</strong> " . $resultado . "</p>";

    if (file_exists($resultado)) {
        if (strpos($resultado, 'error404') !== false) {
            echo "<p style='color: red;'>❌ Aún redirecciona al error404</p>";
        } else {
            echo "<p style='color: green;'>✅ ¡ÉXITO! Se encontró el archivo: " . $resultado . "</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ El archivo NO existe: " . $resultado . "</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

echo "<h2>🧪 Test manual del método buscarArchivo:</h2>";

// Test directo del método de búsqueda
$reflection = new ReflectionClass('ControladorGeneral');
$method = $reflection->getMethod('buscarArchivo');
$method->setAccessible(true);

$resultado_busqueda = $method->invoke(null, "vistas/paginas", "superAdmin/gestionCursos/editarCurso.php");

if ($resultado_busqueda) {
    echo "<p style='color: green;'>✅ Método buscarArchivo encontró: " . $resultado_busqueda . "</p>";
} else {
    echo "<p style='color: red;'>❌ Método buscarArchivo NO encontró el archivo</p>";
}

echo "<h2>📁 Verificación final de archivos:</h2>";

$archivo_target = "vistas/paginas/superAdmin/gestionCursos/editarCurso.php";
if (file_exists($archivo_target)) {
    echo "<p style='color: green;'>✅ Archivo existe en: " . realpath($archivo_target) . "</p>";
} else {
    echo "<p style='color: red;'>❌ Archivo NO existe en: " . $archivo_target . "</p>";
}
