<link rel="stylesheet" href="/PROJECT-CPR/public/assets/css/globals/perfil.css">

<!-- Todo tu contenido de perfil.php -->
<div class="perfil-container">

    <h2 class="titulo-seccion">Cambiar correo</h2>

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

    <form id="form-perfil" action="/project-cpr/public/perfil.php?action=update" method="POST">

        <!-- CAMBIAR CORREO -->
        <div class="grupo">
            <label for="nuevo_correo" class="oculto">Nuevo correo</label>
            <input type="email" id="nuevo_correo" name="nuevo_correo" placeholder="Ingrese nuevo correo">
        </div>

        <div class="grupo">
            <label for="confirm_correo" class="oculto">Confirmar correo</label>
            <input type="email" id="confirm_correo" name="confirm_correo" placeholder="Confirme nuevo correo">
        </div>

        <hr class="divisor">

        <!-- CAMBIAR CONTRASEÑA -->
        <h2 class="titulo-seccion">Cambiar contraseña</h2>

        <div class="grupo">
            <label for="nueva_contra" class="oculto">Nueva contraseña</label>
            <input type="password" id="nueva_contra" name="nueva_contra" placeholder="Ingrese nueva contraseña">
        </div>

        <div class="grupo">
            <label for="confirm_contra" class="oculto">Confirmar contraseña</label>
            <input type="password" id="confirm_contra" name="confirm_contra" placeholder="Confirme nueva contraseña">
        </div>

        <hr class="divisor">

        <!-- CONTRASEÑA ACTUAL (OBLIGATORIA SIEMPRE) -->
        <div class="grupo">
            <label for="actual_contra" class="oculto">Contraseña actual</label>
            <input type="password" id="actual_contra" name="actual_contra" placeholder="Ingrese contraseña actual" required>
        </div>

        <!-- BOTONES -->
        <div class="botones">
            <button type="submit" class="btn-actualizar">Actualizar</button>
            <a href="/PROJECT-CPR/public/logout.php" class="btn-cerrar">Cerrar sesión</a>
        </div>

    </form>
</div>