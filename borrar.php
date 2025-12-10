<?php
require_once 'conexion.php';

// Acepta tanto POST como GET (admin.php usa POST)
$id = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
} else {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
}

$deleted = false;
if ($id > 0) {
    // Usar statement preparado para mayor seguridad (mysqli)
    if ($stmt = $conexion->prepare('DELETE FROM productos WHERE id = ?')) {
        $stmt->bind_param('i', $id);
        $stmt->execute();
        // comprobar filas afectadas
        $deleted = ($stmt->affected_rows > 0);
        $stmt->close();
    } else {
        // Fallback a query simple si prepare falla
        $res = $conexion->query("DELETE FROM productos WHERE id={$id}");
        $deleted = ($conexion->affected_rows > 0);
    }
}

// Registrar información mínima en un log para diagnosticar en el servidor
$log = [];
$log[] = "[" . date('Y-m-d H:i:s') . "] borrar.php called";
$log[] = "id={$id}";
$log[] = "deleted=" . ($deleted ? '1' : '0');
if (isset($conexion->error) && $conexion->error) {
    $log[] = "db_error=" . $conexion->error;
}
// Intentar obtener filas afectadas también si prepare no devolvió affected_rows
if (isset($stmt) && isset($stmt->affected_rows)) {
    $log[] = "affected_rows=" . $stmt->affected_rows;
} elseif (isset($conexion->affected_rows)) {
    $log[] = "affected_rows=" . $conexion->affected_rows;
}

file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'borrar.log', implode(' | ', $log) . PHP_EOL, FILE_APPEND | LOCK_EX);

// Redirigir de vuelta al admin con un parámetro que indique el resultado
if ($deleted) {
    header('Location: admin.php?deleted=1');
} else {
    header('Location: admin.php?deleted=0');
}
exit;
