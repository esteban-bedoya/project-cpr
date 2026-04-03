<!-- Componente: perfil de usuario (datos y configuracion) -->
<link rel="stylesheet" href="/PROJECT-CPR/public/assets/css/globals/perfil.css">

<?php
$perfil_error = $_SESSION['error'] ?? '';
$perfil_error_tipo = 'ninguno';
if ($perfil_error !== '') {
    if (stripos($perfil_error, 'contras') !== false) {
        $perfil_error_tipo = 'seguridad';
    } elseif (stripos($perfil_error, 'correo') !== false) {
        $perfil_error_tipo = 'cuenta';
    }
}
?>

<!-- Todo tu contenido de perfil.php -->
<div class="perfil-container">

    <!-- Bloque exclusivo para admin: gestion de tipos de proceso -->
    <?php if (isset($_SESSION['user']) && $_SESSION['user']['rol'] == 1): ?>
        <h2 class="titulo-seccion">Tipos de procesos</h2>

        <form class="form-procesos" action="/project-cpr/public/perfil.php" method="GET">
            <div class="grupo">
                <label for="proceso_id" class="oculto">Seleccionar proceso</label>
                <select id="proceso_id" name="proceso_id">
                    <option value="">— Nuevo proceso —</option>
                    <?php foreach ($tiposProceso as $tp): ?>
                        <option value="<?= $tp['id'] ?>" <?= ($procesoSeleccionado && $procesoSeleccionado['id'] == $tp['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($tp['nombre']) ?><?= ((int)($tp['estado'] ?? 1) !== 1) ? ' (inactivo)' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>

        <form class="form-procesos" action="/project-cpr/public/perfil.php?action=guardarProceso" method="POST">
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
                <button type="submit" class="btn-actualizar">Guardar</button>
            </div>
        </form>

        <form class="form-procesos" action="/project-cpr/public/perfil.php?action=eliminarProceso" method="POST">
            <input type="hidden" name="proceso_id" value="<?= $procesoSeleccionado['id'] ?? '' ?>">
            <div class="botones">
                <button type="submit" class="btn-cerrar">Eliminar</button>
            </div>
        </form>

    <?php endif; ?>

    <hr class="divisor">

    <!-- Mensajes de estado -->
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

    <section class="perfil-card accordion" data-accordion="cuenta">
        <button class="accordion-toggle" type="button" aria-expanded="false">
            <span>Informacion de la cuenta</span>
            <span class="accordion-icon">∨</span>
        </button>
        <div class="accordion-panel">
            <p class="texto-ayuda">Actualiza tu correo de contacto.</p>

            <div class="grupo">
                <label for="usuario_actual">Usuario</label>
                <input
                    type="text"
                    id="usuario_actual"
                    value="<?= htmlspecialchars($_SESSION['user']['username'] ?? '') ?>"
                    readonly>
            </div>

            <div class="grupo">
                <label for="correo_actual">Correo actual</label>
                <input
                    type="email"
                    id="correo_actual"
                    value="<?= htmlspecialchars($_SESSION['user']['correo'] ?? '') ?>"
                    readonly>
            </div>

            <form action="/project-cpr/public/perfil.php?action=update" method="POST">
                <div class="grupo">
                    <label for="nuevo_correo">Nuevo correo electronico</label>
                    <input type="email" id="nuevo_correo" name="nuevo_correo" placeholder="ejemplo@correo.com" required>
                </div>

                <div class="grupo">
                    <label for="confirm_correo">Confirmar nuevo correo</label>
                    <input type="email" id="confirm_correo" name="confirm_correo" placeholder="Repite el correo" required>
                </div>

                <div class="grupo">
                    <label for="actual_contra_correo">Contrasena actual</label>
                    <input type="password" id="actual_contra_correo" name="actual_contra" placeholder="Para confirmar el cambio" required>
                </div>

                <div class="botones left">
                    <button type="submit" class="btn-actualizar">Guardar correo</button>
                </div>
            </form>
        </div>
    </section>

    <section class="perfil-card accordion" data-accordion="seguridad">
        <button class="accordion-toggle" type="button" aria-expanded="false">
            <span>Seguridad</span>
            <span class="accordion-icon">∨</span>
        </button>
        <div class="accordion-panel">
            <p class="texto-ayuda">Cambia tu contrasena para proteger tu cuenta.</p>

            <form action="/project-cpr/public/perfil.php?action=update" method="POST">
                <div class="grupo">
                    <label for="actual_contra_seguridad">Contrasena actual</label>
                    <input type="password" id="actual_contra_seguridad" name="actual_contra" placeholder="Tu contrasena actual" required>
                </div>

                <div class="grupo">
                    <label for="nueva_contra">Nueva contrasena</label>
                    <input type="password" id="nueva_contra" name="nueva_contra" placeholder="Minimo recomendado: 8 caracteres" required>
                </div>

                <div class="grupo">
                    <label for="confirm_contra">Confirmar nueva contrasena</label>
                    <input type="password" id="confirm_contra" name="confirm_contra" placeholder="Repite la nueva contrasena" required>
                </div>

                <div class="botones left">
                    <button type="submit" class="btn-actualizar">Actualizar contrasena</button>
                </div>
            </form>
        </div>
    </section>

    <section class="perfil-card accordion" data-accordion="sesion">
        <button class="accordion-toggle" type="button" aria-expanded="false">
            <span>Sesion</span>
            <span class="accordion-icon">∨</span>
        </button>
        <div class="accordion-panel">
            <p class="texto-ayuda">Salir solo cerrara la sesion en este dispositivo.</p>
            <div class="botones left">
                <a href="/PROJECT-CPR/public/logout.php" class="btn-cerrar">Cerrar sesion</a>
            </div>
        </div>
    </section>
</div>

<script>
    const acordeones = document.querySelectorAll('.accordion');
    const abrirAccordion = (target) => {
        acordeones.forEach(item => {
            const panel = item.querySelector('.accordion-panel');
            const toggle = item.querySelector('.accordion-toggle');
            const activo = item.dataset.accordion === target;
            item.classList.toggle('open', activo);
            if (toggle) toggle.setAttribute('aria-expanded', activo ? 'true' : 'false');
            if (panel) panel.style.display = activo ? 'block' : 'none';
        });
    };

    const cerrarTodos = () => {
        acordeones.forEach(item => {
            const panel = item.querySelector('.accordion-panel');
            const toggle = item.querySelector('.accordion-toggle');
            item.classList.remove('open');
            if (toggle) toggle.setAttribute('aria-expanded', 'false');
            if (panel) panel.style.display = 'none';
        });
    };

    acordeones.forEach(item => {
        const toggle = item.querySelector('.accordion-toggle');
        if (!toggle) return;
        toggle.addEventListener('click', () => {
            if (item.classList.contains('open')) {
                cerrarTodos();
                return;
            }
            abrirAccordion(item.dataset.accordion);
        });
    });

    cerrarTodos();
</script>

    <!-- Script solo para admin: recarga al cambiar el proceso seleccionado -->
    <?php if (isset($_SESSION['user']) && $_SESSION['user']['rol'] == 1): ?>
    <script>
        const selectProceso = document.getElementById('proceso_id');
        if (selectProceso) {
            selectProceso.addEventListener('change', () => {
                selectProceso.form.submit();
            });
        }
    </script>
<?php endif; ?>
