<?php 
// Configuracion central de la conexion a la base de datos (PDO).
// Este archivo se incluye desde controladores/modelos que requieren acceso a BD.

$host = 'localhost';
$port = '3306';
$dbname = 'project-cpr';
$user = 'root';
$pass = '';

// DSN: Data Source Name con motor, host, puerto, base de datos y codificacion.
$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

try {
    // Se crea la instancia PDO para reutilizarla en consultas.
    $pdo = new PDO($dsn, $user, $pass);
 /*  echo "conexion exitosa"; */
} catch(PDOException $e) {
    // En entorno local muestra el error de conexion.
    echo "conexion fallida". $e->getMessage();
}

?>
