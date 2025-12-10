<?php include "conexion.php";
session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>DonChuy</title>
    <link rel="stylesheet" href="diseño.css">
</head>
<body>

<header class="barra">
    <h1 class="logo"><a href="index.php"><img src="logoDC.png" width="70px"></a>Papelería DonChuy</h1>
    <form action="buscar.php" method="GET" class="search-form">
        <input type="text" name="q" placeholder="Buscar productos..." style="width:360px; padding:8px; font-size:16px;">
        <button type="submit" style="padding:8px 12px; font-size:16px;">Buscar</button>
    </form>

    <div class="botones-superior">

        <a href="carrito.php" class="icono">
            <img src="carrito.png" width="30px">
        </a>
        
        <a href="contraseña.php" class="icono admin">
            <img src="user.png" width="30px">
        </a>

    </div>
</header>

<div class="contenedor-productos">
    <?php
        $sql = "SELECT * FROM productos";
        $resultado = $conexion->query($sql);

        while($fila = $resultado->fetch_assoc()):
    ?>
        <div class="producto">
            <img src="imagenes/<?php echo $fila['imagen']; ?>">
            <h2><?php echo $fila['nombre']; ?></h2>
            <p class="precio">$<?php echo $fila['precio']; ?></p>
            <form action="agregar.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $fila['id'];?>">
                <input type="hidden" name="return_to" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                    <button type="submit">Agregar al carrito</button>
            </form>
        </div>
    <?php endwhile; ?>
</div>

</body>
</html>







