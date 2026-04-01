<?php

session_start();
if (!isset($_SESSION['logged']) || $_SESSION['user']['rol'] != 1) {
    header("Location: /project-cpr/public/login.php");
    exit;
}

require_once __DIR__ . '/../app/controllers/UsuarioController.php';

$controller = new UsuarioController();
$action = $_GET['action'] ?? 'index';
$controller->$action();
