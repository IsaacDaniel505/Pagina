<?php
include "conexion.php";

$id = $_POST['id'];
$sql = "SELECT * FROM productos WHERE id = $id";
$res = $conexion->query($sql);
$p = $res->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto - Papelería DonChuy</title>
    <link rel="stylesheet" href="diseno.css">
</head>
<body>
<header class="barra">
    <h1 class="logo"><a href="index.php"><img src="logoDC.png" width="70px"></a>Papelería DonChuy</h1>

    <div class="botones-superior">

        <a href="carrito.php" class="icono">
            <img src="carrito.png" width="30px">
        </a>
        
        <a href="contraseña.php" class="icono admin">
            <img src="user.png" width="30px">
        </a>

    </div>
</header>
 <br><br><br><br><br><br>
<h2>Editar Producto</h2>

<form action="actualizar.php" method="POST">
    <input type="hidden" name="id" value="<?= $p['id'] ?>">

    <input type="text" name="nombre" value="<?= $p['nombre'] ?>" required>
    <input type="number" name="precio" step="0.01" value="<?= $p['precio'] ?>" required>
    <textarea name="descripcion"><?= $p['descripcion'] ?></textarea>
    <input type="text" name="imagen" value="<?= $p['imagen'] ?>" required>

    <button type="submit">Guardar Cambios</button>
</form>

</body>
</html>