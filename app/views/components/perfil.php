<!-- Componente: perfil de usuario -->
<link rel="stylesheet" href="/PROJECT-CPR/public/assets/css/globals/perfil.css">

<?php
// Ayuda a resaltar el bloque que dio error.
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

<div class="perfil-container">

    <!-- Alertas del ultimo intento -->
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

    <!-- Bloque para cambio de correo -->
    <section class="perfil-card accordion" data-accordion="cuenta">
        <button class="accordion-toggle" type="button" aria-expanded="false">
            <span>Información de la cuenta</span>
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
                    <label for="nuevo_correo">Nuevo correo electrónico</label>
                    <input type="email" id="nuevo_correo" name="nuevo_correo" placeholder="ejemplo@correo.com" required>
                </div>

                <div class="grupo">
                    <label for="confirm_correo">Confirmar nuevo correo</label>
                    <input type="email" id="confirm_correo" name="confirm_correo" placeholder="Repite el correo" required>
                </div>

                <div class="grupo">
                    <label for="actual_contra_correo">Contraseña actual</label>
                    <input type="password" id="actual_contra_correo" name="actual_contra" placeholder="Para confirmar el cambio" required>
                </div>

                <div class="botones left">
                    <button type="submit" class="btn-actualizar">Guardar correo</button>
                </div>
            </form>
        </div>
    </section>

    <!-- Bloque para cambio de contraseña -->
    <section class="perfil-card accordion" data-accordion="seguridad">
        <button class="accordion-toggle" type="button" aria-expanded="false">
            <span>Seguridad</span>
            <span class="accordion-icon">∨</span>
        </button>
        <div class="accordion-panel">
            <p class="texto-ayuda">Cambia tu contraseña para proteger tu cuenta.</p>

            <form action="/project-cpr/public/perfil.php?action=update" method="POST">
                <div class="grupo">
                    <label for="actual_contra_seguridad">Contraseña actual</label>
                    <input type="password" id="actual_contra_seguridad" name="actual_contra" placeholder="Tu contraseña actual" required>
                </div>

                <div class="grupo">
                    <label for="nueva_contra">Nueva contraseña</label>
                    <input type="password" id="nueva_contra" name="nueva_contra" placeholder="Mínimo recomendado: 8 caracteres" required>
                </div>

                <div class="grupo">
                    <label for="confirm_contra">Confirmar nueva contraseña</label>
                    <input type="password" id="confirm_contra" name="confirm_contra" placeholder="Repite la nueva contraseña" required>
                </div>

                <div class="botones left">
                    <button type="submit" class="btn-actualizar">Actualizar contraseña</button>
                </div>
            </form>
        </div>
    </section>

    <!-- Bloque para cierre de sesion -->
    <section class="perfil-card accordion" data-accordion="sesion">
        <button class="accordion-toggle" type="button" aria-expanded="false">
            <span>Sesión</span>
            <span class="accordion-icon">∨</span>
        </button>
        <div class="accordion-panel">
            <p class="texto-ayuda">Salir solo cerrará la sesión en este dispositivo.</p>
            <div class="botones left">
                <a href="/PROJECT-CPR/public/logout.php" class="btn-cerrar">Cerrar sesión</a>
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
