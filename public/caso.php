<?php
// Front controller de detalle de caso (ver/actualizar/mensajes).

// Configuracion de errores para entorno local.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



session_start();

require_once __DIR__ . '/../app/controllers/CasoController.php';

// Seguridad basica: exige sesion activa.
if (!isset($_SESSION['logged'])) {
    header("Location: /project-cpr/public/login.php");
    exit;
}

$controller = new CasoController();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    // Visualizacion del caso.
    $id = $_GET['id'] ?? null;
    if (!$id) {
        die("No se especificó el caso.");
    }

    $controller->show($id);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Acciones del detalle (actualizar o enviar mensaje).
    $action = $_POST['action'] ?? null;
    $id = $_POST['caso_id'] ?? null;

    if (!$action || !$id) {
        die("Acción o caso inválido.");
    }

    switch ($action) {
        case 'updateDetalle':
            $controller->updateDetalle($id);
            break;

        case 'mensaje':
            $controller->storeMensaje($id);
            break;

        default:
            die("Acción no válida.");
    }
}
