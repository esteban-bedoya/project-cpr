<!-- Vista de gestionar casos para comisionado -->
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

    <!-- Header del comisionado -->
    <?php include(__DIR__ . '/../components/header_comisionado.php'); ?>

    <div class="main-content">
        <div class="dashboard-container">

            <?php
            // Filtro activo tomado desde la URL.
            $filtro_actual = $_GET['filtro'] ?? 'todos';
            $form_gestionar = $_SESSION['form_gestionar'] ?? [];
            ?>

            <!-- SIDEBAR -->
            <aside class="sidebar">
                <button class="btn-agregar">Agregar caso</button>

                <a href="?filtro=proximos" class="btn-sidebar urgente <?= $filtro_actual === 'proximos' ? 'active' : '' ?>">
                    <span>Próximos a vencer</span>
                    <span class="num"><?= count($casos_proximos) ?></span>
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
                                <th>Tiempo</th>
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
                                                if (!empty($caso['fecha_cierre'])) {
                                                    $fecha_cierre = new DateTime($caso['fecha_cierre']);
                                                    $hoy = new DateTime();
                                                    $interval = $hoy->diff($fecha_cierre);
                                                    $dias_restantes = (int)$interval->format('%r%a');

                                                    if ($dias_restantes < 0) {
                                                        echo "<span style='color:red;'>$dias_restantes días</span>";
                                                    } else {
                                                        echo "$dias_restantes días";
                                                    }
                                                } else {
                                                    echo "Sin fecha";
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

    <!-- =========================================================== -->
    <!-- ================== MODAL AGREGAR CASO ==================== -->
    <!-- =========================================================== -->
    <div class="modal" id="modal-agregar">
        <div class="modal-content">
            <h3>Agregar caso</h3>
            <?php if (isset($_SESSION['error']) && ($_GET['error'] ?? '') === 'fechacierre'): ?>
                <p style="color:#b00020; font-size:14px; margin-bottom:10px;">
                    <?= $_SESSION['error']; ?>
                </p>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <form action="/project-cpr/public/gestionar.php?action=storeGestionar" method="POST">

                <label>Radicado SENA (opcional)</label>
                <input type="text" name="radicado_sena" maxlength="10" placeholder="Radicado SENA"
                    value="<?= htmlspecialchars($form_gestionar['radicado_sena'] ?? '') ?>">

                <label>Seleccione el tipo de caso</label>
                <select name="tipo_caso_id" id="add-tipo-caso" required>
                    <option value="">- Seleccione -</option>
                    <?php
                    $tiposCaso = Caso::getTiposCaso();
                    foreach ($tiposCaso as $tc) {
                        $selected = ((string)($form_gestionar['tipo_caso_id'] ?? '') === (string)$tc['id']) ? 'selected' : '';
                        echo "<option value='{$tc['id']}' {$selected}>{$tc['nombre']}</option>";
                    }
                    ?>
                </select>

                <label>Seleccione el tipo de proceso</label>
                <select name="tipo_proceso_id" id="add-tipo-proceso" required>
                    <option value="">- Seleccione -</option>
                    <?php
                    $tiposProceso = Caso::getTiposProcesoActivos();
                    foreach ($tiposProceso as $proceso) {
                        if ($proceso['estado'] == 1) {
                            $selected = ((string)($form_gestionar['tipo_proceso_id'] ?? '') === (string)$proceso['id']) ? 'selected' : '';
                            echo "<option value='{$proceso['id']}' {$selected}>{$proceso['nombre']}</option>";
                        }
                    }
                    ?>
                </select>

                <label>Asunto</label>
                <input type="text" name="asunto" required value="<?= htmlspecialchars($form_gestionar['asunto'] ?? '') ?>">

                <label>Detalles del caso</label>
                <textarea name="detalles" rows="4" required><?= htmlspecialchars($form_gestionar['detalles'] ?? '') ?></textarea>

                <label>Fecha de cierre (posterior a la fecha actual)</label>
                <input type="date" name="fecha_cierre" required
                    value="<?= htmlspecialchars($form_gestionar['fecha_cierre'] ?? '') ?>">

                <div class="modal-buttons">
                    <button type="submit" class="btn-guardar">Guardar</button>
                    <button type="button" class="btn-cerrar" onclick="cerrarModalAgregar()">Cerrar</button>
                </div>

            </form>
        </div>
    </div>

    <script>
        const btnAgregar = document.querySelector('.btn-agregar');
        const modalAgregar = document.getElementById('modal-agregar');
        const selectProceso = document.getElementById('add-tipo-proceso');

        btnAgregar.addEventListener('click', () => {
            modalAgregar.style.display = 'flex';
        });

        function cerrarModalAgregar() {
            modalAgregar.style.display = 'none';
        }

        // Cerrar modal si se hace click fuera del contenido
        window.addEventListener('click', e => {
            if (e.target === modalAgregar) cerrarModalAgregar();
        });
    </script>

    <?php if (($_GET['error'] ?? '') === 'fechacierre'): ?>
    <script>
        // Mantener el modal abierto si hubo error de fecha
        modalAgregar.style.display = 'flex';
    </script>
    <?php unset($_SESSION['form_gestionar']); ?>
    <?php endif; ?>

</body>

</html>
