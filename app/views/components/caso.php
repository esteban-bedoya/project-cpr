<link rel="stylesheet" href="/project-cpr/public/assets/css/globals/caso.css">

<div class="case-layout">

    <!-- ============================================================
         SIDEBAR (Filtros de navegación)
    ============================================================ -->
    <div class="case-sidebar">

        <!-- Comisionado -->
        <div class="filter-group">
            <label class="filter-title">Comisionado</label>
            <select disabled>
                <option><?= htmlspecialchars($caso['creado_por_nombre']) ?></option>
            </select>
        </div>

        <!-- Tipo de caso -->
        <div class="filter-group">
            <label class="filter-title">Tipo de caso</label>
            <?php foreach ($tiposCaso as $t): ?>
                <label class="check-item">
                    <input 
                        type="radio" 
                        disabled
                        <?= $caso['tipo_caso_id'] == $t['id'] ? 'checked' : '' ?>
                    >
                    <span><?= htmlspecialchars($t['nombre']) ?></span>
                </label>
            <?php endforeach; ?>
        </div>

        <!-- Estado -->
        <div class="filter-group">
            <label class="filter-title">Estado</label>
            <?php 
            $estados = ['Atendido','No atendido','Pendiente'];
            foreach ($estados as $e): ?>
                <label class="check-item">
                    <input 
                        type="radio" 
                        disabled
                        <?= $caso['estado'] === $e ? 'checked' : '' ?>
                    >
                    <span><?= $e ?></span>
                </label>
            <?php endforeach; ?>
        </div>

        <!-- Tipo de proceso -->
        <div class="filter-group">
            <label class="filter-title">Tipo de proceso</label>
            <select disabled>
                <?php foreach ($tiposProceso as $p): ?>
                    <option <?= $caso['tipo_proceso_id'] == $p['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button class="btn-actualizar" disabled>Actualizar</button>
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
                    <?php if ($e['tipo'] === 'mensaje'): ?>
                        <div class="msg-date"><?= date("d/m/Y H:i", strtotime($e['data']['fecha'])) ?></div>
                        <div class="msg-user"><?= htmlspecialchars($e['data']['username']) ?></div>
                        <div class="msg-body"><?= nl2br(htmlspecialchars($e['data']['mensaje'])) ?></div>
                        <?php if (!empty($e['data']['archivo'])): ?>
                            <a class="msg-file" href="/uploads/<?= $e['data']['archivo'] ?>" target="_blank">
                                📎 Archivo adjunto
                            </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="msg-status-change">
                            <?= date("d/m/Y H:i", strtotime($e['data']['fecha'])) ?> —
                            <strong><?= htmlspecialchars($e['data']['username']) ?> cambió</strong><br>
                            de “<?= $e['data']['estado_anterior'] ?? '—' ?>” a “<?= $e['data']['estado_nuevo'] ?>”
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
                required
            >

            <label class="btn-attach">📎
                <input type="file" name="archivo" hidden>
            </label>

            <button class="btn-enviar">Enviar</button>
        </form>

    </div>
</div>
