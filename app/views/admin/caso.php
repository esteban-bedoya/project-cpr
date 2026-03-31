<?php $activePage = 'caso';

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
    <title>Caso - CPR</title>
    <link rel="stylesheet" href="/project-cpr/public/assets/css/globals/base.css">
    <link rel="stylesheet" href="/project-cpr/public/assets/css/globals/caso.css">
</head>

<body class="private">

    <?php include __DIR__ . '/../components/header_administrador.php'; ?>

    <div class="main-content">

        <?php include __DIR__ . '/../components/caso.php'; ?>

    </div>

</body>

</html>