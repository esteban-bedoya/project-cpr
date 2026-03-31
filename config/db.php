<?php 

$host = 'localhost';
$port = '3306';
$dbname = 'project-cpr';
$user = 'root';
$pass = '';

$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass);
 /*  echo "conexion exitosa"; */
} catch(PDOException $e) {
    echo "conexion fallida". $e->getMessage();
}

?>