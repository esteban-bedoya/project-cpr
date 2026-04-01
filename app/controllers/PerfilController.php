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

    $idUsuario = $_SESSION['user']['id'];

    $nuevoCorreo   = trim($_POST['nuevo_correo'] ?? '');
    $confirmCorreo = trim($_POST['confirm_correo'] ?? '');

    $nuevaContra   = trim($_POST['nueva_contra'] ?? '');
    $confirmContra = trim($_POST['confirm_contra'] ?? '');

    $actualContra  = $_POST['actual_contra'] ?? '';

    $usuario = User::findById($idUsuario);

    // 1️⃣ Validar contraseña actual
    if (!password_verify($actualContra, $usuario['password'])) {
        $_SESSION['error'] = "La contraseña actual es incorrecta.";
        header("Location: /project-cpr/public/perfil.php");
        exit;
    }

    $cambioCorreo = false;
    $cambioContra = false;

    // 2️⃣ Validar y preparar correo
    if ($nuevoCorreo !== '') {
        if ($nuevoCorreo !== $confirmCorreo) {
            $_SESSION['error'] = "Los correos no coinciden.";
            header("Location: /project-cpr/public/perfil.php");
            exit;
        }
        $cambioCorreo = true;
    }

    // 3️⃣ Validar y preparar contraseña
    if ($nuevaContra !== '') {
        if ($nuevaContra !== $confirmContra) {
            $_SESSION['error'] = "Las contraseñas no coinciden.";
            header("Location: /project-cpr/public/perfil.php");
            exit;
        }
        $cambioContra = true;
    }

    // 4️⃣ Si no cambió nada
    if (!$cambioCorreo && !$cambioContra) {
        $_SESSION['error'] = "No se realizaron cambios.";
        header("Location: /project-cpr/public/perfil.php");
        exit;
    }

    // 5️⃣ Actualizar
    User::updatePerfil(
        $idUsuario,
        $cambioCorreo ? $nuevoCorreo : $usuario['correo'],
        $cambioContra ? $nuevaContra : null
    );

    // 6️⃣ Mensaje final claro
    if ($cambioCorreo && $cambioContra) {
        $_SESSION['success'] = "Correo y contraseña actualizados correctamente.";
    } elseif ($cambioCorreo) {
        $_SESSION['success'] = "Correo actualizado correctamente.";
    } elseif ($cambioContra) {
        $_SESSION['success'] = "Contraseña actualizada correctamente.";
    }

    header("Location: /project-cpr/public/perfil.php");
    exit;
}
}
