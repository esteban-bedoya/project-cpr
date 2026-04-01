<?php
// Front controller de busqueda de casos (vista segun rol).
session_start();

// ===============================
// SEGURIDAD
// ===============================
if (!isset($_SESSION['user'])) {
    header("Location: /project-cpr/public/login.php");
    exit;
}

require_once __DIR__ . '/../app/controllers/CasoController.php';

// Cargamos el controlador para listar y filtrar.
$controller = new CasoController();
$action = $_GET['action'] ?? ($_POST['action'] ?? null);

switch ($action) {
    case 'storeGestionar':
        $controller->storeGestionar();
        break;
    default:
        $controller->index();
        break;
}
