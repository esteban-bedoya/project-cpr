<?php

var_dump($_POST);

/* Mostrar errores (temporal) */
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "INICIO AUTH CONTROLLER<br>";

session_start();

/* Importar archivos necesarios */
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {

    public function login() {

        echo "ENTRÉ A LOGIN()<br>";

        if (!isset($_POST['correo']) || !isset($_POST['password'])) {
            echo "NO LLEGÓ POST<br>";
            exit;
        }

        $correo = $_POST['correo'];
        $password = $_POST['password'];

        echo "POST RECIBIDO:<br>";
        var_dump($_POST);
        echo "<br>";

        /* Buscar usuario por correo */
        $user = User::findByEmail($correo);

        echo "USER EN BASE:<br>";
        var_dump($user);
        echo "<br>";

        /* Validar usuario y contraseña */
        if ($user && password_verify($password, $user['password'])) {

            /* Verificar si el usuario está activo */
            if ($user['estado'] != 1) {
                echo "USUARIO INACTIVO<br>";
                $_SESSION['error'] = "Usuario inactivo";
                header("Location: /project-cpr/public/login.php");
                exit;
            }

            echo "USUARIO CORRECTO<br>";

            $_SESSION['logged'] = true;
            $_SESSION['user'] = $user;

            /* Redirección según rol */
            switch ($user['rol']) {
                case 1:
                    header("Location: /project-cpr/app/views/admin/reportes.php");
                    exit;
                case 2:
                    header("Location: /project-cpr/app/views/comisionado/gestionar.php");
                    exit;
                case 3:
                    header("Location: /project-cpr/app/views/super_admin/perfil.php");
                    exit;
                default:
                    echo "ROL DESCONOCIDO<br>";
                    exit;
            }
        }

        echo "PASÉ EL IF → CREDENCIALES INCORRECTAS<br>";

        $_SESSION['error'] = "Credenciales incorrectas";
        header("Location: /project-cpr/public/login.php");
        exit;
    }
}

/* ========================================================= */
/* FRONT CONTROLLER */
/* ========================================================= */

$controller = new AuthController();
$action = $_GET['action'] ?? null;

echo "ACTION: " . $action . "<br>";

if ($action === 'login') {
    $controller->login();
} else {
    echo "ACTION NO DEFINIDA<br>";
}
