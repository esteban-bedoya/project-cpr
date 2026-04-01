<?php

session_start();

require_once '../app/controllers/AuthController.php';

$controller = new AuthController();
$controller->login();
