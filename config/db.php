<?php
$host = 'localhost';
$user = 'root';
$password = ''; // SIN contraseña
$dbname = 'gescon';

$conn = new mysqli($host, $user, $password, $dbname);
// $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
} else {
    //echo "conectado";
    // $pdo = new PDO($dsn, $user, $pass, $options);
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
    }
}
?>
