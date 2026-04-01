<?php


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require '../app/controllers/IndexController.php';
$controller = new IndexController();
$controller->index();
