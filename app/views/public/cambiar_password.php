<?php
// Vista publica para cambiar la contraseña desde un enlace.

session_start();

require_once __DIR__ . '/../../../app/models/User.php';

$activePage = 'login';
$token = trim($_GET['token'] ?? '');
$user = $token !== '' ? User::findByRememberToken($token) : null;

if (!$user) {
    $_SESSION['error'] = 'El enlace no es valido o ya fue utilizado.';
    header('Location: /project-cpr/public/login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar contraseña - CPR</title>
    <?php include __DIR__ . '/../components/favicon.php'; ?>
    <link rel="stylesheet" href="assets/css/globals/base.css" />
    <link rel="stylesheet" href="assets/css/globals/login.css" />
</head>

<body class="public">

    <?php include('../app/views/components/header_public.php'); ?>

    <div class="login-container">
        <div class="login-card">
            <h2>CAMBIAR CONTRASEÑA</h2>
            <p class="helper-text">Hola <?= htmlspecialchars($user['username']); ?>, ingrese su nueva contraseña.</p>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert error">
                    <?= $_SESSION['error']; ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <form action="/project-cpr/public/cambiar_password_procesar.php" method="POST">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token); ?>">

                <label for="password">Nueva contraseña:</label>
                <input type="password" id="password" name="password" placeholder="Ingrese nueva contraseña" required>

                <label for="password_confirm">Confirmar contraseña:</label>
                <input type="password" id="password_confirm" name="password_confirm" placeholder="Ingrese confirmación" required>

                <button type="submit" class="btn-login">CAMBIAR CONTRASEÑA</button>
            </form>

            <a href="/project-cpr/public/login.php" class="forgot">Volver al login</a>
        </div>
    </div>

    <?php include('../app/views/components/footer.php'); ?>

</body>

</html>
