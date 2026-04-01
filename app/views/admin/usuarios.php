<?php $activePage = 'usuarios'; ?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios - CPR</title>

    <!-- Estilos generales -->
    <link rel="stylesheet" href="/project-cpr/public/assets/css/globals/base.css">
    <!-- Estilos específicos -->
    <link rel="stylesheet" href="/project-cpr/public/assets/css/administrador/usuarios.css">
</head>

<body class="private">

    <!-- Header del administrador -->
    <?php include __DIR__ . '/../components/header_administrador.php'; ?>

    <div class="main-content">
        <div class="usuarios-container">

            <!-- ENCABEZADO -->
            <div class="top-actions">
                <button class="btn-agregar" onclick="abrirModalAgregar()">Agregar usuario</button>


                <!-- ===================== -->
                <!-- FILTRO POR ROL        -->
                <!-- ===================== -->
                <div class="filtros" id="filtro-fol">
                    <span class="titulo-filtro">Filtrar rol</span>
                    <label>
                        <input
                            type="radio"
                            name="filtro_rol"
                            value="todos"
                            <?= $filtro_rol === 'todos' ? 'checked' : '' ?>> Todos
                    </label>

                    <label>
                        <input
                            type="radio"
                            name="filtro_rol"
                            value="1"
                            <?= $filtro_rol === '1' ? 'checked' : '' ?>> Administradores
                    </label>

                    <label>
                        <input
                            type="radio"
                            name="filtro_rol"
                            value="2"
                            <?= $filtro_rol === '2' ? 'checked' : '' ?>> Comisionados
                    </label>

                </div>

                <!-- ===================== -->
                <!-- FILTRO POR ESTADO     -->
                <!-- ===================== -->
                <div class="filtros" id="filtro-estado">
                    <span class="titulo-filtro">Filtrar estado</span>
                    <label>
                        <input
                            type="radio"
                            name="filtro_estado"
                            value="activos"
                            <?= $filtro_estado === 'activos' ? 'checked' : '' ?>> Activos
                    </label>

                    <label>
                        <input
                            type="radio"
                            name="filtro_estado"
                            value="inactivos"
                            <?= $filtro_estado === 'inactivos' ? 'checked' : '' ?>> Inactivos
                    </label>

                    <label>
                        <input
                            type="radio"
                            name="filtro_estado"
                            value="todos"
                            <?= $filtro_estado === 'todos' ? 'checked' : '' ?>> Todos
                    </label>

                </div>
            </div>

            <!-- Buscador (visual) -->
            <div class="buscador">
                <span class="icon">🔍</span>
                <input type="text" placeholder="Buscar">
            </div>

            <!-- TABLA DE USUARIOS -->
            <table class="tabla-usuarios">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Documento</th>
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
                            <td><?= htmlspecialchars($usuario['documento']) ?></td>
                            <td><?= $usuario['estado'] == 1 ? 'Activo' : 'Inactivo' ?></td>
                            <td><?= htmlspecialchars($usuario['correo']) ?></td>
                            <td><?= htmlspecialchars($usuario['telefono']) ?></td>

                            <!-- Nunca se muestra la contraseña -->
                            <td>*****</td>

                            <td>
                                <?php
                                switch ($usuario['rol']) {
                                    case 1:
                                        echo 'Administrador';
                                        break;
                                    case 2:
                                        echo 'Comisionado';
                                        break;
                                    case 3:
                                        echo 'Super Admin';
                                        break;
                                    default:
                                        echo 'Desconocido';
                                }
                                ?>
                            </td>

                            <td class="acciones">
                                <span class="editar" onclick="abrirModalEditar(
                                '<?= $usuario['id'] ?>', 
                                '<?= addslashes($usuario['username']) ?>',
                                '<?= addslashes($usuario['documento']) ?>',
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



    <!-- =========================================================== -->
    <!-- ================= MODAL AGREGAR USUARIO ==================== -->
    <!-- =========================================================== -->
    <div class="modal" id="modal-agregar">
        <div class="modal-content">
            <h3>Agregar usuario</h3>

            <form action="/project-cpr/public/usuarios.php?action=store" method="POST">

                <label>Nombre completo:</label>
                <input type="text" name="username" required>

                <label>Documento:</label>
                <input type="text" name="documento" required>

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



    <!-- =========================================================== -->
    <!-- ================== MODAL EDITAR USUARIO ==================== -->
    <!-- =========================================================== -->
    <div class="modal" id="modal-editar">
        <div class="modal-content">
            <h3>Editar usuario</h3>

            <form action="/project-cpr/public/usuarios.php?action=update" method="POST">

                <!-- ID interno oculto (PK autoincrement) -->
                <input type="hidden" name="id" id="edit-id">

                <label>Nombre completo</label>
                <input type="text" name="username" id="edit-username" required>

                <label>Documento</label>
                <input type="text" name="documento" id="edit-documento" required>

                <label>Correo</label>
                <input type="email" name="correo" id="edit-correo">

                <label>Teléfono</label>
                <input type="text" name="telefono" id="edit-telefono">

                <label>Rol</label>
                <select name="rol" id="edit-rol">
                    <option value="1">Administrador</option>
                    <option value="2">Comisionado</option>
                    <option value="3">Super Admin</option>
                </select>

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



    <!-- =========================================================== -->
    <!-- ======================= JS MODALES ========================= -->
    <!-- =========================================================== -->
    <script>
        // Modal agregar
        const modalAgregar = document.getElementById("modal-agregar");

        function abrirModalAgregar() {
            modalAgregar.style.display = "flex";
        }

        function cerrarModalAgregar() {
            modalAgregar.style.display = "none";
        }

        // Modal editar
        const modalEditar = document.getElementById("modal-editar");

        function abrirModalEditar(id, username, documento, rol, correo, telefono, estado) {

            document.getElementById("edit-id").value = id;
            document.getElementById("edit-username").value = username;
            document.getElementById("edit-documento").value = documento;
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

    <script>
        // ============================
        // FILTROS DINÁMICOS
        // ============================

        const radios = document.querySelectorAll(
            'input[name="filtro_estado"], input[name="filtro_rol"]'
        );

        radios.forEach(radio => {
            radio.addEventListener("change", () => {

                const estado = document.querySelector('input[name="filtro_estado"]:checked').value;
                const rol = document.querySelector('input[name="filtro_rol"]:checked').value;

                const nuevaURL = `usuarios.php?filtro_estado=${estado}&filtro_rol=${rol}`;
                window.location.href = nuevaURL;
            });
        });
    </script>

    <script>
        // ============================
        // BUSCADOR EN VIVO
        // ============================

        const buscadorInput = document.querySelector('.buscador input');
        const filas = document.querySelectorAll('.tabla-usuarios tbody tr');

        buscadorInput.addEventListener('input', () => {
            const texto = buscadorInput.value.toLowerCase().trim();

            filas.forEach(fila => {
                const contenidoFila = fila.textContent.toLowerCase();

                // Si la fila contiene el texto -> se muestra
                if (contenidoFila.includes(texto)) {
                    fila.style.display = "";
                } else {
                    fila.style.display = "none";
                }
            });
        });
    </script>




</body>

</html>