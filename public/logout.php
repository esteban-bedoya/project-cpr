<?php
// Archivo de cierre de sesion (logout).
// Limpia sesion y token persistente si existe.

session_start();

// Solo consulta BD si hay cookie persistente.
$require_db = isset($_COOKIE['remember_token']) && isset($_SESSION['user_id']);

// La conexion se carga solo cuando hace falta.
if ($require_db) {
    require '../config/db.php'; // ruta desde public/logout.php hacia config/db.php
}

try {
    if ($require_db) {
        // Borra la cookie del navegador.
        setcookie('remember_token', '', time() - 3600, '/', '', true, true);

        // Borra el token guardado en BD.
        $stmt = $pdo->prepare("UPDATE users SET remember_token = NULL WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
    }

    // Cierra la sesion actual.
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
