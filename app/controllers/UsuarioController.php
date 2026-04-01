<?php
// Controlador de usuarios (CRUD) para administracion.

require_once __DIR__ . '/../models/User.php';

class UsuarioController
{
    public function index()
    {
        // Marca la seccion activa en el menu.
        $activePage = 'usuarios';

        // Filtros opcionales desde querystring.
        $filtro_estado = $_GET['filtro_estado'] ?? 'todos';
        $filtro_rol    = $_GET['filtro_rol'] ?? 'todos';

        // Trae usuarios segun filtros.
        $usuarios = User::filtrar($filtro_estado, $filtro_rol);

        include __DIR__ . '/../views/admin/usuarios.php';
    }


    public function store()
    {
        // Captura datos del formulario de creacion.
        $documento = trim($_POST['documento'] ?? '');
        $username  = trim($_POST['username'] ?? '');
        $password  = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        $rol       = $_POST['rol'];
        $correo    = trim($_POST['correo'] ?? '');
        $telefono  = trim($_POST['telefono'] ?? '');
        $estado    = $_POST['estado'] ?? 1;

        $errores = [];

        if ($username === '') {
            $errores[] = "El nombre completo es obligatorio.";
        }
        if ($documento === '') {
            $errores[] = "El documento es obligatorio.";
        }
        if ($password === '') {
            $errores[] = "La contraseña es obligatoria.";
        }
        if ($passwordConfirm === '') {
            $errores[] = "La confirmación de contraseña es obligatoria.";
        }
        if ($password !== '' && $passwordConfirm !== '' && $password !== $passwordConfirm) {
            $errores[] = "Las contraseñas no coinciden.";
        }
        if ($correo !== '' && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errores[] = "El correo no es válido.";
        }
        if (User::find($documento)) {
            $errores[] = "El documento ya está registrado.";
        }
        if ($correo !== '' && User::findByEmail($correo)) {
            $errores[] = "El correo ya está registrado.";
        }

        if (!empty($errores)) {
            $_SESSION['error'] = $errores;
            $_SESSION['old'] = [
                'username' => $username,
                'documento' => $documento,
                'correo' => $correo,
                'telefono' => $telefono,
                'rol' => $rol,
                'estado' => $estado,
            ];
            header("Location: /project-cpr/public/usuarios.php?modal=agregar");
            exit;
        }

        // Crea el usuario en BD.
        User::create($documento, $username, $password, $rol, $correo, $telefono, $estado);

        $_SESSION['success'] = "Usuario creado exitosamente.";
        unset($_SESSION['old']);
        header("Location: /project-cpr/public/usuarios.php");
        exit;
    }


    public function update()
    {
        // Datos de edicion (incluye password opcional).
        $id = $_POST['id'];
        $rol       = $_POST['rol'];
        $correo    = $_POST['correo'];
        $telefono  = $_POST['telefono'];
        $estado    = $_POST['estado'];
        $password  = $_POST['password'] ?? null;
        $passwordConfirm = $_POST['password_confirm'] ?? null;

        if ($password !== null && trim($password) !== '') {
            if ($passwordConfirm === null || $password !== $passwordConfirm) {
                $_SESSION['error'] = "Las contraseñas no coinciden.";
                header("Location: /project-cpr/public/usuarios.php");
                exit;
            }
        }

        // Actualiza en BD y vuelve al listado.
        User::updateById($id, $rol, $correo, $telefono, $estado, $password);

        header("Location: /project-cpr/public/usuarios.php");
        exit;
    }


    public function delete()
    {
        // Elimina el usuario por id recibido.
        $id = $_GET['id'];
        User::delete($id);

        header("Location: /project-cpr/public/usuarios.php");
        exit;
    }
}
