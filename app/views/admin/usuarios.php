<?php 
// ==============================================
// usuarios.php - Vista principal de usuarios
// ==============================================

// Página activa (puede ser usado en el header para resaltar menú)
$activePage = 'usuarios';



// Importar modelo de usuario
require_once __DIR__ . '/../../models/User.php';

// Traer todos los usuarios de la base de datos
$usuarios = User::all();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios - CPR</title>

    <!-- Estilos generales -->
    <link rel="stylesheet" href="/project-cpr/public/assets/css/globals/base.css">
    <!-- Estilos específicos de esta página -->
    <link rel="stylesheet" href="/project-cpr/public/assets/css/administrador/usuarios.css">
</head>
<body class="private">

<!-- Header del administrador -->
<?php include __DIR__ . '/../components/header_administrador.php'; ?>


<div class="main-content">
    <div class="usuarios-container">

        <!-- Botón agregar usuario y filtros -->
        <div class="top-actions">
            <button class="btn-agregar" onclick="abrirModalAgregar()">Agregar usuario</button>
            <div class="filtros">
                <span class="titulo-filtro">Filtrar</span>
                <label><input type="radio" name="filtro" checked> Activos</label>
                <label><input type="radio" name="filtro"> Inactivos</label>
                <label><input type="radio" name="filtro"> Todos</label>
            </div>
        </div>

        <!-- Buscador (solo visual, no funcional aún) -->
        <div class="buscador">
            <span class="icon">🔍</span>
            <input type="text" placeholder="Buscar">
        </div>

        <!-- Tabla de usuarios -->
        <table class="tabla-usuarios">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>ID</th>
                    <th>Estado</th>
                    <th>Correo</th>
                    <th>Teléfono</th>
                    <th>Contraseña</th>
                    <th>Rol</th>
                    <th>Actualizar</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?= htmlspecialchars($usuario['username']) ?></td>
                        <td><?= htmlspecialchars($usuario['id']) ?></td>
                        <td><?= $usuario['estado'] == 1 ? 'Activo' : 'Inactivo' ?></td>
                        <td><?= htmlspecialchars($usuario['correo']) ?></td>
                        <td><?= htmlspecialchars($usuario['telefono']) ?></td>
                        <td>*****</td>
                        <td>
                            <?php
                                switch ($usuario['rol']) {
                                    case 1: echo 'Administrador'; break;
                                    case 2: echo 'Comisionado'; break;
                                    case 3: echo 'Super Admin'; break;
                                    default: echo 'Desconocido';
                                }
                            ?>
                        </td>
                        <td class="acciones">
                            <!-- Botón editar: al hacer click llama a JS que llena modal -->
                            <span class="editar" onclick="abrirModalEditar(
                                '<?= $usuario['id'] ?>',
                                '<?= addslashes($usuario['username']) ?>',
                                '<?= $usuario['rol'] ?>',
                                '<?= addslashes($usuario['correo']) ?>',
                                '<?= $usuario['telefono'] ?>',
                                '<?= $usuario['estado'] ?>'
                            )">Editar</span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ================= MODAL AGREGAR USUARIO ================= -->
<div class="modal" id="modal-agregar">
    <div class="modal-content">
        <h3>Agregar usuario</h3>

        <!-- Formulario que envía los datos al controller -->
        <form action="/project-cpr/public/usuarios.php?action=store" method="POST">
            <label>Nombre completo:</label>
            <input type="text" name="username" required>

            <label>ID:</label>
            <input type="text" name="id" required>

            <label>Correo:</label>
            <input type="email" name="correo">

            <label>Teléfono:</label>
            <input type="text" name="telefono">

            <label>Contraseña:</label>
            <input type="password" name="password" required>

            <label>Rol:</label>
            <select name="rol">
                <option value="1">Administrador</option>
                <option value="2" selected>Comisionado</option>
                <option value="3">Super Admin</option>
            </select>

            <label>Estado:</label>
            <select name="estado">
                <option value="1" selected>Activo</option>
                <option value="2">Inactivo</option>
            </select>

            <div class="modal-buttons">
                <button type="submit" class="btn-guardar">Agregar</button>
                <button type="button" class="btn-cerrar" onclick="cerrarModalAgregar()">Cerrar</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL EDITAR USUARIO ================= -->
<div class="modal" id="modal-editar">
    <div class="modal-content">
        <h3>Editar usuario</h3>

        <!-- Formulario que envía datos al controller -->
        <form action="/project-cpr/public/usuarios.php?action=update" method="POST">
            <input type="hidden" name="id" id="edit-id">

            <label>Nombre completo</label>
            <input type="text" name="username" id="edit-username" required>

            <label>Rol</label>
            <select name="rol" id="edit-rol">
                <option value="1">Administrador</option>
                <option value="2">Comisionado</option>
                <option value="3">Super Admin</option>
            </select>

            <label>Correo</label>
            <input type="email" name="correo" id="edit-correo">

            <label>Teléfono</label>
            <input type="text" name="telefono" id="edit-telefono">

            <label>Estado</label>
            <select name="estado" id="edit-estado">
                <option value="1">Activo</option>
                <option value="2">Inactivo</option>
            </select>

            <label>Nueva contraseña</label>
            <input type="password" name="password">

            <div class="modal-buttons">
                <button type="submit" class="btn-guardar">Guardar</button>
                <button type="button" class="btn-cerrar" onclick="cerrarModalEditar()">Cerrar</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= JS BÁSICO PARA MODALES ================= -->
<script>
    // -------- MODAL AGREGAR --------
    const modalAgregar = document.getElementById("modal-agregar");
    function abrirModalAgregar() {
        modalAgregar.style.display = "flex";
    }
    function cerrarModalAgregar() {
        modalAgregar.style.display = "none";
    }

    // -------- MODAL EDITAR --------
    const modalEditar = document.getElementById("modal-editar");
    function abrirModalEditar(id, username, rol, correo, telefono, estado) {
        document.getElementById("edit-id").value = id;
        document.getElementById("edit-username").value = username;
        document.getElementById("edit-rol").value = rol;
        document.getElementById("edit-correo").value = correo;
        document.getElementById("edit-telefono").value = telefono;
        document.getElementById("edit-estado").value = estado;
        modalEditar.style.display = "flex";
    }
    function cerrarModalEditar() {
        modalEditar.style.display = "none";
    }
</script>

</body>
</html>