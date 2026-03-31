<?php $activePage = 'gestionar'; 

session_start();

if (!isset($_SESSION['logged']) || $_SESSION['user']['rol'] != 2) {
    header("Location: /project-cpr/public/login.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar - CPR</title>
    <link rel="stylesheet" href="/project-cpr/public/assets/css/globals/base.css">
    <link rel="stylesheet" href="/project-cpr/public/assets/css/comisionado/gestionar.css">
</head>

<body class="private">

    <?php include('../components/header_comisionado.php'); ?>

    <div class="main-content">
        <!-- Todo tu contenido de gestionar.php -->


        <div class="dashboard-container">

            <!-- Sidebar -->
            <aside class="sidebar">

                <button class="btn-agregar">Agregar caso</button>

                <div class="sidebar-section">
                    <span>Asignados</span>
                    <span class="num">20</span>
                </div>

                <div class="sidebar-section">
                    <span>No atendido</span>
                    <span class="num">3</span>
                </div>

                <div class="sidebar-section">
                    <span>Pendiente</span>
                    <span class="num">3</span>
                </div>

                <div class="sidebar-section">
                    <span>Últimos atendidos</span>
                    <span class="num">100</span>
                </div>

            </aside>

            <!-- Contenido principal -->
            <section class="dashboard-content">
                <!-- aquí irá la tabla -->
            </section>

        </div>

    </div>

</body>

</html>