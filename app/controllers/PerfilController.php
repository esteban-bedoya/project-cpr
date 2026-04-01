<?php

require_once __DIR__ . '/../models/User.php';

class PerfilController
{
    public function index()
    {
        $activePage = 'perfil';

        // Vista según rol
        if ($_SESSION['user']['rol'] == 1) {
            include __DIR__ . '/../views/admin/perfil.php';
        } else {
            include __DIR__ . '/../views/comisionado/perfil.php';
        }
    }

    public function update()
    {
        session_start();

        $idUsuario = $_SESSION['user']['id'];

        $nuevoCorreo   = $_POST['nuevo_correo'] ?? null;
        $confirmCorreo = $_POST['confirm_correo'] ?? null;

        $nuevaContra   = $_POST['nueva_contra'] ?? null;
        $confirmContra = $_POST['confirm_contra'] ?? null;

        $actualContra  = $_POST['actual_contra'];

        $usuario = User::findById($idUsuario);

        // 1️⃣ Validar contraseña actual
        if (!password_verify($actualContra, $usuario['password'])) {
            $_SESSION['error'] = "Contraseña actual incorrecta";
            header("Location: /project-cpr/public/perfil.php");
            exit;
        }

        // 2️⃣ Validar correo
        if ($nuevoCorreo && $nuevoCorreo !== $confirmCorreo) {
            $_SESSION['error'] = "Los correos no coinciden";
            header("Location: /project-cpr/public/perfil.php");
            exit;
        }

        // 3️⃣ Validar contraseña nueva
        if ($nuevaContra && $nuevaContra !== $confirmContra) {
            $_SESSION['error'] = "Las contraseñas no coinciden";
            header("Location: /project-cpr/public/perfil.php");
            exit;
        }

        // 4️⃣ Actualizar
        User::updatePerfil(
            $idUsuario,
            $nuevoCorreo ?: $usuario['correo'],
            $nuevaContra
        );

        $_SESSION['success'] = "Perfil actualizado correctamente";
        header("Location: /project-cpr/public/perfil.php");
        exit;
    }
}
