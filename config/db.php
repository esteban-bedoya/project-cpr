<?php 
// Conexion PDO compartida del proyecto.

$host = 'localhost';
$port = '3306';
$dbname = 'project-cpr';
$user = 'root';
$pass = '';

// DSN con host, puerto, base y codificacion.
$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

try {
    // La variable global $pdo la usan varios modelos.
    $pdo = new PDO($dsn, $user, $pass);
 /*  echo "conexion exitosa"; */
} catch(PDOException $e) {
    // En entorno local muestra el error de conexion.
    echo "conexion fallida". $e->getMessage();
}

?>
