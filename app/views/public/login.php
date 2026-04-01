<?php

session_start();

$activePage = 'login';

ini_set('display_errors', 1);
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - CPR</title>
    <link rel="stylesheet" href="assets/css/globals/base.css" />
    <link rel="stylesheet" href="assets/css/globals/login.css" />
</head>

<body class="public">

    <?php include('../app/views/components/header_public.php'); ?>

    <div class="login-container">
        <div class="login-card">
            <h2>INICIAR SESIÓN</h2>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert error">
                    <?= $_SESSION['error']; ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>



            <form action="/project-cpr/public/login_procesar.php" method="POST">


                <p>FORM ACTION REAL: <?php echo htmlentities($_SERVER['REQUEST_URI']); ?></p>

                <label for="correo">Correo:</label>
                <input type="email" id="correo" name="correo" placeholder="Ingrese su correo" required>

                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" placeholder="Ingrese su contraseña" required>

                <button type="submit" class="btn-login">INGRESAR</button>

            </form>

            <a href="#" class="forgot">¿Olvidó su contraseña?</a>
        </div>
    </div>

    <?php include('../app/views/components/footer.php'); ?>

</body>

</html>