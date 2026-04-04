<?php
// Vista publica para solicitar el enlace de recuperacion.

session_start();

$activePage = 'login';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar contraseña - CPR</title>
    <?php include __DIR__ . '/../components/favicon.php'; ?>
    <link rel="stylesheet" href="assets/css/globals/base.css" />
    <link rel="stylesheet" href="assets/css/globals/login.css" />
</head>

<body class="public">

    <?php include('../app/views/components/header_public.php'); ?>

    <div class="login-container">
        <div class="login-card">
            <h2>RECUPERAR CONTRASEÑA</h2>
            <p class="helper-text">Ingrese su correo y el sistema generará el enlace para cambiar la contraseña.</p>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert error">
                    <?= $_SESSION['error']; ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert success">
                    <?= $_SESSION['success']; ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['reset_demo_link'])): ?>
                <div class="alert info demo-link">
                    Link generado para prueba local:
                    <br>
                    <a href="<?= htmlspecialchars($_SESSION['reset_demo_link']); ?>">
                        <?= htmlspecialchars($_SESSION['reset_demo_link']); ?>
                    </a>
                </div>
                <?php unset($_SESSION['reset_demo_link']); ?>
            <?php endif; ?>

            <form action="/project-cpr/public/recuperar_password_procesar.php" method="POST">
                <label for="correo">Correo:</label>
                <input type="email" id="correo" name="correo" placeholder="Ingrese su correo" required>

                <button type="submit" class="btn-login">ENVIAR LINK</button>
            </form>

            <a href="/project-cpr/public/login.php" class="forgot">Volver al login</a>
        </div>
    </div>

    <?php include('../app/views/components/footer.php'); ?>

</body>

</html>
