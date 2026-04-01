<?php
// Controlador de autenticacion: valida credenciales y redirige segun rol.

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../models/User.php';

class AuthController
{

    public function login()
    {
        // Solo se permite acceso por POST desde el formulario de login.
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /project-cpr/public/login.php");
            exit;
        }

        // Validacion basica de campos obligatorios.
        if (!isset($_POST['correo'], $_POST['password'])) {
            echo "POST incompleto";
            exit;
        }

        // Se capturan los datos del formulario.
        $correo = $_POST['correo'];
        $password = $_POST['password'];

        // Se consulta el usuario por correo.
        $user = User::findByEmail($correo);

        // Verifica existencia de usuario y compara hash de password.
        if ($user && password_verify($password, $user['password'])) {


            // Limpiar los mensajes emergentes
            unset($_SESSION['error']);
            unset($_SESSION['success']);

            // Si el usuario esta inactivo se bloquea el acceso.
            if ($user['estado'] != 1) {
                $_SESSION['error'] = "Usuario inactivo";
                header("Location: /project-cpr/public/login.php");
                exit;
            }

            // Se marca la sesion como iniciada y se guarda el usuario.
            $_SESSION['logged'] = true;
            $_SESSION['user'] = $user;

            // Redireccion segun el rol definido en la base de datos.
            switch ($user['rol']) {
                case 1:
                    header("Location: /project-cpr/public/reportes.php");
                    break;
                case 2:
                    header("Location: /project-cpr/public/gestionar.php");
                    break;
                default:
                    header("Location: /project-cpr/public/index.php");
            }
            exit;
        }

        // Credenciales invalidas: se informa y se vuelve al login.
        $_SESSION['error'] = "Credenciales incorrectas";
        header("Location: /project-cpr/public/login.php");
        exit;
    }
}
