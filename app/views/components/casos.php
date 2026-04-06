<!-- Componente de listado y filtros de casos -->
<?php
// Variables esperadas:
// $casos, $tiposCaso, $tiposProceso, $comisionados
// $filtro_estado, $filtro_tipo_caso, $filtro_tipo_proceso, $filtro_comisionado
// $fecha_inicio, $fecha_fin
// Este componente lo usan ambos roles.
?>

<div class="search-container">
    <!-- ================= BLOQUE DE FILTROS ================= -->
    <section class="filters-sidebar">
        <h2 class="filters-title">Aplique los filtros de los casos que desea visualizar</h2>

        <form class="filters-form" method="GET" action="/project-cpr/public/casos.php">
            <input type="hidden" name="aplicar" value="1">
            <!-- Rango de fechas -->
            <div class="filter-group filter-group-dates">
                <label class="filter-label filter-label-inline">Rango de fechas</label>
                <div class="date-range">
                    <span class="date-inline-label">Desde</span>
                    <input type="date" name="fecha_inicio" class="date-input"
                        value="<?= htmlspecialchars($fecha_inicio ?? '') ?>">
                    <span class="date-inline-label">Hasta</span>
                    <input type="date" name="fecha_fin" class="date-input"
                        value="<?= htmlspecialchars($fecha_fin ?? '') ?>">
                </div>
            </div>

            <!-- Estado del caso -->
            <div class="filter-group">
                <label class="filter-select-label" for="filtro_estado">Estado del caso</label>
                <select id="filtro_estado" name="estado" class="filter-select">
                    <?php
                    // Estados fijos del filtro.
                    $estado_options = [
                        'todos' => 'Todos',
                        'Atendido' => 'Atendido',
                        'No atendido' => 'No atendido',
                        'Pendiente' => 'Pendiente'
                    ];
                    foreach ($estado_options as $val => $label) {
                        $checked = ($filtro_estado ?? 'todos') === $val ? 'checked' : '';
                        $selected = ($filtro_estado ?? 'todos') === $val ? 'selected' : '';
                        echo "<option value='{$val}' {$selected}>{$label}</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Tipo de casos -->
            <div class="filter-group">
                <label class="filter-select-label" for="filtro_tipo_caso">Tipo de casos</label>
                <select id="filtro_tipo_caso" name="tipo_caso" class="filter-select">
                    <option value="todos" <?= ($filtro_tipo_caso ?? 'todos') === 'todos' ? 'selected' : '' ?>>Todos</option>
                    <?php foreach (($tiposCaso ?? []) as $tc): ?>
                        <option value="<?= $tc['id'] ?>" <?= (string)($filtro_tipo_caso ?? 'todos') === (string)$tc['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($tc['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Tipo de procesos -->
            <div class="filter-group">
                <label class="filter-select-label" for="filtro_tipo_proceso">Tipo de procesos</label>
                <select id="filtro_tipo_proceso" name="tipo_proceso" class="filter-select">
                    <option value="todos" <?= ($filtro_tipo_proceso ?? 'todos') === 'todos' ? 'selected' : '' ?>>Todos</option>
                    <?php foreach (($tiposProceso ?? []) as $tp): ?>
                        <option value="<?= $tp['id'] ?>" <?= (string)($filtro_tipo_proceso ?? 'todos') === (string)$tp['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($tp['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Comisionado asignado -->
            <div class="filter-group">
                <label class="filter-select-label" for="filtro_comisionado">Comisionado asignado</label>
                <select id="filtro_comisionado" name="comisionado" class="filter-select">
                    <option value="todos" <?= ($filtro_comisionado ?? 'todos') === 'todos' ? 'selected' : '' ?>>Todos</option>
                    <?php foreach (($comisionados ?? []) as $c): ?>
                        <?php
                        // Si esta inactivo, se marca en el nombre.
                        $estado_label = ((int)$c['estado'] === 1) ? '' : ' (Inactivo)';
                        ?>
                        <option value="<?= $c['id'] ?>" <?= (string)($filtro_comisionado ?? 'todos') === (string)$c['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['username']) ?><?= $estado_label ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-filter">Aplicar filtros</button>
                <a class="btn-clear" href="/project-cpr/public/casos.php">Limpiar</a>
            </div>
        </form>
    </section>

    <!-- ================= ÁREA PRINCIPAL ================= -->
    <section class="search-main">
        <!-- Barra de busqueda (front end) -->
        <?php if (!empty($filtros_aplicados)): ?>
            <div class="search-bar">
                <span class="search-icon">🔍</span>
                <input type="text" class="search-input" placeholder="Buscar por asunto, detalles, radicado, #caso...">
            </div>
        <?php endif; ?>

        <!-- Tabla de resultados -->
        <div class="results-table">
            <table class="cases-table">
                <thead>
                    <tr>
                        <th>#Caso</th>
                        <th>#Rad SENA</th>
                        <th>Asunto</th>
                        <th>Estado del caso</th>
                        <th>Comisionado asignado</th>
                        <th>Fecha de creación</th>
                        <th>Fecha de cierre</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($filtros_aplicados) && !empty($casos)): ?>
                        <?php foreach ($casos as $caso): ?>
                            <tr class="table-row">
                                <td class="col-id">
                                    <a href="/project-cpr/public/caso.php?id=<?= $caso['id'] ?>">
                                        <?= htmlspecialchars($caso['numero_caso']) ?>
                                    </a>
                                </td>
                                <td class="col-rad"><?= htmlspecialchars($caso['radicado_sena'] ?? '—') ?></td>
                                <td class="col-description"><?= htmlspecialchars($caso['asunto'] ?? '—') ?></td>
                                <td class="col-status"><?= htmlspecialchars($caso['estado'] ?? '—') ?></td>
                                <td class="col-comisionado"><?= htmlspecialchars($caso['asignado_a_nombre'] ?? 'Sin asignar') ?></td>
                                <td class="col-date"><?= date('d-m-Y', strtotime($caso['fecha_creacion'] ?? '')) ?></td>
                                <td class="col-date-close">
                                    <?= !empty($caso['fecha_cierre']) ? date('d-m-Y', strtotime($caso['fecha_cierre'])) : '—' ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php elseif (!empty($filtros_aplicados)): ?>
                        <tr>
                            <td colspan="7" class="empty-state">No hay casos para este filtro.</td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="empty-state empty-state-initial">Aplique filtros para visualizar los casos.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

</div>

<script>
    // Filtro rapido sobre filas ya cargadas.
    const buscadorInput = document.querySelector('.search-input');
    const filas = document.querySelectorAll('.cases-table tbody tr');

    if (buscadorInput) {
        buscadorInput.addEventListener('input', () => {
            const texto = buscadorInput.value.toLowerCase().trim();
            filas.forEach(fila => {
                const contenidoFila = fila.textContent.toLowerCase();
                fila.style.display = contenidoFila.includes(texto) ? '' : 'none';
            });
        });
    }
</script>
