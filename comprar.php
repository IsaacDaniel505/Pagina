<?php
session_start();
require 'conexion.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (empty($_SESSION['carrito'])) {
		echo "<p>No hay productos en el carrito.</p>";
		exit;
	}

	if (!isset($conexion) || !$conexion) {
		echo "<p>Error: no hay conexión a la base de datos.</p>";
		exit;
	}


	$total = 0.0;
	$items = [];

	foreach ($_SESSION['carrito'] as $producto_id => $cantidad) {
		$producto_id = (int)$producto_id;
		$cantidad = (int)$cantidad;

	$res = $conexion->query("SELECT id, nombre, precio FROM productos WHERE id = $producto_id");
	if (!$res) continue;
	$p = $res->fetch_assoc();

	if (!$p) continue;

	$precio = (float)$p['precio'];
	$subtotal = $precio * $cantidad;
		$total += $subtotal;

		$items[] = [
			'producto_id' => $producto_id,
			'nombre' => $p['nombre'],
			'precio' => $precio,
			'cantidad' => $cantidad,
			'subtotal' => $subtotal,
		];
	}

	if (count($items) === 0) {
		echo "<p>No se pudieron recuperar los detalles de los productos.</p>";
		exit;
	}

	$conexion->autocommit(FALSE);
	try {

	$stmt = $conexion->prepare("INSERT INTO ventas (fecha, total) VALUES (NOW(), ?)");
	if (!$stmt) throw new Exception('Preparar venta falló: ' . $conexion->error);
	$stmt->bind_param('d', $total);
	if (!$stmt->execute()) throw new Exception('Ejecutar venta falló: ' . $stmt->error);
	$venta_id = $conexion->insert_id;
	$stmt->close();

	$stmt_item = $conexion->prepare("INSERT INTO venta_items (venta_id, producto_id, nombre, precio_unitario, cantidad, subtotal) VALUES (?,?,?,?,?,?)");
	if (!$stmt_item) throw new Exception('Preparar item falló: ' . $conexion->error);

		foreach ($items as $it) {
			$vid = (int)$venta_id;
			$pid = (int)$it['producto_id'];

			$nombre = $it['nombre'];
			$precio_unitario = (float)$it['precio'];
			$cantidad = (int)$it['cantidad'];

			$subtotal = (float)$it['subtotal'];

			$stmt_item->bind_param('iisdid', $vid, $pid, $nombre, $precio_unitario, $cantidad, $subtotal);
			if (!$stmt_item->execute()) throw new Exception('Insert item falló: ' . $stmt_item->error);
		}
		$stmt_item->close();

	$conexion->commit();

	unset($_SESSION['carrito']);

	?>
	<!DOCTYPE html>
		<html lang="es">
		<head>
			<meta charset="UTF-8">
			<title>Ticket de Compra #<?= htmlspecialchars($venta_id) ?></title>
			<link rel="stylesheet" href="diseño.css">
		</head>
	<body>
	<header class="barra">
		<h1 class="logo"><a href="index.php"><img src="logoDC.png" width="70px"></a>Papelería DonChuy</h1>
		<div class="botones-superior">
			<a href=".php" class="icono">
				<img src="carrito.png" width="30px">
			</a>
			<a href="contraseña.php" class="icono admin">
				<img src="user.png" width="30px">
			</a>
			<a href="index.php"  class="icon back">
				 <img src="back.png" width="30px">
			</a>
		</div>
	</header>

     <br><br><br><br><br><br>

	<div class="ticket-container">
	<h1>Ticket de Compra</h1>
	<p><strong>Venta ID:</strong> <?= htmlspecialchars($venta_id) ?></p>
	<p><strong>Fecha:</strong> <?= date('Y-m-d H:i:s') ?></p>

	<table border="1" cellpadding="6" cellspacing="0">
			<thead>
				<tr>
					<th>Producto</th>
					<th>Precio unitario</th>
					<th>Cantidad</th>
					<th>Subtotal</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($items as $it): ?>
				<tr>
					<td><?= htmlspecialchars($it['nombre']) ?></td>
					<td>$<?= number_format($it['precio'], 2) ?></td>
					<td><?= (int)$it['cantidad'] ?></td>
					<td>$<?= number_format($it['subtotal'], 2) ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>

	<h2>Total: $<?= number_format($total, 2) ?></h2>
	<p>Gracias por su compra.</p>
	<p><a href="index.php">Volver al inicio</a></p>
	</div>
	</body>
	</html>
	<?php

		exit;

	} catch (Exception $e) {
		$conexion->rollback();
	$conexion->autocommit(TRUE);
	echo "<p>Error al procesar la compra: " . htmlspecialchars($e->getMessage()) . "</p>";
	exit;
	}

}
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Comprar</title>
</head>
<body>
	<p>Accede a este archivo sólo mediante el botón "Comprar todo" desde el carrito.</p>

</body>
</html>

