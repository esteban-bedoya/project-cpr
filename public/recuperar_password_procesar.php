<?php
// Procesa la solicitud de recuperacion de contraseña.

session_start();

require_once '../app/controllers/AuthController.php';

// El controlador genera token y correo.
$controller = new AuthController();
$controller->requestPasswordReset();
