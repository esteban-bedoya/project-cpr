<?php
// Front controller de tipos de proceso (solo admin).

session_start();

if (!isset($_SESSION['logged']) || ($_SESSION['user']['rol'] ?? null) != 1) {
    header("Location: /project-cpr/public/login.php");
    exit;
}

require_once __DIR__ . '/../app/controllers/TipoProcesoController.php';

$controller = new TipoProcesoController();
$action = $_GET['action'] ?? 'index';

if (method_exists($controller, $action)) {
    $controller->$action();
} else {
    echo "Acción no válida";
}
