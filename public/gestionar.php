<?php
// Front controller de la vista "Gestionar" para comisionados.

// Configuracion de errores para entorno local.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Verificar que el usuario esté logueado y tenga rol comisionado (2)
if (!isset($_SESSION['logged']) || $_SESSION['user']['rol'] != 2) {
    header("Location: /project-cpr/public/login.php");
    exit;
}

require_once __DIR__ . '/../app/controllers/CasoController.php';

// Instancia del controlador de casos.
$controller = new CasoController();

// Detectar acción enviada desde formulario o enlace
$action = $_GET['action'] ?? ($_POST['action'] ?? null);

switch ($action) {
    case 'storeGestionar':
        // Llamamos al nuevo método para crear casos desde gestionar
        $controller->storeGestionar();
        break;

    default:
        // Si no hay acción, mostrar la lista filtrada
        $controller->gestionarFiltrado();
        break;
}
