<!-- Componente: perfil de usuario (datos y configuracion) -->
<link rel="stylesheet" href="/PROJECT-CPR/public/assets/css/globals/perfil.css">

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

    <hr class="divisor">





    <h2 class="titulo-seccion">Cambiar correo</h2>


    <!-- Formulario principal de actualizacion de perfil -->
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
