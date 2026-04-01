<?php

session_start();

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {

    public function login() {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /project-cpr/public/login.php");
            exit;
        }

        if (!isset($_POST['correo'], $_POST['password'])) {
            echo "POST incompleto";
            exit;
        }

        $correo = $_POST['correo'];
        $password = $_POST['password'];

        $user = User::findByEmail($correo);

        if ($user && password_verify($password, $user['password'])) {

            if ($user['estado'] != 1) {
                $_SESSION['error'] = "Usuario inactivo";
                header("Location: /project-cpr/public/login.php");
                exit;
            }

            $_SESSION['logged'] = true;
            $_SESSION['user'] = $user;

            switch ($user['rol']) {
                case 1:
                    header("Location: /project-cpr/public/usuarios.php");
                    break;
                case 2:
                    header("Location: /project-cpr/public/gestionar.php");
                    break;
                default:
                    header("Location: /project-cpr/public/index.php");
            }
            exit;
        }

        $_SESSION['error'] = "Credenciales incorrectas";
        header("Location: /project-cpr/public/login.php");
        exit;
    }
}