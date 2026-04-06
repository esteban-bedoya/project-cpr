<?php
// Controlador de perfil: datos del usuario logueado.

require_once __DIR__ . '/../models/User.php';

class PerfilController
{
    public function index()
    {
        // Marca el menu activo en la vista.
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
    // Actualiza correo y/o contraseña del usuario logueado.
    $idUsuario = $_SESSION['user']['id'];

    $nuevoCorreo   = trim($_POST['nuevo_correo'] ?? '');
    $confirmCorreo = trim($_POST['confirm_correo'] ?? '');

    $nuevaContra   = trim($_POST['nueva_contra'] ?? '');
    $confirmContra = trim($_POST['confirm_contra'] ?? '');

    $actualContra  = $_POST['actual_contra'] ?? '';

    $usuario = User::findById($idUsuario);

    // Primero confirma la identidad del usuario.
    if (!password_verify($actualContra, $usuario['password'])) {
        $_SESSION['error'] = "La contraseña actual es incorrecta.";
        header("Location: /project-cpr/public/perfil.php");
        exit;
    }

    $cambioCorreo = false;
    $cambioContra = false;

    // Si cambia el correo, debe confirmarlo.
    if ($nuevoCorreo !== '') {
        if ($nuevoCorreo !== $confirmCorreo) {
            $_SESSION['error'] = "Los correos no coinciden.";
            header("Location: /project-cpr/public/perfil.php");
            exit;
        }
        $cambioCorreo = true;
    }

    // Si cambia la clave, tambien debe confirmarla.
    if ($nuevaContra !== '') {
        if ($nuevaContra !== $confirmContra) {
            $_SESSION['error'] = "Las contraseñas no coinciden.";
            header("Location: /project-cpr/public/perfil.php");
            exit;
        }
        $cambioContra = true;
    }

    // Evita guardar si no hubo cambios.
    if (!$cambioCorreo && !$cambioContra) {
        $_SESSION['error'] = "No se realizaron cambios.";
        header("Location: /project-cpr/public/perfil.php");
        exit;
    }

    // Solo se envia lo que realmente cambio.
    User::updatePerfil(
        $idUsuario,
        $cambioCorreo ? $nuevoCorreo : $usuario['correo'],
        $cambioContra ? $nuevaContra : null
    );

    // Mensaje segun el resultado final.
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
