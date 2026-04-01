<link rel="stylesheet" href="/project-cpr/public/assets/css/globals/caso.css">

<div class="case-layout">

    <!-- ============================================================
         SIDEBAR (Filtros de navegación)
    ============================================================ -->
    <div class="case-sidebar">

        <form method="POST" action="/project-cpr/casos/<?= $caso['id'] ?>/updateDetalle">

            <!-- 1. Comisionado -->
            <div class="filter-group">
                <label class="filter-title">Comisionado</label>
                <select name="comisionado_id" required>
                    <?php foreach (Caso::getComisionadosActivos() as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $caso['asignado_a'] == $c['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['username']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- 2. Estado -->
            <div class="filter-group">
                <label class="filter-title">Estado</label>
                <?php
                $estados = ['Atendido', 'No atendido', 'Pendiente'];
                foreach ($estados as $e): ?>
                    <label class="check-item">
                        <input type="radio" name="estado" value="<?= $e ?>" <?= $caso['estado'] === $e ? 'checked' : '' ?> required>
                        <span><?= $e ?></span>
                    </label>
                <?php endforeach; ?>
            </div>

            <!-- 3. Tipo de proceso -->
            <div class="filter-group">
                <label class="filter-title">Tipo de proceso</label>
                <select name="tipo_proceso_id" required>
                    <?php foreach ($tiposProceso as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= $caso['tipo_proceso_id'] == $p['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- 4. Tipo de caso (solo mostrar) -->
            <div class="filter-group">
                <label class="filter-title">Tipo de caso</label>
                <input type="text" value="<?= htmlspecialchars($caso['tipo_caso_nombre']) ?>" disabled>
            </div>

            <button type="submit" class="btn-actualizar">Actualizar</button>
        </form>
    </div>


    <!-- ============================================================
         PANEL PRINCIPAL DEL CASO
    ============================================================ -->
    <div class="case-content">

        <!-- Header -->
        <div class="case-header">
            #<?= $caso['numero_caso'] ?> | <?= htmlspecialchars($caso['asunto']) ?>
        </div>

        <div class="case-box">

            <!-- ====================================================
                 MENSAJES Y CAMBIOS DE ESTADO ORDENADOS
            ==================================================== -->
            <?php
            // Mezclar mensajes y historial en un solo arreglo de eventos
            $eventos = array_merge(
                array_map(fn($m) => ['tipo' => 'mensaje', 'data' => $m, 'fecha' => $m['fecha']], $mensajes),
                array_map(fn($h) => ['tipo' => 'historial', 'data' => $h, 'fecha' => $h['fecha']], $historial)
            );

            // Ordenar cronológicamente
            usort($eventos, fn($a, $b) => strtotime($a['fecha']) <=> strtotime($b['fecha']));
            ?>

            <?php foreach ($eventos as $e): ?>
                <div class="msg-entry">
                    <div class="msg-date"><?= date("d/m/Y H:i", strtotime($e['fecha'])) ?></div>
                    <?php if ($e['tipo'] == 'mensaje'): ?>
                        <div class="msg-user"><?= htmlspecialchars($e['data']['username']) ?></div>
                        <div class="msg-body"><?= nl2br(htmlspecialchars($e['data']['mensaje'])) ?></div>
                    <?php else: ?>
                        <div class="msg-status-change">
                            <strong><?= htmlspecialchars($e['data']['username']) ?></strong> —
                            <?= htmlspecialchars($e['data']['descripcion']) ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="divider"></div>
            <?php endforeach; ?>

        </div>

        <!-- ============================================================
             INPUT PARA NUEVO MENSAJE
        ============================================================ -->
        <form class="msg-input-box" method="POST" enctype="multipart/form-data"
            action="/project-cpr/casos/<?= $caso['id'] ?>/mensaje">

            <input
                type="text"
                name="mensaje"
                placeholder="Escribir ..."
                class="msg-input"
                required>

            <label class="btn-attach">📎
                <input type="file" name="archivo" hidden>
            </label>

            <button class="btn-enviar">Enviar</button>
        </form>

    </div>
</div>