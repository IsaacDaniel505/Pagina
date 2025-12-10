<?php
include "conexion.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administración Papelería</title>
    <link rel="stylesheet" href="diseno.css">
    
</head>
<body>
    
    <?php if (isset($_GET['deleted'])): ?>
        <?php if ($_GET['deleted'] == '1'): ?>
            <div class="message message-success">Producto eliminado correctamente.</div>
        <?php else: ?>
            <div class="message message-error">No se pudo eliminar el producto. Verifica el id o la conexión.</div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (isset($_GET['added'])): ?>
        <div style="padding:10px; margin:10px 0; border-radius:4px;">
            <?php if ((int)$_GET['added'] > 0): ?>
                <div style="background:#e6ffed; border:1px solid #b6f0c6; padding:8px;">Producto agregado correctamente. ID: <?= (int)$_GET['added'] ?></div>
            <?php else: ?>
                <div style="background:#fff2f2; border:1px solid #f0b6b6; padding:8px;">No se pudo agregar el producto correctamente (id 0).</div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['updated'])): ?>
        <?php if ($_GET['updated'] == '1'): ?>
            <div class="message message-success">Producto actualizado correctamente.</div>
        <?php else: ?>
            <div class="message message-error">No se pudo actualizar el producto. Verifica los datos.</div>
        <?php endif; ?>
    <?php endif; ?>

    <br><br><br><br><br><br>

    <header class="barra">
    <h1 class="logo"><a href="index.php"><img src="logoDC.png" width="70px"></a>Papelería DonChuy</h1>

    <div class="botones-superior">

        <a href="carrito.php" class="icono">
            <img src="carrito.png" width="30px">
        </a>
        
        <a href="" class="icono admin">
            <img src="user.png" width="30px">
        </a>

    </div>
</header>

<div class="admin-container">
    <br><br><br><br><br><br>
    <h1>Administración de Productos</h1>
    <br>

    <h2>Agregar Producto</h2>

    <form action="agregar.php" method="POST" enctype="multipart/form-data">
        <div class="admin-form-row">
            <input type="text" name="Nombre" placeholder="Nombre del producto" required>
            <input type="number" name="precio" step="0.01" placeholder="Precio" required>
            <input type="file" name="imagen" accept="image/*" required>
        </div>
        <div style="margin-top:8px;">
            <textarea name="descripcion" placeholder="Descripción" style="width:100%; min-height:64px;"></textarea>
        </div>
        <div style="margin-top:8px;">
            <button type="submit">Agregar</button>
        </div>
    </form>

    <h2>Productos Registrados</h2>

    <table class="tabla">
    <colgroup>
        <col class="id">
        <col class="nombre">
        <col class="precio">
        <col class="imagen">
        <col class="acciones">
    </colgroup>
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Precio</th>
        <th>Imagen</th>
        <th>Acciones</th>
    </tr>

    <?php
    $sql = "SELECT * FROM productos";
    $res = $conexion->query($sql);

    while ($p = $res->fetch_assoc()):
    ?>
    <tr>
        <td><?= $p['id'] ?></td>
        <td><?= $p['nombre'] ?></td>
        <td>$<?= number_format($p['precio'], 2) ?></td>
        <td><?= $p['imagen'] ?></td>

        <td>
            <form action="editar.php" method="POST" class="actions-form" style="display:inline-block;">
                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                <button type="submit">Actualizar</button>
            </form>

            <form action="borrar.php" method="POST" class="actions-form" style="display:inline-block;">
                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                <button type="submit">Eliminar</button>
            </form>
        </td>
    </tr>
    <?php endwhile; ?>

</table>

</body>
</html>