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
        $documento = $_POST['documento'];
        $username  = $_POST['username'];
        $password  = $_POST['password'];
        $rol       = $_POST['rol'];
        $correo    = $_POST['correo'];
        $telefono  = $_POST['telefono'];
        $estado    = $_POST['estado'] ?? 1;

        // Crea el usuario en BD.
        User::create($documento, $username, $password, $rol, $correo, $telefono, $estado);

        header("Location: /project-cpr/public/usuarios.php");
        exit;
    }


    public function update()
    {
        // Datos de edicion (incluye password opcional).
        $id = $_POST['id'];
        $documento = $_POST['documento'];
        $username  = $_POST['username'];
        $rol       = $_POST['rol'];
        $correo    = $_POST['correo'];
        $telefono  = $_POST['telefono'];
        $estado    = $_POST['estado'];
        $password  = $_POST['password'] ?? null;

        // Actualiza en BD y vuelve al listado.
        User::updateById($id, $documento, $username, $rol, $correo, $telefono, $estado, $password);

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
