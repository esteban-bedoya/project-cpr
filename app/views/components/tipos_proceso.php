<!-- Componente: gestion de tipos de proceso -->
<link rel="stylesheet" href="/PROJECT-CPR/public/assets/css/globals/perfil.css">

<div class="perfil-container tipos-proceso-panel">
    <h2 class="titulo-seccion">Tipos de procesos</h2>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert success">
            <?= $_SESSION['success']; ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert error">
            <?= $_SESSION['error']; ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form class="form-procesos" action="/project-cpr/public/tipos_proceso.php" method="GET">
        <div class="grupo">
            <label for="proceso_id" class="oculto">Seleccionar proceso</label>
            <select id="proceso_id" name="proceso_id">
                <option value="">— Nuevo proceso —</option>
                <?php foreach ($tiposProceso as $tipoProceso): ?>
                    <option value="<?= $tipoProceso['id'] ?>" <?= ($procesoSeleccionado && $procesoSeleccionado['id'] == $tipoProceso['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($tipoProceso['nombre']) ?><?= ((int)($tipoProceso['estado'] ?? 1) !== 1) ? ' (inactivo)' : '' ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>

    <form class="form-procesos" action="/project-cpr/public/tipos_proceso.php?action=guardar" method="POST">
        <input type="hidden" name="proceso_id" value="<?= $procesoSeleccionado['id'] ?? '' ?>">

        <div class="grupo">
            <label for="proceso_nombre" class="oculto">Nombre del proceso</label>
            <input
                type="text"
                id="proceso_nombre"
                name="proceso_nombre"
                placeholder="Nombre del proceso"
                value="<?= isset($procesoSeleccionado['nombre']) ? htmlspecialchars($procesoSeleccionado['nombre']) : '' ?>">
        </div>

        <div class="grupo">
            <label for="proceso_estado" class="oculto">Estado del proceso</label>
            <select id="proceso_estado" name="estado">
                <?php $estadoActual = (int)($procesoSeleccionado['estado'] ?? 1); ?>
                <option value="1" <?= $estadoActual === 1 ? 'selected' : '' ?>>Activo</option>
                <option value="0" <?= $estadoActual === 0 ? 'selected' : '' ?>>Inactivo</option>
            </select>
        </div>

        <div class="botones">
            <button type="submit" class="btn-actualizar btn-tipos-proceso">Guardar</button>
        </div>
    </form>

    <form class="form-procesos" action="/project-cpr/public/tipos_proceso.php?action=eliminar" method="POST">
        <input type="hidden" name="proceso_id" value="<?= $procesoSeleccionado['id'] ?? '' ?>">
        <div class="botones">
            <button type="submit" class="btn-cerrar btn-tipos-proceso">Eliminar</button>
        </div>
    </form>
</div>

<script>
    const selectProceso = document.getElementById('proceso_id');
    if (selectProceso) {
        selectProceso.addEventListener('change', () => {
            selectProceso.form.submit();
        });
    }
</script>
