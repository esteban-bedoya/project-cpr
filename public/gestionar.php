<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


session_start();

if (!isset($_SESSION['logged']) || $_SESSION['user']['rol'] != 2) {
    header("Location: /project-cpr/public/login.php");
    exit;
}


require_once __DIR__ . '/../app/controllers/CasoController.php';

$controller = new CasoController();
$controller->gestionar();