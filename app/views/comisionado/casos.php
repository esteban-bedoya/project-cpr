<?php $activePage = 'casos';
// Vista de busqueda de casos para comisionado.
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Búsqueda - CPR</title>
    <link rel="stylesheet" href="/project-cpr/public/assets/css/globals/base.css">
    <link rel="stylesheet" href="/project-cpr/public/assets/css/comisionado/gestionar.css">
    <link rel="stylesheet" href="/project-cpr/public/assets/css/globals/casos.css">
</head>

<body class="private">

    <!-- Header del comisionado -->
    <?php include __DIR__ . '/../components/header_comisionado.php'; ?>

    <?php $form_gestionar = $_SESSION['form_gestionar'] ?? []; ?>

    <div class="main-content">
        <button type="button" class="btn-agregar">Agregar caso</button>

        <!-- Contenido del modulo de busqueda de casos -->
        <?php include __DIR__ . '/../components/casos.php'; ?>
    </div>

    <div class="modal" id="modal-agregar">
        <div class="modal-content">
            <h3>Agregar caso</h3>
            <?php if (isset($_SESSION['error']) && ($_GET['error'] ?? '') === 'fechacierre'): ?>
                <p style="color:#b00020; font-size:14px; margin-bottom:10px;">
                    <?= $_SESSION['error']; ?>
                </p>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <form action="/project-cpr/public/casos.php?action=storeGestionar" method="POST">
                <label>Radicado SENA (opcional)</label>
                <input
                    type="text"
                    name="radicado_sena"
                    maxlength="10"
                    placeholder="Radicado SENA"
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
                <input type="date" name="fecha_cierre" required value="<?= htmlspecialchars($form_gestionar['fecha_cierre'] ?? '') ?>">

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

        if (btnAgregar && modalAgregar) {
            btnAgregar.addEventListener('click', () => {
                modalAgregar.style.display = 'flex';
            });
        }

        function cerrarModalAgregar() {
            if (modalAgregar) modalAgregar.style.display = 'none';
        }

        window.addEventListener('click', e => {
            if (e.target === modalAgregar) cerrarModalAgregar();
        });
    </script>

    <?php if (($_GET['error'] ?? '') === 'fechacierre'): ?>
        <script>
            if (modalAgregar) modalAgregar.style.display = 'flex';
        </script>
        <?php unset($_SESSION['form_gestionar']); ?>
    <?php endif; ?>

</body>

</html>
