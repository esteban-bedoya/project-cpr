<link rel="stylesheet" href="/project-cpr/public/assets/css/globals/caso.css">

<div class="case-layout">

    <!-- ============================================================
         SIDEBAR (Filtros de navegación)
    ============================================================ -->
    <div class="case-sidebar">

        <form method="POST" action="/project-cpr/public/caso.php">

            <input type="hidden" name="action" value="updateDetalle">
            <input type="hidden" name="caso_id" value="<?= $caso['id'] ?>">

            <!-- 1. Comisionado -->
            <div class="filter-group">
                <label class="filter-title">Comisionado</label>

                <select name="comisionado_id" required>
                    <?php foreach (Caso::getComisionadosActivos() as $c): ?>
                        <option value="<?= $c['id'] ?>"
                            <?= $caso['asignado_a'] == $c['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['username']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <br>

            <!-- 2. Estado -->
            <div class="filter-group">
                <label class="filter-title">Estado</label>

                <?php
                $estados = ['Atendido', 'No atendido', 'Pendiente'];
                foreach ($estados as $e):
                ?>
                    <label class="check-item">
                        <input
                            type="radio"
                            name="estado"
                            value="<?= $e ?>"
                            <?= $caso['estado'] === $e ? 'checked' : '' ?>
                            required>
                        <span><?= $e ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
            <br>

            <!-- 3. Tipo de proceso -->
            <div class="filter-group">
                <label class="filter-title">Tipo de proceso</label>

                <select name="tipo_proceso_id" required>
                    <?php foreach ($tiposProceso as $p): ?>
                        <option value="<?= $p['id'] ?>"
                            <?= $caso['tipo_proceso_id'] == $p['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <br>

            <!-- 4. Tipo de caso (solo mostrar) -->
            <div class="filter-group">
                <label class="filter-title">Tipo de caso</label>
                <input
                    type="text"
                    value="<?= htmlspecialchars($caso['tipo_caso_nombre']) ?>"
                    disabled>
            </div>
            <br>

            <button type="submit" class="btn-actualizar">
                Actualizar
            </button>

        </form>
    </div>

    <!-- ============================================================
         PANEL PRINCIPAL DEL CASO
    ============================================================ -->
    <div class="case-content">

        <!-- Header -->
        <div class="case-header">
            #<?= $caso['numero_caso'] ?> |
            <?= htmlspecialchars($caso['asunto']) ?>
        </div>

        <div class="case-info">
            <div class="info-item">
                <strong>Demandante:</strong>
                <?= htmlspecialchars($caso['demandante_nombre']) ?>
            </div>

            <div class="info-item">
                <strong>Contacto:</strong>
                <?= htmlspecialchars($caso['demandante_contacto']) ?>
            </div>

            <?php if (!empty($caso['detalles'])): ?>
                <div class="info-item info-detalles">
                    <strong>Detalles:</strong><br>
                    <?= nl2br(htmlspecialchars($caso['detalles'])) ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="case-box">
            <div class="case-messages">

                <!-- ====================================================
                     MENSAJES Y CAMBIOS DE ESTADO ORDENADOS
                ==================================================== -->
                <?php
                $eventos = array_merge(
                    array_map(
                        fn($m) => [
                            'tipo'  => 'mensaje',
                            'data'  => $m,
                            'fecha' => $m['fecha']
                        ],
                        $mensajes
                    ),
                    array_map(
                        fn($h) => [
                            'tipo'  => 'historial',
                            'data'  => $h,
                            'fecha' => $h['fecha']
                        ],
                        $historial
                    )
                );

                usort(
                    $eventos,
                    fn($a, $b) =>
                    strtotime($a['fecha']) <=> strtotime($b['fecha'])
                );
                ?>

                <?php foreach ($eventos as $e): ?>
                    <div class="msg-entry">

                        <div class="msg-date">
                            <?= date("d/m/Y H:i", strtotime($e['fecha'])) ?>
                        </div>

                        <?php if ($e['tipo'] == 'mensaje'): ?>

                            <div class="msg-user">
                                <?= htmlspecialchars($e['data']['username']) ?>
                            </div>

                            <div class="msg-body">
                                <?= nl2br(htmlspecialchars($e['data']['mensaje'])) ?>
                            </div>

                            <?php if (!empty($e['data']['archivo'])): ?>
                                <div class="msg-archivo">
                                    <a
                                        href="/project-cpr/public/uploads/casos/<?= htmlspecialchars($e['data']['archivo']) ?>"
                                        target="_blank">
                                        📎 Ver archivo
                                    </a>
                                </div>
                            <?php endif; ?>

                        <?php else: ?>

                            <div class="msg-status-change">
                                <strong><?= htmlspecialchars($e['data']['username']) ?></strong>
                                —
                                <?= htmlspecialchars($e['data']['descripcion']) ?>
                            </div>

                        <?php endif; ?>
                    </div>

                    <div class="divider"></div>
                <?php endforeach; ?>

            </div>
        </div>

        <!-- ============================================================
             INPUT PARA NUEVO MENSAJE
        ============================================================ -->

        <?php if (isset($_GET['error'])): ?>
            <p style="color:red; font-size:14px;">
                <?php
                switch ($_GET['error']) {
                    case 'vacio':
                        echo 'Debes escribir un mensaje o adjuntar un archivo.';
                        break;
                    case 'tipo':
                        echo 'Tipo de archivo no permitido.';
                        break;
                    case 'tamano':
                        echo 'El archivo supera el tamaño permitido.';
                        break;
                    case 'subida':
                        echo 'Error al subir el archivo.';
                        break;
                }
                ?>
            </p>
        <?php endif; ?>


        <form
            class="msg-input-box"
            method="POST"
            enctype="multipart/form-data"
            action="/project-cpr/public/caso.php">

            <input type="hidden" name="action" value="mensaje">
            <input type="hidden" name="caso_id" value="<?= $caso['id'] ?>">

            <input
                type="text"
                name="mensaje"
                placeholder="Escribir mensaje y/o adjuntar un archivo"
                class="msg-input">

            <label class="btn-attach">
                📎
                <input
                    type="file"
                    name="archivo"
                    accept=".pdf,.jpg,.jpeg,.png"
                    hidden>
            </label>

            <button class="btn-enviar">Enviar</button>
        </form>


    </div>
</div>

<script>
    window.addEventListener('load', () => {
        const messages = document.querySelector('.case-messages');
        if (!messages) return;
        messages.scrollTop = messages.scrollHeight;
    });
</script>

