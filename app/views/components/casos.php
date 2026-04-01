<!-- Componente de listado y filtros de casos -->
<?php
// Variables esperadas:
// $casos, $tiposCaso, $tiposProceso, $comisionados
// $filtro_estado, $filtro_tipo_caso, $filtro_tipo_proceso, $filtro_comisionado
// $fecha_inicio, $fecha_fin
?>

<div class="search-container">

    <!-- ================= SIDEBAR DE FILTROS ================= -->
    <aside class="filters-sidebar">
        <h2 class="filters-title">Filtros</h2>

        <form class="filters-form" method="GET" action="/project-cpr/public/casos.php">
            <!-- Rango de fechas -->
            <div class="filter-group">
                <label class="filter-label">Rango de fechas</label>
                <div class="date-range date-range-vertical">
                    <input type="date" name="fecha_inicio" class="date-input"
                        value="<?= htmlspecialchars($fecha_inicio ?? '') ?>">
                    <input type="date" name="fecha_fin" class="date-input"
                        value="<?= htmlspecialchars($fecha_fin ?? '') ?>">
                </div>
            </div>

            <!-- Estado del caso -->
            <div class="filter-group">
                <label class="filter-label filter-toggle">
                    Estado del caso
                    <span class="toggle-icon">∧</span>
                </label>
                <div class="filter-options collapsed">
                    <?php
                    $estado_options = [
                        'todos' => 'Todos',
                        'Atendido' => 'Atendido',
                        'No atendido' => 'No atendido',
                        'Pendiente' => 'Pendiente',
                        'proximos' => 'Próximos a vencer'
                    ];
                    foreach ($estado_options as $val => $label) {
                        $checked = ($filtro_estado ?? 'todos') === $val ? 'checked' : '';
                        echo "<label class='radio-label'><input type='radio' name='estado' value='{$val}' {$checked}><span class='radio-custom'></span>{$label}</label>";
                    }
                    ?>
                </div>
            </div>

            <!-- Tipo de casos -->
            <div class="filter-group">
                <label class="filter-label filter-toggle">
                    Tipo de casos
                    <span class="toggle-icon">∧</span>
                </label>
                <div class="filter-options collapsed">
                    <label class="radio-label">
                        <input type="radio" name="tipo_caso" value="todos"
                            <?= ($filtro_tipo_caso ?? 'todos') === 'todos' ? 'checked' : '' ?>>
                        <span class="radio-custom"></span>Todos
                    </label>
                    <?php foreach (($tiposCaso ?? []) as $tc): ?>
                        <label class="radio-label">
                            <input type="radio" name="tipo_caso" value="<?= $tc['id'] ?>"
                                <?= (string)($filtro_tipo_caso ?? 'todos') === (string)$tc['id'] ? 'checked' : '' ?>>
                            <span class="radio-custom"></span><?= htmlspecialchars($tc['nombre']) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Tipo de procesos -->
            <div class="filter-group">
                <label class="filter-label filter-toggle">
                    Tipo de procesos
                    <span class="toggle-icon">∧</span>
                </label>
                <div class="filter-options collapsed">
                    <label class="radio-label">
                        <input type="radio" name="tipo_proceso" value="todos"
                            <?= ($filtro_tipo_proceso ?? 'todos') === 'todos' ? 'checked' : '' ?>>
                        <span class="radio-custom"></span>Todos
                    </label>
                    <?php foreach (($tiposProceso ?? []) as $tp): ?>
                        <label class="radio-label">
                            <input type="radio" name="tipo_proceso" value="<?= $tp['id'] ?>"
                                <?= (string)($filtro_tipo_proceso ?? 'todos') === (string)$tp['id'] ? 'checked' : '' ?>>
                            <span class="radio-custom"></span><?= htmlspecialchars($tp['nombre']) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Comisionado asignado -->
            <div class="filter-group">
                <label class="filter-label filter-toggle">
                    Comisionado asignado
                    <span class="toggle-icon">∧</span>
                </label>
                <div class="filter-options collapsed">
                    <label class="radio-label">
                        <input type="radio" name="comisionado" value="todos"
                            <?= ($filtro_comisionado ?? 'todos') === 'todos' ? 'checked' : '' ?>>
                        <span class="radio-custom"></span>Todos
                    </label>
                    <?php foreach (($comisionados ?? []) as $c): ?>
                        <?php
                        $estado_label = ((int)$c['estado'] === 1) ? '' : ' (Inactivo)';
                        ?>
                        <label class="radio-label">
                            <input type="radio" name="comisionado" value="<?= $c['id'] ?>"
                                <?= (string)($filtro_comisionado ?? 'todos') === (string)$c['id'] ? 'checked' : '' ?>>
                            <span class="radio-custom"></span><?= htmlspecialchars($c['username']) ?><?= $estado_label ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-filter">Aplicar filtros</button>
                <a class="btn-clear" href="/project-cpr/public/casos.php">Limpiar</a>
            </div>
        </form>
    </aside>

    <!-- ================= ÁREA PRINCIPAL ================= -->
    <section class="search-main">
        <!-- Barra de busqueda (front end) -->
        <div class="search-bar">
            <span class="search-icon">🔍</span>
            <input type="text" class="search-input" placeholder="Buscar por asunto, detalles, radicado, #caso...">
        </div>

        <!-- Tabla de resultados -->
        <div class="results-table">
            <table class="cases-table">
                <thead>
                    <tr>
                        <th>#Caso</th>
                        <th>Rad SENA</th>
                        <th>Asunto</th>
                        <th>Fecha de creación</th>
                        <th>Tiempo de cierre</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($casos)): ?>
                        <?php foreach ($casos as $caso): ?>
                            <tr class="table-row">
                                <td class="col-id">
                                    <a href="/project-cpr/public/caso.php?id=<?= $caso['id'] ?>">
                                        <?= htmlspecialchars($caso['numero_caso']) ?>
                                    </a>
                                </td>
                                <td class="col-rad"><?= htmlspecialchars($caso['radicado_sena'] ?? '—') ?></td>
                                <td class="col-description"><?= htmlspecialchars($caso['asunto'] ?? '—') ?></td>
                                <td class="col-date"><?= date('d-m-Y', strtotime($caso['fecha_creacion'] ?? '')) ?></td>
                                <td class="col-time">
                                    <?php
                                    if (($caso['estado'] ?? '') === 'Atendido') {
                                        echo "Atendido";
                                    } else {
                                        $fecha_cierre = $caso['fecha_cierre'] ?? null;
                                        if ($fecha_cierre) {
                                            $hoy = new DateTime();
                                            $cierre = new DateTime($fecha_cierre);
                                            $interval = $hoy->diff($cierre);
                                            $dias = (int)$interval->format('%r%a');
                                            $class = $dias < 0 ? 'time-overdue' : ($dias <= 2 ? 'time-soon' : 'time-ok');
                                            echo "<span class='{$class}'>{$dias} días</span>";
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
                            <td colspan="5" class="empty-state">No hay casos para este filtro.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

</div>

<script>
    // ============================
    // TOGGLES DE FILTROS
    // ============================
    document.querySelectorAll('.filter-toggle').forEach(toggle => {
        toggle.addEventListener('click', function () {
            const icon = this.querySelector('.toggle-icon');
            const options = this.parentElement.querySelector('.filter-options');
            if (!options) return;
            options.classList.toggle('collapsed');
            icon.textContent = options.classList.contains('collapsed') ? '∧' : '∨';
        });
    });

    // ============================
    // BUSCADOR EN VIVO (CLIENTE)
    // ============================
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
