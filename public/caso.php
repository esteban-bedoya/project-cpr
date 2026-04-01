<?php
// public/caso.php

require_once __DIR__ . '/../app/controllers/CasoController.php';
session_start();

$controller = new CasoController();

// Si es POST, actualizamos el caso
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    if (!$id) die("No se especificó el caso.");
    $controller->updateDetalle($id);
    exit; // updateDetalle ya hace redirección
}

// Si es GET, mostramos el caso
$id = $_GET['id'] ?? null;
if (!$id) die("No se especificó el caso.");

$controller->show($id);
