<?php
session_start();

// Si se envía desde el carrito: campo producto_id y cantidad
if (isset($_POST['producto_id']) || isset($_POST['cantidad'])) {
    $id = $_POST['producto_id'] ?? null;
    $cant = intval($_POST['cantidad'] ?? 0);

    if ($id !== null) {
        if ($cant > 0) {
            $_SESSION['carrito'][$id] = $cant;
        } else {
            // si la cantidad es 0 o negativa, eliminamos del carrito
            unset($_SESSION['carrito'][$id]);
        }
    }

    // redirigir de vuelta a la página previa si es posible
    $redirect = $_SERVER['HTTP_REFERER'] ?? 'admin.php';
    header("Location: " . $redirect);
    exit;

}

// Si se envía desde el formulario de edición de producto: campo id
if (isset($_POST['id'])) {
    include "conexion.php";

    $id = (int)$_POST['id'];
    $nombre = $_POST['nombre'] ?? '';
    $precio = floatval($_POST['precio'] ?? 0);
    $descripcion = $_POST['descripcion'] ?? '';
    $imagen = $_POST['imagen'] ?? '';

    $stmt = $conexion->prepare("UPDATE productos SET nombre = ?, precio = ?, descripcion = ?, imagen = ? WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("sdssi", $nombre, $precio, $descripcion, $imagen, $id);
        if ($stmt->execute()) {
            header("Location: admin.php?updated=1");
            exit;
        }
    }

    header("Location: admin.php?updated=0");
    exit;

}

// Si no se envía información conocida, volver al admin
header("Location: admin.php");
exit;