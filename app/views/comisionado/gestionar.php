<?php
require_once __DIR__ . '/../../models/Caso.php';

$activePage = 'gestionar';


// Obtener casos del comisionado logueado
$casos = Caso::getByComisionado($_SESSION['user']['id']);
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

    <?php include(__DIR__ . '/../components/header_comisionado.php'); ?>

    <div class="main-content">

        <div class="dashboard-container">

            <!-- SIDEBAR -->
            <aside class="sidebar">
                <button class="btn-agregar">Agregar caso</button>

                <button class="btn-sidebar">
                    <span>Asignados</span>
                    <span class="num"><?= count($casos) ?></span>
                </button>

                <button class="btn-sidebar">
                    <span>No atendido</span>
                    <span class="num"><?= count(array_filter($casos, fn($c) => $c['estado'] === 'No atendido')) ?></span>
                </button>

                <button class="btn-sidebar">
                    <span>Pendiente</span>
                    <span class="num"><?= count(array_filter($casos, fn($c) => $c['estado'] === 'Pendiente')) ?></span>
                </button>

                <button class="btn-sidebar">
                    <span>Últimos atendidos</span>
                    <span class="num"><?= count(array_filter($casos, fn($c) => $c['estado'] === 'Atendido')) ?></span>
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
                            <?php if (!empty($casos)): ?>
                                <?php foreach ($casos as $caso): ?>
                                    <tr>
                                        <td>
                                            <a href="/project-cpr/public/caso.php?id=<?= $caso['id'] ?>">
                                                    <?= htmlspecialchars($caso['numero_caso']) ?>
                                            </a>
                                        </td>
                                        <td><?= htmlspecialchars($caso['asunto']) ?></td>
                                        <td><?= date("d-m-Y", strtotime($caso['created_at'] ?? $caso['fecha'] ?? '')) ?></td>
                                        <td><?= htmlspecialchars($caso['tipo_caso_nombre']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4">No hay casos asignados.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </section>

        </div>

    </div>

</body>

</html>
