<?php $activePage = 'perfil';?>
<!-- Vista de perfil para administrador -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - CPR</title>
    <?php include __DIR__ . '/../components/favicon.php'; ?>
    <link rel="stylesheet" href="/project-cpr/public/assets/css/globals/base.css">
</head>

<body class="private">

    <!-- Header del administrador -->
    <?php include __DIR__ . '/../components/header_administrador.php'; ?>
    <!-- Contenido de perfil -->
    <div class="main-content">

        <!-- Formulario compartido entre roles -->
        <?php include __DIR__ . '/../components/perfil.php'; ?>

    </div>

</body>

</html>
