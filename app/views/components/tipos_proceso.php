<!-- Componente: gestion de tipos de proceso -->
<link rel="stylesheet" href="/PROJECT-CPR/public/assets/css/globals/perfil.css">

<?php
$estaEditando = !empty($procesoSeleccionado);
$modoActual = $estaEditando ? 'editar' : 'crear';
$estadoActual = (int)($procesoSeleccionado['estado'] ?? 1);
?>

<div class="perfil-container tipos-proceso-panel">
    <div class="tipos-proceso-hero">
        <div>
            <h1>Tipos de procesos</h1>
            <p class="tipos-proceso-lead">
                Crea nuevos procesos o actualiza los ya existentes.
            </p>
        </div>
    </div>

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

    <section class="tipos-proceso-card">
        <div class="tipos-proceso-card-header">
            <h2>¿Qué deseas hacer?</h2>
        </div>

        <div class="tipos-proceso-mode-switch">
            <a
                href="/project-cpr/public/tipos_proceso.php"
                class="tipo-modo-card <?= $modoActual === 'crear' ? 'is-active' : '' ?>">
                <strong>Crear un proceso nuevo</strong>
            </a>

            <a
                href="/project-cpr/public/tipos_proceso.php<?= !empty($tiposProceso) ? '?proceso_id=' . (int)$tiposProceso[0]['id'] : '' ?>"
                class="tipo-modo-card <?= $modoActual === 'editar' ? 'is-active' : '' ?> <?= empty($tiposProceso) ? 'is-disabled' : '' ?>">
                <strong>Editar un proceso existente</strong>
            </a>
        </div>
    </section>

    <section class="tipos-proceso-card">
        <div class="tipos-proceso-card-header">
            <h2><?= $modoActual === 'editar' ? 'Editar proceso' : 'Crear proceso'; ?></h2>
        </div>

        <form class="form-procesos" action="/project-cpr/public/tipos_proceso.php?action=guardar" method="POST">
            <input type="hidden" name="proceso_id" value="<?= $procesoSeleccionado['id'] ?? '' ?>">

            <?php if ($modoActual === 'editar'): ?>
                <div class="grupo">
                    <label for="proceso_id_selector">Proceso</label>
                    <select id="proceso_id_selector" name="proceso_selector">
                        <?php foreach ($tiposProceso as $tipoProceso): ?>
                            <option value="<?= (int)$tipoProceso['id'] ?>" <?= ($procesoSeleccionado && (int)$procesoSeleccionado['id'] === (int)$tipoProceso['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($tipoProceso['nombre']) ?><?= ((int)($tipoProceso['estado'] ?? 1) !== 1) ? ' (inactivo)' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <div class="grupo">
                <label for="proceso_nombre">Nombre del proceso</label>
                <input
                    type="text"
                    id="proceso_nombre"
                    name="proceso_nombre"
                    placeholder="Nombre del proceso"
                    value="<?= isset($procesoSeleccionado['nombre']) ? htmlspecialchars($procesoSeleccionado['nombre']) : '' ?>"
                    required>
            </div>

            <div class="grupo">
                <label for="proceso_estado">Estado del proceso</label>
                <select id="proceso_estado" name="estado">
                    <option value="1" <?= $estadoActual === 1 ? 'selected' : '' ?>>Activo</option>
                    <option value="0" <?= $estadoActual === 0 ? 'selected' : '' ?>>Inactivo</option>
                </select>
            </div>

            <div class="botones left">
                <button type="submit" class="btn-actualizar btn-tipos-proceso">
                    <?= $modoActual === 'editar' ? 'Guardar cambios' : 'Crear proceso'; ?>
                </button>
                <?php if ($modoActual === 'editar'): ?>
                    <button
                        type="submit"
                        class="btn-eliminar-tipos"
                        form="form-eliminar-proceso"
                        onclick="return confirm('¿Deseas eliminar este tipo de proceso? Esta acción no se puede deshacer.');">
                        Eliminar proceso
                    </button>
                <?php endif; ?>
            </div>
        </form>
    </section>

    <?php if ($modoActual === 'editar'): ?>
        <form id="form-eliminar-proceso" action="/project-cpr/public/tipos_proceso.php?action=eliminar" method="POST">
            <input type="hidden" name="proceso_id" value="<?= $procesoSeleccionado['id'] ?? '' ?>">
        </form>
    <?php endif; ?>
</div>

<?php if ($modoActual === 'editar'): ?>
    <script>
        const procesoSelector = document.getElementById('proceso_id_selector');
        if (procesoSelector) {
            procesoSelector.addEventListener('change', () => {
                window.location.href = `/project-cpr/public/tipos_proceso.php?proceso_id=${procesoSelector.value}`;
            });
        }
    </script>
<?php endif; ?>
