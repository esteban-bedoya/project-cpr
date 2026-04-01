<?php
// Front controller de busqueda de casos (vista segun rol).
session_start();

// ===============================
// SEGURIDAD
// ===============================
if (!isset($_SESSION['user'])) {
    header("Location: /project-cpr/public/login.php");
    exit;
}

// ===============================
// CARGAR VISTA SEGÚN ROL
// ===============================
$rol = $_SESSION['user']['rol'];

if ($rol == 1) {
    require __DIR__ . '/../app/views/admin/casos.php';
} else if ($rol == 2) {
    require __DIR__ . '/../app/views/comisionado/busqueda.php';
} else {
    echo "Rol no permitido.";
    exit;
}
