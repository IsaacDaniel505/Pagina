<?php
require "conexion.php";
session_start();


$busqueda = isset($_GET['q']) ? trim($_GET['q']) : '';
$busqueda_esc = $conexion->real_escape_string($busqueda);


if ($busqueda_esc === '') {
    $sql = "SELECT * FROM productos";
} else {
    $tokens = preg_split('/\s+/', $busqueda_esc);
    $whereParts = [];
    $scoreParts = [];

    foreach ($tokens as $t) {
        if ($t === '') continue;
        $whereParts[] = "Nombre LIKE '%$t%'";
        $scoreParts[] = "(Nombre LIKE '%$t%')";
    }

    $where = implode(' OR ', $whereParts);
    $score = implode(' + ', $scoreParts);

    $sql = "SELECT *, ($score) AS relevance FROM productos WHERE $where ORDER BY relevance DESC";
}

$resultado = $conexion->query($sql);
?>
<br><br><br><br><br><br>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>DonChuy - Buscar</title>
    <link rel="stylesheet" href="diseño.css">
    <link rel="stylesheet" href="estilos.css">
    
</head>
<body>

<header class="barra">
    <h1 class="logo"><a href="index.php"><img src="logoDC.png" width="70px"></a>Papelería DonChuy</h1>
    <form action="buscar.php" method="GET" class="search-form">
        <input type="text" name="q" placeholder="Buscar productos..." value="<?= htmlspecialchars($busqueda) ?>">
        <button type="submit">Buscar</button>
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

<main>

<h1>Resultados para: <?= htmlspecialchars($busqueda) ?></h1>

<div class="catalogo">
<?php
if ($resultado && $resultado->num_rows > 0) {
    while ($p = $resultado->fetch_assoc()) { ?>

        <div class="producto">
            <img src="imagenes/<?= htmlspecialchars($p['imagen']) ?>" alt="<?= htmlspecialchars($p['nombre']) ?>">
            <h3><?= htmlspecialchars($p['nombre']) ?></h3>
            <p class="precio">$<?= htmlspecialchars($p['precio']) ?></p>

            <form action="agregar.php" method="POST">
                <input type="hidden" name="producto_id" value="<?= $p['id'] ?>">
                <input type="hidden" name="return_to" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                <button type="submit">Agregar al carrito</button>
            </form>

        </div>

    <?php }
} else {
    echo "<p class='no-result'>No se encontraron productos.</p>";
}
?>
</div>

</main>

</body>
</html>