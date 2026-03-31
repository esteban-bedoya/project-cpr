<?php

require_once __DIR__ . '/../models/User.php';

class UsuarioController
{
    public function index()
    {
        session_start();

        if (!isset($_SESSION['logged']) || $_SESSION['user']['rol'] != 1) {
            header("Location: /project-cpr/public/login.php");
            exit;
        }

        $usuarios = User::all();
        $activePage = 'usuarios';

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
