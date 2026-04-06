<?php $activePage = 'tipos_proceso';
// Vista de tipos de procesos para administrador.

if (!isset($_SESSION['logged']) || $_SESSION['user']['rol'] != 1) {
    header("Location: /project-cpr/public/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tipos de procesos - CPR</title>
    <?php include __DIR__ . '/../components/favicon.php'; ?>
    <link rel="stylesheet" href="/project-cpr/public/assets/css/globals/base.css">
    <link rel="stylesheet" href="/project-cpr/public/assets/css/globals/perfil.css">
</head>

<body class="private">
    <!-- Header del administrador -->
    <?php include __DIR__ . '/../components/header_administrador.php'; ?>

    <div class="main-content">
        <!-- Contenido del modulo de tipos de procesos -->
        <?php include __DIR__ . '/../components/tipos_proceso.php'; ?>
    </div>
</body>

</html>
