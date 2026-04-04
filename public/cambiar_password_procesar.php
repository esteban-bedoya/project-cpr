<?php
// Procesa el cambio de contraseña desde el enlace de recuperacion.

session_start();

require_once '../app/controllers/AuthController.php';

$controller = new AuthController();
$controller->resetPassword();
