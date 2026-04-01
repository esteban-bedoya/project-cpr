<?php
// Front controller de administracion de usuarios (solo admin).

session_start();
// Seguridad: solo rol 1 (admin) puede acceder.
if (!isset($_SESSION['logged']) || $_SESSION['user']['rol'] != 1) {
    header("Location: /project-cpr/public/login.php");
    exit;
}

require_once __DIR__ . '/../app/controllers/UsuarioController.php';

// Enruta acciones dinamicamente al controlador.
$controller = new UsuarioController();
$action = $_GET['action'] ?? 'index';
$controller->$action();
