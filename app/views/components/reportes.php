<?php
$esAdmin = (int)($_SESSION['user']['rol'] ?? 0) === 1;
$filtroComisionadoTodos = ($filtro_comisionado ?? 'todos') === 'todos';
$resumenTexto = $filtroComisionadoTodos ? 'Todos los comisionados' : ($comisionadoSeleccionado['username'] ?? 'Comisionado seleccionado');
?>

<section class="reports-page">
    <div class="reports-toolbar no-print">
        <div>
            <h1>Seguimiento de casos por comisionado</h1>
            <p class="reports-lead">
                Consulta el comportamiento de los casos dentro del rango de fechas seleccionado, con apoyo visual y resumen listo para impresión o descargar en PDF.
            </p>
        </div>

        <div class="reports-actions">
            <button type="button" class="btn-print" onclick="window.print()">Imprimir / Guardar PDF</button>
        </div>
    </div>

    <form class="reports-filters no-print" id="reports-filters-form" method="GET" action="/project-cpr/public/reportes.php">
        <div class="report-field">
            <label for="fecha_inicio">Desde</label>
            <input id="fecha_inicio" type="date" name="fecha_inicio" max="<?= htmlspecialchars($fechaMaximaFiltro ?? '') ?>" value="<?= htmlspecialchars($fecha_inicio ?? '') ?>">
        </div>

        <div class="report-field">
            <label for="fecha_fin">Hasta</label>
            <input id="fecha_fin" type="date" name="fecha_fin" max="<?= htmlspecialchars($fechaMaximaFiltro ?? '') ?>" value="<?= htmlspecialchars($fecha_fin ?? '') ?>">
        </div>

        <?php if ($esAdmin): ?>
            <div class="report-field report-field-toggle">
                <label class="report-checkbox">
                    <input type="checkbox" name="mostrar_inactivos" value="1" <?= !empty($mostrar_inactivos) ? 'checked' : '' ?>>
                    <span>Mostrar comisionados inactivos</span>
                </label>
            </div>

            <div class="report-field">
                <label for="comisionado">Comisionado</label>
                <select id="comisionado" name="comisionado">
                    <option value="todos" <?= $filtroComisionadoTodos ? 'selected' : '' ?>>Todos</option>
                    <?php foreach (($comisionadosVisibles ?? []) as $comisionado): ?>
                        <option value="<?= (int)$comisionado['id'] ?>" <?= (string)$filtro_comisionado === (string)$comisionado['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($comisionado['username']) ?><?= (int)$comisionado['estado'] === 1 ? '' : ' (Inactivo)' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php else: ?>
            <input type="hidden" name="comisionado" value="<?= (int)($_SESSION['user']['id'] ?? 0) ?>">
            <div class="report-field">
                <label>Comisionado</label>
                <input type="text" value="<?= htmlspecialchars($_SESSION['user']['username'] ?? 'Comisionado') ?>" disabled>
            </div>
        <?php endif; ?>

        <div class="reports-filter-actions">
            <button type="submit" class="btn-apply">Aplicar filtros</button>
            <a href="/project-cpr/public/reportes.php" class="btn-reset">Limpiar</a>
        </div>
    </form>

    <?php if (!empty($error_reporte)): ?>
        <div class="reports-alert no-print"><?= htmlspecialchars($error_reporte) ?></div>
    <?php endif; ?>

    <div class="reports-alert no-print" id="reports-alert-inline" hidden></div>

    <div class="print-header only-print">
        <img src="/project-cpr/public/assets/img/logo-sena-cpr.png" alt="Logo SENA CPR">
        <div>
            <h2>Reporte de casos - Comisión de Personal</h2>
            <p>SENA</p>
            <p><?= htmlspecialchars($rangoTexto) ?></p>
        </div>
    </div>

    <div class="report-meta">
        <div class="meta-item">
            <span>Periodo consultado</span>
            <strong><?= htmlspecialchars($rangoTexto) ?></strong>
        </div>
        <div class="meta-item">
            <span>Comisionados</span>
            <strong><?= htmlspecialchars($resumenTexto) ?></strong>
        </div>
        <div class="meta-item">
            <span>Generado el</span>
            <strong><?= htmlspecialchars($fechaGeneracion) ?></strong>
        </div>
    </div>

    <div class="print-divider only-print"></div>

    <div class="report-cards">
        <article class="report-card">
            <span>Total de casos</span>
            <strong><?= (int)$totalCasos ?></strong>
        </article>
        <article class="report-card">
            <span>Atendidos</span>
            <strong><?= (int)($totalesEstado['Atendido'] ?? 0) ?></strong>
        </article>
        <article class="report-card">
            <span>Pendientes</span>
            <strong><?= (int)($totalesEstado['Pendiente'] ?? 0) ?></strong>
        </article>
        <article class="report-card">
            <span>No atendidos</span>
            <strong><?= (int)($totalesEstado['No atendido'] ?? 0) ?></strong>
        </article>
    </div>

    <div class="report-grid">
        <article class="report-panel">
            <div class="panel-heading">
                <h3>Casos por comisionado</h3>
                <p>Distribución de radicados dentro del periodo consultado.</p>
            </div>
            <div class="chart-wrap">
                <canvas id="chartComisionados"></canvas>
            </div>
        </article>

        <article class="report-panel">
            <div class="panel-heading">
                <h3>Estados de los casos</h3>
                <p>Balance general entre atendidos, pendientes y no atendidos.</p>
            </div>
            <div class="chart-wrap">
                <canvas id="chartEstados"></canvas>
            </div>
        </article>
    </div>

    <article class="report-panel">
        <div class="panel-heading">
            <h3>Resumen por comisionado</h3>
            <p>Detalle consolidado para facilitar la revisión e impresión del reporte.</p>
        </div>

        <div class="report-table-wrap">
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Comisionado</th>
                        <th>Total casos</th>
                        <th>Atendidos</th>
                        <th>Pendientes</th>
                        <th>No atendidos</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($reportePorComisionado)): ?>
                        <?php foreach ($reportePorComisionado as $fila): ?>
                            <tr>
                                <td><?= htmlspecialchars($fila['nombre']) ?></td>
                                <td><?= (int)$fila['total'] ?></td>
                                <td><?= (int)$fila['Atendido'] ?></td>
                                <td><?= (int)$fila['Pendiente'] ?></td>
                                <td><?= (int)$fila['No atendido'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="empty-row">No hay casos en el rango seleccionado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </article>
</section>

<script src="/project-cpr/public/assets/libs/chartjs/chart.umd.min.js"></script>
<script>
    const reportsFiltersForm = document.getElementById('reports-filters-form');
    const fechaInicioInput = document.getElementById('fecha_inicio');
    const fechaFinInput = document.getElementById('fecha_fin');
    const reportsAlertInline = document.getElementById('reports-alert-inline');

    if (reportsFiltersForm && fechaInicioInput && fechaFinInput && reportsAlertInline) {
        reportsFiltersForm.addEventListener('submit', (event) => {
            const fechaInicio = fechaInicioInput.value;
            const fechaFin = fechaFinInput.value;

            if (fechaInicio && fechaFin && fechaInicio > fechaFin) {
                event.preventDefault();
                reportsAlertInline.textContent = 'La fecha inicial no puede ser mayor que la fecha final.';
                reportsAlertInline.hidden = false;
                return;
            }

            reportsAlertInline.hidden = true;
            reportsAlertInline.textContent = '';
        });
    }

    const toggleMostrarInactivos = document.querySelector('input[name="mostrar_inactivos"]');
    if (toggleMostrarInactivos) {
        toggleMostrarInactivos.addEventListener('change', () => {
            toggleMostrarInactivos.form.submit();
        });
    }

    const labelsComisionados = <?= json_encode(array_column($reportePorComisionado, 'nombre'), JSON_UNESCAPED_UNICODE) ?>;
    const dataComisionados = <?= json_encode(array_map('intval', array_column($reportePorComisionado, 'total'))) ?>;
    const dataEstados = <?= json_encode([
        (int)($totalesEstado['Atendido'] ?? 0),
        (int)($totalesEstado['Pendiente'] ?? 0),
        (int)($totalesEstado['No atendido'] ?? 0)
    ]) ?>;

    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                labels: {
                    color: '#17324d',
                    font: {
                        size: 12
                    }
                }
            }
        }
    };

    const ctxComisionados = document.getElementById('chartComisionados');
    if (ctxComisionados) {
        new Chart(ctxComisionados, {
            type: 'bar',
            data: {
                labels: labelsComisionados,
                datasets: [{
                    label: 'Casos radicados',
                    data: dataComisionados,
                    backgroundColor: ['#0f766e', '#1d4ed8', '#d97706', '#b91c1c', '#6d28d9', '#475569'],
                    borderRadius: 10,
                    maxBarThickness: 56
                }]
            },
            options: {
                ...commonOptions,
                scales: {
                    x: {
                        ticks: { color: '#17324d' },
                        grid: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            color: '#17324d'
                        },
                        grid: {
                            color: 'rgba(23, 50, 77, 0.08)'
                        }
                    }
                }
            }
        });
    }

    const ctxEstados = document.getElementById('chartEstados');
    if (ctxEstados) {
        new Chart(ctxEstados, {
            type: 'doughnut',
            data: {
                labels: ['Atendidos', 'Pendientes', 'No atendidos'],
                datasets: [{
                    data: dataEstados,
                    backgroundColor: ['#15803d', '#d97706', '#b91c1c'],
                    borderWidth: 0
                }]
            },
            options: {
                ...commonOptions,
                cutout: '62%'
            }
        });
    }
</script>
