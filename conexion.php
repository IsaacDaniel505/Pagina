<?php
$conexion = new mysqli("localhost", "root", "", "donchuy");

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// 🔥 ESTO ES LO QUE TE FALTABA 🔥
$conexion->set_charset("utf8mb4");
mysqli_query($conexion, "SET NAMES 'utf8mb4'");
?>