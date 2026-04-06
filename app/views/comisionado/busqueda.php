<?php $activePage = 'busqueda';
// Vista de busqueda de casos para comisionado.
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Búsqueda - CPR</title>
    <?php include __DIR__ . '/../components/favicon.php'; ?>
    <link rel="stylesheet" href="/project-cpr/public/assets/css/globals/base.css">
    <link rel="stylesheet" href="/project-cpr/public/assets/css/globals/busqueda_caso.css">
</head>

<body class="private">

    <!-- Header del comisionado -->
    <?php include __DIR__ . '/../components/header_comisionado.php'; ?>

    <div class="main-content">
        <!-- Aqui se carga la vista de busqueda del rol -->
        <?php include __DIR__ . '/../components/busqueda_caso.php'; ?>


    </div>

</body>

</html>
