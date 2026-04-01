<?php
// Front controller de la pagina publica de inicio.


// Configuracion de errores para entorno local.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Carga del controlador y ejecucion de la accion principal.
require '../app/controllers/IndexController.php';
$controller = new IndexController();
$controller->index();
