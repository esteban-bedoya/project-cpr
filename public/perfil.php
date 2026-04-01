<?php
// Front controller del perfil: enruta acciones del usuario.
session_start();

// Seguridad: exige sesion activa.
if (!isset($_SESSION['logged'])) {
    header("Location: /project-cpr/public/login.php");
    exit;
}

require_once __DIR__ . '/../app/controllers/PerfilController.php';

// Determina la accion a ejecutar (GET por defecto).
$controller = new PerfilController();
$action = $_GET['action'] ?? 'index';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // En POST se mantiene la accion definida.
    $action = $_GET['action'] ?? $action;
}

if (method_exists($controller, $action)) {
    $controller->$action();
} else {
    echo "Acción no válida";
}
