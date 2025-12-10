<?php
session_start();
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['vaciar'])) {
        unset($_SESSION['carrito']);
    }
    
    elseif (isset($_POST['eliminar_id'])) {
        $id = (int)$_POST['eliminar_id'];
        if (isset($_SESSION['carrito'][$id])) {
            unset($_SESSION['carrito'][$id]);
        }
    }

    // Actualizar cantidad de un artículo
    elseif (isset($_POST['actualizar_cantidad'])) {
        $id = isset($_POST['producto_id']) ? (int)$_POST['producto_id'] : 0;
        $cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 0;

        if ($id > 0) {
            if ($cantidad <= 0) {
                unset($_SESSION['carrito'][$id]);
            } else {
                $_SESSION['carrito'][$id] = $cantidad;
            }
        }
    }

    elseif (isset($_POST['id'])) {
        $id = (int)$_POST['id'];

        if (!isset($_SESSION['carrito'][$id])) {
            $_SESSION['carrito'][$id] = 1;
        } else {
            $_SESSION['carrito'][$id]++;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>DonChuy - Carrito</title>

    <!-- Tu CSS principal(s) -->
    <link rel="stylesheet" href="estilos.css">
    <link rel="stylesheet" href="diseño.css">

    <!-- Estilos específicos para forzar tamaño uniforme de imágenes en el carrito -->
    <style>
        /* Tamaño fijo para miniaturas del carrito */
        .carrito-img {
            width: 180px !important;
            height: 140px !important;
            object-fit: cover !important; /* usa "contain" si prefieres no recortar */
            display: block;
            border-radius: 4px;
        }

        /* Alineación del ítem del carrito */
        .carrito-item {
            display: flex;
            gap: 16px;
            align-items: center;
            padding: 12px;
            border-bottom: 1px solid #eee;
            background: #fff;
            margin-bottom: 10px;
            border-radius: 6px;
        }

        .item-info { flex: 1; }
        .item-actions { display: flex; gap: 8px; align-items: center; }

        /* Si hay reglas en estilos.css que interfieren, el !important en .carrito-img debe superar eso.
           Si aún ves problemas, revisa que no haya inline styles que modifiquen width/height del <img>. */
    </style>
</head>
<body>

<header class="barra">
    <h1 class="logo"><a href="index.php"><img src="logoDC.png" width="70" alt="Logo"></a> Papelería DonChuy</h1>

    <div class="botones-superior">
        <a href=".php" class="icono">
            <img src="carrito.png" width="30" alt="Carrito">
        </a>
        
        <a href="contraseña.php" class="icono admin">
            <img src="user.png" width="30" alt="Usuario">
        </a>

        <a href="index.php"  class="icon back">
             <img src="back.png" width="30" alt="Volver">
        </a>
    </div>
</header>

<main style="padding: 120px 20px 20px 20px;">
    <h1>Carrito de Compras</h1>

    <div class="carrito-contenedor">
    <?php
    $total = 0;

    if (!empty($_SESSION['carrito'])):
        foreach ($_SESSION['carrito'] as $id => $cant):

            $consulta = $conexion->query("SELECT * FROM productos WHERE id = $id");
            $p = $consulta->fetch_assoc();

            $subtotal = $cant * $p['precio'];
            $total += $subtotal;
    ?>
        <div class="carrito-item">
            <!-- imagen con clase fija; también añadí atributos width/height por si alguna hoja de estilos las borra -->
            <img class="carrito-img" src="imagenes/<?= htmlspecialchars($p['imagen'], ENT_QUOTES) ?>" alt="<?= htmlspecialchars($p['nombre'], ENT_QUOTES) ?>" width="180" height="140">

            <div class="item-info">
                <h3><?= htmlspecialchars($p['nombre'], ENT_QUOTES) ?></h3>
                <p>Precio: $<?= number_format($p['precio'], 2) ?></p>
                <p>Subtotal: $<?= number_format($subtotal, 2) ?></p>
            </div>

            <div class="item-actions">
                <form action="" method="POST" style="display:inline-block;">
                    <input type="hidden" name="producto_id" value="<?= $id ?>">
                    <input type="number" name="cantidad" value="<?= $cant ?>" min="1" style="width:64px; padding:4px;">
                    <button type="submit" name="actualizar_cantidad" class="btn-actualizar">Actualizar</button>
                </form>

                <form action="" method="POST" style="display:inline;">
                    <input type="hidden" name="eliminar_id" value="<?= $id ?>">
                    <button type="submit" class="btn-eliminar-item">Eliminar</button>
                </form>
            </div>
        </div>

    <?php endforeach; else: ?>

        <p class="vacio">Tu carrito está vacío</p>
    <?php endif; ?>
    </div>

    <h2>Total: $<?= number_format($total, 2) ?></h2>

    <?php if (!empty($_SESSION['carrito'])): ?>
    <div class="eliminar-contenedor" style="margin-top:12px;">
        <form action="" method="POST">
            <button type="submit" name="vaciar" class="btn-eliminar">Vaciar carrito</button>
        </form>
    </div>

    <div class="comprar-contenedor" style="margin-top:8px;">
        <form action="comprar.php" method="POST">
            <button type="submit" class="btn-comprar">Comprar todo</button>
        </form>
    </div>
    <?php endif; ?>
</main>

</body>
</html>