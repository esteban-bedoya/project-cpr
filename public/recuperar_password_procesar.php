<?php
// Procesa la solicitud de recuperacion de contraseña.

session_start();

require_once '../app/controllers/AuthController.php';

$controller = new AuthController();
$controller->requestPasswordReset();
