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

            <?php
            $filtro_actual = $_GET['filtro'] ?? 'todos';
            ?>

            <!-- SIDEBAR -->
            <aside class="sidebar">
                <button class="btn-agregar">Agregar caso</button>

                <a href="?filtro=urgentes" class="btn-sidebar urgente <?= $filtro_actual === 'urgentes' ? 'active' : '' ?>">
                    <span>Urgentes</span>
                    <span class="num"><?= count($casos_urgentes) ?></span>
                </a>

                <a href="?filtro=no_atendido" class="btn-sidebar <?= $filtro_actual === 'no_atendido' ? 'active' : '' ?>">
                    <span>No atendidos</span>
                    <span class="num"><?= count($casos_no_atendidos) ?></span>
                </a>

                <a href="?filtro=pendiente" class="btn-sidebar <?= $filtro_actual === 'pendiente' ? 'active' : '' ?>">
                    <span>Pendientes</span>
                    <span class="num"><?= count($casos_pendiente) ?></span>
                </a>

                <a href="?filtro=resueltos" class="btn-sidebar <?= $filtro_actual === 'resueltos' ? 'active' : '' ?>">
                    <span>Resueltos</span>
                    <span class="num"><?= count($casos_resueltos) ?></span>
                </a>

                <a href="?filtro=todos" class="btn-sidebar <?= $filtro_actual === 'todos' ? 'active' : '' ?>">
                    <span>Todos</span>
                    <span class="num"><?= count($casos_todos) ?></span>
                </a>

            </aside>

            <!-- CONTENIDO PRINCIPAL -->
            <section class="dashboard-content">
                <div class="table-container">
                    <table class="cases-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Asunto</th>
                                <th>Fecha creación</th>
                                <th>Caso</th>
                                <th>Proceso</th>
                                <th>Tiempos</th>
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
                                        <td><?= date("d-m-Y", strtotime($caso['fecha_creacion'] ?? '')) ?></td>
                                        <td><?= htmlspecialchars($caso['tipo_caso_nombre']) ?></td>
                                        <td><?= htmlspecialchars($caso['tipo_proceso_nombre']) ?></td>
                                        <!-- Columna de tiempos -->
                                        <td>
                                            <?php
                                            if ($caso['estado'] === 'Atendido') {
                                                echo "Resuelto";
                                            } else {
                                                $plazos = [
                                                    'Denuncia' => 30,
                                                    'Solicitud' => 15,
                                                    'Derecho de petición' => 15,
                                                    'Tutela' => 10
                                                ];

                                                $tipo = $caso['tipo_caso_nombre'] ?? '';
                                                $dias_max = $plazos[$tipo] ?? 15; // default 15 días
                                                $fecha_creacion = strtotime($caso['fecha_creacion']);
                                                $dias_transcurridos = (int)((time() - $fecha_creacion) / 86400); // segundos a días
                                                $dias_restantes = $dias_max - $dias_transcurridos;

                                                if ($dias_restantes < 0) {
                                                    echo "<span style='color:red;'>$dias_restantes días</span>";
                                                } else {
                                                    echo "$dias_restantes días";
                                                }
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6">No hay casos para este filtro.</td>
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