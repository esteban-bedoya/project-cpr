<?php $activePage = 'reportes';
// Vista de reportes para administrador.

session_start();

// Seguridad: solo admin.
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
    <title>Reportes - CPR</title>
    <link rel="stylesheet" href="/project-cpr/public/assets/css/globals/base.css">
    <link rel="stylesheet" href="/project-cpr/public/assets/css/comisionado/________">
</head>

<body class="private">

    <!-- Header del administrador -->
    <?php include __DIR__ . '/../components/header_administrador.php'; ?>

    <div class="main-content">
        <!-- Contenido del modulo de reportes -->
        <?php include __DIR__ . '/../components/reportes.php'; ?>



    </div>

</body>

</html>
