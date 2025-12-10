<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['Nombre']) || isset($_POST['nombre']))) {
    include __DIR__ . DIRECTORY_SEPARATOR . "conexion.php";

    $nombre = trim($_POST['Nombre'] ?? $_POST['nombre'] ?? '');
    $precio = isset($_POST['precio']) ? floatval($_POST['precio']) : 0.0;
    $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';

    $imagen = '';
    if (!empty($_FILES['imagen']['tmp_name'])) {
        $tmp = $_FILES['imagen']['tmp_name'];
        $originalName = basename($_FILES['imagen']['name']);
        $targetDir = __DIR__ . DIRECTORY_SEPARATOR . 'imagenes';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        $ext = pathinfo($originalName, PATHINFO_EXTENSION);
        $safeName = preg_replace('/[^A-Za-z0-9_\\-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
        $imagen = $safeName . '_' . time() . ($ext ? '.' . $ext : '');
        move_uploaded_file($tmp, $targetDir . DIRECTORY_SEPARATOR . $imagen);
    }

    $insert_id = 0;
    $sql = "INSERT INTO productos (nombre, precio, descripcion, imagen) VALUES (?, ?, ?, ?)";
    if ($stmt = $conexion->prepare($sql)) {
        $stmt->bind_param('sdss', $nombre, $precio, $descripcion, $imagen);
        $stmt->execute();
        $insert_id = $conexion->insert_id;
        $stmt->close();
        file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'agregar.log', date('Y-m-d H:i:s') . " | inserted_id={$insert_id}" . PHP_EOL, FILE_APPEND | LOCK_EX);
    } else {
        $nombreEsc = $conexion->real_escape_string($nombre);
        $descripcionEsc = $conexion->real_escape_string($descripcion);
        $imagenEsc = $conexion->real_escape_string($imagen);
        $precioEsc = $conexion->real_escape_string($precio);
        $conexion->query("INSERT INTO productos (nombre, precio, descripcion, imagen) VALUES ('{$nombreEsc}', '{$precioEsc}', '{$descripcionEsc}', '{$imagenEsc}')");
        $insert_id = $conexion->insert_id;
        file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'agregar.log', date('Y-m-d H:i:s') . " | fallback_inserted_id={$insert_id}" . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    header('Location: admin.php?added=' . ($insert_id ?: 0));
    exit;
}

$id = 0;
if (isset($_POST['producto_id'])) {
    $id = (int)$_POST['producto_id'];
} elseif (isset($_POST['id'])) {
    $id = (int)$_POST['id'];
}

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

if (!isset($_SESSION['carrito'][$id])) {
    $_SESSION['carrito'][$id] = 1;
} else {
    $_SESSION['carrito'][$id]++;
}

$return_to = '';
if (!empty($_POST['return_to'])) {
    $return_to = $_POST['return_to'];
} elseif (!empty($_SERVER['HTTP_REFERER'])) {
    $return_to = $_SERVER['HTTP_REFERER'];
}

if ($return_to !== '') {
    $allowed = false;
    $url = parse_url($return_to);
    if (!isset($url['host'])) {
        $allowed = true;
    } else {
        if (isset($_SERVER['HTTP_HOST']) && $url['host'] === $_SERVER['HTTP_HOST']) {
            $allowed = true;
        }
    }

    if ($allowed) {
        header('Location: ' . $return_to);
        exit;
    }
}

header('Location: index.php');
exit;
