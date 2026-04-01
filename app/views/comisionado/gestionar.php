<?php 
$activePage = 'gestionar'; 
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

        <div class="dashboard-container">

            <!-- SIDEBAR -->
            <aside class="sidebar">

                <button class="btn-agregar">Agregar caso</button>

                <button class="btn-sidebar">
                    <span>Asignados</span>
                    <span class="num">20</span>
                </button>

                <button class="btn-sidebar">
                    <span>No atendido</span>
                    <span class="num">3</span>
                </button>

                <button class="btn-sidebar">
                    <span>Pendiente</span>
                    <span class="num">3</span>
                </button>

                <button class="btn-sidebar">
                    <span>Últimos atendidos</span>
                    <span class="num">100</span>
                </button>

            </aside>

            <!-- CONTENIDO PRINCIPAL -->
            <section class="dashboard-content">

                <div class="table-container">

                    <table class="cases-table">

                        <thead>
                            <tr>
                                <th>#Caso</th>
                                <th>Asunto</th>
                                <th>Fecha</th>
                                <th>Tipo</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>7399</td>
                                <td>Presupuesto faltante</td>
                                <td>13-11-2025</td>
                                <td>Denuncia</td>
                            </tr>

                            <tr>
                                <td>7390</td>
                                <td>Acceso al historial académico</td>
                                <td>12-11-2025</td>
                                <td>Solicitud</td>
                            </tr>

                            <tr>
                                <td>6987</td>
                                <td>Corrección formal de datos</td>
                                <td>10-11-2025</td>
                                <td>Derecho de petición</td>
                            </tr>

                            <tr>
                                <td>5638</td>
                                <td>Confidencialidad</td>
                                <td>20-10-2025</td>
                                <td>Tutela</td>
                            </tr>

                            <tr>
                                <td>4565</td>
                                <td>Derecho de acceso a la información</td>
                                <td>15-10-2025</td>
                                <td>Tutela</td>
                            </tr>

                            <tr>
                                <td>3267</td>
                                <td>No procesado</td>
                                <td>15-09-2025</td>
                                <td>Denuncia</td>
                            </tr>

                            <tr>
                                <td>2789</td>
                                <td>Poco presupuesto</td>
                                <td>11-09-2025</td>
                                <td>Denuncia</td>
                            </tr>

                            <tr>
                                <td>2452</td>
                                <td>Certificado de curso finalizado</td>
                                <td>02-09-2025</td>
                                <td>Solicitud</td>
                            </tr>

                            <tr>
                                <td>1245</td>
                                <td>Gastos inflados</td>
                                <td>13-08-2025</td>
                                <td>Denuncia</td>
                            </tr>
                        </tbody>

                    </table>
                </div>

            </section>

        </div>

    </div>

</body>

</html>
