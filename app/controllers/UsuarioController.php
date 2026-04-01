<?php

require_once __DIR__ . '/../models/User.php';

class UsuarioController
{
    public function index()
    {
        // 1️⃣ Obtener filtros desde GET
        $filtro_estado = $_GET['filtro_estado'] ?? 'todos';
        $filtro_rol    = $_GET['filtro_rol'] ?? 'todos';

        // 2️⃣ Obtener usuarios (AQUÍ está la clave)
        $usuarios = User::filtrar($filtro_estado, $filtro_rol);

        // 3️⃣ Variables para la vista
        $activePage = 'usuarios';

        // 4️⃣ Cargar vista
        include __DIR__ . '/../views/admin/usuarios.php';
    }


    public function store()
    {
        $documento = $_POST['documento'];
        $username  = $_POST['username'];
        $password  = $_POST['password'];
        $rol       = $_POST['rol'];
        $correo    = $_POST['correo'];
        $telefono  = $_POST['telefono'];
        $estado    = $_POST['estado'] ?? 1;

        User::create($documento, $username, $password, $rol, $correo, $telefono, $estado);

        header("Location: /project-cpr/public/usuarios.php");
        exit;
    }


    public function update()
    {
        $id = $_POST['id'];
        $documento = $_POST['documento'];
        $username  = $_POST['username'];
        $rol       = $_POST['rol'];
        $correo    = $_POST['correo'];
        $telefono  = $_POST['telefono'];
        $estado    = $_POST['estado'];
        $password  = $_POST['password'] ?? null;

        User::updateById($id, $documento, $username, $rol, $correo, $telefono, $estado, $password);

        header("Location: /project-cpr/public/usuarios.php");
        exit;
    }


    public function delete()
    {
        $id = $_GET['id'];
        User::delete($id);

        header("Location: /project-cpr/public/usuarios.php");
        exit;
    }
}
