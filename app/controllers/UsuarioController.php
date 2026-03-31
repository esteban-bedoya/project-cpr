<?php

require_once __DIR__ . '/../models/User.php';

class UsuarioController
{

    // ====================================
    // LISTAR USUARIOS
    // ====================================
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

    // ====================================
    // GUARDAR NUEVO USUARIO
    // ====================================
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

    // ====================================
    // ACTUALIZAR USUARIO
    // ====================================
    public function update()
    {
        // TOMAR ID DEL INPUT CORRECTO
        $id = $_POST['id'];

        // CAMPOS EDITABLES
        $documento = $_POST['documento'];
        $username  = $_POST['username'];
        $rol       = $_POST['rol'];
        $correo    = $_POST['correo'];
        $telefono  = $_POST['telefono'];
        $estado    = $_POST['estado'];
        $password  = $_POST['password'] ?? null;

        // LLAMAR AL MODELO CON LA NUEVA FIRMA CORRECTA
        User::updateById($id, $documento, $username, $rol, $correo, $telefono, $estado, $password);


        header("Location: /project-cpr/public/usuarios.php");
        exit;
    }


    // ====================================
    // ELIMINAR
    // ====================================
    public function delete()
    {
        $id = $_GET['id'];
        User::delete($id);

        header("Location: /project-cpr/public/usuarios.php");
        exit;
    }
}
