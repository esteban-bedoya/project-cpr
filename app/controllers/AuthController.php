<?php
// Controlador de autenticacion: valida credenciales y redirige segun rol.

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AuthController
{
    // Envia el enlace de recuperacion por SMTP.
    private function sendResetEmail($correo, $nombreUsuario, $link)
    {
        // Envia el correo usando PHPMailer y Gmail SMTP.
        $mailConfig = require __DIR__ . '/../../config/mail.php';

        if (
            empty($mailConfig['username']) ||
            empty($mailConfig['password']) ||
            $mailConfig['username'] === 'tu_correo@gmail.com' ||
            $mailConfig['password'] === 'tu_contrasena_de_aplicacion'
        ) {
            return false;
        }

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $mailConfig['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $mailConfig['username'];
            $mail->Password = $mailConfig['password'];
            $mail->SMTPSecure = $mailConfig['encryption'] === 'ssl'
                ? PHPMailer::ENCRYPTION_SMTPS
                : PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $mailConfig['port'];
            $mail->CharSet = 'UTF-8';

            $mail->setFrom($mailConfig['from_email'], $mailConfig['from_name']);
            $mail->addAddress($correo, $nombreUsuario);
            $mail->Subject = 'Recuperacion de contraseña - CPR';
            $mail->Body = "Hola $nombreUsuario,\n\nUse este enlace para cambiar su contraseña:\n$link";

            return $mail->send();
        } catch (Exception $e) {
            return false;
        }
    }


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
                    header("Location: /project-cpr/public/casos.php");
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

    public function requestPasswordReset()
    {
        // Procesa la solicitud del correo para recuperar contraseña.
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /project-cpr/public/recuperar_password.php");
            exit;
        }

        $correo = trim($_POST['correo'] ?? '');

        if ($correo === '') {
            $_SESSION['error'] = "Ingrese un correo.";
            header("Location: /project-cpr/public/recuperar_password.php");
            exit;
        }

        $user = User::findByEmail($correo);

        if ($user && (int) $user['estado'] === 1) {
            // El token se guarda antes de enviar el correo.
            $token = bin2hex(random_bytes(32));
            User::saveRememberToken($user['id'], $token);

            $link = sprintf(
                'http://%s/project-cpr/public/cambiar_password.php?token=%s',
                $_SERVER['HTTP_HOST'],
                urlencode($token)
            );

            $sent = $this->sendResetEmail($correo, $user['username'], $link);

            if ($sent) {
                $_SESSION['success'] = "Se envio un enlace de recuperacion al correo registrado.";
            } else {
                User::clearRememberToken($user['id']);
                $_SESSION['error'] = "No fue posible enviar el correo de recuperacion. Intente nuevamente.";
            }
        } else {
            // Respuesta generica para no revelar usuarios validos.
            $_SESSION['success'] = "Si el correo existe, se genero un enlace de recuperacion.";
        }

        header("Location: /project-cpr/public/recuperar_password.php");
        exit;
    }

    public function resetPassword()
    {
        // Cambia la contraseña usando el token del enlace.
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /project-cpr/public/login.php");
            exit;
        }

        $token = trim($_POST['token'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        if ($token === '') {
            $_SESSION['error'] = "El enlace no es valido.";
            header("Location: /project-cpr/public/login.php");
            exit;
        }

        if ($password === '' || $password_confirm === '') {
            $_SESSION['error'] = "Complete todos los campos de la nueva contraseña.";
            header("Location: /project-cpr/public/cambiar_password.php?token=" . urlencode($token));
            exit;
        }

        if ($password !== $password_confirm) {
            $_SESSION['error'] = "Las contraseñas no coinciden.";
            header("Location: /project-cpr/public/cambiar_password.php?token=" . urlencode($token));
            exit;
        }

        // El token funciona como llave temporal.
        $user = User::findByRememberToken($token);

        if (!$user) {
            $_SESSION['error'] = "El enlace ya no es valido o ya fue utilizado.";
            header("Location: /project-cpr/public/login.php");
            exit;
        }

        $updated = User::updatePasswordById($user['id'], $password);

        if ($updated) {
            User::clearRememberToken($user['id']);
            $_SESSION['success'] = "La contraseña se cambio correctamente.";
            header("Location: /project-cpr/public/login.php");
            exit;
        }

        $_SESSION['error'] = "No fue posible cambiar la contraseña.";
        header("Location: /project-cpr/public/login.php");
        exit;
    }
}
