<?php $activePage = 'perfil';

session_start();

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
    <title>Perfil - CPR</title>
    <link rel="stylesheet" href="/project-cpr/public/assets/css/globals/base.css">
</head>

<body class="private">

    <?php include('../components/header_administrador.php'); ?>
    <!-- Todo tu contenido de busqueda.php -->
    <div class="main-content">

        <?php include('../components/perfil.php'); ?>

    </div>

</body>

</html>