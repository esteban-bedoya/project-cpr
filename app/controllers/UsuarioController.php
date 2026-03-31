<?php

require_once __DIR__ . '/../models/User.php';

class UsuarioController
{

    // Listar todos los usuarios
    public function index()
    {
        $usuarios = User::all();
        __DIR__ . '/../views/admin/usuarios.php';
    }

    // Mostrar formulario de creación
    public function create()
    {
        include __DIR__ . '/../views/admin/usuarios.php';
    }

    // Guardar nuevo usuario
    public function store()
    {
        $id = $_POST['id'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $rol = $_POST['rol'];
        $correo = $_POST['correo'];
        $telefono = $_POST['telefono'];
        $estado = $_POST['estado'] ?? 1; // Si no envían estado, por defecto activo
        

        User::create($id, $username, $password, $rol, $correo, $telefono, $estado);


        header("Location: /project-cpr/public/usuarios.php");
        exit;
    }

    // Mostrar formulario de edición
    public function edit()
    {
        $id = $_GET['id'];
        $usuario = User::find($id);

        if (!$usuario) {
            echo "Usuario no encontrado";
            exit;
        }

        include __DIR__ . '/../views/admin/usuarios.php';
    }

    // Actualizar usuario existente
    public function update()
    {
        $id = $_POST['id'];
        $username = $_POST['username'];
        $rol = $_POST['rol'];
        $correo = $_POST['correo'];
        $telefono = $_POST['telefono'];
        $estado = $_POST['estado'];

        User::update($id, $username, $rol, $correo, $telefono, $estado);

        header("Location: /project-cpr/public/usuarios.php");
        exit;
    }

    // Eliminar usuario
    public function delete()
    {
        $id = $_GET['id'];
        User::delete($id);

        header("Location: /project-cpr/public/usuarios.php");
        exit;
    }
}
