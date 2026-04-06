<?php
// Procesa el cambio de contraseña desde el enlace de recuperacion.

session_start();

require_once '../app/controllers/AuthController.php';

// El controlador centraliza la validacion del token.
$controller = new AuthController();
$controller->resetPassword();
