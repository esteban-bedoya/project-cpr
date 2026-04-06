<?php
// Procesa el formulario de login (punto de entrada POST).

// Inicia sesion para guardar mensajes y datos del usuario.
session_start();

require_once '../app/controllers/AuthController.php';

// Delega el login en el controlador.
$controller = new AuthController();
$controller->login();
