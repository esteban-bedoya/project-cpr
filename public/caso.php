<?php
// public/caso.php




require_once __DIR__ . '/../app/controllers/CasoController.php';

// Iniciar el controller
$controller = new CasoController();

// Verificar que llegue un ID por GET
$id = $_GET['id'] ?? null;
if (!$id) {
    die("No se especificó el caso.");
}

// Llamar al método show del controller
$controller->show($id);
