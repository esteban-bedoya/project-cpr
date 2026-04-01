<?php
/**
 * Logout adaptado para PROJECT-CPR
 * Cierra sesión, elimina cookies y remember_token si aplica, y redirige a login.php
 */

session_start();

// Determinar si necesitamos conexión a la base de datos
$require_db = isset($_COOKIE['remember_token']) && isset($_SESSION['user_id']);

// Incluir db.php solo si es necesario
if ($require_db) {
    require '../config/db.php'; // ruta desde public/logout.php hacia config/db.php
}

try {
    if ($require_db) {
        // Eliminar remember_token de la cookie
        setcookie('remember_token', '', time() - 3600, '/', '', true, true);

        // Eliminar remember_token de la base de datos
        $stmt = $pdo->prepare("UPDATE users SET remember_token = NULL WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
    }

    // Destruir sesión
    session_destroy();

    // Eliminar cookies de sesión si existen
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 3600, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    }

    // Redirigir a login.php
    header("Location: login.php?msg=logout_success");
    exit();

} catch (Exception $e) {
    // Registrar error en log y redirigir con mensaje de fallo
    error_log("Error al cerrar sesión: " . $e->getMessage());
    header("Location: login.php?error=logout_failed");
    exit();
}
?>
