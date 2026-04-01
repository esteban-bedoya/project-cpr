<?php

require_once __DIR__ . '/../app/controllers/UsuarioController.php';

$controller = new UsuarioController();
$action = $_GET['action'] ?? 'index';

if (method_exists($controller, $action)) {
    $controller->$action();
} else {
    echo "Acción no válida";
}