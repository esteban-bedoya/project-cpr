<?php
session_start();

if (!isset($_SESSION['logged'])) {
    header("Location: /project-cpr/public/login.php");
    exit;
}

require_once __DIR__ . '/../app/controllers/PerfilController.php';

$controller = new PerfilController();
$action = $_GET['action'] ?? 'index';

if (method_exists($controller, $action)) {
    $controller->$action();
} else {
    echo "Acción no válida";
}
