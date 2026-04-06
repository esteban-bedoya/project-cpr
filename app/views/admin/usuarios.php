<!-- Vista de administracion de usuarios (tabla + modales) -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios - CPR</title>
    <?php include __DIR__ . '/../components/favicon.php'; ?>

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
            <!-- Mensajes de resultado -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert success">
                    <?= htmlspecialchars($_SESSION['success']); ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert error">
                    <?php if (is_array($_SESSION['error'])): ?>
                        <ul>
                            <?php foreach ($_SESSION['error'] as $mensaje): ?>
                                <li><?= htmlspecialchars($mensaje) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <?= htmlspecialchars($_SESSION['error']); ?>
                    <?php endif; ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- Acciones y filtros -->
            <div class="top-actions">
                <button class="btn-agregar" onclick="abrirModalAgregar()">Agregar usuario</button>

                <div class="filters-layout">
                    <div class="filters-row">
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

                    <div class="filters-row filters-row-bottom">
                        <div class="filtros" id="filtro-vigencia">
                            <label for="filtro-vigencia-inicio" class="titulo-filtro">Filtrar inicio de vigencia</label>
                            <select id="filtro-vigencia-inicio" name="filtro_vigencia_inicio">
                                <option value="todas" <?= ($filtro_vigencia_inicio ?? 'todas') === 'todas' ? 'selected' : '' ?>>Todas</option>
                                <?php foreach ($vigenciasInicio as $vigenciaInicio): ?>
                                    <option value="<?= htmlspecialchars((string)$vigenciaInicio) ?>" <?= (string)($filtro_vigencia_inicio ?? 'todas') === (string)$vigenciaInicio ? 'selected' : '' ?>>
                                        <?= htmlspecialchars((string)$vigenciaInicio) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Buscador (visual) -->
            <div class="buscador">
                <span class="icon">🔍</span>
                <input type="text" placeholder="Buscar por nombre, documento, correo o teléfono...">
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
                        <th>Rol</th>
                        <th>Vigencia</th>
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
                            <td>
                                <?php if ((int)$usuario['rol'] === 2 && !empty($usuario['vigencia_inicio'])): ?>
                                    <?= htmlspecialchars($usuario['vigencia_inicio']) ?>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>

                            <td class="acciones">
                                <span class="editar" onclick="abrirModalEditar(
                                '<?= $usuario['id'] ?>', 
                                '<?= addslashes($usuario['username']) ?>',
                                '<?= addslashes($usuario['documento']) ?>',
                                '<?= $usuario['rol'] ?>',
                                '<?= addslashes($usuario['correo']) ?>',
                                '<?= $usuario['telefono'] ?>',
                                '<?= $usuario['estado'] ?>',
                                '<?= htmlspecialchars((string)($usuario['vigencia_inicio'] ?? '')) ?>'
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

            <?php $old = $_SESSION['old'] ?? []; ?>
            <form action="/project-cpr/public/usuarios.php?action=store" method="POST" id="form-agregar">

                <label>Nombre completo</label>
                <input type="text" name="username" value="<?= htmlspecialchars($old['username'] ?? '') ?>" required>

                <label>Documento</label>
                <input type="text" name="documento" value="<?= htmlspecialchars($old['documento'] ?? '') ?>" required>

                <label>Correo</label>
                <input type="email" name="correo" value="<?= htmlspecialchars($old['correo'] ?? '') ?>">

                <label>Teléfono</label>
                <input type="text" name="telefono" value="<?= htmlspecialchars($old['telefono'] ?? '') ?>">

                <label>Contraseña</label>
                <input type="password" name="password" id="add-password" required>

                <label>Confirmar contraseña</label>
                <input type="password" name="password_confirm" id="add-password-confirm" required>

                <label>Rol</label>
                <select name="rol" id="add-rol">
                    <option value="" <?= !isset($old['rol']) ? 'selected' : '' ?> disabled>Seleccione un rol</option>
                    <option value="1" <?= (($old['rol'] ?? '') === '1') ? 'selected' : '' ?>>Administrador</option>
                    <option value="2" <?= (($old['rol'] ?? '') === '2') ? 'selected' : '' ?>>Comisionado</option>
                </select>

                <div id="add-vigencia-group">
                    <label>Año de inicio de vigencia</label>
                    <input type="number" name="vigencia_inicio" id="add-vigencia-inicio" min="2000" max="2100" value="<?= htmlspecialchars($old['vigencia_inicio'] ?? '') ?>" placeholder="2026">
                </div>

                <label>Estado</label>
                <select name="estado">
                    <option value="1" <?= (($old['estado'] ?? '1') === '1') ? 'selected' : '' ?>>Activo</option>
                    <option value="2" <?= (($old['estado'] ?? '1') === '2') ? 'selected' : '' ?>>Inactivo</option>
                </select>

                <div class="modal-buttons">
                    <button type="submit" class="btn-guardar">Agregar</button>
                    <button type="button" class="btn-cerrar" onclick="cerrarModalAgregar()">Cerrar</button>
                </div>
            </form>
            <?php unset($_SESSION['old']); ?>
        </div>
    </div>



    <!-- =========================================================== -->
    <!-- ================== MODAL EDITAR USUARIO ==================== -->
    <!-- =========================================================== -->
    <div class="modal" id="modal-editar">
        <div class="modal-content">
            <h3>Editar usuario</h3>

            <form action="/project-cpr/public/usuarios.php?action=update" method="POST" id="form-editar">

                <!-- ID interno oculto (PK autoincrement) -->
                <input type="hidden" name="id" id="edit-id">

                <label>Nombre completo</label>
                <input type="text" id="edit-username" disabled>

                <label>Documento</label>
                <input type="text" id="edit-documento" disabled>

                <label>Correo</label>
                <input type="email" name="correo" id="edit-correo">

                <label>Teléfono</label>
                <input type="text" name="telefono" id="edit-telefono">

                <label>Rol</label>
                <select name="rol" id="edit-rol">
                    <option value="1">Administrador</option>
                    <option value="2">Comisionado</option>
                </select>

                <div id="edit-vigencia-group">
                    <label>Año de inicio de vigencia</label>
                    <input type="number" name="vigencia_inicio" id="edit-vigencia-inicio" min="2000" max="2100" placeholder="2026">
                </div>

                <label>Estado</label>
                <select name="estado" id="edit-estado">
                    <option value="1">Activo</option>
                    <option value="2">Inactivo</option>
                </select>

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
        const addRol = document.getElementById("add-rol");
        const addVigenciaGroup = document.getElementById("add-vigencia-group");
        const addVigenciaInicio = document.getElementById("add-vigencia-inicio");

        function abrirModalAgregar() {
            modalAgregar.style.display = "flex";
        }

        function cerrarModalAgregar() {
            modalAgregar.style.display = "none";
        }

        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('modal') === 'agregar') {
            abrirModalAgregar();
        }

        function actualizarVigenciaAgregar() {
            if (!addRol || !addVigenciaGroup || !addVigenciaInicio) return;
            const esComisionado = addRol.value === '2';
            addVigenciaGroup.style.display = esComisionado ? 'block' : 'none';
            addVigenciaInicio.required = esComisionado;

            if (!esComisionado) {
                addVigenciaInicio.value = '';
            }
        }

        if (addRol) {
            addRol.addEventListener('change', actualizarVigenciaAgregar);
            actualizarVigenciaAgregar();
        }

        // Validacion de contrasena en registro
        const formAgregar = document.getElementById("form-agregar");
        formAgregar.addEventListener("submit", (e) => {
            const pass = document.getElementById("add-password").value.trim();
            const passConfirm = document.getElementById("add-password-confirm").value.trim();

            if (pass !== passConfirm) {
                e.preventDefault();
                alert("Las contraseñas no coinciden.");
            }
        });

        // Modal editar
        const modalEditar = document.getElementById("modal-editar");
        const editRol = document.getElementById("edit-rol");
        const editVigenciaGroup = document.getElementById("edit-vigencia-group");

        function actualizarVigenciaEditar() {
            if (!editRol || !editVigenciaGroup) return;
            editVigenciaGroup.style.display = editRol.value === '2' ? 'block' : 'none';
        }

        function abrirModalEditar(id, username, documento, rol, correo, telefono, estado, vigenciaInicio) {

            document.getElementById("edit-id").value = id;
            document.getElementById("edit-username").value = username;
            document.getElementById("edit-documento").value = documento;
            document.getElementById("edit-rol").value = rol;
            document.getElementById("edit-correo").value = correo;
            document.getElementById("edit-telefono").value = telefono;
            document.getElementById("edit-estado").value = estado;
            document.getElementById("edit-vigencia-inicio").value = vigenciaInicio;
            actualizarVigenciaEditar();

            modalEditar.style.display = "flex";
        }

        function cerrarModalEditar() {
            modalEditar.style.display = "none";
        }

        if (editRol) {
            editRol.addEventListener('change', actualizarVigenciaEditar);
            actualizarVigenciaEditar();
        }
    </script>

    <script>
        // ============================
        // FILTROS DINÁMICOS
        // ============================

        const radios = document.querySelectorAll(
            'input[name="filtro_estado"], input[name="filtro_rol"]'
        );
        const filtroVigenciaInicio = document.getElementById('filtro-vigencia-inicio');

        function aplicarFiltrosUsuarios() {
            const estado = document.querySelector('input[name="filtro_estado"]:checked').value;
            const rol = document.querySelector('input[name="filtro_rol"]:checked').value;
            const vigenciaInicio = filtroVigenciaInicio ? filtroVigenciaInicio.value : 'todas';

            const nuevaURL = `usuarios.php?filtro_estado=${estado}&filtro_rol=${rol}&filtro_vigencia_inicio=${encodeURIComponent(vigenciaInicio)}`;
            window.location.href = nuevaURL;
        }

        radios.forEach(radio => {
            radio.addEventListener("change", aplicarFiltrosUsuarios);
        });

        if (filtroVigenciaInicio) {
            filtroVigenciaInicio.addEventListener('change', aplicarFiltrosUsuarios);
        }
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
