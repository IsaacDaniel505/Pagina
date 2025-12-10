<?php
$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $user = $_POST['usuario'];
    $pass = $_POST['contra'];

    // Credenciales correctas
    $usuarioCorrecto = "eri";
    $contraCorrecta = "josu";

    // Validación
    if ($user === $usuarioCorrecto && $pass === $contraCorrecta) {
        header("Location: admin.php");
        exit();
    } else {
        $mensaje = "Datos incorrectos, inténtalo de nuevo.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>DonChuy</title>
    <link rel="stylesheet" href="diseño.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="barra">
    <h1 class="logo">
        <a href="index.php"><img src="logoDC.png" width="70px"></a>
        Papelería DonChuy
    </h1>

    <input type="text" placeholder="Buscar productos...">

    <div class="botones-superior">
        <a href="carrito.php" class="icono"><img src="carrito.png" width="30px"></a>
        <a href="contraseña.php" class="icono admin"><img src="user.png" width="30px"></a>
    </div>
</header>

<a href="index.php">
    <button><img src="volver.png" width="40px" height="40px"></button>
</a>

<div class="main-container centered-flex">
    <div class="form-container">
        <div class="icon fa fa-user"></div>

        <form method="POST" action="" class="centered-flex">

            <div class="title">INICIAR SESIÓN</div>

            <div class="field">
                <input type="text" name="usuario" placeholder="usuario" id="usuario" style="color:black;">
            </div>

            <div class="field">
                <input type="password" name="contra" placeholder="contraseña" id="contra" style="color:black;">
            </div>

            <div class="btn-container">
                <input type="submit" value="Iniciar Sesión">
            </div>

        </form>

        <!-- mensaje de error -->
        <?php if ($mensaje != ""): ?>
            <p style="color:red;"><?php echo $mensaje; ?></p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>