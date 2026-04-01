<?php $activePage = 'caso'; ?>
<!-- Vista de detalle de caso para comisionado -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caso - CPR</title>
    <link rel="stylesheet" href="/project-cpr/public/assets/css/globals/base.css">
    <link rel="stylesheet" href="/project-cpr/public/assets/css/globals/caso.css">
</head>

<body class="private">

    <!-- Header del comisionado -->
    <?php include __DIR__ . '/../components/header_comisionado.php'; ?>

    <div class="main-content">

        <!-- Componente reutilizable del detalle de caso -->
        <?php include __DIR__ . '/../components/caso.php'; ?>

    </div>

</body>

</html>
